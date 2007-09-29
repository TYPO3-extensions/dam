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
 * indexing lib
 * Part of the DAM (digital asset management) extension.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Lib
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *  125: class tx_dam_indexing
 *
 *              SECTION: Init and setup functions
 *  207:     function init()
 *  222:     function clearCollectedMeta()
 *  233:     function enableMetaCollect($metaCollect=TRUE)
 *  244:     function setIndexRun($tstamp=0)
 *  255:     function setRunType($type)
 *  266:     function setPath($path)
 *  277:     function setPathsList($pathlist)
 *  288:     function setRecursive($recursive=true)
 *  299:     function setPID($pid)
 *  310:     function setDryRun($dryRun=TRUE)
 *  321:     function enableReindexing($doReindexing=TRUE)
 *  331:     function isDryRun()
 *
 *              SECTION: Setup re-storing functions
 *  352:     function serializeSetup($extraSetup='', $serializeData=true)
 *  375:     function restoreSerializedSetup($setup)
 *  406:     function getExtraSetup()
 *
 *              SECTION: Setting/searching for default setup, eg. from file setup
 *  432:     function findSetupInPath($path, $walkUp=true, $basePath='')
 *  464:     function setDefaultSetup($path=false, $walkUp=true, $basePath='')
 *
 *              SECTION: Main indexing functions
 *  498:     function indexUsingCurrentSetup($callbackFunc=NULL, $metaCallbackFunc=NULL, $filePreprocessingCallbackFunc=NULL)
 *  517:     function indexFiles($files, $pid=NULL, $callbackFunc=NULL, $metaCallbackFunc=NULL, $filePreprocessingCallbackFunc=NULL)
 *  586:     function indexFile($pathname, $crdate=0, $pid=NULL, $metaCallbackFunc=NULL, $filePreprocessingCallbackFunc=NULL, $metaPreset=array())
 *
 *              SECTION: Indexing rules
 *  749:     function mergeRuleConf($ruleOpt)
 *  774:     function initEnabledRules()
 *  804:     function initAvailableRules()
 *  845:     function rulesCallback ($type, $meta, $pathname)
 *
 *              SECTION: Collecting file meta data
 *  886:     function getFileMetaInfo($pathname, $meta)
 *  978:     function getMetaLanguage($meta)
 *  999:     function getFileNodeInfo($pathname, $calcHash=false)
 * 1023:     function getFileMimeType($pathname)
 * 1098:     function getFileTextExcerpt($pathname, $file_type, $limit=64000)
 * 1126:     function getWantedCharset()
 * 1141:     function processTextExcerpt($textExcerpt, $limit=64000)
 * 1165:     function getImageDimensions($pathname,$file_type='')
 * 1184:     function getDefaultRecord($table='tx_dam')
 * 1207:     function makeTitleFromFilename ($title)
 * 1222:     function listBeautify($list)
 *
 *              SECTION: Files, folders and paths
 * 1249:     function getFilesInDir($path, $recursive=FALSE, $filearray=array(), $maxDirs=999)
 * 1279:     function collectFiles($path, $recursive=false, $filearray=array())
 * 1301:     function collectFilesByPathList($pathlist, $recursive)
 *
 *              SECTION: Rendering the option form and info
 * 1326:     function getIndexingOptionsForm()
 * 1347:     function getIndexingOptionsInfo()
 * 1374:     function formatOptionsFormRow ($varname, $setup, $title, $desc='', $options='')
 *
 *              SECTION: Collect some stats
 * 1427:     function statBegin()
 * 1438:     function statMeta($meta)
 * 1455:     function statEnd()
 * 1464:     function statClear()
 *
 *              SECTION: Logging
 * 1490:     function writeLog($indexRun, $type, $message, $itemCount, $error)
 * 1515:     function log($message, $itemCount, $error)
 *
 * TOTAL FUNCTIONS: 47
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */





require_once(PATH_t3lib.'class.t3lib_exec.php');


/**
 * Provide indexing functions
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Lib
 */
class tx_dam_indexing {

	/**
	 * Should the whole thing be a dry run
	 */
	var $dryRun = FALSE;

	/**
	 * Should files be reindexed
	 */
	var $doReindexing = FALSE;


	/**
	 * indexing rules objects
	 */
	var $rules = array();

	/**
	 * indexing config
	 */
	var $ruleConf = array();

	/**
	 * values which can be overwritten while indexing
	 */
	var $dataPreset = array();

	/**
	 * this will be fixed values
	 */
	var $dataPostset = array();

	/**
	 * Pid of the sysfolder where the DAM records should be written
	 */
	var $pid = 0;

	/**
	 * the folder to index
	 */
	var $pathlist = array();
	var $recursive = false;

	/**
	 * Should the indexed meta data collected into $this->meta?
	 */
	var $collectMeta = false;

	/**
	 * used to collect meta data of the indexed files
	 */
	var $meta = array();

	/**
	 * used to collect uid's and titles of the indexed files
	 */
	var $infoList = array();


	/**
	 * index run type which will be written to log:
	 * man, auto, cron (4 chars max)
	 */
	 var $indexRunType = 'unkn';

	/**
	 * used to collect some statistics
	 */
	var $stat = array();
	var $statmtime;




	/***************************************
	 *
	 *	 Init and setup functions
	 *
	 ***************************************/


	/**
	 * Initializes.
	 *
	 * @return	void
	 */
	function init()	{
		$this->ruleConf = array();
		$this->dataPreset = array();
		$this->dataPostset = array();
		$this->stat = array();
		$this->indexRun = time();

		$this->clearCollectedMeta();

		$this->initAvailableRules();
	}


	/**
	 * Clears all collected meta data
	 *
	 * @return	void
	 */
	function clearCollectedMeta()	{
		$this->meta = array();
		$this->infoList = array();
	}


