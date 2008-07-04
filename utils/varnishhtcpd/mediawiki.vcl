#
# This is a basic VCL configuration file for varnish.  See the vcl(7)
# man page for details on VCL syntax and semantics.
#
# $Id: default.vcl 1929 2007-08-29 15:37:59Z des $
#

# example vcl for use with mediawiki

backend default {
	.host = "127.0.0.1";
	.port = "80";
}

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


       if (req.request == "PURGE") {
		set req.http.tmp_purge = req.url "#" req.http.host "#";
         	nuke(req.http.tmp_purge);
		error 200 "purged";
        }

	# clean out requests sent via curls -X mode
	if (req.url ~ "http://") {
		set req.url = regsub(req.url, "http://[^/]*","");
	}

	if (req.url == "/lvscheck.html") {
		error 200 "varnish is okay";
	}

	if(req.http.host ~ "xxx) {
		set req.backend = default;
	} else {
		set req.backend = default;
	}

	if (req.request != "GET" && req.request != "HEAD" && req.request != "PURGE") {
		pipe;
	}
	if (req.http.Expect) {
		pipe;
	}

	if (req.http.Cookie ~ "UserID") {	
		set req.http.tmp_userid  = regsuball(req.http.Cookie, "(.*?)(^|;|\s)(.*UserID=[^;]*).*", "\3; ");
	}
	if (req.http.Cookie ~ "session") {	
		set req.http.tmp_session  = regsub(req.http.Cookie, "(.*)(^|;|\s)(.*session=[^;]*).*", "\3; ");
	}
	set req.http.Cookie = "";
	set req.http.Cookie = req.http.tmp_userid " ; " req.http.tmp_session;
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
#	pass;
#}
#
## Called when entering an object into the cache
#


#
## Called when the requested object was found in the cache
#
sub vcl_hit {
	if (!obj.cacheable) {
		pass;
	}
	if (obj.http.X-Cache == "MISS") {
		set obj.http.X-Cache = "HIT";
	}
	deliver;
}
#
## Called when the requested object was not found in the cache
#
sub vcl_miss {
	fetch;
}
#
## Called when the requested object has been retrieved from the
## backend, or the request to the backend has failed
#
sub vcl_fetch {
	if (!obj.valid) {
		error;
	}
	set obj.http.X-Cache = "MISS";
	# this is the old wow ip, so issue redirect

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
	# enforce 30 second cacheabilit
	if ( obj.ttl < 30) {
		obj.ttl = 30s;
	}
	set obj.grace = 10s;
	insert;
}

sub vcl_prefetch {
	pass;
}

#
#
## Called before a cached object is delivered to the client
#
sub vcl_deliver {

	set resp.http.X-Served-By = "<%= hostname %>";


	
    set resp.http.Cache-Control = "private, s-maxage=0, max-age=0, must-revalidate";
    remove resp.http.X-Vary-Options;
#    remove resp.http.X-Powered-By;
#    remove resp.http.X-Time-CPU-Time;
#    remove resp.http.X-Request-Id;

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
