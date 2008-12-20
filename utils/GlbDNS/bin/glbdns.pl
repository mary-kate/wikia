use strict;
use warnings;
BEGIN {require 'conf/wikia.net'};
use GlbDNS;

my $dns = GlbDNS->new();

$dns->start();
