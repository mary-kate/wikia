use strict;
use warnings;
use GlbDNS;

use Sys::Syslog qw(:DEFAULT setlogsock :macros);
my @arguments = @ARGV;
setlogsock('unix') || die "$!";
use Getopt::Long;
use Carp::Heavy;
use Scalar::Util;
our %config = (
              foreground => 0,
              port    => 53,
              address => '0.0.0.0',
              syslog   => 1,
              debug    => 0,
              name     => 'glbdns',
              pidfile  => '',
              loglevel => 1,
              config   => '/etc/glbdns/',
              );

my $uid = getpwnam('nobody');
my $gid = getpwnam('nobody');

my $results = GetOptions("foreground" => \$config{foreground},
                         "port=i"     => \$config{port},
                         "address=s"  => \$config{address},
                         "syslog"     => \$config{syslog},
                         "debug"      => \$config{debug},
                         "name=s"     => \$config{name},
                         "pidfile=s"  => \$config{pidfile},
                         "config=s"   => \$config{config},
                         "logleve=i"  => \$config{loglevel},
                         );

my $command = shift || 'start';
$config{pidfile} = "/var/run/$config{name}.pid" unless $config{pidfile};
$config{foreground} = 1 if($config{debug});

if($command eq 'restart') {
    openlog("$config{name}", 'ndelay,pid', LOG_DAEMON) if($config{syslog});
    # we don't do anything on restart
    stop();
    closelog();
    $command = 'start';
}

if($command eq 'status') {
    if(my $pid = get_pid()) {
        print "$config{name} is running on $pid\n";
        exit 0;
    } else {
        print "$config{name} is not running\n";
        exit 1;
    }
}
if($command eq 'start') {
    if(my $pid = get_pid()) {
        #already running just return
        openlog("$config{name}", 'ndelay,pid', LOG_DAEMON) if($config{syslog});
        mylog(0, 'info', "exiting - process already running on $pid");
        print STDERR "exiting - process already running on $pid\n";
        exit 1;
    }
}

if($command eq 'stop') {
    openlog("$config{name}", 'ndelay,pid', LOG_DAEMON) if($config{syslog});
    if(stop()) {
        exit 0;
    } else {
        # no running
        mylog(0, 'info', "failed to stop daemon - cannot find process");
        print STDERR "$config{name} failed to stop daemon - cannot find process\n";
        exit 1;
    }
}



daemonize() unless $config{foreground};

{
    if(my $pid = fork()) {

        # this is the master session
        # it makes sure to cleanup from the slave
        # it stays as superuser

        open(my $pidfile, "+>$config{pidfile}") || die;
        openlog("$config{name}", 'ndelay,pid', LOG_DAEMON) if($config{syslog});
        mylog(1, 'info', "started master session - child is $pid") || die "$!";
        select($pidfile); $|++; select(STDOUT);
        print $pidfile "$$";
        $SIG{INT} = sub { kill(2,$pid) };
        $0 = "$config{name} - waiting for child $pid";
        waitpid($pid, 0);
        mylog(1, 'info', "exiting master session - child is $pid") || die "$!";
        unlink("/tmp/glbdns.$pid/etc/protocols") || die "$!";
        rmdir("/tmp/glbdns.$pid/etc/") || die;
        rmdir("/tmp/glbdns.$pid/") || die;
        unlink($config{pidfile}) || die $!;
        exit;
    }

    openlog("$config{name} worker", 'ndelay,pid', LOG_DAEMON) if($config{syslog});
    mylog(1, 'info', "started session - ready to listen on $config{address}:$config{port}");
    $0 = "$config{name} worker - starting up";
}

my $dns = GlbDNS->new();

use GlbDNS::Config;

GlbDNS::Config->load_configs($dns, $config{config});

{
    mkdir ("/tmp/glbdns.$$") || die;
    chown($uid,$gid, "/tmp/glbdns.$$") || die;
    chroot("/tmp/glbdns.$$") || die;
    chdir("/");
    drop_privs();
    mkdir ("/etc") || die;
    open(my $protocol, "+>/etc/protocols") || die;
    print $protocol "tcp     6       TCP\n";
    close($protocol);
}

$SIG{INT} = sub { exit };

$dns->start();
exit;

sub stop {
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
    my $pid = shift;
    my $grep = "/bin/grep";
    $grep = "/usr/bin/grep" if ($^O eq 'darwin');
    return !system("/bin/ps aux | $grep $pid | $grep -v grep | $grep $config{name}");
}


sub daemonize {
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
