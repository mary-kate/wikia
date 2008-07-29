<?php
if (!defined('MEDIAWIKI')) die();
/**
 * Universal contact fetching functionality
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author Tomasz Klim <tomek@wikia.com>, MoLi <moli@wikia.com>
 * @copyright Copyright (C) 2007 Tomasz Klim (private, proprietary code)
 */

$wgExtensionCredits['other'][] = array(
	'name' => 'WikiContacts',
	'description' => 'universal contact fetching functionality',
	'author' => 'Tomasz Klim'
);

include_once( "$IP/extensions/wikia/WikiCurl/WikiCurl.php" );

class WikiContacts
{
    final public static function fetch( $provider, $username, $password ) 
    {
		switch ( $provider ) 
		{
			case 'gmail':    return self::fetchGmail  ( $username, $password );
			case 'yahoo':    return self::fetchYahoo  ( $username, $password );
			case 'myspace':  return self::fetchMySpace( $username, $password );
			default:         return self::fetchGmail  ( $username, $password );
		}
    }

    private static function utf162utf8($utf16)
    {
        // oh please oh please oh please oh please oh please
        if(function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($utf16, 'UTF-8', 'UTF-16');
        }

        $bytes = (ord($utf16{0}) << 8) | ord($utf16{1});

        switch(true) {
            case ((0x7F & $bytes) == $bytes):
                // this case should never be reached, because we are in ASCII range
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0x7F & $bytes);

            case (0x07FF & $bytes) == $bytes:
                // return a 2-byte UTF-8 character
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0xC0 | (($bytes >> 6) & 0x1F))
                     . chr(0x80 | ($bytes & 0x3F));

