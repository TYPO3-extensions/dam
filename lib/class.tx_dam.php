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
 *  125: class tx_dam
 *
 *              SECTION: File related function
 *  144:     function file_isIndexed($fileInfo)
 *  165:     function file_compileInfo ($filename, $ignoreExistence=false)
 *  206:     function file_getType ($filename)
 *  228:     function file_calcHash ($fileInfo)
 *  261:     function file_normalizePath ($filename)
 *  276:     function file_absolutePath ($fileInfo)
 *  297:     function file_relativeSitePath ($fileInfo)
 *
 *              SECTION: Path related function
 *  332:     function path_makeRelative ($path, $mountpath=NULL)
 *  351:     function path_makeAbsolute ($path, $mountpath=NULL)
 *  371:     function path_makeClean ($path)
 *  405:     function path_compileInfo ($path)
 *
 *              SECTION: Meta data related function
 *  485:     function meta_getDataForFile($fileInfo, $fields='', $ignoreExistence=false)
 *  517:     function meta_getDataByUid ($uid, $fields='')
 *  541:     function meta_getDataByHash ($hash, $fields='')
 *  565:     function meta_findDataForFile($fileInfo, $hash='', $fields='')
 *  647:     function meta_getDataVariant ($row, $language)
 *  692:     function meta_updateStatus ($meta, $markMissingDeleted=NULL)
 *
 *              SECTION: Media objects functions
 *  730:     function media_getForFile($fileInfo, $hash=false)
 *  747:     function media_getByUid ($uid)
 *  765:     function media_getByHash ($hash)
 *
 *              SECTION: Indexing functions
 *  800:     function index_check ($fileInfo, $hash='')
 *  846:     function index_reconnect($fileInfo, $hash='')
 *  876:     function index_autoProcess($filename, $reindex=false)
 *  929:     function index_process ($filename, $setup=NULL, $callbackFunc=NULL, $metaCallbackFunc=NULL, $filePreprocessingCallbackFunc=NULL)
 *
 *              SECTION: Notify the DAM about file or folder changes
 *  968:     function notify_fileChanged ($filename)
 *  986:     function notify_fileMoved ($src, $dest)
 * 1012:     function notify_fileDeleted ($filename)
 *
 *              SECTION: Converter for names and codes of data formats
 * 1039:     function convert_mediaType($type)
 *
 *              SECTION: Icon functions
 * 1070:     function icon_getFileType ($mimeType, $absolutePath=false)
 * 1123:     function icon_getFolder($pathInfo, $absolutePath=false)
 * 1166:     function icon_getFileTypeImgTag($infoArr, $addAttrib='')
 *
 *              SECTION: Register functions like selection classes, indexing rules, viewer, editors
 * 1200:     function register_dbTrigger ($idName, $class, $position='')
 * 1212:     function register_selection ($idName, $class, $position='')
 * 1225:     function register_indexingRule ($idName, $class, $position='')
 * 1239:     function register_fileType ($fileExtension, $mimeType, $mediaType='')
 * 1259:     function register_previewer ($idName, $class, $position='')
 * 1273:     function register_editor ($idName, $class, $position='')
 * 1287:     function register_action ($idName, $class, $position='')
 * 1298:     function register_fileIconPath ($path)
 *
 *              SECTION: Configuration
 * 1328:     function config_getValue($configPath='', $getProperties=false)
 * 1360:     function config_setValue($configPath='', $value='')
 * 1401:     function config_init($force=false)
 *
 *              SECTION: Internal
 * 1449:     function _getTSconfig ($pid=0)
 * 1488:     function _addItem($idName, $value, &$items, $position='')
 *
 * TOTAL FUNCTIONS: 44
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */



require_once(PATH_txdam.'lib/tx_dam_types.php');
require_once(PATH_txdam.'lib/class.tx_dam_db.php');


/**
 * DAM API functions
 *
 * This is the official API to access DAM functions.
 * If possible no other functions shall be used.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Lib
 */
class tx_dam {



	/***************************************
	 *
	 *	 File related function
	 *
	 ***************************************/


	/**
	 * Checks if the file is already indexed.
	 * Returns the UID of the meta data record if the file is indexed already or false if the file is not indexed yet.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @return	mixed		UID of the meta data record or false.
	 * @see tx_dam::file_compileInfo()
	 */
	function file_isIndexed($fileInfo) {
		$uid = false;

		if(is_array($row = tx_dam::meta_getDataForFile ($fileInfo, 'uid'))) {
			$uid = $row['uid'];
		}

		return $uid;
	}


	/**
	 * Collects and returns an array with physical informations about the file.
	 * This means the file must be existent.
	 * If $ignoreExistence is set the path will be split no matter if the file exists.
	 * File node info will be compiled only if the file exists - of course.
	 *
	 * @param	string		$filename The file name with path
	 * @param	boolean		$ignoreExistence The existence of the file will not be checked and only the file path will be splitted.
	 * @return	array		A file info array with all physical data about the file
	 */
	function file_compileInfo ($filename, $ignoreExistence=false) {
		$fileInfo = false;

		$filename = tx_dam::file_absolutePath ($filename);

		if ($ignoreExistence OR @is_file($filename) ) {
			$fileInfo = array();
			$fileInfo['__type'] = 'file';
			$fileInfo['__exists'] = @is_file($filename);
			$fileInfo['file_name'] = basename($filename);
			$fileInfo['file_title'] = $fileInfo['file_name'];
			$fileInfo['file_path_absolute'] = dirname($filename).'/';
			$fileInfo['file_path'] = tx_dam::path_makeRelative ($fileInfo['file_path_absolute']);
			$fileInfo['file_path_relative'] = $fileInfo['file_path'];
			if ( !$ignoreExistence  AND $fileInfo['__exists'] ) {
				$fileInfo['file_mtime'] = @filemtime($filename);
				$fileInfo['file_ctime'] = @filectime($filename);
				$fileInfo['file_inode'] = @fileinode($filename);
				$fileInfo['file_size'] = @filesize($filename);
				$fileInfo['file_owner'] = @fileowner($filename);
				$fileInfo['file_perms'] = @fileperms($filename);
				$fileInfo['file_writable'] = @is_writable($filename);
				$fileInfo['file_readable'] = @is_readable($filename);
			}
		}
		return $fileInfo;
	}


