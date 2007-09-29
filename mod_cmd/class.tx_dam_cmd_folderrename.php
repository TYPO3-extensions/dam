<?php
/**
 * Command module 'rename folder'
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Peter Kühn <peter.kuehn@wmdb.de>
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 * @subpackage Folder
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   39: class tx_dam_cmd_folderrename extends t3lib_extobjbase
 *   51:     function head()
 *   67:     function main()
 *  110:     function renameForm()
 *  129:     function renameFolder()
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_t3lib.'class.t3lib_extobjbase.php');


/**
 * Class for the folder rename command
 *
 * @author	Peter Kühn <peter.kuehn@wmdb.de>
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 * @subpackage Folder
 */
class tx_dam_cmd_folderrename extends t3lib_extobjbase {

	var $dirInfo;
	var $targetPath = false;
	var $targetDir = false;
	var $pathToTargetDir = false;

	/**
	 * Do some init things and set some things in HTML header
	 *
	 * @return	void
	 */
	function head() {
		global  $LANG, $BACK_PATH, $TYPO3_CONF_VARS;
		$GLOBALS['SOBE']->pageTitle = $LANG->sL('LLL:EXT:lang/locallang_core.xml:file_rename.php.pagetitle');
		if(t3lib_div::_GP('target') && is_dir(t3lib_div::_GP('target'))){
			$this->dirInfo=tx_dam::file_compileInfo(t3lib_div::_GP('target'),1);
			$this->targetDir=$this->dirInfo['file_name'];
			$this->pathToTargetDir=$this->dirInfo['file_path_absolute'];
			$this->targetPath=t3lib_div::_GP('target');
		}
	}

	/**
	 * Main function, rendering the content of the rename form
	 *
	 * @return	void
	 */
	function main()	{
		global  $LANG;
		$content='';
		if (@is_dir($this->targetPath)) {
			$error = '';
			if (is_array($this->pObj->data)) {
				$error = $this->renameFolder();
				if(!$error) {
					//nicht schön aber noch keine idee wie anders....:
					return '
						<script>
						//navframe needs reload:
						top.content.nav_frame.document.location.reload();
						document.location.href=\''.t3lib_div::locationHeaderUrl($this->pObj->redirect).'\';
						</script>
					';
				}
			}
			if($error) {
				$content.= $GLOBALS['SOBE']->doc->section('Error',htmlspecialchars($error),0,1,2);
				$content.= $GLOBALS['SOBE']->doc->spacer(15);
			}


			$pathInfo = tx_dam::path_compileInfo($this->targetPath);
			$content.= tx_dam_guiFunc::getFolderInfoBar($pathInfo);
			$content.= $GLOBALS['SOBE']->doc->spacer(10);

			$code = $this->renameForm();
			$content.= $GLOBALS['SOBE']->doc->section('',$code);
		} else {
// TODO localization
			$content.= 'Fehler beim Umbennen des Verzeichnisses "'.$this->targetPath.'"';
		}
		$content.= '<br /><br />'.$this->pObj->btn_back('',$this->pObj->returnUrl);
		return $content;
	}

	/**
	 * Making the formfields for renaming
	 *
	 * @return	string		HTML content
	 */
	function renameForm()	{
		global $TCA, $BACK_PATH, $LANG, $FILEMOUNTS;
		$content.= '
			<input'.$GLOBALS['SOBE']->doc->formWidth(20).' type="text" name="data[newDirName]" value="'.($this->pObj->data['newDirName']?$this->pObj->data['newDirName']:$this->targetDir).'">
			<div id="c-submit">
				<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:file_rename.php.submit',1).'" />
				<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" onclick="jumpBack(); return false;" />
				<input type="hidden" name="redirect" value="'.htmlspecialchars($this->pObj->returnUrl).'" />
				<input type="hidden" name="target" value="'.$this->targetPath.'" />
			</div>
		';
		return $content;
	}

	/**
	 * Rename the folder and process DB update
	 *
	 * @return	void
	 */
	function renameFolder() {

		$error = FALSE;
		$targetPath=ereg_replace('/$','',$this->targetPath);
		$newDirName=$this->pObj->data['newDirName'];

// TODO :-)
		if(!$newDirName){
			return 'so nich';
		}
		if(strstr($newDirName,'/')){
			return 'so nich';
		}

		require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');
		$file = t3lib_div::makeInstance('tx_dam_tce_file');
		$file->init();
			// Processing rename folder
		$cmd = array();
		$cmd['rename']['NONE']['target'] = $targetPath;
		$cmd['rename']['NONE']['data'] = $newDirName;

		$file->setCmdmap($cmd);
		$log = $file->process();
		if ($file->errors()) {
			$error = $file->getLastError();
		} else {
			//db-update
			$res=$GLOBALS['TYPO3_DB']->exec_SELECT_queryArray(array(
					'SELECT'=>'file_path',
					'FROM'=>'tx_dam',
					'WHERE'=>'1=1'.t3lib_BEfunc::deleteClause('tx_dam'),
					'GROUPBY'=>'file_path',
					'ORDERBY'=>'file_path'
			  	));
			while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$dirInfo=tx_dam::file_compileInfo($row['file_path'],1);
				if(ereg('^'.$targetPath.'/',$dirInfo['file_path_absolute'].$dirInfo['file_name'].'/')){
					$updateDir=ereg_replace('^'.$targetPath,$this->pathToTargetDir.$newDirName,$dirInfo['file_path_absolute'].$dirInfo['file_name']).'/';
					if (!ereg('^/',$row['file_path'])) {
						$updateDir=tx_dam::path_makeRelative($updateDir);
					}
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_dam','file_path="'.$row['file_path'].'"',array('file_path'=>$updateDir));
				}
			}
		}
		return $error;
	}
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_folderrename.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_folderrename.php']);
}
?>