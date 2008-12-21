#!/usr/bin/perl

use strict;
use warnings;

use Working::Daemon;
my $daemon = Working::Daemon->new();

$daemon->parse_options("bool","integer=i","string=s","multi=s%");

$daemon->user("sky");
$daemon->group("sky");
$daemon->name("testdaemon");
$daemon->chroot();
$daemon->drop_privs();


1;