	/**
	 * Returns an array which describes the type of a file.
	 *
	 * example:
	 * $mimeType = array();
	 * $mimeType['file_mime_type'] = 'audio';
	 * $mimeType['file_mime_subtype'] = 'x-mpeg';
	 * $mimeType['file_type'] = 'mp3';
	 *
	 * @param	string		$filename The file name with path
	 * @return	array		Describes the type of a file
	 */
	function file_getType ($filename) {
		$mimeType = array();

		if($uid = tx_dam::file_isIndexed($filename)) {
			$mimeType = tx_dam::meta_getDataByUid($uid, 'file_mime_type,file_mime_subtype,file_type,media_type');
		} else {
			require_once(PATH_txdam.'lib/class.tx_dam_indexing.php');
			$mimeType = tx_dam_indexing::getFileMimeType($filename);
		}

		return $mimeType;
	}


	/**
	 * Calculates a hash value from a file.
	 * The hash is used to identify file changes or a file itself.
	 * Remember that a file can occur multiple times in the filesystem, therefore you can detect only that it is the same file. But you have to take the location (path) into account to identify the right file.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @return	string		hash value
	 */
	function file_calcHash ($fileInfo) {
		$hash = false;

		$filename = tx_dam::file_absolutePath($fileInfo);
		if (function_exists('md5_file')) {
			$hash = @md5_file($filename);
		} else {
			if(filesize ($filename) > 0xfffff ) {	// 1MB
				$cmd = t3lib_exec::getCommand('md5sum');
				$output = array();
				$retval='';
				exec($cmd.' -b "'.escapeshellcmd($filename).'"', $output, $retval);
				$output = explode(' ',$output[0]);
				$match = array();
				if (preg_match('#[0-9a-f]{32}#', $output[0], $match)) {
					$hash = $match[0];
				}
			} else {
				$file_string = t3lib_div::getUrl($filename);
				$hash = md5($file_string);
			}
		}

		return $hash;
	}


	/**
	 * Convert a file path to the format stored in the meta data which is a relative path if possible.
	 *
	 * @param	string		$filename The file name with path
	 * @return	string		Normalized path to file
	 */
	function file_normalizePath ($filename) {
		$file_name = basename($filename);
		$file_path = tx_dam::path_makeRelative (dirname($filename).'/');

		return $file_path.$file_name;
	}


	/**
	 * Convert/returns a file path to a absolute path if possible.
	 * This is for files managed by the DAM only. Other files may fail.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @return	string		Absolute path to file
	 */
	function file_absolutePath ($fileInfo) {
		if (is_array($fileInfo)) {
			$file_name = $fileInfo['file_name'];
			$file_path = $fileInfo['file_path_absolute'] ? $fileInfo['file_path_absolute'] : tx_dam::path_makeAbsolute ($fileInfo['file_path']);
		} else {
			$path_parts = pathinfo($fileInfo);
			$file_path = tx_dam::path_makeAbsolute($path_parts['dirname']);
			$file_name = $path_parts['basename'];
		}

		return $file_path.$file_name;
	}


	/**
	 * Convert a file path to a relative path to PATH_site or getIndpEnv('TYPO3_SITE_URL').
	 * This is for files managed by the DAM only. Other files may fail.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @return	string		Relative path to file
	 */
	function file_relativeSitePath ($fileInfo) {

		if (is_array($fileInfo)) {
			$file_name = $fileInfo['file_name'];
			$file_path = $fileInfo['file_path_absolute'] ? $fileInfo['file_path_absolute'] : tx_dam::path_makeAbsolute ($fileInfo['file_path']);
		} else {
			$file_name = basename($fileInfo);
			$file_path = tx_dam::path_makeAbsolute (dirname($fileInfo).'/');
		}

			// for now path_makeRelative() do what we want but that may change
		$file_path = tx_dam::path_makeRelative ($file_path, PATH_site);

		return $file_path.$file_name;
	}



	/***************************************
	 *
	 *	 Path related function
	 *
	 ***************************************/


	/**
	 * Convert a path to a relative path if possible.
	 * The result is normally a relative path to PATH_site (but don't have to).
	 * It might be possible that back paths '../' will be supported in the future.
	 *
	 * @param	string		$path Path to convert
	 * @param	string		$mountpath Path which will be used as base path. Otherwise PATH_site is used.
	 * @return	string		Relative path
	 */
	function path_makeRelative ($path, $mountpath=NULL) {

		$path = tx_dam::path_makeAbsolute ($path, $mountpath);

		$mountpath = is_null($mountpath) ? PATH_site : tx_dam::path_makeClean ($mountpath);

			// remove the site path from the beginning to make the path relative
			// all other's stay absolute
		return preg_replace('#^'.preg_quote($mountpath).'#','',$path);
	}


	/**
	 * Convert a path to an absolute path
	 *
	 * @param	string		$path Path to convert
	 * @param	string		$mountpath Path which will be used as base path. Otherwise PATH_site is used.
	 * @return	string		Absolute path
	 */
	function path_makeAbsolute ($path, $mountpath=NULL) {

		$path = tx_dam::path_makeClean ($path);

		if(t3lib_div::isAbsPath($path)) {
			return $path;
		}
		$mountpath = is_null($mountpath) ? PATH_site : tx_dam::path_makeClean ($mountpath);
		return $mountpath.$path;
	}


	/**
	 * Cleans a path
	 * - resolve back paths '../'
	 * - append '/' to the path if missing
	 *
	 * @param	string		$path Path to clean
	 * @return	string		Cleaned path
	 */
	function path_makeClean ($path) {
		if ($path) {
			$path = str_replace('/./', '/', $path);
			$path = t3lib_div::resolveBackPath($path);
			$path = preg_replace('#[\/\. ]*$#', '', $path).'/';
			$path = str_replace('//', '/', $path);
		}
		return $path;
	}


