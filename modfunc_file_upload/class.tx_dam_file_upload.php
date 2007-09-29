<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
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
 * Module extension (addition to function menu) 'Upload' for the 'Media>File' module.
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage file
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   98: class tx_dam_file_upload extends t3lib_extobjbase
 *  114:     function modMenu()
 *  135:     function head()
 *  154:     function startUploads()
 *  160:     function hideUploadForm()
 *  164:     function processReqChange()
 *  181:     function loadXMLDoc(url)
 *  204:     function uploadprogress(response)
 *  224:     function isprocessing()
 *  229:     function getprogress()
 *  237:     function collectBatchItems()
 *  271:     function main()
 *  407:     function uploadForm($path, $uploadFields=5)
 *  490:     function uploadProcessing()
 *
 *              SECTION: GUI helper
 *  560:     function getLogMessages($log)
 *  586:     function getUploadedFileList($files)
 *  663:     function getBatchSubmitButton($uidList)
 *
 *              SECTION: misc helper
 *  697:     function getFilesFromLog($log)
 *  715:     function getMaxUploadSize()
 *
 *              SECTION: Indexing and DB
 *  751:     function indexUploadedFiles($fileList)
 *  825:     function getUIDsFromItemarray($itemArr, $makeList=TRUE)
 *  844:     function getErrorMsgFromItem ($itemArr)
 *  864:     function compileItemArray($uidList, $res=FALSE)
 *
 *              SECTION: Arrays and Lists
 *  908:     function array_copy_list($target, $source, $keys='')
 *
 * TOTAL FUNCTIONS: 23
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */




require_once(PATH_t3lib.'class.t3lib_extobjbase.php');


$GLOBALS['LANG']->includeLLFile('EXT:lang/locallang_mod_file_list.xml');
$GLOBALS['LANG']->includeLLFile('EXT:lang/locallang_misc.xml');


require_once(PATH_txdam.'lib/class.tx_dam_tce_file.php');

require_once(PATH_txdam.'lib/class.tx_dam_indexing.php');



/**
 * Module 'Media>File>Upload'
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Mod
 * @subpackage file
 */
class tx_dam_file_upload extends t3lib_extobjbase {


	/**
	 * The target path for upload
	 */
	var $target;

	var $enableBatchProcessing = true;


	/**
	 * Function menu initialization
	 *
	 * @return	array		Menu array
	 */
	function modMenu()    {
		global $LANG;

		return array(
			'tx_dam_file_upload_overwrite' => '',
			'tx_dam_file_upload_showBrowser' => '',
			'tx_dam_file_uploadFields' =>  array(
						5 => '5 '.$LANG->sL('LLL:EXT:lang/locallang_core.xml:file_upload.php.files'),
						10 => '10 '.$LANG->sL('LLL:EXT:lang/locallang_core.xml:file_upload.php.files'),
						15 => '15 '.$LANG->sL('LLL:EXT:lang/locallang_core.xml:file_upload.php.files'),
					),
			'tx_dambatchprocess_setup' => '',
		);
	}


