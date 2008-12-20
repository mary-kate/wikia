package GlbDNS::Config;


use strict;
use warnings;


sub new {
    my $config = bless {}, shift;

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
                add_host($config, join(".", @address[1..3], "in-addr","arpa."), $reverse);
                add_host($config, $base, $rr);
            } elsif ($rr->type eq 'CNAME') {
                add_host($config, $base, $rr);
            } elsif ($rr->type eq 'SOA') {
                $config->{$base}->{soa} = $rr;
            } elsif ($rr->type eq 'MX') {
                $config->{$base}->{mx}->{$rr->exchange} = $rr;
            } else {
                die Dumper($rr);
            }
        }

        my $geo = $config->geo($base);
        foreach my $host (keys %$geo) {

            foreach my $location_name (keys %{$geo->{$host}}) {
                my $location = $config->{$base}->{geo}->{$host}->{$location_name} = {};
                my $hosts = $location->{hosts} = [];
                $location->{lat} = $geo->{$host}->{$location_name}->{lat};
                $location->{lon} = $geo->{$host}->{$location_name}->{lon};
                foreach my $ip (@{$geo->{$host}->{$location_name}->{servers}}) {
                    if($ip =~/\d+\.\d+\.\d+\.\d+/) {
                        push @$hosts, Net::DNS::RR::A->new({name => $host,
                                                            ttl     => ($geo->{$host}->{ttl} || 60),
                                                            type    => 'A',
                                                            class   => 'IN',
                                                            address => $ip,
                                                           });
                    } else {
                        push @$hosts, Net::DNS::RR::CNAME->new({name => $host,
                                                            ttl     => ($geo->{$host}->{ttl} || 60),
                                                            type    => 'CNAME',
                                                            class   => 'IN',
                                                            cname => $ip,
                                                           });
                    }
                    if (exists $geo->{$host}->{$location_name}->{check_type}  &&
                        $geo->{$host}->{$location_name}->{check_type} eq 'http' ) {
                        $config->{_check}->{$ip} = {
                            ip     => $ip,
                            url    => $geo->{$host}->{$location_name}->{url},
                            expect => $geo->{$host}->{$location_name}->{expect},
                            status => 0 };
                    }
                }
            }
        }

    }
    foreach my $domain (keys %$config) {
        next if($domain eq '_check');
        my $domains = $config->{$domain}->{ns} = [];
       foreach my $record (split "\n", $config->ns($domain)) {
           next unless $record;
           my $rr = Net::DNS::RR->new($domain . " " . $record);
           die unless $rr->type eq 'NS';
           push @$domains, $rr;
       }

    }
    






    return $config;
}

sub add_host {
    my ($config, $domain, $entry) = @_;
    if (my $list = $config->{$domain}{host}->{$entry->name}) {
        push @$list, $entry;
    } else {
        $config->{$domain}{host}->{$entry->name} = [$entry];
    }
}


1;
