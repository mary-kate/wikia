<?php
/*
 * Author: Inez Korczynski
 */

# basic permissions
$wgGroupPermissions['sysop']['setadminskin'] = true;
$wgGroupPermissions['staff']['setadminskin'] = true;

$wgHooks['UserSetCookies'][] = 'SetSkinChooserCookies';
function SetSkinChooserCookies( $user, &$session, &$cookies ) {
	$cookies['skinpref'] = join('-',array($user->getOption('skin'), $user->getOption('theme'), $user->getOption('skinoverwrite')));
	return true;
}

$wgHooks['ModifyPreferencesValue'][] = 'SetThemeForPreferences';
function SetThemeForPreferences($pref) {
	global $wgUser, $wgSkinTheme, $wgDefaultTheme;

	$userTheme = $wgUser->getOption('theme');

	# Normalize theme name and set it as a variable for skin object.
	if(isset($wgSkinTheme[$pref->mSkin])){
		if(!in_array($userTheme, $wgSkinTheme[$pref->mSkin])){
			if(in_array($wgDefaultTheme, $wgSkinTheme[$pref->mSkin])){
				$userTheme = $wgDefaultTheme;
			} else {
				$userTheme = $wgSkinTheme[$pref->mSkin][0];
			}
		}
		$pref->mTheme = $userTheme;
	}

	return true;
}

$wgHooks['SavePreferencesHook'][] = 'SavePreferencesSkinChooser';
function SavePreferencesSkinChooser($pref) {
	global $wgUser, $wgCityId, $wgAdminSkin, $wgTitle;

	# Save setting for admin skin
	if(!empty($pref->mAdminSkin)) {
		if( $wgUser->isAllowed( 'setadminskin' ) ) {
			if($pref->mAdminSkin != $wgAdminSkin && !(empty($wgAdminSkin) && $pref->mAdminSkin == 'ds')) {
				$log = new LogPage('var_log');
				if($pref->mAdminSkin == 'ds') {
					WikiFactory::SetVarById( 599, $wgCityId, null);
					$wgAdminSkin = null;
					$log->addEntry( 'var_set', $wgTitle, '', array(wfMsg('skin'), wfMsg('adminskin_ds')));
				} else {
					WikiFactory::SetVarById( 599, $wgCityId, $pref->mAdminSkin);
					$wgAdminSkin = $pref->mAdminSkin;
					$log->addEntry( 'var_set', $wgTitle, '', array(wfMsg('skin'), $pref->mAdminSkin));
				}
				WikiFactory::clearCache( $wgCityId );
			}
		}
	}

	if ( !is_null($pref->mTheme) ) {
		$wgUser->setOption('theme', $pref->mTheme);
	}

	return true;
}


$wgHooks['UserToggles'][] = 'SkinChooserExtraToggle';
function SkinChooserExtraToggle(&$extraToggle) {
	$extraToggle[] = 'skinoverwrite';
	$extraToggle[] = 'showAds';
	return true;
}

