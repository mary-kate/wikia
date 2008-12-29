<?
$wgHooks['GetHTMLAfterBody'][] = 'AnswersCSS';
$wgHooks['MonacoBeforePageBar'][] = 'AnswersAsk';
$wgHooks['CustomArticleFooter'][] = 'AnswersFooter';
$wgHooks['SpecialFooterAfterWikia'][] = 'AnswersJS';
$wgHooks['MonacoBodyContentTop'][] = 'AnswersBodyContentTop';

function AnswersFooter() {
	$tmpl = new EasyTemplate( dirname( __FILE__ ) . '/templates/' );
	echo $tmpl->execute('AnswersFooter'); 
	return true;
}

function AnswersCSS() {
	$tmpl = new EasyTemplate( dirname( __FILE__ ) . '/templates/' );
	echo $tmpl->execute('AnswersCSS'); 
	return true;
}

function AnswersJS() {
	$tmpl = new EasyTemplate( dirname( __FILE__ ) . '/templates/' );
	echo $tmpl->execute('AnswersJS'); 
	return true;
}

function AnswersBodyContentTop() {
	$tmpl = new EasyTemplate( dirname( __FILE__ ) . '/templates/' );
	echo $tmpl->execute('AnswersBodyContentTop'); 
	return true;
}

function AnswersAsk() {
	$tmpl = new EasyTemplate( dirname( __FILE__ ) . '/templates/' );
	echo $tmpl->execute('AnswersAsk'); 
	return true;
}