	/**
	 * Should the indexed meta data collected into $this->meta?
	 *
	 * @param	boolean		If set the indexed meta data collected into $this->meta
	 * @return	void
	 */
	function enableMetaCollect($metaCollect=TRUE)	{
		$this->collectMeta = $metaCollect;
	}


	/**
	 * Set the index run time stamp
	 *
	 * @param	integer		$tstamp time stamp (time())
	 * @return	void
	 */
	function setIndexRun($tstamp=0)	{
		$this->indexRun = $tstamp ? $tstamp : time();
	}


	/**
	 * Set the index run type
	 *
	 * @param	string		$type man, auto, cron (4 chars max)
	 * @return	void
	 */
	function setRunType($type)	{
		$this->indexRunType = $type;
	}


	/**
	 * Set the folder to index
	 *
	 * @param	string		the folder to index
	 * @return	void
	 */
	function setPath($path)	{
		$this->pathlist = array($path);
	}


	/**
	 * Set the list of folders and files to index
	 *
	 * @param	array		the list of folders and files to index
	 * @return	void
	 */
	function setPathsList($pathlist)	{
		$this->pathlist = $pathlist;
	}


	/**
	 * Set the the paths to be be traversed recursivley (or not)
	 *
	 * @param	boolean		If set the paths will be traversed recursivley
	 * @return	void
	 */
	function setRecursive($recursive=true)	{
		$this->recursive = $recursive;
	}


	/**
	 * Set Pid of the sysfolder where the DAM records should be written
	 *
	 * @param	integer		page id
	 * @return	void
	 */
	function setPID($pid)	{
		$this->pid = $pid;
	}


	/**
	 * Set dry run
	 *
	 * @param	boolean		If set indexed data will not be written to db
	 * @return	void
	 */
	function setDryRun($dryRun=TRUE)	{
		$this->dryRun = $dryRun;
	}


	/**
	 * Do reindexing
	 *
	 * @param	boolean		If set already indexed files will be reindexed
	 * @return	void
	 */
	function enableReindexing($doReindexing=TRUE)	{
		$this->doReindexing = $doReindexing;
	}


	/**
	 * Get dry run status
	 *
	 * @return	boolean		If true this is a dry run
	 */
	function isDryRun()	{
		return $this->dryRun;
	}




	/***************************************
	 *
	 *	Setup re-storing functions
	 *
	 ***************************************/


	/**
	 * Returns a serialized setup
	 *
	 * @param	mixed		Any extra data that should be stored with the setup
	 * @param	boolean		If set the setup will returned as array and not serialized
	 * @return	string		serialized setup
	 */
	function serializeSetup($extraSetup='', $serializeData=true) {
		$setup = array(
			'pid' => $this->pid,
			'pathlist' => $this->pathlist,
			'recursive' => $this->recursive,
			'ruleConf' => $this->ruleConf,
			'dataPreset' => $this->dataPreset,
			'dataPostset' => $this->dataPostset,
			'dryRun' => $this->dryRun,
			'doReindexing' => $this->doReindexing,
			'collectMeta' => $this->collectMeta,
			'extraSetup' => $extraSetup,
			);
		return $serializeData ? t3lib_div::array2xml($setup) : $setup;
	}


	/**
	 * Restore a serialized setup
	 *
	 * @param	mixed		setup as string (serialized setup) or array
	 * @return	boolean		True if the restored setup seems to be ok and not garbage
	 */
	function restoreSerializedSetup($setup) {
		$isValid = false;

		$setup = is_array($setup) ? $setup : t3lib_div::xml2array($setup);

			// do a simple check if the setup is a valid one
		if(is_array($setup) AND isset($setup['pid']) AND is_array($setup['pathlist'])) {
			$isValid = true;

			$this->pid = $setup['pid'];
			$this->pathlist = $setup['pathlist'];

			$this->recursive = $setup['recursive'];
			$this->ruleConf = $setup['ruleConf'];
			$this->dataPreset = $setup['dataPreset'];
			$this->dataPostset = $setup['dataPostset'];
			$this->dryRun = $setup['dryRun'];
			$this->doReindexing = $setup['doReindexing'];
			$this->collectMeta = $setup['collectMeta'];

			$this->extraSetup = $setup['extraSetup'];
		}
		return $isValid;
	}


	/**
	 * Returns extra setup data that was stored with a serialized setup
	 *
	 * @return	mixed		Any extra data that was stored with the setup
	 */
	function getExtraSetup() {
		return $this->extraSetup;
	}







	/***************************************
	 *
	 *	Setting/searching for default setup, eg. from file setup
	 *
	 ***************************************/



	/**
	 * Fetches the nearest indexing setup in filesystem.
	 *
	 * @param 	string 		$path Path to search for indexing setup
	 * @param 	boolean 	$walkUp If set it will be searched for indexing setup in folders above the given
	 * @param 	string 		$basePath This absolute path is the limit for searching with $walkUp
	 * @return	string 		Setup file content
	 */
	function findSetupInPath($path, $walkUp=true, $basePath='') {

		$fileName = '.indexing.setup.xml';

		$path = tx_dam::path_makeAbsolute($path);

		if (is_file($path.$fileName) AND is_readable($path.$fileName)) {

			$setup = t3lib_div::getUrl($path.$fileName);
			return $setup;
		}

		if (!$walkUp OR ($path == $basePath)) {
			return false;
		}

		if (tx_dam::path_makeRelative($path)=='') {
			return false;
		}

		return $this->findSetupInPath(dirname($path), $walkUp, $basePath);
	}