$wgHooks['AlternateSkinPreferences'][] = 'WikiaSkinPreferences';
function WikiaSkinPreferences($pref) {
	global $wgOut, $wgSkinTheme, $wgSkipSkins, $wgStylePath, $wgSkipThemes, $wgUser, $wgDefaultSkin, $wgDefaultTheme, $wgSkinPreviewPage, $wgAdminSkin;

	global $wgForceSkin;
	if(!empty($wgForceSkin)) {
		$wgOut->addHTML(wfMsg('this_is_special_wikia'));
		$wgOut->addHTML('<div style="display:none;">'.$pref->getToggle('skinoverwrite').'</div>');
		return true;
	}

	if(!empty($wgAdminSkin)) {
		$defaultSkinKey = $wgAdminSkin;
	} else if(!empty($wgDefaultTheme)) {
		$defaultSkinKey = $wgDefaultSkin . '-' . $wgDefaultTheme;
	} else {
		$defaultSkinKey = $wgDefaultSkin;
	}

	# Load list of skin names
	$validSkinNames = Skin::getSkinNames();

	# And sort them
	foreach ($validSkinNames as $skinkey => & $skinname ) {
		if ( isset( $skinNames[$skinkey] ) )  {
			$skinname = $skinNames[$skinkey];
		}
	}
	asort($validSkinNames);

	$validSkinNames2 = $validSkinNames;

	$previewtext = wfMsg('skinpreview');
	//ticket #2428 - Marooned
	if(isset($wgSkinPreviewPage) && is_string($wgSkinPreviewPage)) {
		$previewLinkTemplate = Title::newFromText($wgSkinPreviewPage)->getLocalURL('useskin=');
	} else {
		$mptitle = Title::newMainPage();
		$previewLinkTemplate = $mptitle->getLocalURL('useskin=');
	}

	# Used to display different background color every 2nd section
	$themeCount = 0;

	# Foreach over skins which contains themes and display radioboxes for them
	foreach($wgSkinTheme as $skinKey => $skinVal) {

		# Do not display skins which are defined in wgSkipSkins array
		if(in_array($skinKey, $wgSkipSkins)) {
			continue;
		}

		$wgOut->addHTML('<div '.($themeCount++%2!=1 ? 'class="prefSection"' : '').'>');
		$wgOut->addHTML('<h5>'.wfMsg( $skinKey . '_skins').'</h5>');
		$wgOut->addHTML('<table style="background: transparent none">');

		# Iterate over themes for one skin
		foreach($skinVal as $themeKey) {

			# Do not display themes which are defined in wgSkipThemes array
			if(isset($wgSkipThemes[$skinKey]) && in_array($themeKey, $wgSkipThemes[$skinKey])) {
				continue;
			}

			# Ignore custom theme because it is only for admins
			if($themeKey == 'custom') {
				continue;
			}

			$skinkey = $skinKey.'-'.$themeKey;

			# Create preview link
			$previewlink = '<a target="_blank" href="'.htmlspecialchars($previewLinkTemplate.$skinKey.'&usetheme='.$themeKey).'">'.$previewtext.'</a>';

			$wgOut->addHTML('<tr>');
			$wgOut->addHTML('<td><input type="radio" value="'.$skinkey.'" id="wpSkin'.$skinkey.'" name="wpSkin"'.($skinkey == $pref->mSkin.'-'.$pref->mTheme ? ' checked="checked" ' : '').'/><label for="wpSkin'.$skinkey.'">'.wfMsg($skinkey).'</label> '.$previewlink.'</td>');
			if ($skinKey == 'monaco') {
				$wgOut->addHTML('<td><label for="wpSkin'.$skinkey.'"><img src="'.$wgStylePath.'/'.$skinKey.'/'.$themeKey.'/images/preview.gif" width="100" /></label>'.($skinkey == $defaultSkinKey ? ' (' . wfMsg( 'default' ) . ')' : '').'</td>');
			}
			$wgOut->addHTML('</tr>');
		}

		if($skinKey == 'monaco') {
			$wgOut->addHTML('<tr><td colspan=2>'.$pref->getToggle('showAds').'</td></tr>');
		}

		$wgOut->addHTML('</table>');
		$wgOut->addHTML('</div>');

		unset($validSkinNames[$skinKey]);
	}

	# Display radio button for monobook skin
	if(isset($validSkinNames['monobook'])) {
		$previewlink = '<a target="_blank" href="'.htmlspecialchars($previewLinkTemplate.'monobook').'">'.$previewtext.'</a>';
		$wgOut->addHTML('<div '.($themeCount++%2!=1 ? 'class="prefSection"' : '').'>');
		$wgOut->addHTML('<h5>'.wfMsg('wikipedia_skin').'</h5>');
		$wgOut->addHTML('<table style="background: transparent none"><tr><td><input type="radio" value="monobook" id="wpSkinmonobook" name="wpSkin"'.($pref->mSkin == 'monobook' ? ' checked' : '').'/><label for="wpSkinmonobook">'.$validSkinNames['monobook'].'</label> '.$previewlink.('monobook' == $defaultSkinKey ? ' (' . wfMsg( 'default' ) . ')' : '').'</td></tr></table>');
		$wgOut->addHTML('</div>');

		unset($validSkinNames['monobook']);
	}

	# Display radio buttons for rest of skin
	if(count($validSkinNames) > 0) {
		$wgOut->addHTML('<div '.($themeCount++%2!=1 ? 'class="prefSection"' : '').'>');
		$wgOut->addHTML('<h5>'.wfMsg('old_skins').'</h5>');
		$wgOut->addHTML('<table style="background: transparent none">');

		foreach($validSkinNames as $skinKey => $skinVal) {
			if ( in_array( $skinKey, $wgSkipSkins ) ) {
				continue;
			}

			$previewlink = '<a target="_blank" href="'.htmlspecialchars($previewLinkTemplate.$skinKey).'">'.$previewtext.'</a>';
			$wgOut->addHTML('<tr><td><input type="radio" value="'.$skinKey.'" id="wpSkin'.$skinKey.'" name="wpSkin"'.($pref->mSkin == $skinKey ? ' checked' : '').'/><label for="wpSkin'.$skinKey.'">'.$skinVal.'</label> '.$previewlink.($skinKey == $defaultSkinKey ? ' (' . wfMsg( 'default' ) . ')' : '').'</td></tr>');
		}

		$wgOut->addHTML('</table>');
		$wgOut->addHTML('</div>');
	}


	# Display skin overwrite checkbox
	$wgOut->addHTML('<br/>'.$pref->getToggle('skinoverwrite'));

	# Display ComboBox for admins/staff only
	if( $wgUser->isAllowed( 'setadminskin' ) ) {

		$wgOut->addHTML("<br/><h2>".wfMsg('admin_skin')."</h2>".wfMsg('defaultskin_choose'));
		$wgOut->addHTML('<select name="adminSkin" id="adminSkin">');

		foreach($wgSkinTheme as $skinKey => $skinVal) {

			# Do not display skins which are defined in wgSkipSkins array
			if(in_array($skinKey, $wgSkipSkins)) {
				continue;
			}
			if($skinKey == 'quartz') {
				$skinKeyA = split('-', $wgAdminSkin);
				if($skinKey != $skinKeyA[0]) {
					continue;
				}
			}

			if(count($wgSkinTheme[$skinKey]) > 0) {
				$wgOut->addHTML('<optgroup label="'.wfMsg($skinKey . '_skins').'">');
				foreach($wgSkinTheme[$skinKey] as $themeKey => $themeVal) {

					# Do not display themes which are defined in wgSkipThemes
					if(isset($wgSkipThemes[$skinKey]) && in_array($themeVal, $wgSkipThemes[$skinKey])) {
						continue;
					}
					if($skinKey == 'quartz') {
						if($themeVal != $skinKeyA[1]) {
							continue;
						}
					}
					$skinkey = $skinKey . '-' . $themeVal;
					$wgOut->addHTML("<option value='{$skinkey}'".($skinkey == $wgAdminSkin ? ' selected' : '').">".wfMsg($skinkey)."</option>");
				}
				$wgOut->addHTML('</optgroup>');
			}
		}
		$wgOut->addHTML("<option value='ds'".(empty($wgAdminSkin) ? ' selected' : '').">".wfMsg('adminskin_ds')."</option>");
		$wgOut->addHTML('</select>');
		$wgOut->addWikiText(wfMsg('skinchooser-customcss'));
	} else {
		$wgOut->addHTML('<br/>');
		if(!empty($wgAdminSkin)) {
            $elems = split('-', $wgAdminSkin);
            $skin = ( array_key_exists(0, $elems) ) ? $elems[0] : null;
            $theme = ( array_key_exists(1, $elems) ) ? $elems[1] : null;
			if($theme != 'custom') {
				$wgOut->addHTML(wfMsg('defaultskin1', wfMsg($skin.'_skins').' '.wfMsg($wgAdminSkin)));
			} else {
				$wgOut->addHTML(wfMsgForContent('defaultskin2', wfMsg($skin.'_skins').' '.wfMsg($wgAdminSkin), Skin::makeNSUrl(ucfirst($skin).'.css','',NS_MEDIAWIKI)));
			}
		} else {
			if(empty($wgDefaultTheme)) {
				$name = $validSkinNames2[$wgDefaultSkin];
			} else {
				$name = wfMsg($wgDefaultSkin.'_skins').' '.wfMsg($wgDefaultSkin.'-'.$wgDefaultTheme);
			}
			$wgOut->addHTML(wfMsg('defaultskin3',$name));
		}
	}

	return false;
}


