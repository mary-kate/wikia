<?php
/**
 * @author： Yaron Koren  翻译:张致信 本档系以电子字典译自繁体版，请自行修订(Translation: Roc Michael Email:roc.no1@gmail.com. This file is translated from Tradition Chinese by using electronic dictionary. Please correct the file by yourself.)  
 */
 
class SF_LanguageZh_cn extends SF_Language {

	/* private */ var $m_SpecialProperties = array(
		//always start upper-case
		SF_SP_HAS_DEFAULT_FORM  => '预设表单',	//(Has default form) 
		SF_SP_HAS_ALTERNATE_FORM  => '代用表单'  //(Has alternate form)
	);

	var $m_Namespaces = array(
		SF_NS_FORM           => '表单',		//	(Form)
		SF_NS_FORM_TALK      => '表单讨论'	//	(Form_talk)  SF 0.7.6前原译为'表单_talk'
	);

}

$m_SpecialPropertyAliases['设有表单'] = SF_SP_HAS_DEFAULT_FORM;	//(Has default form) //Adding the item "Has alternate form", this item will not be suitable for translating into “设有表单＂. It has changed to use “预设表单＂. 

?>


