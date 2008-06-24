<?php
require_once( '../commandLine.inc' );

$adminSkin = getAdminSkin();
$validSkinNames = Skin::getSkinNames();

echo "================================================================================\n";
echo "AdminSkin cleanup for {$wgCityId} / {$wgDBname} / {$wgServer}\n";
echo "MediaWiki:AdminSkin : ".(empty($adminSkin) ? "empty" : $adminSkin)."\n";
echo "\$wgAdminSkin : ".(empty($wgAdminSkin) ? "empty" : $wgAdminSkin)."\n";
echo "\$wgDefaultSkin : ".(empty($wgDefaultSkin) ? "empty" : $wgDefaultSkin)."\n";
echo "\$wgDefaultTheme : ".(empty($wgDefaultTheme) ? "empty" : $wgDefaultTheme)."\n";

$adminSkin_parts = split('-', $adminSkin);

if(count($adminSkin_parts) == 2 && $adminSkin_parts[0] == 'monaco' && in_array($adminSkin_parts[1], $wgSkinTheme['monaco'])) {

	WikiFactory::SetVarById(599, $wgCityId, $adminSkin_parts[0].'-'.$adminSkin_parts[1]);
	WikiFactory::clearCache(599);
	echo "New \$wgAdminSkin : {$adminSkin_parts[0]}-{$adminSkin_parts[1]}\n";

} else if(count($adminSkin_parts) == 2 && $adminSkin_parts[0] != 'monaco' && isset($validSkinNames[$adminSkin_parts[0]]) && in_array($adminSkin_parts[1], $wgSkinTheme[$adminSkin_parts[0]])) {

	WikiFactory::SetVarById(277, $wgCityId, $adminSkin_parts[0]);
        WikiFactory::clearCache(277);

	echo "New \$wgDefaultSkin : {$adminSkin_parts[0]}\n";

	WikiFactory::SetVarById(555, $wgCityId, $adminSkin_parts[1]);
	WikiFactory::clearCache(555);

	echo "New \$wgDefaultTheme : {$adminSkin_parts[1]}\n";

} else if(isset($validSkinNames[$adminSkin_parts[0]])) {

	WikiFactory::SetVarById(277, $wgCityId, $adminSkin_parts[0]);
	WikiFactory::clearCache(277);

	echo "New \$wgDefaultSkin : {$adminSkin_parts[0]}\n";

	WikiFactory::SetVarById(555, $wgCityId, null);
	WikiFactory::clearCache(555);

	echo "New \$wgDefaultTheme : null\n";

} else {

	echo "AdminSkin value is not correct - ignore it\n";

}