$wgHooks['AlternateGetSkin'][] = 'WikiaGetSkin';
function WikiaGetSkin ($user) {
	global $wgCookiePrefix, $wgCookieExpiration, $wgCookiePath, $wgCookieDomain, $wgCookieSecure, $wgDefaultSkin, $wgDefaultTheme, $wgVisitorSkin, $wgVisitorTheme, $wgOldDefaultSkin, $wgSkinTheme, $wgOut, $wgForceSkin, $wgRequest, $wgHomePageName, $wgHomePageSkin, $wgTitle, $wgAdminSkin;

	if(!($wgTitle instanceof Title)) {
		$user->mSkin = &Skin::newFromKey(isset($wgDefaultSkin) ? $wgDefaultSkin : 'monobook');
		return false;
	}

	if( $wgTitle->getText() == $wgHomePageName && $wgTitle->getNamespace() == NS_MAIN ) {
		$user->mSkin = &Skin::newFromKey($wgHomePageSkin);
		return false;
	}

	if(!empty($wgForceSkin)) {
		$elems = split('-', $wgForceSkin);
		$userSkin = ( array_key_exists(0, $elems) ) ? $elems[0] : null;
		$userTheme = ( array_key_exists(1, $elems) ) ? $elems[1] : null;
		$user->mSkin = &Skin::newFromKey($wgRequest->getVal('useskin', $userSkin));
		$user->mSkin->themename = $wgRequest->getVal('usetheme', $userTheme);
		return false;
	}

	if(!empty($wgVisitorTheme) && $wgVisitorSkin == 'quartz') {
		$wgVisitorSkin .= $wgVisitorTheme;
	}

	# Get rid of 'wgVisitorSkin' variable, but sometimes create new one 'wgOldDefaultSkin'
	if($wgDefaultSkin == 'monobook' && substr($wgVisitorSkin, 0, 6) == 'quartz') {
		$wgOldDefaultSkin = $wgDefaultSkin;
		$wgDefaultSkin = $wgVisitorSkin;
	}
	unset($wgVisitorSkin);
	unset($wgVisitorTheme);

	if(strlen($wgDefaultSkin) > 7 && substr($wgDefaultSkin, 0, 6) == 'quartz') {
		$wgDefaultTheme=substr($wgDefaultSkin, 6);
		$wgDefaultSkin='quartz';
	}

	# Get skin logic
	if(!$user->isLoggedIn()) { # If user is not logged in
		if(count($_COOKIE) > 0 && isset($_COOKIE[$wgCookiePrefix.'skinpref'])) { # If user has cookie with variable 'skinpref'
			$skinpref = split('-', $_COOKIE[$wgCookiePrefix.'skinpref']);
			if(true == (bool) $skinpref[2]) { # Doest have overwrite enabled?

				if(!empty($wgAdminSkin)) {
					$elems = split('-',$wgAdminSkin);
                    $userSkin = ( array_key_exists(0, $elems) ) ? $elems[0] : null;
                    $userTheme = ( array_key_exists(1, $elems) ) ? $elems[1] : null;
				} else {
					$userSkin = $skinpref[0];
					$userTheme = $skinpref[1];
				}

			} else {
				$userSkin = $skinpref[0];
				$userTheme = $skinpref[1];
			}
		} else {
			if(!empty($wgAdminSkin)) {
				$adminSkinArray = split('-', $wgAdminSkin);
				$userSkin = isset($adminSkinArray[0]) ? $adminSkinArray[0] : null;
				$userTheme = isset($adminSkinArray[1]) ? $adminSkinArray[1] : null;
			} else {
				$userSkin = $wgDefaultSkin;
				$userTheme = $wgDefaultTheme;
			}
		}
	} else {
		$userSkin = $user->getOption('skin');
		$userTheme = $user->getOption('theme');

		if(true == (bool) $user->getOption('skinoverwrite')) { # Doest have overwrite enabled?
			if(!empty($wgAdminSkin)) {
				$adminSkinArray = split('-', $wgAdminSkin);
				$userSkin = isset($adminSkinArray[0]) ? $adminSkinArray[0] : null;
				$userTheme = isset($adminSkinArray[1]) ? $adminSkinArray[1] : null;
			}
		}
	}

	$userSkin = $wgRequest->getVal('useskin', $userSkin);
	$userTheme = $wgRequest->getVal('usetheme', $userTheme);

	$user->mSkin = &Skin::newFromKey($userSkin);

	$normalizedSkinName = substr(strtolower(get_class($user->mSkin)),4);

	# Normalize theme name and set it as a variable for skin object.
	if(isset($wgSkinTheme[$normalizedSkinName])){
		if(!in_array($userTheme, $wgSkinTheme[$normalizedSkinName])){
			if(in_array($wgDefaultTheme, $wgSkinTheme[$normalizedSkinName])){
				$userTheme = $wgDefaultTheme;
			} else {
				$userTheme = $wgSkinTheme[$normalizedSkinName][0];
			}
		}

		$user->mSkin->themename = $userTheme;
	}
	return false;
}
