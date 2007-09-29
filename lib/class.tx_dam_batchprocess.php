<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2004 René Fritz (r.fritz@colorcube.de)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Part of the DAM (digital asset management) extension.
 *
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *

 *
 * TOTAL FUNCTIONS: 10
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */




$GLOBALS['LANG']->includeLLFile('EXT:dam/lib/locallang.php');

require_once(PATH_txdam.'lib/class.tx_dam_db.php');



/**
 * Batch processing of DAM records
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
 */
class tx_dam_batchProcess {

	/**
	 * Parameter name of the submitted data
	 */
	var $startParam = 'process';
	
	/**
	 * Data which should replace the current record data
	 */
	var $replaceData = array();	


	/**
	 * Data which should be appended the current record data
	 */
	var $appendData = array();	


	/**
	 * Array of the record data which has changed
	 */
	var $updated = array();	


	

	/**
	 * Processes submitted GP data from the preset form
	 * 
	 * @return	boolean		TRUE if data was submitted
	 */
	function processGP() {
		global $SOBE, $BE_USER, $LANG, $BACK_PATH, $TCA;


		if(t3lib_div::_GP($this->startParam)) {
			$data = t3lib_div::_POST('data');
		}

			
		if (is_array($data['tx_dam_simpleforms'][1])) {

			t3lib_div::loadTCA('tx_dam');
			
				// get which fields are append
			$appendFieldsArr = t3lib_div::_POST('data_fixedFields');

			$appendFields=array();
			if (is_array($appendFieldsArr['tx_dam_simpleforms'][1])) {
				foreach($appendFieldsArr['tx_dam_simpleforms'][1] as $field => $isAppend) {
					if($isAppend) $appendFields[] = $field;
				}
			}

				// split data to preset and append
			$this->appendData=array();
			$this->replaceData=array();
			foreach($data['tx_dam_simpleforms'][1] as $field => $value) {
				if (trim($value)) {
					if (in_array($field, $appendFields)) {
						$this->appendData[$field] = $value;
					} else {
						$this->replaceData[$field] = $value;
					}
				}
			}
			return TRUE;
		}
		return FALSE;
	}
	
	
	/**
	 * Run the batch
	 * 
	 * @param 	mixed 	A valid db query result
	 */
	function runBatch($res, $table='tx_dam') {	
		global $TCA;

		if (!$res OR !is_array($TCA[$table])) { return FALSE; }
		
		$this->updated = array();
		
			// is needed to check the input data
		require_once (PATH_t3lib.'class.t3lib_tcemain.php');
		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
		$tce->debug=0;
		$tce->disableRTE=1;
		$tce->stripslashes_values=0;		
		
		
			// is needed to get the record for merging with submittedtceforms data
		require_once (PATH_t3lib.'class.t3lib_transferdata.php');
		$trData = t3lib_div::makeInstance('t3lib_transferData');
		$trData->lockRecords = 0;
		$trData->disableRTE = 1;
			

		
		while($rowRaw = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$uid = $rowRaw['uid'];
			$pid = $rowRaw['pid'];
			
		
				// get record in tceforms format
			$trData->defVals = array();
			$trData->regTableItems_data = array();
			$trData->fetchRecord($table, $uid, '');
			reset($trData->regTableItems_data);
			$row = current($trData->regTableItems_data);			
			
			
		
			$rowUpdate = array();
			
			foreach($this->replaceData as $field => $value) {
				$rowUpdate[$field] = $value;
			}
			
			foreach($this->appendData as $field => $value) {
		
				switch($TCA[$table]['columns'][$field]['config']['type'])	{
					case 'input':
						$rowUpdate[$field] = trim($row[$field].' '.$value);
					break;
					case 'text':
						$rowUpdate[$field] = $row[$field].($row[$field]?"\n":'').$value;
					break;
					case 'select':
					case 'group':
						$data = $this->stripLabelFromGroupData($row[$field]);
						$rowUpdate[$field] = $data.','.$value;
					break;
					case 'none':
					case 'user':
					case 'flex':
					case 'check':
					case 'radio':
					default:
						$rowUpdate[$field] = $value; // replace anyway
					break;
				}						
			}



			if (count($rowUpdate)) {


#TODO  			tx_dam_db::insertMetaRecord($rowUpdate,$row['uid']);
				
				$newRec = array_merge($row,$rowUpdate);
				
				$tce->start( array( $table => array($uid => $newRec) ) ,array() );
				foreach($rowUpdate as $field => $value) {
					$result = $tce->checkValue($table, $field, $value, $uid, 'update', $pid, $pid);
					if (isset($result['value']))	{
						$rowUpdate[$field] = $result['value'];
					} else {
#TODO is that right?
						unset($rowUpdate[$field]);
					}
				}					

			
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, 'uid='.$uid, $rowUpdate);
				echo $GLOBALS['TYPO3_DB']->sql_error();
				
				$rowInfo = array();
				foreach ($rowRaw as $field => $dummy) {
					$rowInfo[$field] = isset($newRec[$field]) ? $newRec[$field] : $rowRaw[$field];
				}
	
				
				$this->updated[$table][$uid]['data'] = $rowUpdate;
				$this->updated[$table][$uid]['info'] = $rowInfo;
			}
		}
	}	
			
			
			



	/********************************
	 *
	 * GUI misc
	 *
	 ********************************/

			
	/**
	 * Show the gui: preset form
	 * 
	 * @return	string 		HTML
	 */
	function showPresetForm() {	
		global $SOBE, $LANG;
		
		$content = '';
		$content.= $SOBE->doc->section('',$LANG->getLL('tx_dam_batchProcess.introduction'),0,1);
		$content.= $SOBE->doc->spacer(5);
#TODO $rec - saved preset ?			
		$code = $this->getPresetForm($rec, $fixedFields, $LANG->getLL('tx_dam_batchProcess.appendDesc'));
		
		$cnBgColor = t3lib_div::modifyHTMLcolor($SOBE->doc->bgColor3,-5,-5,-5);
		$content.= $SOBE->doc->section('','<table border="0" cellpadding="4" width="100%"><tr><td bgcolor="'.$cnBgColor.'">'.
						$code.
						'</td></tr></table>',0,1);

		$content.= '<br /><div style="margin-left:35px;"><input type="submit" name="'.$this->startParam.'" value="'.$LANG->getLL('tx_dam_batchProcess.submit').'" /></div><br />';

		
		return $content;	
	}
			
			
	/**
	 * Show the gui: result table
	 * 
	 * @return	string 		HTML
	 */
	function showResult() {	
		global $SOBE, $LANG;
		
		$content = '';
			
		$content.= $SOBE->doc->section('',$LANG->getLL('tx_dam_batchProcess.processed'),0,1);
		$content.= $this->getResultTable();

		return $content;	
	}		
	
	/**
	 * Render a form with TCEForms to edit/enter the preset data
	 * 
	 * @param	array		Data which should be edited in the form
	 * @param	array		Which fields are set to fixed
	 * @param	string		Description given at the top
	 * @return	string 		HTML
	 */
	function getPresetForm ($rec, $fixedFields, $description) {
		global $SOBE, $BE_USER, $LANG, $BACK_PATH, $TCA;

		$content = '';

		if(!is_array($rec)) $rec = array();
		if(!is_array($fixedFields)) $fixedFields = array();
		$rec['uid'] = 1;
		$rec['pid'] = 1;
		$rec['media_type'] = 0;

			// fake table - to be safe
		t3lib_div::loadTCA('tx_dam');
		$TCA['tx_dam_simpleforms'] = $TCA['tx_dam'];

		require_once (PATH_txdam.'lib/class.tx_dam_simpleforms.php');
		$form = t3lib_div::makeInstance('tx_dam_simpleForms');
		$form->initDefaultBEmode();
		$form->removeRequired($TCA['tx_dam_simpleforms']);
		$form->tx_dam_fixedFields = $fixedFields;

#TODO $description have <b> tags in it. Therefore htmlspecialchars() is not very nice
			// add message for checkboxes
		$content.= '<tr bgcolor="'.$SOBE->doc->bgColor4.'">
				<td nowrap="nowrap" valign="middle">'.
				'<img src="clear.gif" width="7" height="10" alt="" />'.
				'<img src="'.$BACK_PATH.'gfx/pil2down.gif" width="12" height="7" vspace="2" alt="" />'.
				'<img src="clear.gif" width="10" height="10" alt="" /></td>
				<td valign="top">'.$description.'</td>
			</tr>
			<tr>
				<td colspan="2"><img src="clear.gif" width="1" height="8" alt="" /></td>
			</tr>';


		$columnsOnly=$TCA['tx_dam']['txdamInterface']['index_fieldList'];

		if ($columnsOnly)	{
			$content.= $form->getListedFields('tx_dam_simpleforms', $rec, $columnsOnly);
		} else {
			$content.= $form->getMainFields('tx_dam_simpleforms', $rec);
		}

		$content = $form->wrapTotal($content, $rec, 'tx_dam_simpleforms');

		$SOBE->doc->JScode .='
		'.$form->printNeededJSFunctions_top();
		$content.= $form->printNeededJSFunctions();

		unset($TCA['tx_dam_simpleforms']);

		return $content;
	}		
		

    /**
	 * Render the table with result records
	 * 
	 * @return	string		Rendered Table
	 */
    function getResultTable()   {
        global $BACK_PATH, $BE_USER, $LANG, $SOBE;

		if (!count($this->updated)) { return ; }

            // init table layout
        $refTableLayout = array (
            'table' => array ('<table border="0" cellpadding="1" cellspacing="1" class="typo3-recent-edited">', '</table>'),
            'defRow' => array (
                'tr' => array('<tr class="bgColor4">','</tr>'),
                'defCol' => Array('<td valign="top">','</td>')
            )
        );

        $cTable=array();
        $tr=0;

        foreach ($this->updated as $table => $recdata) {
	        foreach ($recdata as $uid => $data) {
	        	$row = $data['info'];

	                // Create output item for record
	            $recordElementLink = tx_dam_div::getItemFromRecord($table, $row);
#TODO more info	
	                // Add row to table
	            $td=0;
	            $cTable[$tr][$td++] = $recordElementLink;
	            $tr++;
	        }
        }

            // Return rendered table
        return $SOBE->doc->table($cTable, $refTableLayout);
    }	




	/***************************************
	 *
	 *	 Helper
	 *
	 ***************************************/


    /**
	 * I'm wondering why there's no function like this somewhere else ?????
	 * 
	 * @params	string		group element data from t3lib_transferdata
	 * @return	string		comma list
	 */
	function stripLabelFromGroupData($data) {
		$itemArray = array();
		$temp_itemArray = t3lib_div::trimExplode(',',$data,1);
		foreach($temp_itemArray as $dbRead)	{
			$recordParts = explode('|',$dbRead);
			$itemArray[] = $recordParts[0];
		}
		return implode (',', $itemArray);
	}
				
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_batchprocess.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_batchprocess.php']);
}


?>