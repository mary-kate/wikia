package Working::Daemon;

use 5.008008;
use strict;
use warnings;
use Data::Dumper;

use Getopt::Long;

our ($uid, $gid);
our %config;
sub new {
    my $class = shift;
    my $self = bless {}, $class;
    return $self;
}

sub parse_options {
    my $self = shift;
    my @args = @_;
    my %options;
    GetOptions(\%options, @args);
    print Dumper(\%options);
    exit;


}

sub change_root {
    my $self = shift;

    mkdir ("/tmp/glbdns.$$") || die;
    chown($uid,$gid, "/tmp/glbdns.$$") || die;
    chroot("/tmp/glbdns.$$") || die;
    chdir("/");
    $self->drop_privs();
    mkdir ("/etc") || die;
    open(my $protocol, "+>/etc/protocols") || die;
    print $protocol "tcp     6       TCP\n";
    close($protocol);

}

sub stop {
    my $class = shift;

    if(my $pid = get_pid()) {
        while(check_pid($pid)) {
            kill(2, $pid);
            mylog(0, 'info', "sent SIGINT to $pid - waiting on stopped pid $pid");
            print STDERR "sent SIGINT to $pid - waiting on stopped pid $pid\n";
            sleep 1;
        }
        mylog(0, 'info',"Stopped $config{name} on $pid");
        print STDERR "$config{name} stopped on $pid\n";
        return $pid;
    }
    return 0;
}

sub get_pid {
    my $class = shift;
    if(-r $config{pidfile}) {
        open(my $pidfile, "<$config{pidfile}") || die;
        my $line = <$pidfile>;
        close($pidfile);
        $line =~/(\d+)/;
        if(my $pid_to_check = $1) {
            $ENV{PATH} = '';
            return $pid_to_check if(check_pid($pid_to_check));
        }
    }
   return 0;
}

sub check_pid {
    my $self = shift;
    my $pid  = shift;
    my $grep = "/bin/grep";
    $grep = "/usr/bin/grep" if ($^O eq 'darwin');
    return !system("/bin/ps aux | $grep $pid | $grep -v grep | $grep $config{name}");
}


sub daemonize {
    my $self = shift;
    use POSIX qw(setsid);
    defined(my $pid = fork) || die "Can't fork: $!";
    if ($pid) {
        print "$config{name} started on $pid\n";
        exit 0;
    }
    setsid() || die "Can't start a new session: $!";
    open (STDIN , '/dev/null') || die "Can't read /dev/null: $!";
    open (STDOUT, '>/dev/null') || die "Can't write to /dev/null: $!";
    open (STDERR, '>/dev/null') || die "Can't write to /dev/null: $!";
}

sub mylog {
  my ($level, $prio, $msg) = @_;
  return 1 if ($level > $config{loglevel});
  if ($config{syslog}) {
    syslog($prio, $msg) || die "$!";
  } else {
    print STDERR "$prio - $msg\n";
  }
}




sub drop_privs {
  # drop user to nobody
  $< = $uid;
  $> = $uid;
  # drop group to nobody
  $( = $gid;
  $) = $gid;
}
# Preloaded methods go here.

# Autoload methods go after =cut, and are processed by the autosplit program.

1;
__END__
# Below is stub documentation for your module. You'd better edit it!

=head1 NAME

Working::Daemon - Perl extension for blah blah blah

=head1 SYNOPSIS

  use Working::Daemon;
  blah blah blah

=head1 DESCRIPTION

Stub documentation for Working::Daemon, created by h2xs. It looks like the
author of the extension was negligent enough to leave the stub
unedited.

Blah blah blah.

=head2 EXPORT

None by default.



=head1 SEE ALSO

Mention other useful documentation such as the documentation of
related modules or operating system documentation (such as man pages
in UNIX), or any relevant external documentation such as RFCs or
standards.

If you have a mailing list set up for your module, mention it here.

If you have a web site set up for your module, mention it here.

=head1 AUTHOR

Artur Bergman, E<lt>sky@apple.comE<gt>

=head1 COPYRIGHT AND LICENSE

Copyright (C) 2008 by Artur Bergman

This library is free software; you can redistribute it and/or modify
it under the same terms as Perl itself, either Perl version 5.8.8 or,
at your option, any later version of Perl 5 you may have available.


=cut
