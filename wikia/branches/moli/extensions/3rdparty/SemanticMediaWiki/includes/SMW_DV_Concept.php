<?php
/**
 * @file
 * @ingroup SMWDataValues
 */

/**
 * This datavalue is used as a container for concept descriptions as used
 * on Concept pages with the #concept parserfunction. It has a somewhat
 * non-standard interface as compared to other datavalues, but this is not
 * an issue.
 *
 * @author Markus Krötzsch
 * @note AUTOLOADED
 */
class SMWConceptValue extends SMWDataValue {

	protected $m_concept = ''; // XML-safe, HTML-safe, Wiki-compatible concept expression (query string)
	protected $m_docu = '';    // text description of concept, can only be set by special function "setvalues"

	protected function parseUserValue($value) {
		$this->clear();
		// this function is normally not used for this class, not created from user input directly
		$this->m_concept = smwfXMLContentEncode($value);
		if ($this->m_caption === false) {
			$this->m_caption = $value;
		}
		return true;
	}

	protected function parseXSDValue($value, $unit) {
		// normally not used, store should use setValues
		$this->clear();
		$this->m_concept = $value;
		$this->m_caption = $this->m_concept; // this is our output text
	}

	protected function clear() {
		$this->m_concept = '';
		$this->m_docu = '';
		$this->m_queryfeatures = 0;
		$this->m_size = -1;
		$this->m_depth = -1;
	}

	public function getShortWikiText($linked = NULL) {
		return $this->m_caption;
	}

	public function getShortHTMLText($linker = NULL) {
		return $this->getShortWikiText($linker); // should be save (based on xsdvalue)
	}

	public function getLongWikiText($linked = NULL) {
		if (!$this->isValid()) {
			return $this->getErrorText();
		} else {
			return $this->m_caption;
		}
	}

	public function getLongHTMLText($linker = NULL) {
		if (!$this->isValid()) {
			return $this->getErrorText();
		} else {
			return $this->m_caption; // should be save (based on xsdvalue)
		}
	}

	public function getXSDValue() {
		return $this->getWikiValue(); // no XML encoding in DB for concepts, simplifies direct access in store
	}

	public function getWikiValue(){
		return str_replace(array('&lt;','&gt;','&amp;'),array('<','>','&'), $this->m_concept);
	}

	public function getExportData() {
		if ($this->isValid()) {
			$qp = new SMWQueryParser();
			$desc = $qp->getQueryDescription($this->getWikiValue());
			$exact = true;
			$owldesc = $this->descriptionToExpData($desc, $exact);
			if ($owldesc === false) {
				$element = new SMWExpData(SMWExporter::getSpecialElement('owl','Thing'));
			}
			if (!$exact) {
				$result = new SMWExpData(new SMWExpElement(''));
				$result->addPropertyObjectValue(SMWExporter::getSpecialElement('rdf', 'type'),
				                                new SMWExpData(SMWExporter::getSpecialElement('owl', 'Class')));
				$result->addPropertyObjectValue(SMWExporter::getSpecialElement('rdfs', 'subClassOf'), $owldesc);
				return $result;
			} else {
				return $owldesc;
			}
		} else {
			return NULL;
		}
	}
	
