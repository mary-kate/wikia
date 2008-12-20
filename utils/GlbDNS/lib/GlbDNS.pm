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

sub new {
    my $class = shift;
    my $self = bless {}, $class;
    $self->{dns} = Net::DNS::Nameserver->new(
        Verbose => 1,
        ReplyHandler => sub { $self->request(@_) },
        );
    $self->{config} = GlbDNS::Config->new;

    foreach my $check (values %{$self->{config}->{_check}}) {
        $status{$check->{ip}} = 0;
        threads->create('check_service', $check->{ip}, $check->{url}, $check->{expect});
    }
    return $self;
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
    while(sum(values %status) == 0) {
        print "Waiting to start\n";
        sleep 1;
    }
    print "Starting the loop\n";
    $self->{dns}->main_loop;
}

sub request {
    my ($self, $qname, $qclass, $qtype, $peerhost, $query) = @_;
    my ($rcode, $ans, $auth, $add) = (undef, [], [], []);

    my @query = split(/\./, $qname);

    my $domain = $self->get_domain($qname);

    return ("NXDOMAIN", [],[],[],{}) unless($domain);

#    $query->print;

    if ($qtype eq 'A' || $qtype eq 'PTR' || $qtype eq 'CNAME') {
        ($rcode, $ans) = $self->lookup($qname, $domain);
    }

    $auth = $domain->{ns};

    foreach my $ns (@$auth) {
        my $ns_domain = $self->get_domain($ns->nsdname);
        if ($ns_domain) {
            my ($result, $host) = $self->lookup($ns->nsdname, $ns_domain);
            push @$add, @$host;
        }
    }

    return ($rcode, $ans, $auth, $add, { aa => 1 });
    }


#my ($lat, $lon) = (42,93);
my ($lat, $lon) = (0,0);

sub lookup {
    my $self = shift;
    my $qname = shift;
    my $domain = shift;

    if (my $geo = $domain->{geo}->{$qname}) {
        my %distance;
        foreach my $server (keys %$geo) {

            $distance{$server} = $self->distance($geo->{$server}->{lat}, $geo->{$server}->{lon}, $lat, $lon);
        }
        my @answer;
        foreach my $server (@{[sort { $distance{$a} <=> $distance{$b} } keys %distance ]}) {
            foreach my $host (@{$geo->{$server}->{hosts}}) {
                my $key = $host->type eq 'A' ? $host->address : $host->cname;
                push @answer, $host if (!exists $status{$key} || $status{$key});

            }
            return ('NOERROR', \@answer) if (@answer);
        }

    }

    if (my $reply = $domain->{host}->{$qname}) {
        return ('NOERROR', $reply);
    }
    return ('NXDOMAIN', []);
}

sub get_domain {
    my $self = shift;
    my $qname = shift;
    my @query = split(/\./, $qname);

    my $domain;
    while(@query) {
        last if $domain = $self->{config}->{join(".",@query) . "."};
        shift @query;
    }
    return $domain;
}


my $pi = atan2(1,1) * 4;
my $earth_radius = 6378;
sub distance {
    my ($self, $tlat, $tlon, $slat, $slon) = @_;

    my $tlat_r = $tlat * ($pi/180);
    my $tlon_r = $tlon * ($pi/180);
    my $slat_r = $slat * ($pi/180);
    my $slon_r = $slon * ($pi/180);

#    print "$tlat $tlon => $slat $slon\n";
#    print "$tlat_r $tlon_r => $slat_r $slon_r\n";

    my $delta_lat = $slat_r - $tlat_r;
    my $delta_lon = $slon_r - $tlon_r;

    my $temp = sin($delta_lat/2.0)**2 + cos($tlat_r) * cos($slat_r) * sin($delta_lon/2.0)**2;

    return atan2(sqrt($temp),sqrt(1-$temp));
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