	/**
	 * Collects and returns an array with info's about the given path/folder.
	 * Returns false if the path is not a folder.
	 *
	 * Example:
	 * __type => dir
	 * dir_path => /var/www/dam/fileadmin//test/
	 * dir_path_from_mount => test/
	 * dir_path_relative => fileadmin/test/
	 * dir_name => test
	 * dir_title => test
	 * dir_size => 115
	 * dir_tstamp => 1132751825
	 * dir_writable => 1
	 * dir_readable => 1
	 * dir_owner => 1000
	 * dir_perms => 16895
	 * mount_id => 875349e03c95ae6bc79dc22c0b7c2f7c
	 * mount_name => fileadmin/
	 * mount_path => /var/www/dam/fileadmin/
	 * mount_type =>
	 * web_nonweb => web
	 *
	 * @param	string		$path Path to a folder (not file)
	 * @return	array		Info array
	 */
	function path_compileInfo ($path) {
		global $FILEMOUNTS, $TYPO3_CONF_VARS;

		$pathInfo = false;

		require_once (PATH_t3lib.'class.t3lib_basicfilefunc.php');

		$basicFF = t3lib_div::makeInstance('t3lib_basicFileFunctions');
		$basicFF->init($FILEMOUNTS, $TYPO3_CONF_VARS['BE']['fileExtensions']);

		$path = tx_dam::path_makeAbsolute($path);

		$path = $basicFF->is_directory($path);
		$path = $path ? $path.'/' : '';

		if($path) {

			$pathInfo = array();
			$pathInfo['__type'] = 'dir';
			$pathInfo['__exists'] = @is_dir($path);
			$pathInfo['__protected'] = @is_file($path.'.htaccess');
			$pathInfo['__protected_type'] = $pathInfo['__protected'] ? 'htaccess' : '';
			$pathInfo['dir_ctime'] = @filectime($path);
			$pathInfo['dir_mtime'] = @filemtime($path);
			$pathInfo['dir_size'] = @filesize($path);
			$pathInfo['dir_type'] = @filetype($path);
			$pathInfo['dir_owner'] = @fileowner($path);
			$pathInfo['dir_perms'] = @fileperms($path);
				// I have no idea why these are negated in t3lib_basicfilefunc
			$pathInfo['dir_writable'] = @is_writable($path);
			$pathInfo['dir_readable'] = @is_readable($path);

				// find mount
			$pathInfo['mount_id'] = $basicFF->checkPathAgainstMounts($path);
			$pathInfo['mount_path'] =  $FILEMOUNTS[$pathInfo['mount_id']]['path'];
			$pathInfo['mount_name'] =  $FILEMOUNTS[$pathInfo['mount_id']]['name'];
			$pathInfo['mount_type'] =  $FILEMOUNTS[$pathInfo['mount_id']]['type'];
			// $pathInfo['web_nonweb'] = t3lib_BEfunc::getPathType_web_nonweb($path); // prevent using t3lib_BEfunc
			$pathInfo['web_nonweb'] = t3lib_div::isFirstPartOfStr($path, t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT')) ? 'web' : '';

				// extra path info
			$pathInfo['dir_name'] = basename($path);
			$pathInfo['dir_title'] = $pathInfo['dir_name'];
			$pathInfo['dir_path_absolute'] = $path;
			$pathInfo['dir_path_relative'] = tx_dam::path_makeRelative($path);
			$pathInfo['dir_path_from_mount'] = tx_dam::path_makeRelative($path, $pathInfo['mount_path']);

			// ksort($pathInfo);

// TODO localization
			if ($pathInfo['dir_name']=='_temp_')	{
				$pathInfo['dir_title'] = 'TEMP';
			}
			if ($pathInfo['dir_name']=='_recycler_')	{
				$pathInfo['dir_title'] = 'RECYCLER';
			}

		}

		return $pathInfo;
	}




	/***************************************
	 *
	 *	 Meta data related function
	 *
	 ***************************************/


	/**
	 * Fetches the meta data from the index by a given file path or file info array.
	 * The field list to be fetched can be passed.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param	string		$fields A list of fields to be fetched. Default is a list of fields generated by tx_dam_db::getMetaInfoFieldList().
	 * @param	boolean		$ignoreExistence The existence of the file will not be checked if filename is passed.
	 * @return	array
	 */
	function meta_getDataForFile($fileInfo, $fields='', $ignoreExistence=false) {
		$meta = false;

		if (!is_array($fileInfo)) {
			$fileInfo = tx_dam::file_compileInfo ($fileInfo, $ignoreExistence);
		}

		if (is_array($fileInfo)) {
			$fields = $fields ? $fields : tx_dam_db::getMetaInfoFieldList();

			$where = array();
			$where['file_name'] = 'file_name='.$GLOBALS['TYPO3_DB']->fullQuoteStr($fileInfo['file_name'],'tx_dam');
			$where['file_path'] = 'file_path='.$GLOBALS['TYPO3_DB']->fullQuoteStr($fileInfo['file_path'],'tx_dam');

			if ($rows = tx_dam_db::getDataWhere($fields, $where, '', '', '1')) {
				reset ($rows);
				$meta = current($rows);
			}
		}

		return $meta;
	}


	/**
	 * Fetches the meta data from the index by a given UID.
	 * The field list to be fetched can be passed.
	 *
	 * @param	integer		$uid UID of the meta data record
	 * @param	string		$fields A list of fields to be fetched. Default is a list of fields generated by tx_dam_db::getMetaInfoFieldList().
	 * @return	array		Meta data array or false
	 */
	function meta_getDataByUid ($uid, $fields='') {
		$row = false;

		if($uid = intval($uid)) {
			if ($rows = tx_dam_db::getDataWhere($fields, 'uid='.$uid, '', '', '1')) {
				reset ($rows);
				$row = current($rows);
			}
		}

		return $row;
	}


	/**
	 * Fetches the meta data from the index by a given file hash.
	 * The field list to be fetched can be passed.
	 * This function returns an array of meta data arrays because it's possible to match more than one index entry!
	 * To get meta data for a file use meta_getDataForFile() instead.
	 *
	 * @param	string		$hash Hash value for the file
	 * @param	string		$fields A list of fields to be fetched. Default is a list of fields generated by tx_dam_db::getMetaInfoFieldList().
	 * @return	array		Array of Meta data arrays or false.
	 */
	function meta_getDataByHash ($hash, $fields='') {
		$rows = false;

		if($hash) {
			$rows = tx_dam_db::getDataWhere($fields, 'file_hash='.$GLOBALS['TYPO3_DB']->fullQuoteStr($hash,'tx_dam'));
		}

		return $rows;
	}


	/**
	 * Fetches the meta data from the index by a given file path or file info array and a hash value.
	 * This can be used to find the meta data for a "lost" file so the file and meta data can be reconnected.
	 * Index entries which have a current valid file but have the same hash value will be removed from the result.
	 * It will be searched in deleted records too to find a related index entry.
	 * This function returns an array of meta data arrays because it's possible to match more than one index entry!
	 * The returned records may be related to an existing file! In that case changing the index entry will create a lost file again!
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param	string		$hash The hash value will be used to identify the file if the file name was not found. That can happen if the file was renamed or moved without index update.
	 * @param	string		$fields A list of fields to be fetched. Default is a list of fields generated by tx_dam_db::getMetaInfoFieldList().
	 * @return	array		Array of Meta data arrays or false.
	 */
	function meta_findDataForFile($fileInfo, $hash='', $fields='') {
		$rows = false;

		if (!is_array($fileInfo)) {
			$fileInfo = tx_dam::file_compileInfo ($fileInfo);
		}

		if (is_array($fileInfo)) {

			if (!$hash) {
				$hash = tx_dam::file_calcHash ($fileInfo);
			}

			$fields = $fields ? $fields : tx_dam_db::getMetaInfoFieldList();


			$where = array();
			$where['deleted'] = '';
			$where['file_name'] = 'file_name='.$GLOBALS['TYPO3_DB']->fullQuoteStr($fileInfo['file_name'],'tx_dam');
			$where['file_path'] = 'file_path='.$GLOBALS['TYPO3_DB']->fullQuoteStr($fileInfo['file_path'],'tx_dam');
			if ($hash) {
				$where['hash'] = 'file_hash='.$GLOBALS['TYPO3_DB']->fullQuoteStr($hash,'tx_dam');
			}

				// max 3 tries to find the record
				// 1: hash, name and path
				// 2: hash, name
				// 3: by hash only
			for ($index = 0; $index < 3; $index++) {

				$rowsResult = tx_dam_db::getDataWhere($fields, $where, '', '', '1');
				if(is_array($rowsResult)) {
					$rows = array();
					foreach ($rowsResult as $row) {
							// the file itself - we're done
						if($row['file_name']==$fileInfo['file_name'] AND $row['file_path']==$fileInfo['file_path']) {
							$rows[$row['uid']] = $row;
							break;
						}
							// a lost file
						if(!(@is_file($row['file_path'].$row['file_name']))) {
							$rows[$row['uid']] = $row;
							continue;
						}
					}
					break;

				} else {
					if (!$where['hash']) {
						// just leave the for-loop if there's no hash
						break;
					}
					switch ($index) {
						case 0:
							// search for filename AND hash
							unset($where['file_path']);
							break;
						case 1:
							// search for hash only
							unset($where['file_name']);
							break;

						default:
							break 2;
					}
				}
			}
		}

		return $rows;
	}


	/**
	 * Fetches the meta data variant from the index by a given meta data record array. The result will have the same fields selected as the given record.
	 * For now different languages can be fetched.
	 * Later this function may support versions too.
	 *
	 * @param	array		$row Meta data record array with 'uid' field
	 * @param	mixed		$language The uid of the sys_language or the ISO code
	 * @return	array		Meta data array or false
	 */
	function meta_getDataVariant ($row, $language) {
		$row = false;

		if($uid = intval($row['uid'])) {
			$fields = implode(',', array_keys($row));

			$sys_language_uid = 0;
			if(t3lib_div::testInt($language)) {
				$sys_language_uid = intval($language);
			} else {
					// Finding the uid for an ISO code:
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
														'uid',
														'sys_language',
														'lg_iso_2='.$GLOBALS['TYPO3_DB']->fullQuoteStr($language,'sys_language').
															' AND deleted=0'
													);
				if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$sys_language_uid = intval($row['uid']);
				}
			}

			$where = array();
			$where['sys_language_uid'] = $sys_language_uid;
			$where['l18n_parent'] = '';
			if($sys_language_uid) {
				$where['l18n_parent'] = ' AND l18n_parent='.$uid;
			}

			if ($rows = tx_dam_db::getDataWhere($fields, $where, '', '', '1')) {
				reset ($rows);
				$row = current($rows);
			}
		}

