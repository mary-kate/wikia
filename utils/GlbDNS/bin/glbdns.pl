use strict;
use warnings;
use GlbDNS;

use Working::Daemon::Syslog;
my $daemon = Working::Daemon->new();

$daemon->name("glbdns");
$daemon->parse_options(
    "port=i"     => 53             => "Which port number to listen to",
    "address=s"  => "0.0.0.0"      => "IP Address to listen to",
    "syslog"     => 0              => "Syslog",
    "config=s"   => "/etc/glbdns/" => "Configuration directory",
    "loglevel=i" => 1              => "What level of messaes to log, higher is more verbose",
    );

# this should be support cleaner by Working::Daemon
if ($daemon->options->{syslog}) {
    bless $daemon, 'Working::Daemon::Syslog';
    $daemon->init;
}

$daemon->do_action;

my $dns = GlbDNS->new($daemon);

use GlbDNS::Config;

GlbDNS::Config->load_configs($dns, $daemon->options->{config});

$dns->start();
exit;
__END__
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
