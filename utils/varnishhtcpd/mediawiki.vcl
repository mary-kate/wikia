C{
#include <string.h>
  double TIM_real(void);
  void TIM_format(double t, char *p);
}C

C{
  #include <dlfcn.h>
  #include <stdlib.h>
  #include <stdio.h>
  #include <string.h>
  #include <GeoIPCity.h>
  #include <pthread.h>

  pthread_key_t geoip_key;

  GeoIP* geoip () {
    GeoIP *gi;
    if(!geoip_key) {
      pthread_key_create(&geoip_key, NULL);
    }
    gi = pthread_getspecific(geoip_key);
    if(gi = pthread_getspecific(geoip_key)) {
      return gi;
    } else {
      gi = GeoIP_open_type(GEOIP_CITY_EDITION_REV1,GEOIP_STANDARD);
      pthread_setspecific(geoip_key, gi);
      return gi;
    }
  }
}C

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



# Below is a commented-out copy of the default VCL logic.  If you
# redefine any of these subroutines, the built-in logic will be
# appended to your code.

## Called when a client request is received
#
sub vcl_recv {

    if(req.http.host == "geoip.example") {
      error 200 "OK";
    }

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
	if (req.url ~ "^http://") {
		set req.url = regsub(req.url, "http://[^/]*","");
	}


	if (req.url == "/lvscheck.html") {
		error 200 "varnish is okay";
	}

	if (req.http.Expect) {
		pipe;
	}

	{
	  C{
	    char *cookie = 0;
	    char *current_cookie = 0;
	    int keep = 0;
	    int end = 0;
	    current_cookie = cookie = VRT_GetHdr(sp, HDR_REQ, "\007cookie:");
	    while(cookie && *cookie != '\0') {
	      if(cookie[1] ==  ';'
		 || cookie[1] == '\0') {
		if (keep) {
		  keep = 0;
		} else {
		  memset(current_cookie, 32, (cookie + ( cookie[1] == '\0' ? 1 : 2) - current_cookie));
		}
		/* jump 2 to avoid the ; -- if we are at the end it doesn't matter */
		current_cookie = cookie + 2;
		cookie++;
		continue;
	      }

	      if(!keep && ((*cookie == 's' && !memcmp(cookie, "session", 7))
			   || (*cookie == 'U' && !memcmp(cookie, "UserID", 6))
			   || (*cookie == 'U' && !memcmp(cookie, "UserName", 8))
			   || (*cookie == 'T' && !memcmp(cookie, "Token", 5))
			   || (*cookie == 'L' && !memcmp(cookie, "LoggedOut", 9)))) {
		keep = 1;
	      }
	      cookie++;
	    }
	  }C
	     #;
	}

	if (req.request != "GET" && req.request != "HEAD" && req.request != "PURGE") {
		pipe;
	}

	if ((req.http.Pragma ~ "xxno-cache" && req.url ~ "raw") || req.request == "PURGE") {
	  if (req.http.host ~ "images.wikia.com" 
	      || req.http.host ~ "nocookie.net" 
	      || req.http.X-Initial-Url ~ "images.wikia.com"
	      || req.http.X-Initial-Url ~ "nocookie.net") {
	    nuke(req.url, "origin-images.wikia.com");
	  } else {
	    nuke(req.url, req.http.host);
	  }
	  if (req.request == "PURGE") {
	    error 200 "purged";
	  }

	}


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

sub vcl_miss {
}

#
## Called when the requested object has been retrieved from the
## backend, or the request to the backend has failed
#
sub vcl_fetch {


	set obj.http.X-Orighost = req.http.host;
	set obj.http.X-Served-By-Backend = obj.http.X-Served-By;
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

# if the backend is down, just server this traffic
	set obj.grace = 300s;

# ignore the cache rules on images

	# do not cache 404
	if(obj.status == 404) {
	  set obj.ttl = 0s;
	  set obj.http.Cache-Control = "max-age=0";
	  set obj.http.Expires = "Thu, 01 Jan 1970 00:00:00 GMT";
	  pass;
	}
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
  
  set resp.http.X-Served-By = "varnishx";
  

  if (obj.hits > 0) {
    set resp.http.X-Cache = "HIT";	
    set resp.http.X-Cache-Hits = obj.hits;
  } else {
    set resp.http.X-Cache = "MISS";	
  }



  if ( resp.http.X-Pass-Cache-Control ) {
    set resp.http.Cache-Control = resp.http.X-Pass-Cache-Control;
  } elsif ( resp.status == 304 ) {
# no headers on if-modified since
  } elsif ( resp.http.origurl ~ ".*/.*\.(css|js)"
	    || resp.http.orgiurl ~ "raw") {
# dont touch it let mediawiki decide
  } elsif (! resp.http.X-Orig-Host ~ "images.wikia.com") {
# lighttpd knows what it is doing
  } else {
#follow squid content here 
    set resp.http.cache-control = "private, s-maxage=0, max-age=0, must-revalidate";
  }

  if (!resp.status == 304) {
    C{
      char *cache = VRT_GetHdr(sp, HDR_REQ, "\016cache-control:");
      char date[40];
      int max_age;
      int want_equals = 0;
      if(cache) {
	while(*cache != '\0') {
	  if (want_equals && *cache == '=') {
	    cache++;
	    max_age = strtoul(cache, 0, 0);
	    break;
	  }

	  if (*cache == 'm' && !memcmp(cache, "max-age", 7)) {
	    cache += 7;
	    want_equals = 1;
	    continue;
	  }
	  cache++;
	}
	if (max_age) {
	  TIM_format(TIM_real() + max_age, date);
	  VRT_SetHdr(sp, HDR_RESP, "\010Expires:", date, vrt_magic_string_end);
	}
      }
    }C  
       #;
  }

  if( resp.http.cache-control ~ "max-age=0") {
    set resp.http.Expires = "Thu, 01 Jan 1970 00:00:00 GMT";
  }
  
  deliver;
}

sub vcl_error {
  if(req.http.host == "geoip.example.com") {
  set obj.http.Content-Type = "text/plain";
    C{
      GeoIP *gi = geoip();
      char json[255];
      char *ip = VRT_IP_string(sp, VRT_r_client_ip(sp));
      GeoIPRecord *record = GeoIP_record_by_addr(gi, ip);

      if(record) {
        snprintf(json, 255, "Geo = {\"city\":\"%s\",\"country\":\"%s\",\"lat\":\"%f\",\"lon\":\"%f\",\"classC\":\"%s\",\"netmask\":\"%d\"}",
                 record->city,
                 record->country_code,
                 record->latitude,
                 record->longitude,
                 ip,
                 GeoIP_last_netmask(gi)
                 );
        VRT_synth_page(sp, 0, json,  vrt_magic_string_end);
      } else {
        VRT_synth_page(sp, 0, "Geo = {}",  vrt_magic_string_end);
      }


    }C
       }
       
       if(req.url ~ "lvscheck.html") {
       synthetic {"varnish is okay"};
       }

  deliver;

}