	/**
	 * Initialize indexing with a default set
	 *
	 * @param 	string 		$path If set this is the path to search for indexing setup
	 * @param 	boolean 	$walkUp If set it will be searched for indexing setup in folders above the given
	 * @param 	string 		$basePath This path is the limit for searching with $walkUp
	 * @return void
	 */
	function setDefaultSetup($path=false, $walkUp=true, $basePath='') {
		global $TYPO3_CONF_VARS;

		$setup = false;
		$basePath = $basePath ? $basePath : PATH_site;

		if ($path) {
			$setup = $this->findSetupInPath($path, $walkUp, $basePath);
		}

		if ($setup) {
			$this->restoreSerializedSetup($setup);
		} else {

			$this->restoreSerializedSetup($TYPO3_CONF_VARS['EXTCONF']['dam']['indexing']['defaultSetup']);
		}
	}



	/***************************************
	 *
	 *	Main indexing functions
	 *
	 ***************************************/


	/**
	 * Start an indexing process from the current setup
	 *
	 * @param	mixed		$callbackFunc Callback function for the finished indexed file.
	 * @param	mixed		$metaCallbackFunc Callback function which will be called during indexing to allow modifications to the meta data.
	 * @param	mixed		$filePreprocessingCallbackFunc Callback function for pre processing the to be indexed file.
	 * @return	void
	 */
	function indexUsingCurrentSetup($callbackFunc=NULL, $metaCallbackFunc=NULL, $filePreprocessingCallbackFunc=NULL)	{
		if(is_array($this->pathlist) AND count($this->pathlist) AND $this->pid) {
			$files = $this->collectFilesByPathList($this->pathlist, $this->recursive);
			return $this->indexFiles($files, $this->pid, $callbackFunc, $metaCallbackFunc, $filePreprocessingCallbackFunc);
		}
	}


	/**
	 * Index files passed as array in format from getFilesInDir()
	 *
	 * @param	array		$files Array of file paths
	 * @param	integer		$pid The PID where the records will be stored
	 * @param	mixed		$callbackFunc Callback function for the finished indexed file.
	 * @param	mixed		$metaCallbackFunc Callback function which will be called during indexing to allow modifications to the meta data.
	 * @param	mixed		$filePreprocessingCallbackFunc Callback function for pre processing the to be indexed file.
	 * @return	array		Info array about indexed files and meta data records.
	 * @see getFilesInDir()
	 */
	function indexFiles($files, $pid=NULL, $callbackFunc=NULL, $metaCallbackFunc=NULL, $filePreprocessingCallbackFunc=NULL)	{

		if (is_array($files) && count($files)) {

			$this->setIndexRun();

			$this->statBegin();

			$pid = is_null($pid) ? $this->pid : $pid;

			$this->initEnabledRules();

			foreach($this->rules as $classname => $setup)	{
				$this->rules[$classname]['obj']->preIndexing();
			}

			foreach($files as $key => $pathname) {

// TODO search for default setup for THIS file path
// cache path setup in array
				$meta = $this->indexFile($pathname, $this->indexRun, $pid, $metaCallbackFunc, $filePreprocessingCallbackFunc);

				if($callbackFunc) {
					call_user_func ($callbackFunc, 'postTrigger', $meta, $pathname, $key, $this);
				}

			}

			foreach($this->rules as $classname => $setup)	{
				$this->rules[$classname]['obj']->postIndexing($this->infoList);
			}

			$this->statEnd($meta);

			if($this->stat['newIndexed']) {
				$this->log ('New files indexed', $this->stat['newIndexed'], 0);
			}
			if($this->stat['reIndexed']) {
				$this->log ('Files reindexed', $this->stat['reIndexed'], 0);
			}
		}
		return $this->infoList;
	}