	/**
	 * Initialize the class and set some HTML header code
	 *
	 * @return	void
	 */
	function head()	{
		global $BE_USER, $LANG, $BACK_PATH, $FILEMOUNTS, $TYPO3_CONF_VARS;

		//
		// Init gui items and ...
		//

		$this->wasUploadForm = t3lib_div::_GP('uploadmagic');
		t3lib_div::_GETset('', 'uploadmagic');

		$this->pObj->addParam['SET[function]'] = 'tx_dam_file_upload';

		$this->pObj->guiItems->registerFunc('getOptions', 'footer');


		$damSiteUrl = t3lib_div::getIndpEnv('TYPO3_SITE_URL').str_replace(PATH_site, '', PATH_txdam);


		$this->pObj->doc->JScodeArray['upload_progress'] = '
		function startUploads()	{
			hideUploadForm();
			document.getElementById("uploadstatus").style.display = "block";
			uploadprogress("");
		}

		function hideUploadForm()	{
			document.getElementById("uploadform").style.display = "none";
		}

		function processReqChange()	{
			if (req.readyState == 4)
			{
				if (req.status == 200)
				{
					response = req.responseXML.documentElement;
					method = response.getElementsByTagName("method")[0].firstChild.data;
					result = response.getElementsByTagName("result")[0].firstChild.data;
					eval(method + "(result)");
				}else{
//					alert("There was a problem retrieving the XML data:\n" + req.statusText);
				}
			}
		}

		var req;

		function loadXMLDoc(url)	{
			if(window.XMLHttpRequest)
			{
				req = new XMLHttpRequest();
				req.onreadystatechange = processReqChange;
				req.open("GET", url, true);
				req.send(null);
			}else if(window.ActiveXObject)
			{
				req = new ActiveXObject("Msxml2.XMLHTTP");
				if(req)
				{
					req.onreadystatechange = processReqChange;
					req.open("GET", url, true);
					req.send();
				}
			}
		}

		var isstarted = 0;
		var isdone = 0;
		var t = 0;

		function uploadprogress(response)	{
			tmessage = document.getElementById("uploadstatus");
			if(response == "0" && isstarted == 1)
			{
				tmessage.innerHTML = "'.$LANG->getLL('tx_dam_file_upload.uploadFinished',1).'";
				isdone = 1;
			}else{
				if(response != "")
				{
					if(isstarted == 0) tmessage.innerHTML = "'.$LANG->getLL('tx_dam_file_upload.uploadStarted',1).'";
					else tmessage.innerHTML = "'.$LANG->getLL('tx_dam_file_upload.uploadProgress',1).' "+response;
					isstarted = 1;
				}else{
					getprogress();
					turl = "'.$damSiteUrl.'modfunc_file_upload/upload_status.php";
					loadXMLDoc(turl);
				}
			}
		}

		function isprocessing()	{
			uploadprogress("");
			if(isdone == 0) getprogress();
		}

		function getprogress()	{
			if(t) window.clearTimeout(t);
			if(isdone == 0) t = window.setTimeout("isprocessing()",800);
		}';


		$this->pObj->doc->JScodeArray['collectBatchItems'] = '

		function collectBatchItems()	{
			var elts      = document.forms["editform"].elements["process_recs[]"];
			var elts_cnt  = (typeof(elts.length) != "undefined")
						? elts.length
						: 0;
			var uidList = "";

			if (elts_cnt) {
				for (var i = 0; i < elts_cnt; i++) {
					if (elts[i].checked) {
						uidList = uidList + "," + elts[i].value;
					}
				}
			} else if (elts.checked) {
				uidList = elts.value;
			}

			if(uidList=="") {
				alert("'.$LANG->getLL('tx_dam_file_upload.noProcSelection',1).'");
				return false;
			} else {
				document.forms["editform"].elements["batch_items"].value = uidList;
				return true;
			}
		}';

	}


