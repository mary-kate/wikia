package GlbDNS::Config;


use strict;
use warnings;
use Data::Dumper;
use Net::DNS::RR::A;

sub load_configs {
    my $class = shift;
    my $glbdns = shift;
    my $path = shift;
    opendir(DIR, $path) || die "$!";
    local($/);
    for my $file (readdir(DIR)) {
        next if (-d $file);
        next if ($file =~/^(\.|#)/);
        next if ($file =~/~$/);
        my $mtime = @{[stat("$path/$file")]}[9];
        open(CONF, "<", "$path/$file") || die;
        my $package_name = $file;
        $package_name =~s/\W//;
        $package_name = "GlbDNS::Config::$package_name";
        my $config = <CONF>;
        eval "
package $package_name;
#line 1 $path/$file
$config
";
        die $@ if $@;
#        print "loaded GlbDNS::Config::$package_name\n";
        close(CONF);
        if($package_name->can('new')) {
            $package_name->new($glbdns, $mtime);
        } else {
            warn "Loaded $package_name from $file but no method 'new' found\n";
        }


    }



}

sub new {
    my $config = bless {}, shift;
    my $glbdns  = shift;
    my $mtime   = shift;
    foreach my $base ($config->domains) {
        foreach my $record (split "\n", $config->hosts($base)) {
            next unless $record;
            my @record = split /\s+/, $record;

            if ($record[0] !~ /^\d+$/) {
                $record[0] = "$record[0].$base";
            } else {
                unshift @record, $base;
            }
            my $rr = Net::DNS::RR->new(join " ", @record);

            if ($rr->type eq 'A') {

                my @address = reverse(split(/\./, $rr->address));


                my $address = join(".", reverse( split(/\./, $rr->address)) ) ;
                my $reverse = Net::DNS::RR->new("$address.in-addr.arpa. " . $rr->ttl . " IN PTR  " . $rr->name);
                add_host($glbdns, join(".", @address[1..3], "in-addr","arpa."), $reverse);
                add_host($glbdns, $base, $rr);
            } elsif ($rr->type eq 'CNAME') {
                add_host($glbdns, $base, $rr);
            } elsif ($rr->type eq 'SOA') {
                $glbdns->{hosts}->{$rr->name}->{SOA} = $rr;
                my ($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = gmtime($mtime);
                $rr->serial($year+1900 . "$mon$mday$hour$min");
            } elsif ($rr->type eq 'MX') {
                $glbdns->{hosts}->{$rr->name}->{MX}->{$rr->exchange} = $rr;
            } else {
                die Dumper($rr);
            }
            $glbdns->{hosts}->{$rr->name}->{domain} = $base;
        }


        my $geo = $config->geo($base);
        foreach my $host (keys %$geo) {
            $glbdns->{hosts}->{$host}->{domain} = $base;

            foreach my $location_name (keys %{$geo->{$host}}) {
                my $location = $glbdns->{hosts}->{$host}->{geo}->{$location_name} = {};
                my $hosts = $location->{hosts} = [];
                $location->{lat} = $geo->{$host}->{$location_name}->{lat};
                $location->{lon} = $geo->{$host}->{$location_name}->{lon};
                $location->{radius} = $geo->{$host}->{$location_name}->{radius};

                foreach my $ip (keys %{$geo->{$host}->{$location_name}->{servers}}) {
                    my $weight = $geo->{$host}->{$location_name}->{servers}->{$ip};

                    if($ip =~/\d+\.\d+\.\d+\.\d+/) {
                        push  @$hosts, [ Net::DNS::RR::A->new({name => $host,
                                                               ttl     => ($geo->{$host}->{ttl} || 60),
                                                               type    => 'A',
                                                               class   => 'IN',
                                                               address => $ip,
                                                               weight  => $weight,
                                                               }) ]
                    } else {
                        push @$hosts, [ Net::DNS::RR::CNAME->new({name => $host,
                                                                  ttl    => ($geo->{$host}->{ttl} || 60),
                                                                  type   => 'CNAME',
                                                                  class  => 'IN',
                                                                  cname  => $ip,
                                                                  weight => $weight,
                                                                  }) ]
                    }
                    if (exists $geo->{$host}->{$location_name}->{check_type}  &&
                        $geo->{$host}->{$location_name}->{check_type} eq 'http' ) {
                        $glbdns->{checks}->{$ip} = {
                            ip     => $ip,
                            url    => $geo->{$host}->{$location_name}->{url},
                            expect => $geo->{$host}->{$location_name}->{expect},
                            status => 0 };
                    }
                }
            }
        }

        my $ns = $glbdns->{hosts}->{$base}->{NS} = [];
        foreach my $record (split "\n", $config->ns($base)) {
            next unless $record;
            my $rr = Net::DNS::RR->new($base . " " . $record);
            die unless $rr->type eq 'NS';
            push @$ns, $rr;
        }
    }

    return $config;
}

sub add_host {
    my ($glbdns, $domain, $entry) = @_;
    if (my $list = $glbdns->{hosts}->{$entry->name}->{$entry->type}) {
        push @$list, $entry;
    } else {
        $glbdns->{hosts}->{$entry->name}->{$entry->type} = [$entry];
    }
}
1;
__END__

# data structure idea


{
    host => {
        ns => { any nameserver records },
        geo => { any geo data }
        mx => { mx }
        record => [$records],
    }

1;
