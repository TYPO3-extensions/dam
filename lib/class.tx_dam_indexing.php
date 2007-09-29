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
 * indexing lib
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
 *   93: class tx_dam_index 
 *  164:     function init()	
 *  180:     function setDryRun($dryRun=TRUE)	
 *  190:     function enableReindexing($doReindexing=TRUE)	
 *  199:     function clearCollectedMeta()	
 *  211:     function indexFiles($files, $pid=NULL)	
 *  248:     function indexFile($absFile, $crdate=0, $pid=NULL, $metaCallbackFunc=NULL)	
 *
 *              SECTION: index rules
 *  348:     function mergeRuleConf($ruleOpt) 
 *  370:     function initEnabledRules() 
 *  398:     function initAvailableRules() 
 *  434:     function rulesCallback ($type, $meta, $absFile) 
 *
 *              SECTION: file meta data
 *  476:     function getFileMetaInfo($absFile, $meta)	
 *  561:     function getCleanTitle ($title) 
 *  576:     function getFileNodeInfo($absFile)	
 *  603:     function getFileMimeType($absFile)	
 *  678:     function getFileTextExcerpt($absFile,$file_type, $limit=64000) 
 *  712:     function getImageDimensions($absFile,$file_type='') 
 *
 *              SECTION: files folders paths
 *  742:     function getFilesInDir($path, $recursive=FALSE, $extended=FALSE, $filearray=array())	
 *
 *              SECTION: Rendering the option form and info
 *  790:     function getIndexingOptionsForm() 
 *  809:     function getIndexingOptionsInfo() 
 *  836:     function formatOptionsFormRow ($varname,$setup,$title,$desc='',$options='') 
 *
 *              SECTION: collect some stats
 *  884:     function statBegin() 
 *  894:     function statEnd($meta) 
 *  911:     function statClear() 
 *
 * TOTAL FUNCTIONS: 23
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */





require_once(PATH_t3lib.'class.t3lib_exec.php');

require_once(PATH_txdam.'lib/class.tx_dam_types.php');
require_once(PATH_txdam.'lib/class.tx_dam_db.php');

