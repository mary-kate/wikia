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
use List::Util qw(shuffle);
my %counters : shared;

my $gi = Geo::IP->open_type( GEOIP_CITY_EDITION_REV1, GEOIP_STANDARD);


#to enable testing
our %TEST = ( noadmin => 0,
              nosocket => 0
    );

sub new {
    my $class = shift;
    my $self = bless {}, $class;
    my $daemon = shift;
    $self->{name} = $daemon->name;

    $self->{dns} = Net::DNS::Nameserver->new(
        Verbose => $main::config{debug} || 0,
        LocalAddr => $daemon->options->{address},
        LocalPort => $daemon->options->{port},
        ReplyHandler => sub { $self->request(@_) },
        ) unless ($TEST{nosocket});

    #threads->create(sub { while(1) { sleep 60; print Dumper(\%counters) } });
    threads->create(\&admin) unless ($TEST{noadmin});

    return $self;
}

sub admin {
    my $sock = IO::Socket::INET->new
        (Listen    => 5,
         LocalAddr => 'localhost',
         LocalPort => 9000,
         Proto     => 'tcp',
         Reuse     => 1
        );
    while(my $connection = $sock->accept) {
        $connection->print(Dumper \%counters);
        $connection->print(Dumper \%status);
        close($connection);
    }
}

sub check_service {

    my ($ip, $url, $expect, $interval) = @_;
    while(1) {
        my $foo = get("http://$ip/$url");
        if ($foo && $foo =~/$expect/) {
            $status{$ip} = $status{$ip} + 1;
        } else {
            $status{$ip} = 0;
        }
        sleep $interval;
    }
}

sub start {
    my $self = shift;
    $0 = "$self->{name} worker - waiting for status checks before accepting requests";
    while(keys %status && sum(values %status) == 0) {
        sleep 1;
    }
    $0 = "$self->{name} worker - accepting requests";

    foreach my $check (values %{$self->{checks}}) {
        $status{$check->{ip}} = 0;
        threads->create('check_service', $check->{ip}, $check->{url}, $check->{expect}, ($check->{interval} || 5));
    }

    $self->{dns}->main_loop;
}

sub request {
    my ($self, $qname, $qclass, $qtype, $peerhost, $query) = @_;
    my ($rcode, $ans, $auth, $add) = (undef, [], [], []);
    $counters{"Lookup|" .$qname}++;
    my $response_incudes_ns = 0;

    $counters{Request}++;

    my @query = split(/\./, $qname);

    my $host = $self->{hosts}->{$qname};
    unless($host) {
        my $domain = $self->get_host($qname);
        if ($domain) {
            $domain = $self->{hosts}->{$domain->{__DOMAIN__}};
            return ("NXDOMAIN", [], $domain->{SOA}, [],{ aa => 1});
        }
        return ("REFUSED", [], [], [],{ aa => 0});
    }

    my $domain = $self->get_host($host->{domain});

    if (($qtype eq 'ANY' || $qtype eq 'CNAME' || $qtype eq 'A' || $qtype eq 'AAAA') && $host->{CNAME}) {
        push @$ans, $self->lookup($qname, "CNAME", $host, $peerhost);
        $qname = $host->{CNAME}->[0]->cname;
        $host = $self->{hosts}->{$qname};
    }

    if ($qtype eq 'ANY' || $qtype eq 'A' || $qtype eq 'PTR') {
        push @$ans, $self->lookup($qname, $qtype, $host, $peerhost);
    }

    if ($qtype eq 'ANY' || $qtype eq 'AAAA') {
        my @answer = $self->lookup($qname, $qtype, $host, $peerhost);
        # if we get a specific AAAA query
        # and this host exists (otherwise we wouldnt have come this far
        # then we have to return to SOA in auth 0 ANS and NO ERROR
        # RFC 4074 4.1 and 4.2
        if($qtype eq 'AAAA' && !@answer && !@$ans) {
            return ("NOERROR", [], [@{$domain->{SOA}}], [], { aa => 1 });
        }
    }



    if ($qtype eq 'ANY' || $qtype eq 'NS') {
        push @$ans, @{$domain->{NS}};
        $response_incudes_ns++;
    }
    if ($qtype eq 'ANY' || $qtype eq 'SOA') {
        push @$ans, @{$domain->{SOA}};
    }
    if ($qtype eq 'ANY' || $qtype eq 'MX') {
        push @$ans, @{$domain->{MX}} if($domain->{MX}); # need test
        foreach my $mx (@{$domain->{MX}}) {
            my $mx_host = $self->get_host($mx->exchange);
            push @$add, $self->lookup($mx->exchange, "A", $mx_host, $peerhost);
        }
    }



    $auth = $domain->{NS} unless($response_incudes_ns);
    foreach my $ns (@{$domain->{NS}}) {
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
#    $peerhost = "207.216.165.95";
#    $peerhost = "75.101.17.33";
#    $peerhost = "216.224.121.134";
#    $peerhost = "82.138.248.236";
    if (my $geo = $host->{__GEO__}) {

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
                next if ($geo->{$server}->{radius} &&
                         $geo->{$server}->{radius} < $distance{$server});
                $counters{"Location|$qname|$server"}++;
                foreach my $host (@{$geo->{$server}->{hosts}}) {
                    my $key = $host->type eq 'A' ? $host->address : $host->cname;
                    push @answer, $host if (!exists $status{$key} || $status{$key});

                }
                if(@answer) {
                    @answer = shuffle(@answer);
                    push @answer, $geo->{$server}->{source}->{$qname} if($geo->{$server}->{source}->{$qname});
                    return @answer;
                }
            }
        }
        $counters{Failed_geo_look}++;
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