	/**
	 * Indexing a single file.
	 * Use indexUsingCurrentSetup() or indexFiles() instead.
	 *
	 * @param	string		$pathname: ...
	 * @param	integer		$crdate: timestamp of the index run
	 * @param	integer		$pid: The sysfolder to store the meta data record
	 * @param	mixed		$metaCallbackFunc Will be called to process the meta data
	 * @param	mixed		$filePreprocessingCallbackFunc Will be called to allow preprocessing of the file before indexing
	 * @param	array		$metaPreset: ...
	 * @return	array		Meta data array. $meta['fields'] has the record data.
	 */
	function indexFile($pathname, $crdate=0, $pid=NULL, $metaCallbackFunc=NULL, $filePreprocessingCallbackFunc=NULL, $metaPreset=array())	{
		global $BE_USER;

		$pid = is_null($pid) ? $this->pid : $pid;

		if ($filePreprocessingCallbackFunc) {
			call_user_func ($filePreprocessingCallbackFunc, 'filePreprocessing', $pathname, $this);
		}
// might be possible to have $pathname call by reference and change the filename - usable for copying files before indexing??? Needs to be tested.
// Answer: Note:  Note that the parameters for call_user_func() are not passed by reference.

		if (is_array($meta = $this->getFileNodeInfo($pathname, true))) {

			$meta = t3lib_div::array_merge_recursive_overrule($metaPreset, $meta);

			$status = tx_dam::index_check ($meta['fields'], $meta['fields']['file_hash']);
			$uid = intval($status['meta']['uid']);

			if ($uid) {

				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_dam', 'uid='.intval($uid));
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

					// this is needed for fields like group/MM
				require_once (PATH_t3lib.'class.t3lib_transferdata.php');
				$processData = t3lib_div::makeInstance('t3lib_transferData');
				$row = $processData->renderRecordRaw('tx_dam', $row['uid'], $row['pid'], $row);

					// index rule use 'row' for merging
				$meta['row'] = $row;
				$meta['reindexed'] = $this->doReindexing;
			} else {
				$uid = 'NEW';
			}

// TODO handle TXDAM_file_missing and reconnect file to index

			if (($status['__status'] == TXDAM_file_unknown) OR (($status['__status'] > TXDAM_file_unknown) AND $this->doReindexing)) {

				$mimeType = array();
				$mimeType['fields'] = $this->getFileMimeType($pathname);

				$meta = t3lib_div::array_merge_recursive_overrule(array('fields' => $this->getDefaultRecord()), $meta);
				$meta = t3lib_div::array_merge_recursive_overrule($meta, $mimeType);

				$meta['fields']['uid'] = $uid;
				$meta['fields']['pid'] = $pid;

				$meta['fields']['index_type'] = $this->indexRunType;

				$meta = $this->getFileMetaInfo($pathname, $meta);

				if($meta['textExtract']) {
					$meta['textExtract'] = $this->processTextExcerpt($meta['textExtract']);
				} else {
					$meta['textExtract'] = $this->getFileTextExcerpt($pathname, $meta['fields']['file_type']);
				}

				$meta['fields']['search_content'] = $meta['textExtract'];

				$meta['fields']['abstract'] = $meta['fields']['abstract']?$meta['fields']['abstract']:trim($meta['fields']['search_content']);


				$meta['fields']['language'] = $this->getMetaLanguage($meta);


				$meta['fields']['file_dl_name'] = $meta['fields']['file_dl_name']?$meta['fields']['file_dl_name']:$meta['fields']['file_name'];

				$meta['fields']['crdate'] = $crdate?$crdate:time();
				$meta['fields']['tstamp'] = time();
				$meta['fields']['cruser_id'] = intval($BE_USER->user['uid']);

				$meta['fields']['date_cr'] = $meta['fields']['date_cr']?$meta['fields']['date_cr']:time();
				$meta['fields']['date_mod'] = $meta['fields']['date_mod']?$meta['fields']['date_mod']:$meta['fields']['date_cr'];

// TODO category handling - merging?

				foreach ($this->dataPreset as $field => $value) {
					if ($value AND !$meta['fields'][$field]) {
						$meta['fields'][$field] = $value;
					}
				}
				$meta['fields'] = array_merge($meta['fields'],$this->dataPostset);


				$meta = $this->rulesCallback('process', $meta, $pathname);
				if ($metaCallbackFunc) {
					$meta = call_user_func ($metaCallbackFunc, 'process', $meta, $pathname, $this);
				}

				if (!$this->dryRun) {
					$meta['fields']['uid'] = tx_dam_db::insertUpdateData($meta['fields']);
					if (!$meta['fields']['uid']) {
						$this->log ('Meta record could not be inserted: '.$pathname, 1, 1);
					}
				}

				if($meta['fields']['uid']) {
					$this->infoList[] = array(
						'uid' => $meta['fields']['uid'],
						'title' => $meta['fields']['title'],
						'reindexed' => $meta['reindexed'],
						);
					$this->stat['newIndexed'] += ($meta['reindexed'] ? 0 : 1);
					$this->stat['reIndexed'] += ($meta['reindexed'] ? 1 : 0);
				}

				$meta = $this->rulesCallback('post', $meta, $pathname);
				if ($metaCallbackFunc) {
					$meta = call_user_func ($metaCallbackFunc, 'post', $meta, $pathname, $this);
				}

				if ($this->collectMeta) {
					$currentUid = $meta['fields']['uid'];
					$this->meta[$currentUid] = $meta;
				}


					// TODO indexing of childs to this file - eg. images from a OpenOffice file
				if (is_array($meta['childs'])) {
					foreach ($meta['childs'] as $fileDef) {
						$pathname = $fileDef['pathname'];

						if (file_exists($pathname)) {
							if ($meta['fields']['file_hash'] AND $fileDef['fileStorageType'] == 'moveToInternal') {
								$storageFolder = PATH_site.'uploads/tx_dam/'.$meta['fields']['file_hash'].'/';
								$targetFile = $storageFolder.basename($pathname);
								if(!is_dir($storageFolder)) {
									t3lib_div::mkdir ($storageFolder);
								}
								@unlink($targetFile);
								rename($pathname, $targetFile);
								$pathname = $targetFile;
							}
							$metaPreset = is_array($fileDef['metaPreset']) ? $fileDef['metaPreset'] : array();
							$metaPreset['fields']['parent_id'] = $currentUid;
							$this->indexFile($pathname, $crdate, $pid, $metaCallbackFunc, $filePreprocessingCallbackFunc, $metaPreset);
						}
					}
				}

				$this->statMeta($meta);

				return $meta;


			} elseif (is_array($meta['row'])) {
				$meta['fields'] = $meta['row'];

				$this->statMeta($meta);

				return $meta;
			}

		} else {
			if(!is_file($pathname)) {
				$this->log ('Is not a file: '.$pathname, 1, 1);

			} elseif (!is_readable($pathname)) {
				$this->log ('Is not readable: '.$pathname, 1, 1);
			}
		}
		return FALSE;
	}




	/***************************************
	 *
	 *	 Indexing rules
	 *
	 ***************************************/


	/**
	 * Merge options from rule forms ($data['rules'])
	 *
	 * @param	array		$ruleOpt: ...
	 * @return	void
	 */
	function mergeRuleConf($ruleOpt='') {
			// walk through the index rules
		$this->initAvailableRules();
		foreach($this->rules as $classname => $setup)	{

			if (is_array($ruleOpt) AND is_array($ruleOpt[$classname])) {
					// this is set in the class itself
				unset($ruleOpt[$classname]['shy']);
				$this->rules[$classname]['obj']->setup = t3lib_div::array_merge_recursive_overrule($this->rules[$classname]['obj']->setup, $ruleOpt[$classname]);
			} else {
				$this->rules[$classname]['obj']->setup = t3lib_div::array_merge_recursive_overrule($this->rules[$classname]['obj']->setup, $this->ruleConf[$classname]);
			}
			$this->rules[$classname]['obj']->processOptionsForm();
			$this->ruleConf[$classname] = $this->rules[$classname]['obj']->setup;
		}
	}