	/**
	 * Main function
	 *
	 * @return	string		HTML content
	 */
	function main()	{
		global $LANG, $FILEMOUNTS;

		$content = '';

		$uidList = $GLOBALS['TYPO3_DB']->cleanIntList(t3lib_div::_POST('batch_items'));

		if ((t3lib_div::_GP('batch') OR t3lib_div::_GP('process')) AND ($uidList)) {

				// Output header with path info and folder browser
			$cmdIcons = array('back' => '&nbsp;&nbsp;&nbsp;'.$this->pObj->btn_back());
			$content .= $this->pObj->getPathInfoHeaderBar($this->pObj->pathInfo, FALSE, $cmdIcons);
			$content .= $this->pObj->doc->spacer(10);

			require_once(PATH_txdam.'lib/class.tx_dam_batchprocess.php');
			$batch = t3lib_div::makeInstance(tx_dam_batchProcess);



			if($batch->processGP()) {

				$infoFields = $batch->getProcessFieldList();
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($infoFields, 'tx_dam', 'tx_dam.uid IN ('.$uidList.')');
				if($res) {
					$batch->runBatch($res);
				}
				$files = $this->compileItemArray($uidList, $res);
				$GLOBALS['TYPO3_DB']->sql_free_result($res);

				$uidList = $this->getUIDsFromItemArray($files);

				$code = $this->getUploadedFileList($files);
				$code .= $this->getBatchSubmitButton($uidList);

				$content .= $this->pObj->doc->spacer(5);
				$content .= $this->pObj->doc->section($LANG->getLL('tx_dam_file_upload.uploadFiles'), $code);


			} else {


				//
				// Batch processing: show preset form
				//

				$content .= '<input type="hidden" name="batch" value="1" />';
				$content .= '<input type="hidden" name="batch_items" value="'.$uidList.'" />';

				$content .= $batch->showPresetForm();

			}




		} elseif (is_array(t3lib_div::_GP('file'))) {

			//
			// Upload processing: move files and sho result
			//

			$content .= $this->uploadProcessing();

		} else {


			//
			// Upload Form
			//

				// add some options
			$this->pObj->addOption('funcCheck', 'tx_dam_file_upload_showBrowser', $LANG->getLL('tx_dam_file_upload.showBrowser'));



				// Output header with path info
			$cmdIcons = array();
			if (!$this->pObj->MOD_SETTINGS['tx_dam_file_upload_showBrowser']) {
					// disable refresh button if no file browser
				$cmdIcons['refresh'] = '';
			}
			$content .= $this->pObj->getPathInfoHeaderBar($this->pObj->pathInfo, TRUE, $cmdIcons);


				// for display of uploading progress
			$content .= '
				<div id="uploadstatus" class="bgColor-20" style="display:none; margin:2em 0 2em 0; padding:0.5em; text-align:center; border-top:1px solid #666; border-bottom:1px solid #666">...</div>';


				// "uploadmagic" is set as GET var to detect failed uploads with lost POST data
			$this->pObj->addParams['uploadmagic'] = '1';
			$content .= '</form>'.$this->pObj->getFormTag('uploadform');
			$this->pObj->addParams['uploadmagic'] = '';


			//
			// Upload failed because of braindead PHP upload handling which gives no error for too big files.
			// Even the POST data is not existend. Search for "uploadmagic" in code.
			//

			if ($this->wasUploadForm) {
				$content .= $this->pObj->doc->spacer(5);
				$content .= $this->pObj->doc->section($LANG->getLL('tx_dam_file_upload.uploadFailed'), $LANG->getLL('tx_dam_file_upload.uploadFailedDesc',1),0,0,2);
				$content .= $this->pObj->doc->spacer(5);
			}


				// Unset "uploadmagic" via POST to make clear that upload worked
			$content .='
					<input type="hidden" name="uploadmagic" value="0" />';


				// Upload form
			$content .= $this->pObj->doc->section('', $this->uploadForm($this->pObj->pathInfo['dir_path_absolute'], $this->pObj->MOD_SETTINGS['tx_dam_file_uploadFields']));


				// Show browseable file list
			if ($this->pObj->MOD_SETTINGS['tx_dam_file_upload_showBrowser']) {
				$content .= $this->pObj->doc->spacer(15);
				$code = $this->pObj->getBrowseableFolderList($this->pObj->path);
				$content .= $this->pObj->doc->section('', $code);
			}


		}

		return $content;

	}


