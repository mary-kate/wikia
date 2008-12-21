#!/usr/bin/perl

use strict;
use warnings;

use Working::Daemon;
use Data::Dumper;
my $daemon = Working::Daemon->new();

$daemon->parse_options("bool","integer=i","string=s","multi=s%");
print Dumper($daemon->options);
$daemon->user("sky");
$daemon->group("sky");
$daemon->name("testdaemon");
$daemon->change_root();
$daemon->drop_privs();


1;