	/**
	 * Initialize the available indexing rules.
	 * Creates the objects and init the objects with the user defined setup.
	 *
	 * @return	void
	 */
	function initEnabledRules() {
		global $TYPO3_CONF_VARS;

		$this->initAvailableRules();

		$this->rules=array();
		if (is_array($this->ruleConf))	{
			foreach($this->ruleConf as $classname => $setup)	{

				if ($setup['enabled'] AND is_object($obj = &t3lib_div::getUserObj($TYPO3_CONF_VARS['EXTCONF']['dam']['indexRuleClasses'][$classname],'user_',TRUE)))      {

					$this->rules[$classname]['obj'] = &$obj;
					if (is_array($this->ruleConf[$classname])) {
						$this->rules[$classname]['obj']->setup = array_merge($this->rules[$classname]['obj']->setup, $this->ruleConf[$classname]);
					}
					$this->rules[$classname]['shy'] = $this->rules[$classname]['obj']->setup['shy'];
					$this->rules[$classname]['title'] = $this->rules[$classname]['obj']->getTitle();
					$this->rules[$classname]['desc'] = $this->rules[$classname]['obj']->getDescription();

				}
			}
		}
	}


	/**
	 * Initialize the available indexing rules
	 *
	 * @return	void
	 */
	function initAvailableRules() {
		global $TYPO3_CONF_VARS;

		if (is_array($this->rules) AND count($this->rules)) {
				// init already done
			return;
		}
		$this->rules=array();
		if (is_array($TYPO3_CONF_VARS['EXTCONF']['dam']['indexRuleClasses']))	{
			foreach($TYPO3_CONF_VARS['EXTCONF']['dam']['indexRuleClasses'] as $classname => $classfile)	{
				if (is_object($obj = &t3lib_div::getUserObj($TYPO3_CONF_VARS['EXTCONF']['dam']['indexRuleClasses'][$classname])))      {

						// this is set in the class itself
					unset($this->ruleConf[$classname]['shy']);

					$this->rules[$classname]['obj'] = &$obj;
// TODO maybe a bug? when is that not an array???? PHP5 bug?
					if (is_array($this->ruleConf[$classname])) {
						$this->rules[$classname]['obj']->setup = array_merge($this->rules[$classname]['obj']->setup, $this->ruleConf[$classname]);
					} else {
						$this->ruleConf[$classname] = $this->rules[$classname]['obj']->setup;
					}

						// visible
					$this->rules[$classname]['shy'] = $this->rules[$classname]['obj']->setup['shy'];
					$this->rules[$classname]['title'] = $this->rules[$classname]['obj']->getTitle();
					$this->rules[$classname]['desc'] = $this->rules[$classname]['obj']->getDescription();
				}
			}
		}
	}


	/**
	 * Calls indexing rules
	 *
	 * @param	string		$type: "process" calls processMeta() and "post" postProcessMeta()
	 * @param	array		$meta     file meta information which should be extended
	 * @param	string		$pathname file with absolut path
	 * @return	array		file meta information
	 */
	function rulesCallback ($type, $meta, $pathname) {
		if (is_array($this->rules)) {
			foreach($this->rules as $rule)	{
				switch ($type) {

					case 'process':
					default:
						if(is_callable(array($rule['obj'], 'processMeta'))) {
							$meta = $rule['obj']->processMeta($meta, $pathname, $this);
						}
					break;

					case 'post':
						if(is_callable(array($rule['obj'], 'postProcessMeta'))) {
							$meta = $rule['obj']->postProcessMeta($meta, $pathname, $this);
						}
					break;
				}
			}
		}
		return $meta;
	}





	/***************************************
	 *
	 *	 Collecting file meta data
	 *
	 ***************************************/


	/**
	 * get meta information from a file using the metaExtract service
	 *
	 * @param	string		file with absolut path
	 * @param	array		file meta information which should be extended
	 * @return	array		file meta information
	 */
	function getFileMetaInfo($pathname, $meta)	{

		$TX_DAM = $GLOBALS['T3_VAR']['ext']['dam'];

		$conf = array();
		$conf['wantedCharset'] = $this->getWantedCharset();

		if (is_file($pathname) && is_readable($pathname)) {

			$fileType = $meta['fields']['file_type'];

				// get media type from file type
			$meta['fields']['media_type'] = $TX_DAM['file2mediaCode'][$fileType];
				//  or from mime type
			$meta['fields']['media_type'] = $meta['fields']['media_type'] ? $meta['fields']['media_type'] :  tx_dam::convert_mediaType($meta['fields']['file_mime_type']);

			$mediaType = tx_dam::convert_mediaType($meta['fields']['media_type']);

// TODO services image:* a good idea?

				// find a service for that file type
			if (!is_object($serviceObj = t3lib_div::makeInstanceService('metaExtract', $fileType))) {
					// find a global service for that media type
				$serviceObj = t3lib_div::makeInstanceService('metaExtract', $mediaType.':*');
			}
			if (is_object($serviceObj)) {
				$serviceObj->setInputFile($pathname, $fileType);
				$conf['meta'] = $meta;
				if ($serviceObj->process('', '', $conf) > 0 AND (is_array($svmeta = $serviceObj->getOutput()))) {
						$meta = t3lib_div::array_merge_recursive_overrule($meta, $svmeta);
				}
				$serviceObj->__destruct();
				unset($serviceObj);
			}


// TODO should iptc or exif come first? I guess IPTC is more important while it is edited by hand normally. Also exif and iptc should not conflict.
			$metaExtractServices = array(TXDAM_mtype_image => 'image:exif, image:iptc');
// TODO should be possible to register other services too


				// make simple image size detection if not yet done
			if ($meta['fields']['media_type'] == TXDAM_mtype_image AND intval($meta['fields']['hpixels']) == 0) {
				$imgsize = $this->getImageDimensions ($pathname);
				$meta = t3lib_div::array_merge_recursive_overrule($meta, $imgsize);
			}

					// read exif, iptc data
			if ($metaExtractServices[$meta['fields']['media_type']]) {	// 2

				$metaExtractSubTypes = t3lib_div::trimExplode(',', $metaExtractServices[$meta['fields']['media_type']], 1);
				foreach ($metaExtractSubTypes as $subType) {

					if ($serviceObj = t3lib_div::makeInstanceService('metaExtract', $subType)) {

						$serviceObj->setInputFile($pathname, $fileType);
						$conf['meta'] = $meta;
						if ($serviceObj->process('','',$conf)>0 AND (is_array($svmeta = $serviceObj->getOutput()))) {

							$meta = t3lib_div::array_merge_recursive_overrule($meta, $svmeta);

						}
						$serviceObj->__destruct();
						unset($serviceObj);
					}
				}
			}

				// convert extra meta data to xml
			if (is_array($meta['fields']['meta'])) {
					// content in array is expected as utf-8 because of xml functions
				$meta['fields']['meta'] = t3lib_div::array2xml($meta['fields']['meta']);
			}

			// If no title then the file-name is set as title. This will raise the hits considerably if the search matches the document name.
			if ($meta['fields']['title']=='')	{
				$meta['fields']['title']= $this->makeTitleFromFilename ($meta['fields']['file_name']);
			}

			$meta['fields']['keywords'] = $this->listBeautify($meta['fields']['keywords']);

		}
		return $meta;
	}