	/**
	 * Rendering the upload file form fields
	 *
	 * @return	string		HTML content
	 */
	function uploadForm($path, $uploadFields=5)	{
		global $BACK_PATH, $LANG, $FILEMOUNTS;


			// number of max upload fields
		$maxUploads = 15;
		$uploadFields = t3lib_div::intInRange($uploadFields, 5, $maxUploads);


		$content = '';

		//
		// Output upload form
		//

			// Making the selector box for the number of concurrent uploads
		$select = t3lib_BEfunc::getFuncMenu($this->pObj->addParams,'SET[tx_dam_file_uploadFields]', $this->pObj->MOD_SETTINGS['tx_dam_file_uploadFields'], $this->pObj->MOD_MENU['tx_dam_file_uploadFields']);

		$content .= $this->pObj->doc->spacer(5);
		$content .= $this->pObj->doc->funcMenu('', '<div id="c-select">'.$select.'<div>');



			// Make checkbox for "overwrite"
		$code .='
			<div id="c-override">
				<input type="hidden" name="SET[tx_dam_file_upload_overwrite]" value="0" />
				<input type="checkbox" id="tx_dam_file_upload_overwrite" name="SET[tx_dam_file_upload_overwrite]" value="1"'.($this->pObj->MOD_SETTINGS['tx_dam_file_upload_overwrite']?' checked="checked"':'').' />
				<label for="tx_dam_file_upload_overwrite">'.$LANG->getLL('overwriteExistingFiles',1).'</label>
			</div>
			';

			// Produce the number of upload-fields needed:
		$code .= '
			<div id="c-upload">
		';
		for ($a=0; $a<$uploadFields; $a++)	{
				// Adding 'size="50" ' for the sake of Mozilla!
			$code .='
				<input type="file" name="upload_'.$a.'"'.$this->pObj->doc->formWidth(35).' size="50" />
				<input type="hidden" name="file[upload]['.$a.'][target]" value="'.htmlspecialchars($path).'" />
				<input type="hidden" name="file[upload]['.$a.'][data]" value="'.$a.'" /><br />
			';
		}

#		$code .= $this->pObj->doc->spacer(5);




		if ($upload_max_filesize = $this->getMaxUploadSize()) {
			$code .= '
				<input type="hidden" name="MAX_FILE_SIZE" value="'.$upload_max_filesize.'" />
				<p class="typo3-dimmed">'.sprintf($LANG->getLL('tx_dam_file_upload.maxSizeHint',1), t3lib_div::formatSize($upload_max_filesize)).'</p>
			';
		}


		$code .= '
			</div>
		';

			// Submit button:
		$code .= '
			<div id="c-submit">
				<input type="button" value="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:file_upload.php.submit',1).'" onclick=" document.uploadform.submit(); startUploads();" />
			</div>
		';


		$content .= $code;



		return $content;
	}


	/**
	 * Processing the upload and display information
	 *
	 * @return	string		HTML content
	 */
	function uploadProcessing()	{
		global $BACK_PATH, $LANG, $FILEMOUNTS, $TYPO3_CONF_VARS;

		$content = '';

		//
		// Processing uploads
		//

		$file = t3lib_div::makeInstance('tx_dam_tce_file');
		$file->init();
		$file->overwriteExistingFiles($this->pObj->MOD_SETTINGS['tx_dam_file_upload_overwrite']);
		$log = $file->process();




		//
		// Content output
		//


			// Output header with path info
		$cmdIcons = array();
		$cmdIcons['back'] = '&nbsp;&nbsp;&nbsp;'.$this->pObj->btn_back();
		$content .= $this->pObj->getPathInfoHeaderBar($this->pObj->pathInfo, FALSE, $cmdIcons);

		if (count($log['cmd']['upload']) AND ($files = $this->getFilesFromLog($log['cmd']['upload']))) {

			$files = $this->indexUploadedFiles($files);
			$uidList = $this->getUIDsFromItemArray($files);

			$code = $this->getUploadedFileList($files);
			$code .= $this->pObj->doc->spacer(5);
			$code .= $this->getBatchSubmitButton($uidList);

			$content .= $this->pObj->doc->spacer(5);
			$content .= $this->pObj->doc->section('Uploaded files:', $code);

		} else {

			$content .= $this->pObj->doc->spacer(10);
			$content .= $this->pObj->doc->section($LANG->getLL('tx_dam_file_upload.uploadNothing'), '<p class="typo3-dimmed">'.sprintf($LANG->getLL('tx_dam_file_upload.maxSizeExceeded',1), t3lib_div::formatSize($this->getMaxUploadSize())).'</p>');
		}


		if ($messages = $this->getLogMessages($log['cmd']['upload'])) {
			$content .= $this->pObj->doc->spacer(5);
			$content .= $this->pObj->doc->section($LANG->getLL('tx_dam_file_upload.uploadMessages'), $messages);
		}

		return $content;
	}