            case (0xFFFF & $bytes) == $bytes:
                // return a 3-byte UTF-8 character
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0xE0 | (($bytes >> 12) & 0x0F))
                     . chr(0x80 | (($bytes >> 6) & 0x3F))
                     . chr(0x80 | ($bytes & 0x3F));
        }

        // ignoring UTF-32 for now, sorry
        return '';
    }

    private static function fetchGmail( $username, $password ) 
    {
		$handler = new WikiCurl();

		// we allow cookies, but don't store them, because of major security hole in Gmail,
		// related to 'GX' cookie, which allows one to take over any Gmail account.
		// worked on 2007-02-01 12:59 local time (+1)
		$handler->setCookies( '/dev/null' );


		$postal_data = array(
			'ltmpl' 		=> 'yj_blanco',
			'ltmplcache' 	=> 2,
			'continue' 		=> 'http://mail.google.com/mail/',
			'service' 		=> 'mail',
			'rm' 			=> 'false',
			'ltmpl' 		=> 'yj_blanco',
			'Email' 		=> $username,
			'Passwd' 		=> $password,
			'rmShown' 		=> 1,
			'null' 			=> 'Sign+in'
		);
		$ret = $handler->get('https://www.google.com/accounts/LoginAuth', $postal_data);
		
		if ( strpos( $ret, 'Redirecting' ) || strpos( $ret, 'Personal information' ) ) 
		{  // we are logged in, let's analyze the Contacts page

			// this method works perfectly for me (Tomasz Klim), but doesn't work for John Q. Smith
			// anyway, we need to load this page, to set some cookies needed by the below page
			$ret = $handler->get('https://mail.google.com/mail/', array('v'=>'cl', 'pnl'=>'a', 'ui'=>'html', 'zy'=>'n'));

			
//			$ret = $handler->get('https://mail.google.com/mail/',array('ik' => '', 'view' => 'sec', 'zx' => ''));
			$result = $handler->get('http://mail.google.com/mail/contacts/data/export', 
				array('exportType' => 'ALL', 'groupToExport' => '', 'out' => 'HTML', 'exportEmail' => 'true')
			);
			
			$result = str_replace("\n", "", $result);
			$result = str_replace("\r", "", $result);

			if (!empty($result))
			{
				$csv = array();
				preg_match_all('|<tr><td>(.*?)</td><td>(.*?)</td></tr>|', $result, $csv);
				//preg_match_all('|(.*?),(.*?),|', $result, $csv);
				$users = $csv[1];
				$emails = $csv[2];
				$contacts = array();
				foreach ( $users as $id => $user ) {
					$user = (empty($user) || ($user == '-')) ? $emails[$id] : $user;
					$contacts[] = array( 'email' => $emails[$id], 'name' => $user );
				}
				return $contacts;
			} 
			else
			{
				return array();
			}
		}
		else 
		{
			return false;
		}
    }

    private static function fetchYahoo( $username, $password ) 
    {
		$handler = new WikiCurl();
		$handler->setCookies( '/dev/null' );

		$ret = $handler->post('http://login.yahoo.com/config/login', array( 
											'login'      => $username,
											'passwd'     => $password,
											'.src'       => '',
											'.tries'     => '5',  // 1
											'.bypass'    => '',
											'.partner'   => '',
											'.md5'       => '',
											'.hash'      => '',
											'.js'		 => '',
											'promo'		 => '',
											'.intl'      => 'us',
											'.challenge' => 'nz276e0rDBNEtOMcEXReHRDGC8Qt',
											'.u'         => 'd6cqat13p144e',
											'.yplus'     => '',
											'.emailCode' => '',
											'pkg'        => '',
											'stepid'     => '',
											'.ev'        => '',
											'hasMsgr'    => '0',
											'.v'         => '0',
											'.chkP'      => 'N',
											'.done'      => 'http://address.mail.yahoo.com/',
											'.last'      => '',
											'.pd'		 =>	'_var=0&c=' ));

		if ( !preg_match( "/invalid/i", $ret ) && !preg_match( "/not yet taken/i", $ret ) )  // we are logged in, let's analyze the Contacts page
		{  
			// get random value of URL -> added by MoLi
			$res = $handler->get('http://address.mail.yahoo.com',array());
			preg_match_all("/rand=(.*?)\"/", $res, $array_names);	
			$rand_value = str_replace('"', '', $array_names[0][0]);

			// get crumb value 
			$res = $handler->get('http://address.mail.yahoo.com/?1&VPC=import_export&A=B&.rand='.$rand_value,array());
			preg_match_all("/id=\"crumb1\" value=\"(.*?)\"/", $res, $array_names);	
			$crumb = $array_names[1][0];
			
			$ret = $handler->post( 'http://address.mail.yahoo.com/', 
							array( 
									'.crumb' => $crumb, 
									'VPC' => 'import_export',
									'submit[action_export_outlook]' => 'Export Now',
									'A' => 'B' 
								 )
							);

			$ret = substr( $ret, strpos( $ret, "\r\n\r\n" ) + 4 );
			//$wgOut->addWikiText( '<pre>' . $ret . '</pre>' );

			$fileContentArr = explode( "\n", $ret );

			$abColumnHeadLine = trim( $fileContentArr[0] );
			$abColumnHeadLine = str_replace( '"', '', $abColumnHeadLine );

			$abColumnHeadArr = explode( ",", $abColumnHeadLine );
			unset( $fileContentArr[0] );

			foreach ( $fileContentArr as $key => $value ) 
			{
				$listColumnLine = trim( $value );
				$listColumnLine = str_replace( '"', '', $listColumnLine );

				$listColumnArr = explode( ",", $listColumnLine );

				unset( $list_ );
				foreach ( $listColumnArr as $listColumnKey => $listColumnValue ) 
				{
					$listKey = $abColumnHeadArr[$listColumnKey];
					$list_[$listKey] = $listColumnValue;
				}

				if ( is_array( $list_ ) ) {
					$list[] = $list_;
				}
	    	}

			if ( is_array( $list ) ) 
			{
				$cnt = 0;
				$contacts = array();

				foreach ( $list as $entry => $container ) 
				{
					if (!empty($container["E-mail Address"]))
					{
						$cnt++;
						$contacts[] = array( 'email' => $container["E-mail Address"], 'name' => $container["First Name"] . ' ' . $container["Last Name"] );
					}
				}
				return $contacts;
	    	} 
	    	else 
	    	{
				return array();
			}
		} 
		else 
		{
	    	return false;
		}
    }

    private static function fetchMyspace( $username, $password ) 
    {  // 151235739
		$handler = new WikiCurl();
		$handler->setCookies( '/dev/null' );

		$ret = $handler->get('http://www.myspace.com');

		preg_match( "/MyToken=([^\"]+)\"/", $ret, $token );
		$token = $token[1];

		$ret = $handler->post( "http://login.myspace.com/index.cfm?fuseaction=login.process&MyToken=$token",
					 array( 'ctl00%24Main%24SplashDisplay%24login%24loginbutton.x' => '38',
							'ctl00%24Main%24SplashDisplay%24login%24loginbutton.y' => '15',
							'email' => $username,
							'password' => $password ) );

		//global $wgOut;
		//$wgOut->addWikiText( '<pre>' . $ret . '</pre>' );

		preg_match( "/fuseaction=user&Mytoken=(.*)\"/", $ret, $token );
		$token = $token[1];

		// note that this is *not* the same page, as in post, since token has changed
		$handler->setReferer( "http://login.myspace.com/index.cfm?fuseaction=login.process&MyToken=$token" );

		$ret = $handler->get('http://home.myspace.com/index.cfm', array('fuseaction' => 'user', 'MyToken' => $token));
		//$wgOut->addWikiText( '<pre>' . $ret . '</pre>' );

		if ( !strpos( $ret, "You Must Be Logged-In to do That!" ) ) 
		{  // we are logged in, let's analyze the Contacts page
			
			preg_match( "/AddressBookHyperLink\" href=\"([^\"]+)\"/", $ret, $redirpage );
			$ret = $handler->get( $redirpage[1] );

			echo "<pre>".print_r($ret, true)."</pre>";
			exit;

			$regexp = "<a href=\"#\" onclick=\"[^\"]*\" title=\"View this Contact\">(.*?)<\/a>";
			preg_match_all( "/$regexp/s", $ret, $username );

			$regexp = "<td class=\"email\">(.*?)<\/td>";
			preg_match_all( "/$regexp/s", $ret, $emails );

			$regexp = "href=\"([^\"]*)\"><font[^>]*>SignOut";
			preg_match_all( "/$regexp/s", $ret, $logout );

			$ret = $handler->get( $logout[1][0] );

			if ( is_array( $emails[1] ) ) 
			{
				$cnt = 0;
				$total = sizeof( $emails[1] );
				$contacts = array();

				for ( $i = 0; $i < $total; $i++ ) 
				{
					$cnt++;
					$emails[1][$i] = str_replace( "<br>", "", $emails[1][$i] );
					$contacts[] = array( 'email' => $emails[1][$i], 'name' => $username[1][$i] );
				}
				return $contacts;
			} 
			else 
			{
				return array();
			}
		} 
		else 
		{
	    	return false;
		}
    }
}

?>