	/**
	 * detect the language of the files text excerpt using the textLang service
	 *
	 * @param	array		file meta information which should be extended
	 * @return	string		language iso code
	 */
	function getMetaLanguage($meta)	{
		$language = '';

		if ($meta['fields']['search_content'] AND is_object($serviceObj = t3lib_div::makeInstanceService('textLang'))) {
			$serviceObj->process($meta['fields']['search_content']);
			$language = $serviceObj->getOutput();
			$serviceObj->__destruct();
			unset($serviceObj);
		}

		return $language;
	}


	/**
	 * get basic file meta info
	 *
	 * @param	string		$pathname absolute path to file
	 * @param	boolean		$calcHash if true a hash of the file will be created
	 * @return	array		file information
	 */
	function getFileNodeInfo($pathname, $calcHash=false)	{

		$meta=false;

		$fileInfo = tx_dam::file_compileInfo ($pathname);

		if (is_array($fileInfo) && $fileInfo['__exists']) {
			$meta = array();
			$meta['fields'] = $fileInfo;
			if($calcHash) {
				$meta['fields']['file_hash'] = tx_dam::file_calcHash($fileInfo);
			}
		}

		return $meta;
	}


	/**
	 * get the mime type of a file with full path
	 *
	 * @param	string		$pathname absolute path to file
	 * @return	array		file information
	 */
	function getFileMimeType($pathname)	{

		$TX_DAM = $GLOBALS['T3_VAR']['ext']['dam'];

		$mimeType = array();
		$mimeType['fulltype'] = '';
		$mimeType['file_mime_type'] = '';
		$mimeType['file_mime_subtype'] = '';
		$mimeType['file_type'] = '';

		$fileinfo = pathinfo($pathname);
		$mimeType['file_type'] = strtolower($fileinfo['extension']);

			// try first to get the mime type by extension with own array
			// I made the experience that it is a bit safer than with 'file'
		if (isset($TX_DAM['file2mime'][$mimeType['file_type']])) {

			$mt = $TX_DAM['file2mime'][$mimeType['file_type']];

			// next try
		} elseif(function_exists('mime_content_type')) {
				// available in PHP 4.3.0
			$mt = mime_content_type($pathname);

			// last chance
		} else {
			$osType = TYPO3_OS;
			if (!($osType=='WIN')) {

#			'opt' => ' -i -M '.PATH_txdam."bin/magic.mime ",
#			'opt' => ' -i -M /usr/share/misc/magic.mime;###PATH###bin/magic.mime ',
#			'opt' => ' -i -M ###PATH###bin/magic.mime ',

				if($cmd = t3lib_exec::getCommand('file')) {
					$dummy = array();
					$ret = false;
					$mimeTypeTxt = exec ($cmd.' --mime "'.$pathname.'"', $dummy, $ret);
					if (!$ret AND strstr ($mimeTypeTxt,basename($pathname).':')) {
						$a = explode (':', $mimeTypeTxt);
						$a = explode (';', trim($a[1]));
						//a[1]: text/plain, English; charset=iso-8859-1
						$a = explode (',', trim($a[0]));
						$a = explode (' ', trim($a[0]));
						$mt = trim($a[0]);
					}
				}
			}
		}

		$mtarr = explode ('/', $mt);
		if (is_array($mtarr) && count($mtarr)==2) {

			$mimeType['fulltype'] = $mt;
			$mimeType['file_mime_type'] = $mtarr[0];
			$mimeType['file_mime_subtype'] = $mtarr[1];
		}

		if ($mimeType['file_type'] == '') {
			$mimeType['file_type'] = array_search($mimeType['fulltype'],$TX_DAM['file2mime'],true);
		}

		unset($mimeType['fulltype']);

		return $mimeType;
	}


	/**
	 * get an excerpt from a text file using the textExtract service
	 *
	 * @param	string		file with absolut path
	 * @param	string		file type like 'jpg'
	 * @param	integer		limits the lenght of the text excerpt to $limit bytes
	 * @return	string		text excerpt of false
	 */
	function getFileTextExcerpt($pathname, $file_type, $limit=64000) {

		$textExcerpt = FALSE;

		if (is_object($serviceObj = t3lib_div::makeInstanceService('textExtract',$file_type))) {

			$conf = array();

			if ($limit) {
				$conf['limitOutput'] = $limit+3000;
			}
			$conf['wantedCharset'] = $this->getWantedCharset();

			$serviceObj->setInputFile($pathname, $file_type);
			$serviceObj->process('', '', $conf);
			$textExcerpt = $this->processTextExcerpt($serviceObj->getOutput());

			unset($serviceObj);
		}
		return $textExcerpt;
	}


