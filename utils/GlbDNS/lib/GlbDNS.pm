package GlbDNS;

use 5.008008;
use strict;
use warnings;
our $VERSION = '0.01';
use Net::DNS::Nameserver;
use Data::Dumper;
use threads;
use threads::shared;
use LWP::Simple;
use List::Util qw(sum);
my %status : shared;
my %stats : shared;
use Geo::IP;

my $gi = Geo::IP->open_type( GEOIP_CITY_EDITION_REV1, GEOIP_STANDARD);

sub new {
    my $class = shift;
    my $self = bless {}, $class;
    my $daemon = shift;
    $self->{name} = $daemon->name;
    $self->{dns} = Net::DNS::Nameserver->new(
        Verbose => $main::config{debug},
        LocalAddr => $daemon->options->{address},
        LocalPort => $daemon->options->{port},
        ReplyHandler => sub { $self->request(@_) },
        );

    return $self;
}

sub add_config {
    my $self = shift;
    my $config = shift;
    $self->{config}->{ref($config)} = $config;

    foreach my $domain ($config->domains) {
        $self->{domain}->{$domain} = $config;
    }

    foreach my $check (values %{$config->{_check}}) {
        $status{$check->{ip}} = 0;
        threads->create('check_service', $check->{ip}, $check->{url}, $check->{expect});
    }

}

sub check_service {

    my ($ip, $url, $expect) = @_;
    while(1) {
        my $foo = get("http://$ip/$url");
        if ($foo && $foo =~/$expect/) {
            $status{$ip} = $status{$ip} + 1;
        } else {
            $status{$ip} = 0;
        }
        sleep 1;
    }
}

sub check_status {
    my $self = shift;
    my $rr = shift;

}

sub start {
    my $self = shift;
    $0 = "$self->{name} worker - waiting for status checks before accepting requests";
    while(keys %status && sum(values %status) == 0) {
        sleep 1;
    }
    $0 = "$self->{name} worker - accepting requests";
    $self->{dns}->main_loop;
}

sub request {
    my ($self, $qname, $qclass, $qtype, $peerhost, $query) = @_;
    my ($rcode, $ans, $auth, $add) = (undef, [], [], []);

    my @query = split(/\./, $qname);
    warn "Got query $qname foo";
    my $host = $self->{hosts}->{$qname};
    return ("NXDOMAIN", [],[],[],{}) unless($host);

    my $domain = $self->get_host($host->{domain});

    if ($host->{CNAME}) {
        push @$ans, $self->lookup($qname, "CNAME", $host, $peerhost);
        $qname = $host->{CNAME}->[0]->cname;
        $host = $self->{hosts}->{$qname};
    }

    if ($qtype eq 'ANY' || $qtype eq 'A' || $qtype eq 'PTR') {
        push @$ans, $self->lookup($qname, $qtype, $host, $peerhost);
    }

    if ($qtype eq 'ANY' || $qtype eq 'NS') {
        push @$ans, @{$domain->{NS}};
    }
    if ($qtype eq 'ANY' || $qtype eq 'SOA') {
        push @$ans, $domain->{SOA};
    }
    if ($qtype eq 'ANY' || $qtype eq 'MX') {
        push @$ans, values %{$domain->{MX}};
    }


    $auth = $domain->{NS};

    foreach my $ns (@$auth) {
        my $ns_domain = $self->get_host($ns->nsdname);
        if ($ns_domain) {
            push @$add, $self->lookup($ns->nsdname, "A", $ns_domain, $peerhost);
        }
    }

    $rcode = "NOERROR";

    return ($rcode, $ans, $auth, $add, { aa => 1 });
}




sub lookup {
    my $self = shift;
    my $qname = shift;
    my $qtype = shift;
    my $host = shift;
    my $peerhost = shift;
    my @answer;

    return unless $host;

    if (my $geo = $host->{geo}) {

        my $record = $gi->record_by_addr($peerhost);
        my $location;
        if($record) {
            my ($lat, $lon) = (0,0);
            $lat = $record->latitude;
            $lon = $record->longitude;

            my %distance;
            foreach my $server (keys %$geo) {

                $distance{$server} = $self->distance($geo->{$server}->{lat}, $geo->{$server}->{lon}, $lat, $lon);
            }

            my @answer;
            foreach my $server (@{[sort { $distance{$a} <=> $distance{$b} } keys %distance ]}) {
                print "Distance $server $geo->{$server}->{radius} < $distance{$server}\n" if($geo->{$server}->{radius});
                next if ($geo->{$server}->{radius} &&
                         $geo->{$server}->{radius} < $distance{$server});

                foreach my $host_data (@{$geo->{$server}->{hosts}}) {
                    my $host = $host_data->[0];
                    my $chance = $host_data->[1];
                    my $key = $host->type eq 'A' ? $host->address : $host->cname;
                    push @answer, $host if (!exists $status{$key} || $status{$key});

                }
                return (@answer) if (@answer);
            }
        }
    }

    if ($qtype eq 'ANY') {
        push @answer, @{$host->{A}} if $host->{A};
        push @answer, @{$host->{AAAA}} if $host->{AAAA};
        push @answer, @{$host->{CNAME}} if $host->{CNAME};
    } else {
        push @answer, @{$host->{$qtype}} if ($host->{$qtype});
    }
    return @answer;
}

sub get_host {
    my $self = shift;
    my $qname = shift;
    my @query = split(/\./, $qname);
    while(@query) {
        my $test_domain = join (".", @query);
        if($self->{hosts}->{$test_domain}) {
            return $self->{hosts}->{$test_domain};
        }
        shift @query;
    }
    return;
}


my $pi = atan2(1,1) * 4;
my $earth_radius = 6378;

sub distance {
    my ($self, $tlat, $tlon, $slat, $slon) = @_;

    my $tlat_r = int($tlat) * ($pi/180);
    my $tlon_r = int($tlon) * ($pi/180);
    my $slat_r = int($slat) * ($pi/180);
    my $slon_r = int($slon) * ($pi/180);

#    print "$tlat $tlon => $slat $slon\n";
#    print "$tlat_r $tlon_r => $slat_r $slon_r\n";

    my $delta_lat = $slat_r - $tlat_r;
    my $delta_lon = $slon_r - $tlon_r;

    my $temp = sin($delta_lat/2.0)**2 + cos($tlat_r) * cos($slat_r) * sin($delta_lon/2.0)**2;

    return (atan2(sqrt($temp),sqrt(1-$temp)) * 12756.32);
}

1;
__END__
# Below is stub documentation for your module. You'd better edit it!

=head1 NAME

GlbDNS - Perl extension for blah blah blah

=head1 SYNOPSIS

  use GlbDNS;
  blah blah blah

=head1 DESCRIPTION

Stub documentation for GlbDNS, created by h2xs. It looks like the
author of the extension was negligent enough to leave the stub
unedited.

Blah blah blah.

=head2 EXPORT

None by default.



=head1 SEE ALSO

Mention other useful documentation such as the documentation of
related modules or operating system documentation (such as man pages
in UNIX), or any relevant external documentation such as RFCs or
standards.

If you have a mailing list set up for your module, mention it here.

If you have a web site set up for your module, mention it here.

=head1 AUTHOR

Artur Bergman, E<lt>sky@apple.comE<gt>

=head1 COPYRIGHT AND LICENSE

Copyright (C) 2008 by Artur Bergman

This library is free software; you can redistribute it and/or modify
it under the same terms as Perl itself, either Perl version 5.8.8 or,
at your option, any later version of Perl 5 you may have available.


=cut