	/********************************
	 *
	 * GUI helper
	 *
	 ********************************/


	/**
	 * Creates a list of uploaded files
	 *
	 * @param	array		$log: ...
	 * @return	string		HTML content
	 */
	function getLogMessages($log)	{
		$messages = false;
		if(is_array($log)) {
			$msgArr = array();
			foreach ($log as $id => $data) {

				if(is_array($data['errors']) AND count($data['errors'])) {
					foreach ($data['errors'] as $error) {
						$msgArr[] = htmlspecialchars($error['msg']);
					}
				}
			}
			if (count($msgArr)) {
				$messages = '<p>'.implode('</p><p>', $msgArr).'</p>';
			}
		}
		return $messages;
	}


	/**
	 * Creates a list of uploaded files
	 *
	 * @param	array		$log: ...
	 * @return	string		HTML content
	 */
	function getUploadedFileList($files)	{
		global $BACK_PATH, $LANG;

		$content = '';

			// init table layout
		$tableLayout = array(
			'table' => array('<table border="0" cellpadding="1" cellspacing="1" id="typo3-filelist">', '</table>'),
			'defRow' => array(
				'tr' => array('<tr>','</tr>'),
				'defCol' => array('<td valign="middle" class="bgColor4">','</td>'),
				'990' => array('<td valign="middle" class="bgColor4">','</td>'),
				'996' => array('<td valign="middle" class="bgColor5">','</td>'),
			),
			'0' => array(
				'tr' => array('<tr class="c-headLine">','</tr>'),
				'defCol' => array('<td valign="middle" class="c-headLine">','</td>'),
				'2' => array('<td valign="middle" class="c-headLine" style="width:165px;">','</td>'),
			),
		);


		$table=array();
		$tr=0;

			// header
		$td=0;
		$table[$tr][$td++] = '&nbsp;';
		$table[$tr][$td++] = '&nbsp;';
		$table[$tr][$td++] = $LANG->getLL('c_file');
		$table[$tr][$td++] = $LANG->getLL('c_fileext');
		$table[$tr][$td++] = $LANG->getLL('c_tstamp');
		$table[$tr][$td++] = $LANG->getLL('c_size');
		# $table[$tr][$td++] = $LANG->getLL('c_rw');
		$table[$tr][$td++] = '&nbsp;';
		$tr++;


		foreach ($files as $item) {

			$row = $item['meta'];

			$fileIcon = tx_dam::icon_getFileTypeImgTag($row, 'title="'.htmlspecialchars($row['file_type']).'"');


				// Add row to table
			$td=0;
			if ($item['uid']) {
				#$table[$tr][$td++] = $this->pObj->doc->icons(-1); // Ok;
				$table[$tr][$td++] = $this->enableBatchProcessing ? '<input type="checkbox" name="process_recs[]" value="'.$row['uid'].'" />': '';
				$table[$tr][$td++] = $fileIcon;
				$table[$tr][$td++] = htmlspecialchars(t3lib_div::fixed_lgd_cs($row['file_name'], 30));
				$table[$tr][$td++] = htmlspecialchars(strtoupper($row['file_type']));
				$table[$tr][$td++] = date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'], $row['file_ctime']);
				$table[$tr][$td++] = htmlspecialchars(t3lib_div::formatSize($row['file_size']));
				$table[$tr][$td++] = $this->pObj->btn_editRec_inNewWindow('tx_dam', $item['uid']);
			} else {
					// failure
				$table[$tr][$td++] = $this->pObj->doc->icons(2); // warning
				$table[$tr][$td++] = $fileIcon;
				$table[$tr][$td++] = htmlspecialchars(t3lib_div::fixed_lgd_cs($row['file_name'], 30));
				$table[$tr][$td++] = htmlspecialchars(strtoupper($row['file_type']));
				$table[$tr][$td++] = '';
				$table[$tr][$td++] = '';
				$table[$tr][$td++] = htmlspecialchars($this->getErrorMsgFromItem($item));
			}

			$tr++;
		}

			// render table
		$code = $this->pObj->doc->table($table, $tableLayout);

		return $code;
	}


