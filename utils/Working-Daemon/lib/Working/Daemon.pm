package Working::Daemon;

use 5.008008;
use strict;
use warnings;
use Data::Dumper;
use File::Copy;
use Getopt::Long;
use Carp;

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
    $self->options(\%options);
    $self->assign_options(qw(user group name));
    return \%options;

}

sub assign_options {
    my ($self, @options) = @_;
    foreach my $option (@options) {
        $self->$option($self->options->{$option})
            if (exists $self->options->{$option});
    }
}

sub chroot {
    my $self = shift;

    my $tmpdir = $self->tmpdir;

    mkdir ($tmpdir) || die;
    chown($self->uid,$self->gid, $tmpdir) || croak("Cannot chown $tmpdir to (". $self->uid . ":". $self->gid . "): $!");


    foreach my $dir ($self->chroot_dirs) {
        mkdir("$tmpdir/$dir") || croak "Cannot create $tmpdir/$dir: $!";
    }
    foreach my $file_to_copy ($self->chroot_files) {
        copy("$file_to_copy", "$tmpdir/$file_to_copy") || croak "Cannot copy $file_to_copy -> $tmpdir/$file_to_copy: $!";
    }

    chroot("$tmpdir/") || croak ("Can't chroot to $tmpdir: $!");;
    chdir("/");
}


# perl really need the protocols file to function
sub chroot_files {
    my $self = shift;
    return ("/etc/protocols");
}

sub chroot_dirs {
    my $self = shift;
    return ("/etc/");
}

sub tmpdir {
    my $self = shift;
    return "/tmp/" . $self->name . ".$$";
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

sub log {
    my ($self, $level, $prio, $msg) = @_;
    return if ($level > $self->log_level);
    $self->do_log($prio, $msg);
}


sub do_log {
    my ($self, $prio, $msg) = @_;
    print STDERR "$prio - $msg";
}


sub drop_privs {
    my $self = shift;
  # drop user
    $< = $self->uid;
    $> = $self->uid;
  # drop group
    $( = $self->gid;
    $) = $self->gid;
}


sub uid {
    my $self = shift;
    return scalar getpwnam($self->user);
}

sub gid {
    my $self = shift;
    return scalar getpwnam($self->user);
}



# accessors

sub user {
    my $self = shift;
    if (@_) {
        return $self->{__PACKAGE__}->{user} = shift;
    } elsif (exists($self->{__PACKAGE__}->{user})) {
        return $self->{__PACKAGE__}->{user};
    } else {
        return "nobody";
    }
}

sub log_level {
    my $self = shift;
    if (@_) {
        return $self->{__PACKAGE__}->{log_level} = shift;
    } elsif (exists($self->{__PACKAGE__}->{log_level})) {
        return $self->{__PACKAGE__}->{log_level};
    } else {
        return 1;
    }
}


sub group {
    my $self = shift;
    if (@_) {
        return $self->{__PACKAGE__}->{group} = shift;
    } elsif (exists($self->{__PACKAGE__}->{group})) {
        return $self->{__PACKAGE__}->{group};
    } else {
        return "nobody";
    }
}

sub name {
    my $self = shift;
    if (@_) {
        return $self->{__PACKAGE__}->{name} = shift;
    } elsif (exists($self->{__PACKAGE__}->{name})) {
        return $self->{__PACKAGE__}->{name};
    } else {
        return "unnamed app";
    }
}



sub options {
    my $self = shift;
    if (@_) {
        return $self->{__PACKAGE__}->{options} = shift;
    } elsif (exists($self->{__PACKAGE__}->{options})) {
        return $self->{__PACKAGE__}->{options};
    } else {
        return {};
    }
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