	public function descriptionToExpData($desc, &$exact) {
		if ( ($desc instanceof SMWConjunction) || ($desc instanceof SMWDisjunction) ) {
			$result = new SMWExpData(new SMWExpElement(''));
			$result->addPropertyObjectValue(SMWExporter::getSpecialElement('rdf', 'type'),
			                                new SMWExpData(SMWExporter::getSpecialElement('owl', 'Class')));
			$elements = array();
			foreach ($desc->getDescriptions() as $subdesc) {
				$element = $this->descriptionToExpData($subdesc, $exact);
				if ($element === false) {
					$element = new SMWExpData(SMWExporter::getSpecialElement('owl','Thing'));
				}
				$elements[] = $element;
			}
			$prop = ($desc instanceof SMWConjunction)?'intersectionOf':'unionOf';
			$result->addPropertyObjectValue(SMWExporter::getSpecialElement('owl', $prop),
			                                SMWExpData::makeCollection($elements));
		} elseif ($desc instanceof SMWClassDescription) {
			if (count($desc->getCategories()) == 1) { // single category
				$result = new SMWExpData(SMWExporter::getResourceElement(end($desc->getCategories())));
			} else { // disjunction of categories
				$result = new SMWExpData(new SMWExpElement(''));
				$elements = array();
				foreach ($desc->getCategories() as $cat) {
					$elements[] = new SMWExpData(SMWExporter::getResourceElement($cat));;
				}
				$result->addPropertyObjectValue(SMWExporter::getSpecialElement('owl', 'unionOf'),
				                                SMWExpData::makeCollection($elements));
			}
			$result->addPropertyObjectValue(SMWExporter::getSpecialElement('rdf', 'type'),
			                                new SMWExpData(SMWExporter::getSpecialElement('owl', 'Class')));
		} elseif ($desc instanceof SMWConceptDescription) {
			$result = new SMWExpData(SMWExporter::getResourceElement($desc->getConcept()));
		} elseif ($desc instanceof SMWSomeProperty) {
			$result = new SMWExpData(new SMWExpElement(''));
			$result->addPropertyObjectValue(SMWExporter::getSpecialElement('rdf', 'type'),
			                                new SMWExpData(SMWExporter::getSpecialElement('owl', 'Restriction')));
			$result->addPropertyObjectValue(SMWExporter::getSpecialElement('owl', 'onProperty'),
			                                new SMWExpData(SMWExporter::getResourceElement($desc->getProperty())));
			$subdata = $this->descriptionToExpData($desc->getDescription(), $exact);
			if ( ($desc->getDescription() instanceof SMWValueDescription) && 
			     ($desc->getDescription()->getComparator() == SMW_CMP_EQ) ) {
				$result->addPropertyObjectValue(SMWExporter::getSpecialElement('owl', 'hasValue'), $subdata);
			} else {
				if ($subdata === false) {
					$owltype = SMWExporter::getOWLPropertyType(SMWDataValueFactory::getPropertyObjectTypeID($desc->getProperty()));
					if ($owltype == 'ObjectProperty') {
						$subdata = new SMWExpData(SMWExporter::getSpecialElement('owl','Thing'));
					} elseif ($owltype == 'DatatypeProperty') {
						$subdata = new SMWExpData(SMWExporter::getSpecialElement('rdfs','Literal'));
					} else { // no restrictions at all with annotation properties ...
						return new SMWExpData(SMWExporter::getSpecialElement('owl','Thing'));
					}
				}
				$result->addPropertyObjectValue(SMWExporter::getSpecialElement('owl', 'someValuesFrom'), $subdata);
			}
		} elseif ($desc instanceof SMWValueDescription) {
			if ($desc->getComparator() == SMW_CMP_EQ) {
				$result = $desc->getDataValue()->getExportData();
			} else { // alas, OWL cannot represent <= and >= ...
				$exact = false;
				$result = false;
			}
		} elseif ($desc instanceof SMWThingDescription) {
			$result = false;
		} else {
			$result = false;
			$exact = false;
		}
		return $result;
	}

	/**
	 * Special features for Type:Code formating.
	 */
	protected function getCodeDisplay($value, $scroll = false) {
		$result = str_replace( array('<', '>', ' ', '://', '=', "'"), array('&lt;', '&gt;', '&nbsp;', '<!-- -->://<!-- -->', '&#x003D;', '&#x0027;'), $value);
		if ($scroll) {
			$result = "<div style=\"height:5em; overflow:auto;\">$result</div>";
		}
		return "<pre>$result</pre>";
	}

	public function setValues($concept, $docu, $queryfeatures, $size, $depth) {
		$this->setUserValue($concept); // must be called to make object valid (parent implementation)
		$this->m_docu = $docu?smwfXMLContentEncode($docu):'';
		$this->m_queryfeatures = $queryfeatures;
		$this->m_size = $size;
		$this->m_depth = $depth;
	}

	public function getDocu() {
		return $this->m_docu;
	}

	public function getSize() {
		return $this->m_size;
	}

	public function getDepth() {
		return $this->m_depth;
	}

	public function getQueryFeatures() {
		return $this->m_queryfeatures;
	}

}