	/**
	 * get the charset that is used for storage of meta data
	 *
	 * @return	string		charset eg utf-8 or iso-8859-1
	 */
	function getWantedCharset() {
		global $TYPO3_CONF_VARS;

		$wantedCharset = $TYPO3_CONF_VARS['BE']['forceCharset'] ? $TYPO3_CONF_VARS['BE']['forceCharset'] : 'iso-8859-1';
		return $wantedCharset;
	}


	/**
	 * get an excerpt from a text file using the textExtract service
	 *
	 * @param	string		content
	 * @param	integer		limits the lenght of the text excerpt to $limit bytes
	 * @return	string		text excerpt
	 */
	function processTextExcerpt($textExcerpt, $limit=64000) {

		$textExcerpt = trim($textExcerpt);

			// double linebreak is enough
		while (strpos($textExcerpt, "\n\n\n")) {
			$textExcerpt = str_replace("\n\n\n", "\n\n", $textExcerpt);
		}
		if ($limit) {
			$textExcerpt = substr($textExcerpt, 0, $limit);
		}

		return $textExcerpt;
	}


	/**
	 * get the image size of an file in pixels
	 * TODO use service?
	 *
	 * @param	string		file with absolut path
	 * @param	string		file type like 'jpg'
	 * @return	array
	 */
	function getImageDimensions($pathname,$file_type='') {
		$meta = array();

		if(function_exists('getimagesize')) {
			$size = getImageSize($pathname);
			$meta['fields']['hpixels'] = $size[0];
			$meta['fields']['vpixels'] = $size[1];
		}

		return $meta;
	}


	/**
	 * Gets default record.
	 *
	 * @param	string		Database Tablename
	 * @return	array		"default" row.
	 */
	function getDefaultRecord($table='tx_dam')	{
		global $TCA;

		$row = array();
		if ($TCA[$table])	{
			t3lib_div::loadTCA($table);

			foreach($TCA[$table]['columns'] as $field => $info)	{
				if (isset($info['config']['default']))	{
					$row[$field] = $info['config']['default'];
				}
			}
		}
		return $row;
	}


	/**
	 * convert/cleans a file name to be more usable as title
	 *
	 * @param	string		Filename or similar
	 * @return	string		Title string
	 */
	function makeTitleFromFilename ($title) {
		$extpos = strrpos($title,'.');
		$title= $extpos ? substr($title, 0, $extpos) : $title; // remove extension
		$title=str_replace('_',' ',$title);	// Substituting "_" for " " because many filenames may have this instead of a space char.
		$title=str_replace('%20',' ',$title);
		return $title;
	}


	/**
	 * Removes emty entries from a comma list
	 *
	 * @param	string		$list: comma list
	 * @return	string		cleaned list
	 */
	function listBeautify($list) {
		if (!is_array($list)) {
			$list = t3lib_div::trimExplode(',', $list, 1);
		}
		return implode(',', $list);
	}





	/***************************************
	 *
	 *	 Files, folders and paths
	 *
	 ***************************************/


	/**
	 * Returns an array with the names of files in a specific path
	 *
	 * @param	string		Path to start to collect files
	 * @param	boolean		Go recursive into subfolder?
	 * @param	array		Array of file paths
	 * @param	integer		$maxDirs limit the read directories
	 * @return	array		Array of file paths
	 */
	function getFilesInDir($path, $recursive=FALSE, $filearray=array(), $maxDirs=999)	{
		if ($path)	{
			$absPath = tx_dam::path_makeAbsolute($path);
			$d = @dir($absPath);
			if (is_object($d))	{
				while($entry=$d->read()) {
					if (@is_file($absPath.$entry))	{
						if (!preg_match('/^\./',$entry) && !preg_match('/~$/',$entry)) {
							$key = md5($absPath.$entry);
							$filearray[$key] = $absPath.$entry;
						}
					} elseif ($recursive && $maxDirs>0 && @is_dir($absPath.$entry) && !preg_match('/^\./',$entry) && $entry!='CVS')	{
						$filearray = $this->getFilesInDir($absPath.$entry, true, $filearray, $maxDirs-1);
					}
				}
				$d->close();
			}
		}
		return $filearray;
	}


	/**
	 * Returns an array with the names of files in a specific path
	 *
	 * @param	string		Path to start to collect files. If it is a file itself it will be added to the list too
	 * @param	boolean		Go recursive into subfolder?
	 * @param	array		Array of file paths
	 * @return	array		Array of file paths
	 */
	function collectFiles($path, $recursive=false, $filearray=array())	{
		if ($path) {

			$pathname = tx_dam::file_absolutePath($path);

			if(is_file($pathname))	{
				$filearray[md5($pathname)] = $pathname;
			} else {
				$filearray = $this->getFilesInDir($path, $recursive, $filearray);
			}
		}
		return $filearray;
	}


	/**
	 * Returns an array with files collected from a list (array) of paths and files
	 *
	 * @param	string		Path/file list
	 * @param	boolean		Go recursive into subfolder?
	 * @return	array		Array of file paths
	 */
	function collectFilesByPathList($pathlist, $recursive)	{
		$filearray = array();

		foreach($pathlist as $path) {
			$filearray = $this->collectFiles($path, $recursive, $filearray);
		}

		return $filearray;
	}




	/*******************************************************
	 *
	 * Rendering the option form and info
	 *
	 *******************************************************/


	/**
	 * Returns the form of indexing options
	 *
	 * @return	string HTML content
	 */
	function getIndexingOptionsForm() {
			// walk through the index rules
		$this->initAvailableRules();
		$optContent='';
		foreach($this->rules as $classname => $setup)	{
			$options = $this->rules[$classname]['obj']->getOptionsForm();
			$optContent .= $this->formatOptionsFormRow ('[rules]['.$classname.']',
										$this->rules[$classname]['obj']->setup,
										$this->rules[$classname]['title'],	$this->rules[$classname]['desc'],
										$options);
		}
		return $optContent;
	}

// TODO move section to own class?