		return $row;
	}


	/**
	 * Checks if a file was changed or if it's missing and updates the status accordingly
	 *
	 * @param	array		$row Meta data record array with 'uid' field
	 * @return	integer		New status value eg. TXDAM_status_file_changed, TXDAM_status_file_missing
	 */
	function meta_updateStatus ($meta, $markMissingDeleted=NULL) {
		$status = TXDAM_status_file_ok;

		$filepath = tx_dam::file_absolutePath ($meta);
		$fileInfo = tx_dam::file_compileInfo ($filepath);
		if($fileInfo['__exists']) {
			$hash = tx_dam::file_calcHash ($fileInfo);
			if (!($fileInfo['file_mtime']==$meta['file_mtime']) OR !($hash==$meta['file_hash'])) {
				$status = TXDAM_status_file_changed;
				tx_dam_db::updateStatus($meta['uid'], $status, $fileInfo, $hash);
			}
		} else {
			$status = TXDAM_status_file_missing;
			tx_dam_db::updateStatus($meta['uid'], $status, NULL, NULL, ($markMissingDeleted ? 1 : NULL));
		}

		return $status;
	}




	/***************************************
	 *
	 *	 Media objects functions
	 *
	 ***************************************/



	/**
	 * Returns a media object by a given file path or file info array.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param	string		$hash If set the hash value can be used to identify the file if the file name was not found. That can happen if the file was renamed or moved without index update.
	 * @return	object		media object or false
	 * @see tx_dam_media
	 */
	function media_getForFile($fileInfo, $hash=false) {
// FIXME tx_dam_media is not yet finished
return NULL;
		require_once(PATH_txdam.'lib/class.tx_dam_media.php');
		$media = t3lib_div::makeInstance('tx_dam_media');
		$media->fetchIndexFromFilename ($fileInfo, $hash);
		return $media;
	}


	/**
	 * Fetches a media object from the index by a given UID.
	 *
	 * @param	integer		$uid UID of the meta data record
	 * @return	object		media object or false
	 * @see tx_dam_media
	 */
	function media_getByUid ($uid) {
// FIXME tx_dam_media is not yet finished
return NULL;
		require_once(PATH_txdam.'lib/class.tx_dam_media.php');
		$media = t3lib_div::makeInstance('tx_dam_media');
		$media->fetchIndexFromMetaUID ($uid);
		return $media;
	}


	/**
	 * Returns media objects from the index by a given file hash.
	 * This function returns an array of media objects because it's possible to match more than one index entry!
	 *
	 * @param	string		$hash Hash value for the file
	 * @return	array		Array of media objects or false.
	 * @see tx_dam_media
	 */
	function media_getByHash ($hash) {
// FIXME tx_dam_media is not yet finished
return NULL;
		require_once(PATH_txdam.'lib/class.tx_dam_media.php');
		$mediaArr = false;
		if ($rows = tx_dam::meta_getDataByHash ($hash, '*')) {
			$mediaArr = array();
			foreach ($rows as $row) {
				$mediaArr[$row['uid']] = t3lib_div::makeInstance('tx_dam_media');
				$mediaArr[$row['uid']]->setMetaData ($row);
			}
		}
		return $mediaArr;
	}




	/***************************************
	 *
	 *	 Indexing functions
	 *
	 ***************************************/



	/**
	 * Do a check if a file is already indexed and have an entry in the DAM table
	 * This function return a status value and the meta data array, while file_isIndexed() just returns the uid.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param	string		$hash The hash value will be used to identify the file if the file name was not found. That can happen if the file was renamed or moved without index update.
	 * @return	array		status: array('__status' => TXDAM_file_notfound,'meta' => array(...));
	 * @see file_isIndexed()
	 */
	function index_check ($fileInfo, $hash='') {

		$status = array(
				'__status' => TXDAM_file_notfound,
				'meta' => array(),
			);

		if (!is_array($fileInfo)) {
			$fileInfo = tx_dam::file_compileInfo ($fileInfo, true);
		}
		if (!$hash) {
			$hash = $fileInfo['file_hash'];
		}
// FIXME $hash is not used - what is the concept?
		if (is_array($fileInfo)) {

			$status['__status'] = TXDAM_file_unknown;

			$where = array();
			$where['file_name'] = 'file_name='.$GLOBALS['TYPO3_DB']->fullQuoteStr($fileInfo['file_name'],'tx_dam');
			$where['file_path'] = 'file_path='.$GLOBALS['TYPO3_DB']->fullQuoteStr($fileInfo['file_path'],'tx_dam');
			if ($rows = tx_dam_db::getDataWhere('', $where, '', '', '1')) {
				reset($rows);
				$row = current($rows);
				$status['meta'] = $row;

				if (!$fileInfo['__exists']) {
					$status['__status'] = TXDAM_file_missing;
				} elseif ($row['file_mtime']==$fileInfo['file_mtime']) {
					$status['__status'] = TXDAM_file_ok;
				} else {
					$status['__status'] = TXDAM_file_changed;
				}
			}
		}
		return $status;
	}


	/**
	 * Tries to find a lost index entry for a lost file and reconnect these items
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().
	 * @param	string		$hash The hash value will be used to identify the file if the file name was not found. That can happen if the file was renamed or moved without index update.
	 * @return	array		status: array('__status' => TXDAM_file_notfound,'meta' => array(...));
	 */
	function index_reconnect($fileInfo, $hash='') {

		$status = array(
				'__status' => TXDAM_file_unknown,
				'meta' => array(),
			);

		$metaArr = tx_dam::meta_findDataForFile($fileInfo, $hash);
		foreach ($metaArr as $meta) {
			$srcInfo = tx_dam::file_compileInfo ($meta, true);
			if (!$srcInfo['__exists']) {
				$status['meta'] = $meta;
				tx_dam::notify_fileMoved (tx_dam::file_pathAbsolute($srcInfo), tx_dam::file_pathAbsolute($fileInfo));
				$status['__status'] = TXDAM_file_changed;
				break;
			}
		}
		return $status;
	}




	/**
	 * Process auto indexing for the given file.
	 *
	 * @param	string		$filename Filename with path
	 * @param	boolean		$reindex If set already indexed files will be reindexed
	 * @return	array		Meta data array. $meta['fields'] has the record data. Returns false when nothing was indexed.
	 */
	function index_autoProcess($filename, $reindex=false) {
		global $TYPO3_CONF_VARS;

		static $index;


			// disable auto indexing by setup
		if(tx_dam::config_getValue('setup.indexing.auto.disable')) {
			return false;

		}

			// we don't index indexing setup files
		if (basename($filename)=='.indexing.setup.xml') {
			return false;
		}

		$filename = tx_dam::file_absolutePath($filename);

		if(!$reindex AND tx_dam::file_isIndexed($filename)) {
			return false;
		}


		require_once(PATH_txdam.'lib/class.tx_dam_indexing.php');
		$index = t3lib_div::makeInstance('tx_dam_indexing');
		$index->init();

		$index->setDefaultSetup(dirname($filename));
		$index->initEnabledRules();

			// overrule some parameter from setup
		$index->setPath($filename);
		$index->setPID(tx_dam_db::getPid());
		$index->setRunType('auto');
		$index->enableMetaCollect();

			// indexing ...
		$index->indexUsingCurrentSetup();

		return current($index->meta);
	}


	/**
	 * Process indexing for the given file, folder or a list of files and folders.
	 *
	 * @param	mixed		$filename A single filename or folder path or a list of files and path as array
	 * @param	mixed		$setup Setup as string (serialized setup) or array. See tx_dam_indexing::restoreSerializedSetup()
	 * @param	mixed		$callbackFunc Callback function for the finished indexed file.
	 * @param	mixed		$metaCallbackFunc Callback function which will be called during indexing to allow modifications to the meta data.
	 * @param	mixed		$filePreprocessingCallbackFunc Callback function for pre processing the to be indexed file.
	 * @return	array		Info array about indexed files and meta data records.
	 */
	function index_process ($filename, $setup=NULL, $callbackFunc=NULL, $metaCallbackFunc=NULL, $filePreprocessingCallbackFunc=NULL) {
// TODO how to set run type???
		require_once(PATH_txdam.'lib/class.tx_dam_indexing.php');
		$index = t3lib_div::makeInstance('tx_dam_indexing');
		$index->init();
		$index->setRunType('man');
		$index->setPID(tx_dam_db::getPid());
		$index->setDefaultSetup();
		if ($setup) {
			$index->restoreSerializedSetup($setup);
		} else {
			$index->setDefaultSetup(dirname($filename));
		}
		$index->initEnabledRules();
		if(is_array($filename)) {
			$index->setPathsList($filename);
		} else {
			$index->setPath($filename);
		}
		return $index->indexUsingCurrentSetup($callbackFunc, $metaCallbackFunc, $filePreprocessingCallbackFunc);
	}





	/***************************************
	 *
	 *   Notify the DAM about file or folder changes
	 *
	 ***************************************/



	/**
	 * Notifies the DAM about (external) changes/update of a file.
	 * This will update the file related meta data of the file like date and size.
	 *
	 * @param	string		$filename Filename with path
	 * @return	void
	 */
	function notify_fileChanged ($filename) {
		if (is_array($meta = tx_dam::meta_getDataForFile($filename))) {
			tx_dam::meta_updateStatus ($meta);
		} else {
				// file is not yet indexed
			tx_dam::index_autoProcess($filename);
		}
	}


	/**
	 * Notifies the DAM about (external) changes to names and movements about files or folders.
	 * This will update all related meta data
	 *
	 * @param	string		$src File/folder name with path of the source that was changed.
	 * @param	string		$dest File/folder name with path of the destination which is a new name or/and a new location.
	 * @return	void
	 */
	function notify_fileMoved ($src, $dest) {
		if ($uid = tx_dam::file_isIndexed($src)) {
			$fileInfo = file_compileInfo ($dest);
			if ($fileInfo['__exists']) {
				$values = array();
				$values['uid'] = $uid;
				$values['deleted'] = '0';
				$values['file_name'] = $fileInfo['file_name'];
				$values['file_path'] = $fileInfo['file_path'];
				$values['file_mtime'] = $fileInfo['file_mtime'];
				tx_dam_db::insertUpdateData($values);
			}
		} else {
				// file is not yet indexed
			tx_dam::index_autoProcess($dest);
		}
	}


	/**
	 * Notifies the DAM about a deleted file.
	 * This will remove the file from the index.
	 *
	 * @param	string		$filename Filename with path
	 * @return	void
	 */
	function notify_fileDeleted ($filename) {
		if ($uid = tx_dam::file_isIndexed($filename)) {
			$values = array();
			$values['uid'] = $uid;
			$values['deleted'] = '1';
			tx_dam_db::insertUpdateData($values);
		}
	}





	/***************************************
	 *
	 *   Converter for names and codes of data formats
	 *
	 ***************************************/



	/**
	 * Converts the media type name to integer and vice versa.
	 *
	 * @param	mixed		$type Media type name or media type code to convert. Integer or 'text','image','audio','video','interactive', 'service','font','model','dataset','collection','software','application'
	 * @return	mixed		Media type name or media type code
	 */
	function convert_mediaType($type) {

		if(!strcmp($type,intval($type))) {
			$type = $GLOBALS['T3_VAR']['ext']['dam']['code2media'][$type];
		} else {
			$type = $GLOBALS['T3_VAR']['ext']['dam']['media2code'][$type];
		}
		return $type;
	}





	/***************************************
	 *
	 *	 Icon functions
	 *
	 ***************************************/



	/**
	 * Returns the icon filepath for a file type icon for a given file.
	 * $mimeType = tx_dam::file_getType($filename);
	 *
	 * @param	array		$mimeType Describes the type of a file. Can be meta record array or array from tx_dam::file_getType().
	 * @param	boolean		$absolutePath If set the path to the icon is absolute. By default it's relative to typo3/ folder.
	 * @return	string		Icon image file path
	 * @see tx_dam::file_getType()
	 */
	function icon_getFileType ($mimeType, $absolutePath=false) {
		static $iconCache = array();

		$iconfile = false;

		if (is_array($mimeType)) {

			if ($cached = $iconCache[$mimeType['file_type']]) {
				$iconfile = $cached;

			} elseif ($cached = $iconCache[$mimeType['__'.$mimeType['media_type']]]) {
				$iconfile = $cached;

			} else {

				foreach ( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['fileIconPaths'] as $pathIcons ) {
					if (@file_exists($pathIcons.$mimeType['file_type'].'.gif')) {
						$iconfile = $pathIcons.$mimeType['file_type'].'.gif';
						$iconCache[$mimeType['file_type']] = $iconfile;
						break;
					}
				}

				if(!$iconfile AND $mediaType = tx_dam::convert_mediaType($mimeType['media_type'])) {
					foreach ( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['fileIconPaths'] as $pathIcons ) {
						if (@file_exists($pathIcons.'mtype_'.$mediaType.'.gif')) {
							$iconfile = $pathIcons.'mtype_'.$mediaType.'.gif';
							$iconCache['__'.$mimeType['media_type']] = $iconfile;
						}
					}
				}
			}
		}
		if (!$iconfile) {
			$iconfile = PATH_txdam.'i/18/'.'mtype_undefined.gif';
		}
		if (!$absolutePath) {
// TODO this might not work always and could be cached too
				$iconfile = '../'.str_replace (PATH_site, '', $iconfile);
		}

		return $iconfile;
	}


	/**
	 * Returns the icon filepath for a folder icon for a given path.
	 *
	 * @param	array		$pathInfo Path info array: $pathInfo = tx_dam::path_getInfo($path)
	 * @param	boolean		$absolutePath If set the path to the icon is absolute. By default it's relative to typo3/ folder.
	 * @return	string		Iconpath
	 * @see tx_dam::path_getInfo()
	 */
	function icon_getFolder($pathInfo, $absolutePath=false)	{

		if ($pathInfo['mount_path'] == $pathInfo['dir_path_absolute']) {
			switch($pathInfo['mount_type'])	{
				case 'user':	$iconfile = 'gfx/i/_icon_ftp_user.gif';	break;
				case 'group':	$iconfile = 'gfx/i/_icon_ftp_group.gif';	break;
				default:		$iconfile = 'gfx/i/_icon_ftp.gif';	break;
			}
		}
		else {
			if($pathInfo['__protected']) {
				$iconfile = PATH_txdam_rel.'i/_icon_'.$pathInfo['web_nonweb'].'folders_protected.gif';
			}
			elseif ($pathInfo['dir_name']=='_temp_')	{
				$iconfile = 'gfx/i/sysf.gif';
			}
			elseif ($pathInfo['dir_name']=='_recycler_')	{
				$iconfile = 'gfx/i/recycler.gif';
			}
// what is this - makes it sense?
			elseif ($pathInfo['mount_id']=='_temp_')	{
				$iconfile = 'gfx/i/_icon_ftp.gif';
			} else {
				$iconfile = 'gfx/i/_icon_'.$pathInfo['web_nonweb'].'folders'.($pathInfo['dir_writable']?'':'_ro').'.gif';
			}
		}

		if ($absolutePath) {
			$iconfile = PATH_t3lib.$iconfile;
		}

		return $iconfile;
	}


	/**
	 * Returns a file or folder icon for a given (file)path as HTML img tag.
	 *
	 * @param	array		$infoArr info array: eg. $pathInfo = tx_dam::path_getInfo($path)
	 * @param	boolean		$addAttrib Additional attributes for the image tag..
	 * @return	string		Icon img tag
	 * @see tx_dam::path_getInfo()
	 */
	function icon_getFileTypeImgTag($infoArr, $addAttrib='')	{

		if (isset($infoArr['dir_name'])) {
			$iconfile = tx_dam::icon_getFolder ($infoArr);
		}
		elseif (isset($infoArr['file_name'])) {
			$iconfile = tx_dam::icon_getFileType ($infoArr);
		}

		$icon = '<img'.t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], $iconfile, 'width="18" height="16"').' '.trim($addAttrib).' alt="" />';

		return $icon;
	}




	/***************************************
	 *
	 *   Register functions like selection classes, indexing rules, viewer, editors
	 *
	 ***************************************/



	/**
	 * Register a meta data trigger class
	 *
	 * @param	string		$idName This is the ID of the selection. Chars allowed only: [a-zA-z]
	 * @param	string		$class reference, '[file-reference":"]["&"]class'. See t3lib_div::callUserFunction().
	 * @param	string		$position can be used to set the position of a new item within the list of existing items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or "bottom"/blank which is default). If "after"/"before" then submodule will be inserted after/before the existing item with [item-key] if found. If not found, the bottom of list. If "top" the item is inserted in the top of the item list.
	 * @return	void
	 * @see tx_dam_db
	 */
	function register_dbTrigger ($idName, $class, $position='') {
		tx_dam::_addItem($idName, $class, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['dbTriggerClasses'], $position);
	}

	/**
	 * Register a selection class
	 *
	 * @param	string		$idName This is the ID of the selection. Chars allowed only: [a-zA-z]
	 * @param	string		$class Function/Method reference, '[file-reference":"]["&"]class/function["->"method-name]'. See t3lib_div::callUserFunction().
	 * @param	string		$position can be used to set the position of a new item within the list of existing items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or "bottom"/blank which is default). If "after"/"before" then submodule will be inserted after/before the existing item with [item-key] if found. If not found, the bottom of list. If "top" the item is inserted in the top of the item list.
	 * @return	void
	 */
	function register_selection ($idName, $class, $position='') {
		tx_dam::_addItem($idName, $class, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['selectionClasses'], $position);
	}


	/**
	 * Register an indexing rule class
	 *
	 * @param	string		$idName This is the ID of the indexing rule. Chars allowed only: [a-zA-z]
	 * @param	string		$class Function/Method reference, '[file-reference":"]["&"]class/function["->"method-name]'. See t3lib_div::callUserFunction().
	 * @param	string		$position can be used to set the position of a new item within the list of existing items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or "bottom"/blank which is default). If "after"/"before" then submodule will be inserted after/before the existing item with [item-key] if found. If not found, the bottom of list. If "top" the item is inserted in the top of the item list.
	 * @return	void
	 */
	function register_indexingRule ($idName, $class, $position='') {
		tx_dam::_addItem($idName, $class, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['indexRuleClasses'], $position);
	}


	/**
	 * Register a file type
	 * This extends or overwrite the internal list of mime types which is used to detect a media type and a file type.
	 *
	 * @param	string		$fileExtension File extension. Eg. jpg, pdf, ...
	 * @param	string		$mimeType Eg. image/tiff, audio/x-mpeg
	 * @param	string		$mediaType Optional, if mime type is different. Eg. 'text','image','audio','video','interactive', 'service','font','model','dataset','collection','software','application'
	 * @return	void
	 */
	function register_fileType ($fileExtension, $mimeType, $mediaType='') {
		$fileExtension = strtolower($fileExtension);
		$GLOBALS['T3_VAR']['ext']['dam']['file2mime'][$fileExtension] = $mimeType;
		if ($mediaType) {
			$GLOBALS['T3_VAR']['ext']['dam']['file2mediaCode'][$fileExtension] = tx_dam::convert_mediaType($mediaType);
		}
	}

