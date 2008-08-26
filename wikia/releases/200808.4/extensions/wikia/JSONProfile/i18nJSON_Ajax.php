<?php
$wgAjaxExportList [] = 'wfGetJsTranslation';
function wfGetJsTranslation($lang, $do_on_fly=false) {
	
	$lang_array = getTransList("all");
	$output = "";
	
	foreach ($lang_array as $id=>$val) {
		//$output .= "\"".$id."\"" . ":" . "\"".$id."\",\n";
		$lang_array[$id] = $id;
	}
	
	$lang_array_lang = getTransList($lang);
	foreach ($lang_array_lang as $id=>$val) {
		$lang_array[$id] = $val;
	}
	$output = "";
	foreach ($lang_array as $id=>$val) {
		$output .= "\"".$id."\"" . ":" . "\"".$val."\",\n";
	}
	
	if (strlen($output)) $output = substr($output, 0, strlen($output)-2);
	
	return "i18n." . $lang . "={" . $output . "};" . ($do_on_fly ? "\nxlateOnFly('{$lang}');\ni18n.setlanguage('{$lang}');" : "");
	
}

function getTransList($lang) {
	$lang_trans = trim(wfMsg("Jstrans_" . $lang));
	if (substr($lang_trans, -1)==",") $lang_trans = substr($lang_trans, 0, strlen($lang_trans)-1);
		
	if ($lang_trans == "&lt;Jstrans_".$lang."&gt;") return array();
	else return breakdown_lang($lang_trans);
}

function breakdown_lang($lang_trans, $ex_key=",\n") {
	
	$messages = explode($ex_key, $lang_trans );
	$full_message_array = array();
	foreach( $messages as $message ){
		
		if(trim($message)=="") continue;
		$message_array = explode(":", $message);
		
		//invalid line
		if( count( $message_array ) != 2 ){
			continue;
		}
		
		
		
		$id = trim($message_array[0]);
		$val = trim($message_array[1]);
		
		if ((substr($id,0,1)=="\"" && substr($id, -1)=="\"") ||  (substr($id,0,1)=="'" && substr($id, -1)=="'")) {
			$id=substr($id, 1, strlen($id)-2);
		}
		if ((substr($val,0,1)=="\"" && substr($val, -1)=="\"") ||  (substr($val,0,1)=="'" && substr($val, -1)=="'")) {
			$val=substr($val, 1, strlen($val)-2);
		}
		if (trim($id) != "") $full_message_array[$id]=$val;
		
	}
	return $full_message_array;
}

$wgAjaxExportList[] = 'saveTransListMulti';
function saveTransListMulti() {
	/*
	global $wgRequest;
	
	$lang = $wgRequest->getVal("lang");
	$lang_changes = $wgRequest->getVal("trans_change");
	$wpSourceForm = $wgRequest->getVal("source");
	*/
	
	if(!isset($_POST['lang']) || !isset($_POST['trans_changes']) || !isset($_POST['source'])) {
		return "no";
	}
	$lang = $_POST['lang'];
	$lang_changes = $_POST['trans_changes'];
	$wpSourceForm = $_POST['source'];
	
	$lang_array = getTransList($lang);
	$changes_array = breakdown_lang($lang_changes, ",||");
	
	//return $lang_changes;
	//return "-" . sizeof($changes_array) . "-";
	
	$change_output = "";
	foreach ($changes_array as $change_id=>$change_text) {
		$lang_array[$change_id] = $change_text;
		//$change_output .= "i18n." . $lang . "[\"{$change_id}\"]=\"{$change_text}\";\n";
	}
	
	$output = "";
	foreach ($lang_array as $id=>$val) {
		$output .= "\"".$id."\"" . ":" . "\"".$val."\",\n";
	}
	
	
	$title = "Jstrans_" . $lang;
	$page_title = Title::makeTitleSafe( NS_MEDIAWIKI, $title );
	$article = new Article($page_title);
	$article->doEdit( $output, "Search translation JSON");
	
	sleep(1);
	
	//------------------
	
	$lang_array_all = getTransList("all");
	$all_size = sizeof($lank_array_all);
	
	$output_all = "";
	foreach ($lang_array as $id=>$val) {
		$lang_array_all[$id] = "\"*\"";
	}
	
	if(sizeof($lang_array_all) > $all_size) {
		foreach ($lang_array_all as $id=>$val) {
			$output_all .= "\"".$id."\"" . ":" . "\"".$val."\",\n";
		}
		
		
		$title = "Jstrans_all";
		$page_title = Title::makeTitleSafe( NS_MEDIAWIKI, $title );
		$article = new Article($page_title);
		$article->doEdit( $output_all, "Search translation JSON");
		sleep(1);
		//------------------
	}
	
	//return "lang: {$lang}\ntrans_changes: {$lang_changes}\nsource: {$wpSourceForm}";
	return "<script type=\"text/javascript\">location.href='{$wpSourceForm}?saved=1';</script>";
	
}
?>
