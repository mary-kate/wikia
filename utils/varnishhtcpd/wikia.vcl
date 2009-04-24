
# declare the function signature
# so we can use them
C{
#include <string.h>
  double TIM_real(void);
  void TIM_format(double t, char *p);
}C



# init GeoIP code
C{
  #include <dlfcn.h>
  #include <stdlib.h>
  #include <stdio.h>
  #include <string.h>
  #include <GeoIPCity.h>
  #include <pthread.h>

  pthread_mutex_t geoip_mutex = PTHREAD_MUTEX_INITIALIZER;

  GeoIP* gi;
  void geo_init () {
    if(!gi) {
      gi = GeoIP_open_type(GEOIP_CITY_EDITION_REV1,GEOIP_MEMORY_CACHE);
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

acl SJC {
    "192.168.10.10";
}

acl LON {
    "192.168.11.10";
}

acl IOWA {
    "192.168.12.10";
}

backend default {
	.host = "127.0.0.1";
	.port = "8001";
}

backend athena_dev {
	.host = "x.x.x.212";
	.port = "80";
}


director athena_sjc random {
  .retries = 2;
  {
    .backend = {
      .host = "x.x.x.72";
      .port = "81";
      .probe = {
	.url = "/athena/config/";
	.timeout = 0.5s;
	.window = 5;
	.threshold = 3;
      }
    }
    .weight = 100;
  }
  {
    .backend = {
      .host = "x.x.x.73";
      .port = "81";
      .probe = {
	.url = "/athena/config/";
	.timeout = 0.5s;
	.window = 5;
	.threshold = 3;
      }
    }
    .weight = 100;
  }
}



director london_to_iowa random {
  .retries = 2;
  {
    .backend = { 
      .host = "x.x.x.134";
      .probe = {
	.url = "/lvscheck.html";
	.timeout = 0.5s;
	.window = 5;
	.threshold = 3;
      }
    }
    .weight  = 100;  
  }
  {
    .backend = { 
      .host = "x.x.x.133";
      .probe = {
	.url = "/lvscheck.html";
	.timeout = 0.5s;
	.window = 5;
	.threshold = 3;
      }
    }
    .weight  = 100;
  }
}


#used by varnish in London
backend origin_images_sjc {
	.host = "x.x.x.196";
	.port = "80";
}

backend origin_html_sjc {
	.host = "x.x.x.196";
	.port = "80";
}

backend wikia {

	.host = "x.x.x.87";
	.port = "80";
}

backend armchair {
	.host = "x.x.x.142";
	.port = "80";
}

#backend gamewikis {
#	.host = "x.x.x.25";
#	.port = "80";
#}

backend images {
	.host = "x.x.x.189";
	.port = "80";
}

backend ap8 {  .host = "x.x.x.143";
  .port = "80";
}

backend ap4 {
  .host = "x.x.x.149";
  .port = "80";
}

backend ap18 {
  .host = "x.x.x.30";
  .port = "80";
}


## Called when a client request is received
#
sub vcl_recv {

  # normalize Accept-Encoding to reduce vary
  if (req.http.Accept-Encoding) {
    if (req.http.User-Agent ~ "MSIE 6" && req.url ~ "__varnish_athena") {
      unset req.http.Accept-Encoding;
    } elsif (req.http.Accept-Encoding ~ "gzip") {
      set req.http.Accept-Encoding = "gzip";
    } elsif (req.http.Accept-Encoding ~ "deflate") {
      set req.http.Accept-Encoding = "deflate";
    } else {
      unset req.http.Accept-Encoding;
    }
  }

# clean out requests sent via curls -X mode and LWP
  if (req.url ~ "^http://") {
    set req.url = regsub(req.url, "http://[^/]*","");
  }


  # get out error handler for geoiplookup
  if(req.http.host == "geoiplookup.wikia.com" || req.url == "/__varnish_geoip") {
    set req.http.host = "geoiplookup.wikia.com";
    error 200 "OK";
  }

  # lvs check
  if (req.url == "/lvscheck.html") {
    error 200 "OK";
  }

  if (req.url == "/__varnish_servername") {
    error 200 "OK";
  }

  #normalise images
  if(req.http.host ~ "images.wikia.com" || req.http.host ~ "nocookie.net") {
    set req.http.host = "origin-images.wikia.com";
  }

  if(req.url ~ "^/(skins|extensions)/") {
    set req.http.host = "origin-images.wikia.com";
    set req.url = regsub(req.url, "^", "/common");
  }

  if(req.url == "/favicon.ico") {
    set req.url = "/central/images/6/64/Favicon.ico";
    set req.http.host = "origin-images.wikia.com";
  }
 
  set req.http.X-Orig-Cookie = req.http.Cookie;
  if(req.http.Cookie ~ "(session|UserID|UserName|Token|LoggedOut)") {
    # dont do anything, the user is logged in
  } else {
    # dont care about any other cookies
    unset req.http.Cookie;
  }


  if(server.ip ~ SJC) {
    if (req.http.cookie ~ "backendhost=ap8") {
      set req.backend = ap8;
    } elsif ( req.http.cookie ~ "backendhost=ap4") {
      set req.backend = ap4;
    } elsif ( req.http.cookie ~ "backendhost=ap18") {
      set req.backend = ap18;
    } elsif(req.http.host ~ "^(nwn|oblivion|meta|war|gw)$") {
      set req.backend = wikia;
    } elsif(req.http.host ~ "^siwiki.sportsillustrated.cnn.com$") {
      set req.backend = armchair;
    } elsif(req.http.host ~ "^thirdturn.armchairgm.com$") {
      set req.backend = wikia;
    } elsif(req.http.host ~ "armchairgm.com$") {
      set req.backend = armchair;
    } elsif(req.http.host ~ "armchairgm.wikia.com$") {
      set req.backend = armchair;
    } elsif(req.http.host ~ "(tor5|techteam-qa1).wikia.com$") {
      set req.backend = ap8;
    } elsif(req.http.host ~ "origin-images.wikia.com") {
      set req.backend = images;
    } else {
      set req.backend = wikia;
    }
  } else {
    if (server.ip ~ LON) {

# If there is no cookie or this is an image request
# go to Iowa # relies on the cookie cleaning highe rup
      if(req.http.host ~ "origin-images.wikia.com" || !req.http.Cookie) {
        set req.backend = london_to_iowa;
      } else {
        set req.backend = origin_html_sjc;
      }
    } else {

# iowa
      if(req.http.host ~ "origin-images.wikia.com") {
        set req.backend = origin_images_sjc;
      } else {
        set req.backend = origin_html_sjc;
      }
    }
# pipes go direct
    if (req.request != "GET" && req.request != "HEAD" && req.request != "PURGE") {
      set req.backend = origin_html_sjc;
    }
  }





# normalize utm.gif so it doesnt cache break
# not sure we use this anymore
  if (req.url ~ "/__utm.gif") {
    set req.url = "/__utm.gif";
  }

#  set req.http.origurl = req.url;


  # Yahoo uses this to check for 404
  if (req.url ~ "^/SlurpConfirm404") {
    error 404 "Not found";
  }


  if (req.http.Expect) {
    pipe;
  }

  # pipe post
  if (req.request != "GET" && req.request != "HEAD" && req.request != "PURGE") {
    pipe;
  }

  # dont cache Authenticate calls
  # we dont use those?
  if (req.http.Authenticate) {
    pass;
  }
  set req.grace = 3600s;
  lookup;
}




sub vcl_hash {
	set req.hash += req.url;
	set req.hash += req.http.host;
	hash;
}

sub vcl_pipe {
  # do the right XFF processing
  set bereq.http.X-Forwarded-For = req.http.X-Forwarded-For;
  set bereq.http.X-Forwarded-For = regsub(bereq.http.X-Forwarded-For, "$", ", ");
  set bereq.http.X-Forwarded-For = regsub(bereq.http.X-Forwarded-For, "$", client.ip);
  set bereq.http.Cookie = req.http.X-Orig-Cookie;
  set bereq.http.connection = "close";
}

sub vcl_hit {
  if (req.request == "PURGE") {
    set obj.ttl = 1s;
    set obj.grace = 5s;
    error 200 "Purged.";
  }
}

sub vcl_miss {

  if (req.request == "PURGE") {
    error 404 "Not purged";
  }

  set bereq.http.X-Forwarded-For = req.http.X-Forwarded-For;
  set bereq.http.X-Forwarded-For = regsub(bereq.http.X-Forwarded-For, "$", ", ");
  set bereq.http.X-Forwarded-For = regsub(bereq.http.X-Forwarded-For, "$", client.ip);

  # for nef needs to be generic
  set bereq.http.Cookie = req.http.X-Orig-Cookie;

}

#
## Called when the requested object has been retrieved from the
## backend, or the request to the backend has failed
#
sub vcl_fetch {



	if ( obj.http.X-Pass-Cache-Control ) {
	  set obj.http.X-Internal-Pass-Cache-Control = obj.http.X-Pass-Cache-Control;
	} elsif ( obj.status == 304 ) {
# no headers on if-modified since
	} elsif ( req.url ~ ".*/index\.php.*(css|js)"
		  || req.url ~ "raw") {
# dont touch it let mediawiki decide
	} elsif (req.http.Host ~ "images.wikia.com") {
# lighttpd knows what it is doing
	} elsif (req.http.Host.host ~ "(geoiplookup|athena-ads)"
		 || req.url ~ "/__varnish") {
	  
	} else {
#follow squid content here
	  set obj.http.X-Internal-Pass-Cache-Control = "private, s-maxage=0, max-age=0, must-revalidate";
	}


	if(req.url ~"action=render$") {
#force cache this
	  set obj.ttl = 600s;
	  set obj.grace = 600s;
	  set obj.http.X-Cacheable = "YES - FORCED";
	  deliver;
	}


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


	if(req.url == "/robots.txt") {
		   set obj.http.X-Pass-Cache-Control = "max-age=600";
		   set obj.ttl = 86400s;
	}

       if (obj.ttl < 1s) {
           set obj.ttl = 5s;
           set obj.grace = 5s;
           set obj.http.X-Cacheable = "YES - FORCED";
           deliver;
        } else {
          set obj.http.X-Cacheable = "YES";
# if the backend is down, just server this traffic
     	 if (obj.ttl < 600s) {
	  set obj.grace = 5s;
	 } else {
          set obj.grace = 3600s;

	  }
        }


# ignore the cache rules on images
	if (req.http.host ~ "images.wikia.com") {
	  set obj.ttl = 604800s;
	}

	# do not cache 404
	if(obj.status == 404) {
	  set obj.http.Cache-Control = "max-age=10";
	  set obj.ttl = 10s;
	  set obj.grace = 10s;
	}
	if(req.url ~ "esitest") {
		   esi;
		   set obj.http.Content-Encoding = "gzip";
		   set obj.ttl = 1s;
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


  #add or append Served By
  if(!resp.http.X-Served-By) {
    set resp.http.X-Served-By  = server.identity;
    if (obj.hits > 0) {
      set resp.http.X-Cache = "HIT";
    } else {
      set resp.http.X-Cache = "MISS";
    }
    set resp.http.X-Cache-Hits = obj.hits;
  } else {
# append current data
    set resp.http.X-Served-By = regsub(resp.http.X-Served-By, "$", ", ");
    set resp.http.X-Served-By = regsub(resp.http.X-Served-By, "$", server.identity);
    if (obj.hits > 0) {
      set resp.http.X-Cache = regsub(resp.http.X-Cache, "$", ", HIT");
    } else {
      set resp.http.X-Cache = regsub(resp.http.X-Cache, "$" , ", MISS");
    }
    set resp.http.X-Cache-Hits = regsub(resp.http.X-Cache-Hits, "$", ", ");
    set resp.http.X-Cache-Hits = regsub(resp.http.X-Cache-Hits, "$", obj.hits);
  }


  set resp.http.X-Age = resp.http.Age;

  unset resp.http.Age;
  unset resp.http.X-Varnish;
  unset resp.http.Via;
  unset resp.http.X-Vary-Options;
  unset resp.http.X-Powered-By;

  # these are upstream varnishes
  # dont change anything

    if ( client.ip ~ LON
      || client.ip ~ SJC
      || client.ip ~ IOWA
	 ) {
    unset resp.http.X-CPU-Time;
    unset resp.http.X-Real-Time;
    unset resp.http.X-Served-By-Backend;
    unset resp.http.X-User-Id;
    unset resp.http.X-Namespace-Number;
    unset resp.http.X-Internal-Pass-Cache-Control;
    deliver;
  } 

    if (resp.http.X-Internal-Pass-Cache-Control) {
      set resp.http.Cache-Control = resp.http.Internal-X-Pass-Cache-Control;
    }


  # if there isnt an expiry
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

  deliver;
}

sub vcl_error {
  if(req.http.host == "geoiplookup.wikia.com" || req.url == "/__varnish_geoip") {
    set obj.http.Content-Type = "text/plain";
    set obj.http.x-internal-pass-cache-control = "private, s-maxage=0, max-age=360";

    C{
      char *ip = VRT_IP_string(sp, VRT_r_client_ip(sp));
      char date[40];
      char json[255];

      pthread_mutex_lock(&geoip_mutex);

      if(!gi) { geo_init(); }

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
	pthread_mutex_unlock(&geoip_mutex);
        VRT_synth_page(sp, 0, json,  vrt_magic_string_end);
      } else {
	pthread_mutex_unlock(&geoip_mutex);
        VRT_synth_page(sp, 0, "Geo = {}",  vrt_magic_string_end);
      }


      TIM_format(TIM_real(), date);
      VRT_SetHdr(sp, HDR_OBJ, "\016Last-Modified:", date, vrt_magic_string_end);
    }C
    deliver;
       }

  # check if site is working
  if(req.url ~ "lvscheck.html") {
    synthetic {"varnish is okay"};
    deliver;
  }
  if (req.url ~ "/__varnish_servername") {
  synthetic server.identity;
     deliver;
 }

  deliver;

}

