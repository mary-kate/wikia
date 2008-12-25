#!/usr/bin/perl

use strict;
use warnings;

use Working::Daemon;
use Data::Dumper;
my $daemon = Working::Daemon->new();
our $VERSION = 0.45;
$daemon->parse_options("bool"      => "Test if you can set bools",
                       "integer=i" => "Integer settings",
                       "string=s"  => "String setting",
                       "multi=s%"  => "Multiset variable");
$daemon->chroot(0);
#$daemon->daemon(0);
$daemon->user("sky");
$daemon->group("sky");
$daemon->name("testdaemon");

$daemon->do_action();
$daemon->change_root();
$daemon->drop_privs();

sleep 10;
1;
