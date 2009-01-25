# Before `make install' is performed this script should be runnable with
# `make test'. After `make install' it should work as `perl GlbDNS.t'

#########################

# change 'tests => 1' to 'tests => last_test_to_print';

use Test::More tests => 1;
BEGIN { use_ok('GlbDNS') };
BEGIN { use_ok('GlbDNS::Zone') };

$GlbDNS::TEST{nosocket} = 1;
$GlbDNS::TEST{noadmin}  = 1;

use Working::Daemon;
my $daemon = Working::Daemon->new();

$daemon->name("glbdns");
$daemon->parse_options(
    "port=i"     => 53             => "Which port number to listen to",
    "address=s"  => "0.0.0.0"      => "IP Address to listen to",
    "syslog"     => 0              => "Syslog",
    "config=s"   => "/etc/glbdns/" => "Configuration directory",
    "loglevel=i" => 1              => "What level of messaes to log, higher is more verbose",
    "zones=s"    => "zone/"        => "Where to find zone files",
    );


my $glbdns = GlbDNS->new($daemon);
isa_ok($glbdns, "GlbDNS");

eval { GlbDNS::Zone->load_configs($glbdns, "zones/broken_origin.zone") };
is($@, "'not.qualified' needs to be terminated with a . to be a FQDN at zones/broken_origin.zone:2\n", '$ORIGIN needs to be a FQDN');

eval { GlbDNS::Zone->load_configs($glbdns, "zones/no_origin.zone") };
is($@, "No \$ORIGIN domain has been specified, don't know what domain we are working on at zones/no_origin.zone:3\n", "And we need an origin");

eval { GlbDNS::Zone->load_configs($glbdns, "zones/doesntexist.zone") };
is($@, "Cannot find zone file 'zones/doesntexist.zone'\n", "Testing non existant file");
#GlbDNS::Zone->load_configs($glbdns, "zones/example.local.zone");

1;

