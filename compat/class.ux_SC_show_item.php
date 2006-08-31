<?php

/**
 * @ignore
 */
class ux_SC_show_item extends SC_show_item {

	/**
	 * Main function. Will generate the information to display for the item set internally.
	 *
	 * @return	void
	 */
	function main()	{
		global $LANG;

		if ($this->access)	{
			$returnLinkTag = t3lib_div::_GP('returnUrl') ? '<a href="'.t3lib_div::_GP('returnUrl').'" class="typo3-goBack">' : '<a href="#" onclick="window.close();">';

				// render type by user func
			$typeRendered = false;
			if (is_array ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/show_item.php']['typeRendering'])) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/show_item.php']['typeRendering'] as $classRef) {
					$typeRenderObj = t3lib_div::getUserObj($classRef);
					if(is_object($typeRenderObj) && method_exists($typeRenderObj, 'isValid') && method_exists($typeRenderObj, 'render'))	{
						if ($typeRenderObj->isValid($this->type, $this)) {
							$this->content .=  $typeRenderObj->render($this->type, $this);
							$typeRendered = true;
							break;
						}
					}
				}
			}

				// if type was not rendered use default rendering functions
			if(!$typeRendered) {
					// Branch out based on type:
				switch($this->type)	{
					case 'db':
						$this->renderDBInfo();
					break;
					case 'file':
						$this->renderFileInfo($returnLinkTag);
					break;
				}
			}

				// If return Url is set, output link to go back:
			if (t3lib_div::_GP('returnUrl'))	{
				$this->content = $this->doc->section('',$returnLinkTag.'<strong>'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.goBack',1).'</strong></a><br /><br />').$this->content;

				$this->content .= $this->doc->section('','<br />'.$returnLinkTag.'<strong>'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.goBack',1).'</strong></a>');
			}
		}
	}

	/**
	 * End page and print content
	 *
	 * @return	void
	 */
	function printContent()	{
		$this->content.= $this->doc->endPage();
		$this->content = $this->doc->insertStylesAndJS($this->content);
		echo $this->content;
	}

}

?>