// TODO register name as LANG ressource? Or put it into the class?
// TODO make it possible to register media_types?
	/**
	 * Register a class which renders a "previewer".
	 * A previewer will render a - yes - preview of a file. This can be a thumbnail or a small embedded mp3 player.
	 * A previewer can be used in thumbnail view or at the top of a record.
	 *
	 * @param	string		$idName This is the ID of the previewer. Chars allowed only: [a-zA-z]
	 * @param	string		$class Function/Method reference, '[file-reference":"]["&"]class/function["->"method-name]'. See t3lib_div::callUserFunction().
	 * @param	string		$position can be used to set the position of a new item within the list of existing items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or "bottom"/blank which is default). If "after"/"before" then submodule will be inserted after/before the existing item with [item-key] if found. If not found, the bottom of list. If "top" the item is inserted in the top of the item list.
	 * @return	void
	 */
	function register_previewer ($idName, $class, $position='') {
		tx_dam::_addItem($idName, $class, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['previewerClasses'], $position);
	}


	/**
	 * Register a class which renders a "editor".
	 * A editor is something that modifies a file. This can be a text editor or a module to crop an image.
	 *
	 * @param	string		$idName This is the ID of the editor. Chars allowed only: [a-zA-z]
	 * @param	string		$class Function/Method reference, '[file-reference":"]["&"]class/function["->"method-name]'. See t3lib_div::callUserFunction().
	 * @param	string		$position can be used to set the position of a new item within the list of existing items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or "bottom"/blank which is default). If "after"/"before" then submodule will be inserted after/before the existing item with [item-key] if found. If not found, the bottom of list. If "top" the item is inserted in the top of the item list.
	 * @return	void
	 */
	function register_editor ($idName, $class, $position='') {
		tx_dam::_addItem($idName, $class, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['editorClasses'], $position);
	}


	/**
	 * Register a "action" class.
	 * A action is something that renders buttons, control icons, ..., which executes command for an item.
	 *
	 * @param	string		$idName This is the ID of the action. Chars allowed only: [a-zA-z]
	 * @param	string		$class Function/Method reference, '[file-reference":"]["&"]class/function["->"method-name]'. See t3lib_div::callUserFunction().
	 * @param	string		$position can be used to set the position of a new item within the list of existing items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or "bottom"/blank which is default). If "after"/"before" then submodule will be inserted after/before the existing item with [item-key] if found. If not found, the bottom of list. If "top" the item is inserted in the top of the item list.
	 * @return	void
	 */
	function register_action ($idName, $class, $position='') {
		tx_dam::_addItem($idName, $class, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['actionClasses'], $position);
	}


	/**
	 * Register a path to additional file type icons
	 *
	 * @param	string		$path Absolute path to icon files
	 * @return	void
	 */
	function register_fileIconPath ($path) {
		if(!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['fileIconPaths'])) {
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['fileIconPaths'] = array();
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['fileIconPaths'][] = $path;
		} else {
			array_unshift ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dam']['fileIconPaths'], $path);
		}
	}





	/***************************************
	 *
	 *   Configuration
	 *
	 ***************************************/



	/**
	 * Return configuration values which are mainly defined by TSconfig.
	 * The configPath must begin with "setup." or "mod."
	 * "setup" is mapped to tx_dam TSConfig key.
	 *
	 * @param	string		$configPath Pointer to an "object" in the TypoScript array, fx. 'setup.selections.default'
	 * @param	boolean		$getProperties return the properties array instead of the value. Means to return the stuff set by a dot. Eg. setup.xxxx.xxx
	 * @return	mixed		Just the value or when $getProperties is set an array with the properties of the $configPath.
	 */
	function config_getValue($configPath='', $getProperties=false) {
		$configValues = false;

		$config = $GLOBALS['T3_VAR']['ext']['dam']['config'];

		if(!is_array($config)) {
			tx_dam::config_init();
		}

		if ($configPath AND is_object($GLOBALS['BE_USER'])) {
			$configValues = $GLOBALS['BE_USER']->getTSConfig($configPath, $config['mergedTSconfig']);
		}

		if ($getProperties) {
			$configValues = $configValues['properties'];
		} else {
			$configValues = $configValues['value'];
		}

		return $configValues;
	}

	/**
	 * Set a dam config value
	 * The configPath must begin with "setup." or "mod."
	 * "setup" is mapped to tx_dam TSConfig key.
	 *
	 * @param	string		$configPath Pointer to an "object" in the TypoScript array, fx. 'setup.selections.default'
	 * @param	mixed 		$value Value to be set. Can be an array but must be in TSConfig format
	 * @return void
	 * @todo map user setup/options to dam setup?
	 */
	function config_setValue($configPath='', $value='') {

		$config = & $GLOBALS['T3_VAR']['ext']['dam']['config'];

		$perfomMerge = false;
		if(!is_array($config)) {
			tx_dam::config_init();
		}

		if ($configPath) {
			list ($baseKey, $options) = explode('.', $configPath, 2);
			if (($baseKey=='setup') OR ($baseKey=='mod')) {
				$options = explode('.', $options);
				$lastOption = count ($options);
				$optionArrPath = & $config['definedTSconfig'][$baseKey.'.'];
				$optCount = 0;
				foreach ($options as $optionValue) {
					$optCount++;
					if ($optCount < $lastOption) {
						$optionArrPath = & $optionArrPath[$optionValue.'.'];
					} else {
						$optionArrPath = & $optionArrPath[$optionValue.(is_array($value)?'.':'')];
					}

				}
				$optionArrPath = $value;
				$perfomMerge = true;
			}
		}
		if ($perfomMerge) {
			$config['mergedTSconfig'] = t3lib_div::array_merge_recursive_overrule($config['pageUserTSconfig'], $config['definedTSconfig']);
		}
	}


	/**
	 * Init dam config values - which means they are fetched from TSConfig
	 *
	 * @param	boolean $force Will force the initialitzation to be done again except definedTSconfig set by config_setValue
	 * @return void
	 */
	function config_init($force=false) {

		$config = & $GLOBALS['T3_VAR']['ext']['dam']['config'];

		$perfomMerge = false;
		if(!is_array($config)) {
			$config = array();
			$config['definedTSconfig'] = array();
			$config['mergedTSconfig'] = array();
		}
		if(($force OR !is_array($config['userTSconfig'])) AND ($TSconfig = tx_dam::_getTSconfig())) {
			$config['pageUserTSconfig'] = $config['userTSconfig'] = $TSconfig;
			$perfomMerge = true;
		}

		if($force OR !is_array($config['pageTSconfig'])) {
			if ($pid = tx_dam_db::getPid() AND ($TSconfig = tx_dam::_getTSconfig($pid))) {
				$config['pageTSconfig'] = $TSconfig;
				$config['pageUserTSconfig'] = t3lib_div::array_merge_recursive_overrule($config['pageTSconfig'], $config['userTSconfig']);
				$perfomMerge = true;
			}
		}

		if ($perfomMerge) {
			$config['mergedTSconfig'] = t3lib_div::array_merge_recursive_overrule($config['pageUserTSconfig'], $config['definedTSconfig']);
		}
	}







	/***************************************
	 *
	 *   Internal
	 *
	 ***************************************/



	/**
	 * get TSConfig values for initialization
	 * @access private
	 * @param integer $pid If set page TSConfig will be fetched otherwise user TSConfig
	 * @return array
	 */
	function _getTSconfig ($pid=0) {
		$values = false;

		if (is_object($GLOBALS['BE_USER'])) {
			$TSconfig = '';
			if ($pid) {
				require_once(PATH_t3lib.'class.t3lib_befunc.php');
				$TSconfig = t3lib_BEfunc::getPagesTSconfig($pid);
			}

				// get global config
			$TSConfValues = $GLOBALS['BE_USER']->getTSConfig('tx_dam', $TSconfig);
			$global = $TSConfValues['properties'];

				// get mod config of dam_* modules
			$TSConfValues = $GLOBALS['BE_USER']->getTSConfig('mod', $TSconfig);
			if (is_array($mod = $TSConfValues['properties'])) {
				foreach($mod as $key => $value) {
					if (!(substr($key, 0, 7)=='txdamM1')) {
						unset($mod[$key]);
					}
				}
			}
			$values = array('setup.' => $global, 'mod.' => $mod);
		}
		return $values;
	}


	/**
	 * Adds a an item to an array with the possibility to request a position efore/after another item
	 *
	 * @access private
	 * @param	string		$idName
	 * @param	mixed		$value
	 * @param	array		$items
	 * @param	string		$position can be used to set the position of a new item within the list of existing items. $position has this syntax: [cmd]:[item-key]. cmd can be "after", "before" or "top" (or "bottom"/blank which is default). If "after"/"before" then submodule will be inserted after/before the existing item with [item-key] if found. If not found, the bottom of list. If "top" the item is inserted in the top of the item list.
	 * @return	void
	 */
	function _addItem($idName, $value, &$items, $position='')	{

		$position .= ';bottom';
		$posList = t3lib_div::trimExplode(';', $position, 1);

		$element = array($idName => $value);

		$placed = false;

		foreach ($posList as $posDef) {
			list($place, $itemRef) = t3lib_div::trimExplode(':', $posDef, 1);
			switch($place)	{
				case 'after':
				case 'before':
					$pointer = false;
					if (isset($items[$itemRef])) {
						$pointer = $itemRef;
					}
					if ($pointer) {
						$newArr = array();
						foreach($items as $key => $v) {
							if ($pointer == $key AND $place=='before') {
								$newArr[$idName] = $value;
							}
							$newArr[$key] = $v;
							if ($pointer == $key AND $place=='after') {
								$newArr[$idName] = $value;
							}
						}
						$placed = true;
						$items = $newArr;
					}
				break;
				case 'top':
					$items = array_merge($element, $items);
					$placed = true;
				break;
				default:
						// append to the list
					$items[$idName] = $value;
					$placed = true;
				break;
			}
			if ($placed) break;
		}
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam.php']);
}
?>