/**
 * Provide indexing functions
 * 
 * @author	René Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_dam
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
	 * Should the indexed meta data collected into $this->meta?
	 */
	var $collectMeta = FALSE;
	
	/**
	 * used to collect some statistics
	 */
	var $meta = array();
	
	
	
	/**
	 * used to collect some statistics
	 */
	var $stat = array();
	var $stattotaltime;
	var $statmtime;


	/**
	 * db object
	 */
	var $damDB;



	/**
	 * Initializes.
	 * 
	 * @return	[type]		...
	 */
	function init()	{
		$this->db = t3lib_div::makeInstance('tx_dam_db');
		
		$this->setup = array();
		$this->dataPreset = array();
		$this->dataPostset = array();
		$this->stat = array();
		$this->clearCollectedMeta();
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
	 * Clears all collected meta data
	 * 
	 * @return	void		
	 */
	function clearCollectedMeta()	{
		$this->meta = array();
	}

	/**
	 * Index files passed as array in format from getFilesInDir()
	 * 
	 * @param	array		files info array
	 * @param	boolean		do reindexing?
	 * @return	void		
	 * @see getFilesInDir()
	 */
	function indexFiles($files, $pid=NULL)	{
		if (is_array($files) && count($files)) {
			
			$this->statBegin();
				
			$pid = is_null($pid) ? $this->pid : $pid;
			
			$this->initEnabledRules();
#debug($this->ruleConf,'$this->ruleConf');
#debug($this->rules,'$this->rules');
			foreach($this->rules as $classname => $setup)	{
				$this->rules[$classname]['obj']->preIndexing();
			}

			foreach($files as $val) {
#debug($val);
				$absFile = is_array($val) ? $val['absFile'] : $val;
#debug($absFile);
				$meta = $this->indexFile($absFile, time(), $pid);
#debug($meta);
			}

			foreach($this->rules as $classname => $setup)	{
				$this->rules[$classname]['obj']->postIndexing();
			}
			
			$this->statEnd($meta);
		}

	}




	/**
	 * @param	[type]		$absFile: ...
	 * @param	[type]		$pid: ...
	 * @param	[type]		$crdate: ...
	 * @param	[type]		$this->doReindexing: ...
	 * @param	[type]		$metaCallbackFunc: ...
	 * @return	[type]		...
	 */
	function indexFile($absFile, $crdate=0, $pid=NULL, $metaCallbackFunc=NULL)	{
		global $BE_USER;

		$pid = is_null($pid) ? $this->pid : $pid;
		
		if (is_array($meta = $this->getFileNodeInfo($absFile))) {
#debug($meta,'$meta');			
		
			list($status,$uid)=$this->db->checkFileIsIndexed ($meta['fields']['file_name'],$meta['fields']['file_path'],$meta['fields']['file_mtime']);
#debug(array($status,$uid));
			if (intval($uid) && $this->doReindexing) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_dam', 'uid='.intval($uid));
				$meta['row'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			} else {
				$uid = 'NEW';
			}


			if ($status<0 OR $this->doReindexing) {

				$mimeType=array();
				$mimeType['fields'] = $this->getFileMimeType($absFile);

				$meta = t3lib_div::array_merge_recursive_overrule(array('fields'=>$this->getDefaultRecord()), $meta);
				$meta = t3lib_div::array_merge_recursive_overrule(array('fields'=>$this->dataPreset), $meta);
				$meta = t3lib_div::array_merge_recursive_overrule($meta, $mimeType);

				$meta['textExtract'] = $this->getFileTextExcerpt($absFile, $meta['fields']['file_type']);
				$meta['fields']['search_content'] = $meta['textExtract'];

				$meta = $this->getFileMetaInfo($absFile, $meta);

				$meta['fields']['abstract'] = $meta['fields']['abstract']?$meta['fields']['abstract']:trim($meta['fields']['search_content']);

				$meta['fields']['file_dl_name'] = $meta['fields']['file_dl_name']?$meta['fields']['file_dl_name']:$meta['fields']['file_name'];

				$meta['fields']['uid'] = $uid;
				$meta['fields']['pid'] = $pid;
				$meta['fields']['crdate'] = $crdate?$crdate:time();
				$meta['fields']['tstamp'] = time();
				$meta['fields']['cruser_id'] = $BE_USER->id;

				$meta['fields']['date_cr'] = $meta['fields']['date_cr']?$meta['fields']['date_cr']:time();
				$meta['fields']['date_mod'] = $meta['fields']['date_mod']?$meta['fields']['date_mod']:$meta['fields']['date_cr'];

#debug($meta, 'meta', __LINE__, __FILE__);
#TODO category handling - merging?

				$meta['fields'] = array_merge($meta['fields'],$this->dataPostset);

				
#TODO				$meta['fields']['meta'] = serialize($meta['meta']);
#debug($meta['fields']);
				
				$meta = $this->rulesCallback('process', $meta, $absFile);
				if ($metaCallbackFunc) {
					$meta = call_user_func ($metaCallbackFunc, 'process', $meta, $absFile, $this);
				}

				if (!$this->dryRun) {
					$meta['fields']['uid'] = $this->db->insertMetaRecord($meta['fields'], $meta['fields']['uid']);
				}
				
				$meta = $this->rulesCallback('post', $meta, $absFile);
				if ($metaCallbackFunc) {
					$meta = call_user_func ($metaCallbackFunc, 'post', $meta, $absFile, $this);
				}
				
				if (!$this->dryRun) {
					$this->db->updateBrowseTypes($meta['fields']);
				}
				
				if ($this->collectMeta) {
					$this->meta[$meta['fields']['uid']] = $meta;
				}
				
				$this->statMeta($meta);
				
				return $meta['fields'];
			}

		}
		return FALSE;
	}




	/***************************************
	 *
	 *	 index rules
	 *
	 ***************************************/
	 
	/**
	 * Merge options from rule forms ($data['rules'])
	 * 
	 * @param	[type]		$ruleOpt: ...
	 * @return	void		
	 */
	function mergeRuleConf($ruleOpt) {

		if(is_array($ruleOpt)) {
				// walk through the index rules
			$this->initAvailableRules();
			foreach($this->rules as $classname => $setup)	{

				if (is_array($ruleOpt[$classname])) {
						// this is set in the class itself
					unset($ruleOpt[$classname]['shy']);
					$this->rules[$classname]['obj']->setup = t3lib_div::array_merge_recursive_overrule($this->rules[$classname]['obj']->setup, $ruleOpt[$classname]);
				}
				$this->rules[$classname]['obj']->processOptionsForm();
				$this->ruleConf[$classname] = $this->rules[$classname]['obj']->setup;
			}
		#debug($this->ruleConf);
		}
	}

	/**
	 * [Describe function...]
	 * 
	 * @return	[type]		...
	 */
	function initEnabledRules() {
		global $TYPO3_CONF_VARS, $SOBE;
		
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
	 * [Describe function...]
	 * 
	 * @return	[type]		...
	 */
	function initAvailableRules() {
		global $TYPO3_CONF_VARS, $SOBE;
	
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
					$this->rules[$classname]['obj']->setup = array_merge($this->rules[$classname]['obj']->setup, $this->ruleConf[$classname]);
				
						// visible
					$this->rules[$classname]['shy'] = $this->rules[$classname]['obj']->setup['shy'];
					$this->rules[$classname]['title'] = $this->rules[$classname]['obj']->getTitle();
					$this->rules[$classname]['desc'] = $this->rules[$classname]['obj']->getDescription();
				}
			}
		}		
	}	



	/**
	 * [Describe function...]
	 * 
	 * @param	[type]		$type: ...
	 * @param	[type]		$meta: ...
	 * @param	[type]		$absFile: ...
	 * @return	[type]		...
	 */
	function rulesCallback ($type, $meta, $absFile) {
		if (is_array($this->rules)) {
			foreach($this->rules as $rule)	{
				switch ($type) {
					
					case 'process':
					default:
						if(is_callable(array($rule['obj'], 'processMeta'))) {
							$meta = $rule['obj']->processMeta($meta, $absFile, $this);
						}
					break;
					
					case 'post':
						if(is_callable(array($rule['obj'], 'postProcessMeta'))) {
							$meta = $rule['obj']->postProcessMeta($meta, $absFile, $this);
						}
					break;
				}
			}
		}
		return $meta;
	}



	/***************************************
	 *
	 *	 file meta data
	 *
	 ***************************************/



	
	/**
	 * get meta information from a file using the metaExtract service
	 * 
	 * @param	[type]		$absFile: ...
	 * @param	[type]		$meta: ...
	 * @return	array		file meta information
	 * @params string 	file with absolut path
	 * @params array 	file meta information which should be extended
	 */
	function getFileMetaInfo($absFile, $meta)	{
		
		$TX_DAM = $GLOBALS['T3_VAR']['ext']['dam'];

		if (is_file($absFile) && is_readable($absFile)) {
			
			$fileType = $meta['fields']['file_type'];

#			$meta[]['meta'] = is_array($meta['meta'])?$meta['meta']:array();
						
				// get media type from file type
			$meta['fields']['media_type'] = $TX_DAM['file2mediaCode'][$fileType];
				//  or from mime type
			$meta['fields']['media_type'] = $meta['fields']['media_type'] ? $meta['fields']['media_type'] : $TX_DAM['media2code'][$meta['fields']['file_mime_type']];


#TODO when should iptc?
# services image:* a good idea?

				// find a service for that file type
			if (!is_object($serviceObj = t3lib_div::makeInstanceService('metaExtract',$fileType))) {
					// find a global service for that media type
				$serviceObj = t3lib_div::makeInstanceService('metaExtract',$meta['fields']['media_type'].':*');
			}
			if (is_object($serviceObj)) {
				$serviceObj->setInputFile($absFile,$fileType);
				if ($serviceObj->process('','',array('meta'=>$meta))>0 AND (is_array($svmeta = $serviceObj->getOutput()))) {
						$meta = t3lib_div::array_merge_recursive_overrule($meta,$svmeta);
				}
				$serviceObj->__destruct();
				unset($serviceObj);
			}


				// detect language
			if ($meta['fields']['search_content'] AND is_object($serviceObj = t3lib_div::makeInstanceService('textLang'))) {
				$serviceObj->process($meta['fields']['search_content'],'',$conf);
				$output = $serviceObj->getOutput();
				$serviceObj->__destruct();
				unset($serviceObj);
				
				$meta['fields']['language'] = $output ? $output : '';
			}


				// image
			if ($meta['fields']['media_type']==2) {

#TODO image size detection by IM?

					//image size
				$imgsize = $this->getImageDimensions ($absFile);
				$meta = t3lib_div::array_merge_recursive_overrule($meta, $imgsize);

					// read exif data
				if ($serviceObj = t3lib_div::makeInstanceService('metaExtract','image:exif')) {
					$serviceObj->setInputFile($absFile,$fileType);
					if ($serviceObj->process('','',array('meta'=>$meta))>0 AND (is_array($svmeta = $serviceObj->getOutput()))) {
#debug($svmeta);
						$meta = t3lib_div::array_merge_recursive_overrule($meta, $svmeta);
						#$meta['meta'] = array_merge($meta['meta'],array('exif'=>$svmeta['exif']));
#debug($meta);
					}
					$serviceObj->__destruct();
					unset($serviceObj);
				}
			}


			// If no title then the file-name is set as title. This will raise the hits considerably if the search matches the document name.
			if ($meta['fields']['title']=='')	{
				$meta['fields']['title']= tx_dam_div::makeTitleFromFilename ($meta['fields']['file_name']);
			}
			
		}
		return $meta;
	}



	/**
	 * get basic file meta info
	 * 
	 * @param	[type]		$absFile: ...
	 * @return	array		file information
	 * @params string 	file with absolut path
	 */
	function getFileNodeInfo($absFile)	{

#TODO should that be an option?		$pfile = realpath($absFile);
		$pfile = $absFile;
		$meta=false;
		if (is_file($pfile) && is_readable($pfile)) {
			$meta = array();

			$meta['fields']['file_name'] = basename($absFile);
			$meta['fields']['file_dl_name'] = $meta['fields']['file_name'];
			$meta['fields']['file_path'] = tx_dam_div::getRelPath (dirname($pfile).'/');
			$meta['fields']['file_mtime'] = filemtime($pfile);
			$meta['fields']['file_ctime'] = filectime($pfile);
			$meta['fields']['file_inode'] = fileinode($pfile);
			$meta['fields']['file_size'] = filesize($pfile);
		}

		return $meta;
	}


	/**
	 * get the mime type of a file with full path
	 * 
	 * @param	[type]		$absFile: ...
	 * @return	array		file information
	 * @params string 	file with absolut path
	 */
	function getFileMimeType($absFile)	{
		
		$TX_DAM = $GLOBALS['T3_VAR']['ext']['dam'];

		$mimeType = array();
		$mimeType['fulltype'] = '';
		$mimeType['file_mime_type'] = '';
		$mimeType['file_mime_subtype'] = '';
		$mimeType['file_type'] = '';

		$finfo = pathinfo($absFile);
		$mimeType['file_type'] = strtolower($finfo['extension']);

			// try first to get the mime type by extension with own array
			// I made the experience that it is a bit safer than with 'file'
		if (isset($TX_DAM['file2mime'][$mimeType['file_type']])) {

			$mt=$TX_DAM['file2mime'][$mimeType['file_type']];

			// next try
		} elseif(function_exists('mime_content_type')) {
				// available in PHP 4.3.0
			$mt = mime_content_type($absFile);

			// last chance
		} else {
			$osType = t3lib_exec::_getOS();
			if ($osType!='WIN') {

#			'opt' => ' -i -M '.t3lib_extMgm::extPath('dam')."bin/magic.mime ",
#			'opt' => ' -i -M /usr/share/misc/magic.mime;###PATH###bin/magic.mime ',
#			'opt' => ' -i -M ###PATH###bin/magic.mime ',

				$mimeTypeTxt = exec (t3lib_exec::getCommand('file').' --mime "'.$absFile.'"', $dummy, $ret);
				if (!$ret AND strstr ($mimeTypeTxt,basename($absFile).':')) {
					$a = explode (':', $mimeTypeTxt);
					$a = explode (';', trim($a[1]));
					//a[1]: text/plain, English; charset=iso-8859-1
					$a = explode (',', trim($a[0]));
					$a = explode (' ', trim($a[0]));
					$mt = trim($a[0]);
				}
			}
		}

		$mtarr = explode ('/', $mt);
		if (is_array($mtarr) && count($mtarr)==2) {

			$mimeType['fulltype'] = $mt;
			$mimeType['file_mime_type'] = $mtarr[0];
			$mimeType['file_mime_subtype'] = $mtarr[1];

			if ($mimeType['file_type']=='') {
				$mimeType['file_type']=array_search($mimeType['fulltype'],$TX_DAM['file2mime'],true);
			}
		}

		unset($mimeType['fulltype']);

		return $mimeType;
	}




	/**
	 * get an excerpt from a text file using the textExtract service
	 * 
	 * @param	[type]		$absFile: ...
	 * @param	[type]		$file_type: ...
	 * @param	[type]		$limit: ...
	 * @return	string		text excerpt of false
	 * @params string 	file with absolut path
	 * @params string 	file type like 'jpg'
	 */
	function getFileTextExcerpt($absFile,$file_type, $limit=64000) {
		$output = FALSE;

		if (is_object($serviceObj = t3lib_div::makeInstanceService('textExtract',$file_type))) {

			if ($limit) {
				$conf = array('limitOutput'=>$limit+3000);
			}
			$serviceObj->setInputFile($absFile,$file_type);
			$serviceObj->process('','',$conf);
			$output = trim($serviceObj->getOutput());

				// double linebreak is enough
			while (strpos($output,"\n\n\n")) {
				$output = str_replace("\n\n\n","\n\n", $output);
			}
			if ($limit) {
				$output=substr($output,0,$limit);
			}
			unset ($serviceObj);
		}
		return $output;
	}


	/**
	 * get the image size of an file in pixels
	 * 
	 * @param	[type]		$absFile: ...
	 * @param	[type]		$file_type: ...
	 * @return	array		
	 * @params string 	file with absolut path
	 * @params string 	file type like 'jpg'
	 */
	function getImageDimensions($absFile,$file_type='') {
		$meta = array();

		if(function_exists('getimagesize')) {
			$size = GetImageSize ($absFile);
			$meta['fields']['hpixels'] = $size[0];
			$meta['fields']['vpixels'] = $size[1];
		}

		return $meta;
	}




	/**
	 * Gets default record. Maybe not used anymore. FE-editor?
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


	/***************************************
	 *
	 *	 files folders paths
	 *
	 ***************************************/


	/**
	 * Returns an array with the names of files in a specific path
	 * 
	 * @param	[type]		$path: ...
	 * @param	[type]		$recursive: ...
	 * @param	[type]		$filearray: ...
	 * @param	[type]		$filearray: ...
	 * @return	array		file information
	 * @params string 	path
	 * @params boolean 	walk through subfolders?
	 */
	function getFilesInDir($path, $recursive=FALSE, $extended=FALSE, $filearray=array(), $maxDirs=999)	{
		if ($path)	{
			$path = preg_replace('#/$#','',$path);
			$absPath = tx_dam_div::getAbsPath($path);
			$d = @dir($absPath);
			if (is_object($d))	{
				while($entry=$d->read()) {
					if (@is_file($absPath.'/'.$entry))	{
						if (!preg_match('/^\./',$entry) && !preg_match('/~$/',$entry)) {
							$fI = pathinfo($entry);
							$key = md5($absPath.'/'.$entry);
							if ($extended) {
							    $filearray[$key]=$fI;
								$filearray[$key]['dirname'] = basename($path);
								$filearray[$key]['path'] = $path.'/';
								$filearray[$key]['absPath'] = $absPath.'/';
								$filearray[$key]['absFile'] = $absPath.'/'.$fI['basename'];
								$filearray[$key]['mtime'] = filemtime($absPath.'/'.$entry);
							} else {
								$filearray[$key] = $absPath.'/'.$fI['basename'];
							}
						}
					} elseif ($recursive && $maxDirs>0 && @is_dir($absPath.'/'.$entry) && !preg_match('/^\./',$entry) && $entry!='CVS')	{
						$filearray = $this->getFilesInDir($path.'/'.$entry, true, $extended, $filearray, $maxDirs-1);
					}
				}
				$d->close();
			} else return FALSE;
		}
		return $filearray;
	}
	
	

	/*******************************************************
	 *
	 * Rendering the option form and info
	 *
	 *******************************************************/
	 

	/**
	 * [Describe function...]
	 * 
	 * @return	[type]		...
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

	/**
	 * [Describe function...]
	 * 
	 * @return	[type]		...
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
	 * Returns the form of indexing options
	 * 
	 * @param	array		preset record data
	 * @param	array		fields which are preset as fixed fields
	 * @param	[type]		$title: ...
	 * @param	[type]		$desc: ...
	 * @param	[type]		$options: ...
	 * @return	string		
	 * @params  string
	 */
	function formatOptionsFormRow ($varname,$setup,$title,$desc='',$options='') {
		global $SOBE;
		
		$out = '';
		$tdone='';

		$enabled = $setup['enabled'];
		
		if($setup['shy']) {
			$out .= '<input type="hidden" name="data'.$varname.'[enabled]" value="'.($enabled?'1':'0').'" />';
		} else {
			$out .= '<tr bgcolor="'.$SOBE->doc->bgColor5.'">';
	
			if($varname!='info') {
	
				$tdone='<td>&nbsp;</td>';
				$out .= '<td bgcolor="'.$SOBE->doc->bgColor4.'" width="1%"><input type="hidden" name="data'.$varname.'[enabled]" value="0" />'.
					'<input type="checkbox" name="data'.$varname.'[enabled]"'.($enabled?' checked="checked"':'').' value="1" />'.
					'</td>';
			}
	
			$out .= '<td bgcolor="'.$SOBE->doc->bgColor5.'"><strong>'.$title.'</strong></td>'.
				'</tr>';
	
			if($desc) {
				$out .= '
				<tr>'.$tdone.'<td bgcolor="'.$SOBE->doc->lgBgColor5.'">'.$desc.'</td></tr>';
			}
	
			if($options) {
				$out .= '
				<tr>'.$tdone.'<td bgcolor="'.$SOBE->doc->bgColor3.'" style="border-bottom:2px '.$SOBE->doc->bgColor5.' solid;">'.$options.'</td></tr>';
			}
	
			$out .= '<tr height="5" bgcolor="'.$SOBE->doc->bgColor.'">'.$tdone.'<td></td></tr>';
		}
		return $out;
	}
	 

	/***************************************
	 *
	 *	 collect some stats
	 *
	 ***************************************/


	/**
	 * Init statistics
	 *
	 * @return	void
	 */
	function statBegin() {
		$this->stattotaltime = $this->statmtime = t3lib_div::milliseconds();
	}

	/**
	 * [Describe function...]
	 * 
	 * @param	array		$meta: Meta data
	 * @return	void
	 */
	function statMeta($meta) {
		$this->statmtime = t3lib_div::milliseconds()-$this->statmtime;

		$this->stat['mediaTypeCount'][$meta['fields']['media_type']]++;
		$this->stat['mediaTypeTime'][$meta['fields']['media_type']] += $this->statmtime;
		if($meta['fields']['search_content']) {
			$this->stat['textExtract']++;
		}
		$this->stat['totalCount']++;
	}
	
	/**
	 * [Describe function...]
	 * 
	 * @return	void
	 */
	function statEnd() {
		$this->stat['totalTime'] = t3lib_div::milliseconds()-$this->stattotaltime;
	}
	
	/**
	 * [Describe function...]
	 * 
	 * @return	void
	 */
	function statClear() {
		$this->stat=array();
	}
	
	

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_indexing.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_indexing.php']);
}


 ?>