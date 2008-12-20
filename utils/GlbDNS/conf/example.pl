use strict;
use warnings;
use Net::DNS::RR;
use Data::Dumper;
use GlbDNS::Config;
package GlbDNS::Config;

sub domains { "example.net." }

sub ns { return q{

60 IN NS ns-lon-1.example.net.
60 IN NS ns-lon-2.example.net.
60 IN NS ns-sjc-1.example.net.
60 IN NS ns-nj-1.example.net.

}}

sub geo {
    return {
        "glb.example.net" => {
            "iowa"  => { lon => '42',
                         lat => '91',
                         servers => ['208.68.167.146'],
                         ttl => 60,
                         check_type => 'http',
                         url => 'lvscheck.html',
                         expect => 'is okay',
            },
            "lon"   => { lon => '0',
                         lat => '51',
                         servers => ['83.223.112.142','83.223.112.138'],
                         ttl => 60,
                         check_type => 'http',
                         url => 'lvscheck.html',
                         expect => 'is okay',
            },
            "sjc"   => { lon => "121",
                         lat => '37',
                         servers => ["216.224.121.143", "216.151.156.12"],
                         ttl => 60,
                         check_type => 'http',
                         url => 'lvscheck.html',
                         expect => 'is okay',
            },
        },
        "glb-images.example.net" => {
            "lon"   => { lon => '0',
                         lat => '51',
                         servers => ['83.223.112.142','83.223.112.138'],
                         ttl => 60,
                         check_type => 'http',
                         url => 'lvscheck.html',
                         expect => 'is okay',
            },
            "panther"   => { lon => "42",
                             lat => '92',
                             servers => ["g1.panthercnd.com"],
                             ttl => 60,
            },
        }
    };
}

sub hosts {q{

600 IN SOA ns1.wikia.net. dnsmaster.wikia-inc.com.




180 IN MX 1 ASPMX.L.GOOGLE.COM.
180 IN MX 3 ALT1.ASPMX.L.GOOGLE.COM.
180 IN MX 3 ALT2.ASPMX.L.GOOGLE.COM.
180 IN MX 5 ASPMX2.GOOGLEMAIL.COM.
180 IN MX 5 ASPMX3.GOOGLEMAIL.COM.
180 IN MX 5 ASPMX4.GOOGLEMAIL.COM.
180 IN MX 5 ASPMX5.GOOGLEMAIL.COM.

googleffffffffdfb14994 60 IN CNAME google.com.



ns1             600 IN A         76.9.5.203
ns2             600 IN A         216.224.121.138
ns5             600 IN A 63.219.151.12
ns6             600 IN A 64.246.42.203
ns7             600 IN A 205.234.170.139

edge5           60 IN A 208.68.167.146
edge6           60 IN A 208.68.167.150
edge5-usshc     60 IN A 208.68.167.146
edge6-usshc     60 IN A 208.68.167.150

core5           60 IN A 208.68.167.130
core6           60 IN A 208.68.167.131
io-core-vip     60 IN A 208.68.167.129

edge5-edge6-2   60 IN A 208.68.162.161
edge6-edge5-2   60 IN A 208.68.162.162

edge5-edge6-4   60 IN A 208.68.162.165
edge6-edge5-4   60 IN A 208.68.162.166

edge5-core5     60 IN A 208.68.162.169
core5-edge5     60 IN A 208.68.162.170

core5-edge6     60 IN A 208.68.162.173
edge6-core5     60 IN A 208.68.162.174

core5-core6     60 IN A 208.68.162.177
core6-core5     60 IN A 208.68.162.178

edge6-core6     60 IN A 208.68.162.181
core6-edge6     60 IN A 208.68.162.182

edge5-core6     60 IN A 208.68.162.185
core6-edge5     60 IN A 208.68.162.186




ns-lon-1        60 IN A 83.223.112.142
ns-lon-2        60 IN A 83.223.112.138
ns-sjc-1        60 IN A 216.224.121.143
ns-nj-1         60 IN A 76.9.5.204

london-cache    60 IN A 83.223.112.142
london-cache    60 IN A 83.223.112.138

fooo 60 IN A 127.0.0.1
fooo 60 IN A 127.0.0.2
foo2 60 IN A 127.0.0.1


}}



1;

