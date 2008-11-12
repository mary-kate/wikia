<?php

class CreateLawProposal extends CreateForm {
	
	function displayFormExtra(){
		$output  = "";
		$output .= '<span class="title">justification</span><br><textarea tabindex="'.$this->tab_counter.'" accesskey="," name="justification" id="justification" class="createbox" rows="10" cols="80"></textarea><br><br>';
		$this->tab_counter++;
		return $output;
	}
	
	function displayForm(){
		global $wgOut, $wgStyleVersion, $wgExtensionsPath;
		$wgOut->addScript("<script type=\"text/javascript\" src=\"{$wgExtensionsPath}/wikia/onejstorule.js{$wgStyleVersion}\"></script>\n");
		$wgOut->addScript("<script type=\"text/javascript\" src=\"{$wgExtensionsPath}/wikia/CreateForms/CreatePolitics.js{$wgStyleVersion}\"></script>\n");
		$output = $this->displayFormStart();
		
		$output .= $this->displayFormPageTitle();
		$output .= $this->displayFormPageText();
		$output .= $this->displayFormExtra();
		$output .= $this->displayFormPageCategories();
		$output .= $this->displayFormCommon();
		$output .= $this->displayFormEnd();
		return $output;
	}
}

?>
