#
# This is a basic VCL configuration file for varnish.  See the vcl(7)
# man page for details on VCL syntax and semantics.
#
# $Id: default.vcl 1929 2007-08-29 15:37:59Z des $
#

# Default backend definition.  Set this to point to your content
# server.

backend default {
	.host = "127.0.0.1";
	.port = "80";
}

backend wikia {
	.host = "xxxxxx"; 
	.port = "80";
}

# Below is a commented-out copy of the default VCL logic.  If you
# redefine any of these subroutines, the built-in logic will be
# appended to your code.

## Called when a client request is received
#
sub vcl_recv {


	if (req.http.Accept-Encoding) {
		if (req.http.Accept-Encoding ~ "gzip") {
	            set req.http.Accept-Encoding = "gzip";
	        } elsif (req.http.Accept-Encoding ~ "deflate") {
	            set req.http.Accept-Encoding = "deflate";
	        } else {
	            # unkown algorithm
	            remove req.http.Accept-Encoding;
	        }
	}

	# clean out requests sent via curls -X mode and LWP
	if (req.url ~ "http://") {
		set req.url = regsub(req.url, "http://[^/]*","");
	}


	if (req.request == "PURGE") {
	  if (req.http.purgeurl) {
	    purge_hash(req.http.purgeurl); 
	    error 200 "purged";
	  } else {
	    error 503 "empty purgeurl";
	  }
	}

	if (req.url == "/lvscheck.html") {
		error 200 "varnish is okay";
	}

	set req.backend = wikia;

	if (req.url ~ "/__utm.gif") {
		set req.url = "/__utm.gif";
	}

        if (req.url ~ "/rx") {
              error 200 "not serving this";
        }


	if (req.request != "GET" && req.request != "HEAD" && req.request != "PURGE") {
		pipe;
	}
	if (req.http.Expect) {
		pipe;
	}

	if (req.http.User-Agent ~ "Opera") {
		pipe;
	}

	if (req.http.Cookie ~ "UserID") {	
		set req.http.tmp_userid  = regsuball(req.http.Cookie, "(.*?)(^|;|\s)(.*UserID=[^;]*).*", "\3; ");
	} else { 
		set req.http.tmp_userid = " ";
  	}
	if (req.http.Cookie ~ "UserName") {	
		set req.http.tmp_username  = regsuball(req.http.Cookie, "(.*?)(^|;|\s)(.*UserName=[^;]*).*", "\3; ");
	} else { 
		set req.http.tmp_username = " ";
  	}
	if (req.http.Cookie ~ "session") {	
		set req.http.tmp_session  = regsub(req.http.Cookie, "(.*)(^|;|\s)(.*session=[^;]*).*", "\3; ");
	} else { 
		set req.http.tmp_session = " ";
  	}
	if (req.http.Cookie ~ "Token") {	
		set req.http.tmp_token  = regsub(req.http.Cookie, "(.*)(^|;|\s)(.*Token=[^;]*).*", "\3; ");
	} else { 
		set req.http.tmp_token = " ";
  	}
	if (req.http.Cookie ~ "LoggedOut") {	
		set req.http.tmp_loggedout  = regsub(req.http.Cookie, "(.*)(^|;|\s)(.*LoggedOut=[^;]*).*", "\3; ");
	} else { 
		set req.http.tmp_loggedout = " ";
  	}
	set req.http.Cookie = "";
	set req.http.Cookie = req.http.tmp_userid " ; " req.http.tmp_session " ; " req.http.tmp_token " ; " req.http.tmp_username " ; " req.http.tmp_loggedout;
	remove req.http.tmp_userid;
	remove req.http.tmp_session;
	remove req.http.tmp_token;
	remove req.http.tmp_loggedout;
	remove req.http.tmp_username;

	if (req.http.Authenticate) {
		pass;
	}
	lookup;
}


sub vcl_hash {
	set req.hash += req.url;
	set req.hash += req.http.host;
	hash;
}

#
## Called when entering pipe mode
#
#sub vcl_pipe {
#	pipe;
#}
#
## Called when entering pass mode
#
#sub vcl_pass {
#        set resp.http.foo = "x";
#	pass;
#}
#
## Called when entering an object into the cache
#


#
## Called when the requested object was found in the cache
#
sub vcl_hit {
       if (req.request == "PURGE") {
		error 200 "purged";
        }

	if (!obj.cacheable) {
		pass;
	}
	deliver;
}
#
## Called when the requested object was not found in the cache
#
sub vcl_miss {
  if (req.request == "PURGE") {
#         	nuke;
    error 200 "purged";
  }
  
  fetch;
}
#
## Called when the requested object has been retrieved from the
## backend, or the request to the backend has failed
#
sub vcl_fetch {
  
  if (!obj.cacheable) {
    set obj.http.X-Cacheable = "NO:Not-Cacheable";
    pass;
  }
  if (obj.http.Cache-Control ~ "private") {
    if(req.http.Cookie ~"(UserID|_session)") {
      set obj.http.X-Cacheable = "NO:Got Session";
    } else {
      set obj.http.X-Cacheable = "NO:Cache-Control=private";
    }
    pass;
  }
  if (obj.http.Set-Cookie ~ "(UserID|_session)") {
    set obj.http.X-Cacheable = "NO:Set-Cookie";
    pass;
  }
  
  set obj.http.X-Cacheable = "YES";
  set obj.grace = 10s;
  deliver;
}

sub vcl_prefetch {
  pass;
}

#
#
## Called before a cached object is delivered to the client
#
sub vcl_deliver {
  
  set resp.http.X-Served-By = "varnish1";
  
  if (obj.hits > 0) {
    set resp.http.X-Cache = "HIT";	
    set resp.http.X-Cache-Hits = obj.hits;
  } else {
    set resp.http.X-Cache = "MISS";	
  }
  set resp.http.Cache-Control = "private, s-maxage=0, max-age=0, must-revalidate";
  set resp.http.Expires = "Thu, 01 Jan 1970 00:00:00 GMT";
  if(!resp.http.Vary) {
    set resp.http.Vary = "Accept-Encoding,Cookie";
  }
  deliver;
}
#
## Called when an object nears its expiry time
#
#sub vcl_timeout {
#	discard;
#}
#
## Called when an object is about to be discarded
#
#sub vcl_discard {
#    discard;
#}