	function getBatchSubmitButton($uidList) {
		global $LANG;

		$content = '';

			// Submit button:
		if ($uidList AND $this->enableBatchProcessing) {
			$content .= '
			<div id="c-submit">
				<input type="submit" name="batch" value="'.$LANG->getLL('tx_dam_file_upload.submitProcess').'" onclick="return collectBatchItems();" />
				<input type="hidden" name="batch_items" value="" />
			</div>
			';
		}
#				<input type="hidden" name="batch_items" value="'.htmlspecialchars($uidList).'" />
		return $content;
	}




	/********************************
	 *
	 * misc helper
	 *
	 ********************************/


	/**
	 * collect list of uploaded files
	 *
	 * @param	array		$log: ...
	 * @return	array		File list array
	 */
	function getFilesFromLog($log)	{
		$files = array();

		foreach ($log as $id => $data) {
			if($data['target_file']) {
				$files[md5($data['target_file'])] = $data['target_file'];
			}
		}

		return $files;
	}


	/**
	 * Return max upload file size
	 *
	 * @return integer Maximum file size for uploads in bytes
	 */
	function getMaxUploadSize() {
		$upload_max_filesize = ini_get('upload_max_filesize');
		$match = array();
		if (preg_match('#(M|MB)$#i', $upload_max_filesize, $match)) {
			$upload_max_filesize = intval($upload_max_filesize)*1048576;
		} elseif (preg_match('#(k|kB)$#i', $upload_max_filesize, $match)) {
			$upload_max_filesize = intval($upload_max_filesize)*1024;
		}

		$maxFileSize = intval($GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'])*1024;

		if (min($maxFileSize, $upload_max_filesize)==0) {
			$upload_max_filesize = max($maxFileSize, $upload_max_filesize);
		} else {
			$upload_max_filesize = min($maxFileSize, $upload_max_filesize);
		}
		return $upload_max_filesize;
	}




	/********************************
	 *
	 * Indexing and DB
	 *
	 ********************************/



	/**
	 * Index uploaded files
	 *
	 * @param	array		$fileList: ...
	 * @return	array		Data
	 */
	function indexUploadedFiles($fileList)	{
		global $BACK_PATH, $LANG;
		$files = array();
		$fieldList = tx_dam_db::getMetaInfoFieldList(FALSE);

			// Init indexing object
		$indexObj = t3lib_div::makeInstance('tx_dam_indexing');
		$indexObj->init();
		$indexObj->initEnabledRules();
#		$crdate = time();

		$indexedFiles = $indexObj->indexFiles($fileList, $this->pObj->defaultPid);

		foreach ($indexedFiles as $k => $data) {

			$metaRec = tx_dam::meta_getDataByUid ($data['uid'], '*');

			$fi = array();
			if(is_array($metaRec) AND $metaRec['uid']) {
				$metaRec = $this->array_copy_list(array(), $metaRec, $fieldList);
			} else {
				$fI = pathinfo($data['orig_filename']);
				$metaRec = array();
				$metaRec['file_name'] = $fI['basename'];
				$metaRec['file_type'] = $fI['extension'];
			}

			$files[] = array(
				'uid' => $metaRec['uid'],
				'meta' => $metaRec,
				'errors' => $data['errors'],
			);

		}

		return $files;
	}




	/********************************
	 *
	 * Media record list handling
	 *
	 ********************************/

#TODO cleanup and use media objects!!


//	array of asset items used by some functions to pass meta data
//
//	var $itemArr = array(
//		'some_id' => array(
//				'uid' => $row['uid'],	// if uid is 0 then meta is NOT from a record
//				'meta' => $row,
//				'meta_is_all' => 1, // set if not full record is in meta
//				'errors' => array(
//					0 => array(
//						'major' => $error,	// the main error number
//						'minor' => $details_nr,	// the detail error number
//						'msg' => sprintf($details, $data[0], $data[1], $data[2], $data[3], $data[4]), // this should be set localized if available
//					),
//				)
//			)
//		);

	/**
	 * Extract a list of uid's from an item array
	 *
	 * @param	array		array of item arrays
	 * @param	boolean		If set a comma list string is returned, otherwise an array
	 * @return	array		List of uid's
	 */
	function getUIDsFromItemarray($itemArr, $makeList=TRUE) {
		$uidList = array();

		foreach ($itemArr as $item) {
			if($item['uid']=intval($item['uid'])) {
				$uidList[$item['uid']] = $item['uid'];
			}
		}
		$uidList = $makeList ? implode(',', $uidList) : $uidList;
		return $uidList;
	}


	/**
	 * Extract an error message from an item
	 *
	 * @param	array		item array
	 * @return	string		error message
	 */
	function getErrorMsgFromItem ($itemArr) {
		$errMsg = '';

		foreach ($itemArr as $item) {
			if(is_array($item['errors'])) {
				$error = end($item['errors']);
				$errMsg = $error['msg'];
			}
		}
		return $errMsg;
	}


	/**
	 * Creates a list of indexed files by uid
	 *
	 * @param	string		$uidList Comma list of uid's
	 * @param	mixed		$res DB result pointer. If set will be used to fetch records instead of the $uidList.
	 * @return	array		Data
	 */
	function compileItemArray($uidList, $res=FALSE)	{
		global $BACK_PATH, $LANG;

		$items = array();

		if ($res) {
			$GLOBALS['TYPO3_DB']->sql_data_seek($res,0);
		} else {
			$infoFields = tx_dam_db::getMetaInfoFieldList();
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($infoFields, 'tx_dam', 'tx_dam.uid IN ('.$GLOBALS['TYPO3_DB']->cleanIntList($uidList).')');
		}

		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{

			$items[$row['uid']] = array(
				'uid' => $row['uid'],
				'meta' => $row,
				'meta_is_all' => 0, // set if not full record is in meta
				'errors' => array(),
			);
		}

		return $items;
	}





	/********************************
	 *
	 * Arrays and Lists
	 *
	 ********************************/


	/**
	 * Copies array vlaues by a list of keys to a tzarget array
	 *
	 * @param	array		$target: target array
	 * @param	array		$source: source array
	 * @param	mixed		$keys: key names to copy - array or comma list
	 * @return	array		Copied array
	 */
	function array_copy_list($target, $source, $keys='') {
		if (!is_array($keys)) {
			$keys = t3lib_div::trimExplode(',', $keys, 1);
		}
		foreach ($keys as $key) {
			if (isset($source[$key])) {
				$target[$key] = $source[$key];
			}
		}
		return $target;
	}



}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_file_upload/class.tx_dam_file_upload.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/modfunc_file_upload/class.tx_dam_file_upload.php']);
}

?>