	/**
	 * Returns the info of indexing options that are activated
	 *
	 * @return	string HTML content
	 */
	function getIndexingOptionsInfo() {
			// walk through the index rules
		$optContent = '';
		$this->initEnabledRules();
		foreach($this->rules as $classname => $setup)	{

			if(!$this->rules[$classname]['shy']) {
				$optContent .= $this->formatOptionsFormRow ('info',
											array(),
											$this->rules[$classname]['title'],	'',
											$this->rules[$classname]['obj']->getOptionsInfo());
			}
		}
		return $optContent;
	}


	/**
	 * Returns a row of indexing options
	 *
	 * @param	string		$varname: ...
	 * @param	array		$setup: ...
	 * @param	string		$title: ...
	 * @param	string		$desc: ...
	 * @param	string		$options: ...
	 * @return	string HTML content
	 */
	function formatOptionsFormRow ($varname, $setup, $title, $desc='', $options='') {

		$out = '';
		$tdone='';

		$enabled = $setup['enabled'];

		if($setup['shy']) {
			$out .= '<input type="hidden" name="data'.$varname.'[enabled]" value="'.($enabled?'1':'0').'" />';
		} else {
			$out .= '<tr bgcolor="'.$GLOBALS['SOBE']->doc->bgColor5.'">';

			if($varname!='info') {

				$tdone='<td>&nbsp;</td>';
				$out .= '<td bgcolor="'.$GLOBALS['SOBE']->doc->bgColor4.'" width="1%"><input type="hidden" name="data'.$varname.'[enabled]" value="0" />'.
					'<input type="checkbox" name="data'.$varname.'[enabled]"'.($enabled?' checked="checked"':'').' value="1" />'.
					'</td>';
			}

			$out .= '<td bgcolor="'.$GLOBALS['SOBE']->doc->bgColor5.'"><strong>'.$title.'</strong></td>'.
				'</tr>';

			if($desc) {
				$out .= '
				<tr>'.$tdone.'<td bgcolor="'.$GLOBALS['SOBE']->doc->lgBgColor5.'">'.$desc.'</td></tr>';
			}

			if($options) {
				$out .= '
				<tr>'.$tdone.'<td bgcolor="'.$GLOBALS['SOBE']->doc->bgColor3.'" style="border-bottom:2px '.$GLOBALS['SOBE']->doc->bgColor5.' solid;">'.$options.'</td></tr>';
			}

			$out .= '<tr height="5" bgcolor="'.$GLOBALS['SOBE']->doc->bgColor.'">'.$tdone.'<td></td></tr>';
		}
		return $out;
	}




	/***************************************
	 *
	 *	 Collect some stats
	 *
	 ***************************************/


	/**
	 * Init statistics
	 *
	 * @return	void
	 */
	function statBegin() {
		$this->statmtime = t3lib_div::milliseconds();
		$this->stat['totalStartTime'] = $this->stat['totalStartTime'] ? $this->stat['totalStartTime'] : $this->statmtime;
		$this->stat['newIndexed'] = 0;
		$this->stat['reIndexed'] = 0;
	}

	/**
	 * Add item to statistics
	 *
	 * @param	array		$meta: Meta data
	 * @return	void
	 */
	function statMeta($meta) {
		$this->statmtime = t3lib_div::milliseconds()-$this->statmtime;
		$this->stat['totalTime'] = t3lib_div::milliseconds()-$this->stat['totalStartTime'];

		$this->stat['mediaTypeCount'][$meta['fields']['media_type']]++;
		$this->stat['mediaTypeTime'][$meta['fields']['media_type']] += $this->statmtime;
		if($meta['fields']['search_content']) {
			$this->stat['textExtract']++;
		}
		$this->stat['totalCount']++;
	}

	/**
	 * End statistics
	 *
	 * @return	void
	 */
	function statEnd() {
		$this->stat['totalTime'] = t3lib_div::milliseconds()-$this->stat['totalStartTime'];
	}

	/**
	 * Clear statistics
	 *
	 * @return	void
	 */
	function statClear() {
		$this->stat = array();
	}






	/************************************
	 *
	 * Logging
	 *
	 ************************************/


	/**
	 * Writes an entry in the logfile
	 *
	 * @param	integer		$indexRun: The time stamp of the index run
	 * @param	string		$type: man(ual), auto, cron
	 * @param	string		$message: short description
	 * @param	integer		$itemCount: number of elements indexed (is 1 for error entry)
	 * @param	integer		$error: flag. 0 = message, 1 = error (user problem), 2 = System Error (which should not happen)
	 * @return	integer		uid of the inserted log entry
	 */
	function writeLog($indexRun, $type, $message, $itemCount, $error) {

		$fields_values = array (
			'pid' => intval($this->pid),
			'cruser_id' => intval($GLOBALS['BE_USER']->user['uid']),
			'tstamp' => $GLOBALS['EXEC_TIME'],
			'crdate' => intval($indexRun),
			'error' => intval($error),
			'type' => substr($type, 0, 4),
			'message' => $message,
			'item_count' => intval($itemCount),
		);

		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_dam_log_index', $fields_values);
		return $GLOBALS['TYPO3_DB']->sql_insert_id();
	}

	/**
	 * Writes an entry in the logfile
	 *
	 * @param	string		$message: short description
	 * @param	integer		$itemCount: number of elements indexed (is 1 for error entry)
	 * @param	integer		$error: flag. 0 = message, 1 = error (user problem), 2 = System Error (which should not happen)
	 * @return	integer		uid of the inserted log entry
	 */
	function log($message, $itemCount, $error) {
		if (!$this->dryRun) {
		return $this->writeLog($this->indexRun, $this->indexRunType, $message, $itemCount, $error);
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_indexing.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_indexing.php']);
}


 ?>