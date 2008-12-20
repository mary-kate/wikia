use strict;
use warnings;
BEGIN {require 'conf/example.pl'};
use GlbDNS;

my $dns = GlbDNS->new();

$dns->start();
