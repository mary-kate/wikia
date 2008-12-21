#!/usr/bin/perl

use strict;
use warnings;

use Working::Daemon;
my $daemon = Working::Daemon->new();

$daemon->parse_options("bool","integer=i","string=s","multi=s%");

1;
