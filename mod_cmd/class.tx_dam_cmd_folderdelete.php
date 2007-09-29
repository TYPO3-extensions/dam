<?php
/**
 * Command module 'delete folder'
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
 *   38: class tx_dam_cmd_folderdelete extends t3lib_extobjbase
 *   46:     function head()
 *   56:     function main()
 *   72:     function renderForm()
 *   91:     function processDelete()
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_t3lib.'class.t3lib_extobjbase.php');

/**
 * Class for the folder delete command
 *
 * @author	Peter Kühn <peter.kuehn@wmdb.de>
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-ModCmd
 * @subpackage Folder
 */
class tx_dam_cmd_folderdelete extends t3lib_extobjbase {

	var $targetDir;
	var $targetDirInfo;
	var $existingFiles=array();
	var $deleteRecursive=TRUE;
	var $deleteProcessed=FALSE;

	function head() {
		global  $LANG, $BACK_PATH, $TYPO3_CONF_VARS;
		$GLOBALS['SOBE']->pageTitle = $LANG->sL('LLL:EXT:lang/locallang_core.xml:cm.delete',1);
		$this->targetDir=t3lib_div::_GP('target');
		$this->targetDirInfo=tx_dam::file_compileInfo($this->targetDir,1);
		$this->existingFiles=t3lib_div::getAllFilesAndFoldersInPath(array(),$this->targetDir,'',1);
		$this->deleteRecursive=count($this->existingFiles)===1;
		if(t3lib_div::_GP('deleteConfirmed')) $this->processDelete();
	}

	function main(){
		global $LANG;
		if(!$this->deleteProcessed){
			$content = $this->renderForm();
		}else{
			return '
						<script>
						//navframe needs reload:
						top.content.nav_frame.document.location.reload();
						document.location.href=\''.t3lib_div::locationHeaderUrl($this->pObj->redirect).'\';
						</script>
					';
		}
		return $content;
	}

	function renderForm(){
		global $LANG;
		if(!$this->deleteRecursive){
			//2B continued: delete recursive...
			$content=$GLOBALS['SOBE']->doc->section('&nbsp;','Das ausgewählte Verzeichniss enthält Daten und kann nicht gelöscht werden.',1,1,2,1);
		}else{
			$content.=
			$GLOBALS['SOBE']->doc->section('&nbsp;',sprintf($LANG->sL('LLL:EXT:lang/locallang_core.xml:mess.delete'),$this->targetDirInfo['file_path'].$this->targetDirInfo['file_name'].'/'),1,1,2,1).'
			<div id="c-submit">
				<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:cm.delete',1).'" />
				<input type="submit" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.cancel',1).'" onclick="jumpBack(); return false;" />
				<input type="hidden" name="redirect" value="'.htmlspecialchars($this->pObj->returnUrl).'" />
				<input type="hidden" name="target" value="'.$this->targetDir.'" />
				<input type="hidden" name="deleteConfirmed" value="1" />
			</div>';
		}
		$content.= '<br /><br />'.$this->pObj->btn_back('',$this->pObj->returnUrl);
		return $content;
	}
	function processDelete(){
		require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');
		$file = t3lib_div::makeInstance('tx_dam_tce_file');
		$file->init();
			// Processing rename folder
		$cmd = array();
		$cmd['delete']['NONE']['data'] = ereg_replace('/$','',$this->targetDir);
		$file->setCmdmap($cmd);
		$log = $file->process();
		if ($file->errors()) {
			$this->error = $file->getLastError();
		} else {
			$this->deleteProcessed=true;
		}
	}
}



//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_folderdelete.php'])    {
//	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/mod_cmd/class.tx_dam_cmd_folderdelete.php']);
//}


?>