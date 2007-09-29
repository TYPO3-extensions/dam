<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2004 Kasper Skaarhoj (kasper@typo3.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
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
 * TCE (TYPO3 Core Engine) file-handling
 * This script serves as the fileadministration part of the TYPO3 Core Engine.
 * Basically it includes two libraries which are used to manipulate files on the server.
 *
 * For syntax and API information, see the document 'TYPO3 Core APIs'
 *
 * Revised for TYPO3 3.6 July/2003 by Kasper Skaarhoj
 *
 * @author	Kasper Skaarhoj <kasper@typo3.com>
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Core
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   84: class tx_dam_extFileFunctions extends t3lib_extFileFunctions
 *   98:     function processData()
 *
 *              SECTION: File operation functions
 *  169:     function func_delete($cmds)
 *  257:     function func_upload($cmds, $id)
 *  350:     function getMaxUploadSize()
 *  378:     function func_rename($cmds, $id)
 *  498:     function writeLog($action,$error,$details_nr,$details,$data, $actionName='', $id='')
 *  519:     function errors()
 *  529:     function getLastError($getFullLogEntry=FALSE)
 *
 *
 *  558: class tx_dam_tce_file
 *  578:     function init($file='')
 *  614:     function overwriteExistingFiles($overwriteExistingFiles)
 *  625:     function setCmdmap($fileCmds)
 *  634:     function initClipboard()
 *  656:     function process()
 *  670:     function errors()
 *  680:     function getLastError($getFullLogEntry=FALSE)
 *
 * TOTAL FUNCTIONS: 15
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once (PATH_t3lib.'class.t3lib_basicfilefunc.php');
require_once (PATH_t3lib.'class.t3lib_extfilefunc.php');



/**
 * @ignore
 */
class ux_t3lib_extFileFunctions extends t3lib_extFileFunctions	{

	/**
	 * defines if the meta data in the database should be updated on file changes
	 */
	var $processMetaUpdate = true;

	/**
	 * error logging
	 * @see getLastError()
	 */
	var $log = array(
			'errors' => 0,
			'cmd' => array(),
			);


	/**
	 * Processing the command array in $this->fileCmdMap
	 *
	 * @return	void
	 */
	function processData()	{
		global $TYPO3_CONF_VARS;

		if (!$this->isInit) return FALSE;

		$this->log = array(
			'errors' => 0,
			'cmd' => array(),
			);

		if (is_array($this->fileCmdMap))	{

				// Traverse each set of actions
			foreach($this->fileCmdMap as $action => $actionData)	{

					// Traverse all action data. More than one file might be affected at the same time.
				if (is_array($actionData))	{
					foreach($actionData as $id => $cmdArr)	{

							// Clear file stats
						clearstatcache();

							// Branch out based on command:
						switch ($action)	{
							case 'delete':
								$this->func_delete($cmdArr, $id);
							break;
							case 'copy':
								$this->func_copy($cmdArr, $id);
							break;
							case 'move':
								$this->func_move($cmdArr, $id);
							break;
							case 'rename':
								$this->func_rename($cmdArr, $id);
							break;
							case 'newfolder':
								$this->func_newfolder($cmdArr, $id);
							break;
							case 'newfile':
								$this->func_newfile($cmdArr, $id);
							break;
							case 'editfile':
								$this->func_edit($cmdArr, $id);
							break;
							case 'upload':
								$this->func_upload($cmdArr, $id);
							break;
							case 'unzip':
								$this->func_unzip($cmdArr, $id);
							break;
						}

							// hook
						if (is_array($TYPO3_CONF_VARS['EXTCONF']['dam']['fileTriggerClasses']) AND count($TYPO3_CONF_VARS['EXTCONF']['dam']['processTriggerClasses']))	{
							foreach($TYPO3_CONF_VARS['EXTCONF']['dam']['fileTriggerClasses'] as $classKey => $classRef)	{
								if (is_object($obj = &t3lib_div::getUserObj($classRef)))	{
									if (method_exists($obj, 'filePostTrigger')) {
										$obj->filePostTrigger($action, $this->log['cmd'][$action][$id]);
									}
								}
							}
						}

					}
				}
			}
		}



	}




	/*************************************
	 *
	 * File operation functions
	 *
	 **************************************/

	/**
	 * Deleting files and folders (action=4)
	 *
	 * @param	array		$cmds['data'] is the the file/folder to delete
	 * @return	boolean		Returns true upon success
	 */
	function func_delete($cmds, $id)	{
		if (!$this->isInit) return FALSE;

			// Checking path:
		$theFile = $cmds['data'];

			// main log entry
		$this->log['cmd']['delete'][$id] = array(
				'errors' => array(),
				'orig_filename' => $theFile,
				'target_file' => '',
				'target_path' => '',
				);


#		$theFile = preg_replace('#/$#', '', $theFile);

		if (!$this->isPathValid($theFile))	{
			$this->writelog(4,2,101,'Target "%s" had invalid path (".." and "//" is not allowed in path).',array($theFile), 'delete', $id);
			return FALSE;
		}

			// Recycler moving or not?
		if ($this->useRecycler && $recyclerPath=$this->findRecycler($theFile))	{
				// If a recycler is found, the deleted items is moved to the recycler and not just deleted.
			$newCmds=array();
			$newCmds['data']=$theFile;
			$newCmds['target']=$recyclerPath;
			$newCmds['altName']=1;
			$this->func_move($newCmds);
			$this->writelog(4,0,4,'Item "%s" moved to recycler at "%s"',array($theFile,$recyclerPath), 'delete', $id);

// TODO moved file might have a new name!!!!!

				// add file to log entry
			$this->log['cmd']['delete'][$id]['target_file'] = $theFile;
			$this->log['cmd']['delete'][$id]['target_path'] = $recyclerPath;

				// update meta data
			if ($this->processMetaUpdate) {
				tx_dam::notify_fileDeleted($theFile, $recyclerPath.'/'.basename($theFile));
			}

			return TRUE;

		} elseif ($this->useRecycler != 2) {	// if $this->useRecycler==2 then we cannot delete for real!!
			if (@is_file($theFile))	{	// If we are deleting a file...
				if ($this->actionPerms['deleteFile'])	{
					if ($this->checkPathAgainstMounts($theFile))	{
						if (@unlink($theFile))	{
							$this->writelog(4,0,1,'File "%s" deleted',array($theFile), 'delete', $id);

								// update meta data
							if ($this->processMetaUpdate) {
								tx_dam::notify_fileDeleted($theFile);
							}

							return TRUE;
						} else $this->writelog(4,1,110,'Could not delete file "%s". Write-permission problem?', array($theFile), 'delete', $id);
					} else $this->writelog(4,1,111,'Target was not within your mountpoints! T="%s"',array($theFile), 'delete', $id);
				} else $this->writelog(4,1,112,'You are not allowed to delete files','', 'delete', $id);
				// FINISHED deleting file

			} elseif (@is_dir($theFile)) {	// if we're deleting a folder
				if ($this->actionPerms['deleteFolder'])	{
					$theFile = $this->is_directory($theFile);
					if ($theFile)	{
						if ($this->checkPathAgainstMounts($theFile))	{	// I choose not to append '/' to $theFile here as this will prevent us from deleting mounts!! (which makes sense to me...)
							if ($this->actionPerms['deleteFolderRecursively'] && !$this->dont_use_exec_commands)	{
									// No way to do this under windows
								$cmd = 'rm -Rf "'.$theFile.'"';
								exec($cmd);		// This is a quite critical command...
								clearstatcache();
								if (!@file_exists($theFile))	{
									$this->writelog(4,0,2,'Directory "%s" deleted recursively!',array($theFile), 'delete', $id);

										// update meta data
									if ($this->processMetaUpdate) {
										tx_dam::notify_fileDeleted($theFile);
									}

									return TRUE;
								} else $this->writelog(4,2,119,'Directory "%s" WAS NOT deleted recursively! Write-permission problem?',array($theFile), 'delete', $id);
							} else {
								if (@rmdir($theFile))	{
									$this->writelog(4,0,3,'Directory "%s" deleted',array($theFile), 'delete', $id);

										// update meta data
									if ($this->processMetaUpdate) {
										tx_dam::notify_fileDeleted($theFile);
									}

									return TRUE;
								} else $this->writelog(4,1,120,'Could not delete directory! Write-permission problem? Is directory "%s" empty? (You are not allowed to delete directories recursively).',array($theFile), 'delete', $id);
							}
						} else $this->writelog(4,1,121,'Target was not within your mountpoints! T="%s"',array($theFile), 'delete', $id);
					} else $this->writelog(4,2,122,'Target seemed not to be a directory! (Shouldn\'t happen here!)','', 'delete', $id);
				} else $this->writelog(4,1,123,'You are not allowed to delete directories','', 'delete', $id);
				// FINISHED copying directory

			} else $this->writelog(4,2,130,'The item was not a file or directory! "%s"',array($theFile), 'delete', $id);
		} else $this->writelog(4,1,131,'No recycler found!','', 'delete', $id);


	}

	/**
	 * Upload of files (action=1)
	 *
	 * @param	array		$cmds['data'] is the ID-number (points to the global var that holds the filename-ref  ($GLOBALS['HTTP_POST_FILES']['upload_'.$id]['name']). $cmds['target'] is the target directory
	 * @param	string		$id: $_FILES['upload_'.$id]
	 * @return	string		Returns the new filename upon success
	 */
	function func_upload($cmds, $id)	{

		if (!$this->isInit) return FALSE;

		if (!$_FILES['upload_'.$id]['name'])	{
			return;
		}

			// filename of the uploaded file
		$theFile = $_FILES['upload_'.$id]['tmp_name'];
			// filesize of the uploaded file
		$theFileSize = $_FILES['upload_'.$id]['size'];
			// The original filename
// TODO  stripslashes needed ??
		$theName = $this->cleanFileName(stripslashes($_FILES['upload_'.$id]['name']));
// TODO format
			// main log entry
		$this->log['cmd']['upload'][$id] = array(
				'errors' => array(),
				'orig_filename' => $theName,
				'target_file' => '',
				'target_path' => $this->fileCmdMap['upload'][$id]['target'],
				);

			// Check if the file is uploaded
		if (!(is_uploaded_file($theFile) && $theName))	{
			$this->writelog(1,2,106,'The uploaded file did not exist!','', 'upload', $id);
			return;
		}

			// check upload permissions
		if (!$this->actionPerms['uploadFile'])	{
			$this->writelog(1,1,105,'You are not allowed to upload files!','', 'upload', $id);
			return;
		}

			// check if the file size exceed permissions
		$maxBytes = $this->getMaxUploadSize();
		if (!($theFileSize<($maxBytes)))	{
			$this->writelog(1,1,104,'The uploaded file exceeds the size-limit of %s (%s Bytes).',array(t3lib_div::formatSize($maxBytes), $maxBytes), 'upload', $id);
			return;
		}

			// Check the target dir
		$theTarget = $this->is_directory($cmds['target']);

			// check if target is inside of a mount point
		if (!($theTarget && $this->checkPathAgainstMounts($theTarget.'/')))	{
			$this->writelog(1,1,103,'Destination path "%s" was not within your mountpoints!',array($theTarget.'/'), 'upload', $id);
			return;
		}


			// check if the file extension is allowed
		$fI = t3lib_div::split_fileref($theName);
		if (!($this->checkIfAllowed($fI['fileext'], $theTarget, $fI['file']))) {
			$this->writelog(1,1,102,'Fileextension "%s" is not allowed in "%s"!',array($fI['fileext'],$theTarget.'/'), 'upload', $id);
			return;
		}

			// Create unique file name
		$theNewFile = $this->getUniqueName($theName, $theTarget, $this->dontCheckForUnique);
		if (!$theNewFile)	{
			$this->writelog(1,1,101,'No unique filename available in "%s"!',array($theTarget.'/'), 'upload', $id);
			return;
		}

			// move uploaded file to target location
		t3lib_div::upload_copy_move($theFile,$theNewFile);
		clearstatcache();

			// moving file did not work
		if (!@is_file($theNewFile))	{
			$this->writelog(1,1,100,'Uploaded file could not be moved! Write-permission problem in "%s"?',array($theTarget.'/'), 'upload', $id);
			return;
		}

		$this->internalUploadMap[$id] = $theNewFile;
		$this->writelog(1,0,1,'Uploading file "%s" to "%s"',array($theName, $theNewFile, $id), 'upload', $id);

			// add file to log entry
		$this->log['cmd']['upload'][$id]['target_file'] = $theNewFile;

		return $theNewFile;

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

		$maxFileSize = $this->maxUploadFileSize*1024;
		$maxFileSize = $maxFileSize ? $maxFileSize : intval($GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'])*1024;

		if (min($maxFileSize, $upload_max_filesize)==0) {
			$upload_max_filesize = max($maxFileSize, $upload_max_filesize);
		} else {
			$upload_max_filesize = min($maxFileSize, $upload_max_filesize);
		}
		return $upload_max_filesize;
	}


	/**
	 * This creates a new file. (action=8)
	 *
	 * @param	array		$cmds['data'] is the new filename. $cmds['target'] is the path where to create it
	 * @return	string		Returns the new filename upon success
	 */
	function func_newfile($cmds, $id)	{

		if (!$this->isInit) return FALSE;

		$extListTxt = $GLOBALS['TYPO3_CONF_VARS']['SYS']['textfile_ext'];

		$newName = tx_dam::file_makeCleanName($cmds['data']);
#		$newName = $this->cleanFileName($cmds['data']);

		if (empty($newName))	{ return; }


			// main log entry
		$this->log['cmd']['newfile'][$id] = array(
				'errors' => array(),
				'target_path' => $cmds['target'],
				'target_file' => $newName,
				);


		if (!$this->checkFileNameLen($newName))	{
			$this->writelog(5,1,124,'New name "%s" was too long (max %s characters)',array($newName,$this->maxInputNameLen), 'newfile', $id);
			return;
		}
			// Check the target dir
		if (!$theTarget = $this->is_directory($cmds['target'])) {
			$this->writelog(8,2,104,'Destination "%s" was not a directory',array($cmds['target']), 'newfile', $id);
			return;
		}

			// Fetches info about path, name, extention of $theTarget
		$fileInfo = t3lib_div::split_fileref($theTarget);

		if (!$this->actionPerms['newFile'])	{
			$this->writelog(8,1,103,'You are not allowed to create files!', '', 'newfile', $id);
			return;
		}

		$theNewFile = $theTarget.'/'.$newName;
		if (!$this->checkPathAgainstMounts($theNewFile))	{
			$this->writelog(8,1,102,'Destination path "%s" was not within your mountpoints!',array($theTarget.'/'), 'newfile', $id);
			return;
		}

		if (@file_exists($theNewFile))	{
			$this->writelog(8,1,101,'File "%s" existed already!',array(tx_dam::file_normalizePath($theNewFile)), 'newfile', $id);
			return;
		}

		$fI = t3lib_div::split_fileref($theNewFile);
		if (!$this->checkIfAllowed($fI['fileext'], $fileInfo['path'], $fI['file'])) {
			$this->writelog(8,1,106,'Fileextension "%s" was not allowed!',array($fI['fileext']), 'newfile', $id);
			return;
		}

		if (!t3lib_div::inList($extListTxt, $fI['fileext']))	{
			$this->writelog(8,1,107,'Fileextension "%s" is not a textfile format! (%s)',Array($fI['fileext'], $extListTxt), 'newfile', $id);
			return;
		}

		if (!t3lib_div::writeFile($theNewFile, $cmds['content']))	{
			$this->writelog(8,1,100,'File "%s" was not created! Write-permission problem in "%s"?',array($fI['file'], $theTarget), 'newfile', $id);
			return;
		}

		clearstatcache();
		$this->writelog(8,0,1,'File created: "%s"',array($fI['file']), 'newfile', $id);
		return $theNewFile;

	}


	/**
	 * Writing/update the content of textfiles (action=9)
	 *
	 * @param	array		$cmds['data'] is the new content. $cmds['target'] is the target (file or dir)
	 * @param	string		$id: ID of the item
	 * @return	boolean		Returns true on success
	 */
	function func_edit($cmds, $id)	{

		if (!$this->isInit) return FALSE;

		$theTarget = tx_dam::file_absolutePath($cmds['target']);

		$content = $cmds['content'] ? $cmds['content'] : $cmds['data'];

		$extList = $GLOBALS['TYPO3_CONF_VARS']['SYS']['textfile_ext'];

		$type = filetype($theTarget);
		if (!($type=='file'))	{		// $type MUST BE file
			$this->writelog(9,2,123,'Target "%s" was not a file!', array($theTarget), 'edit', $id);
			return;
		}

		$fileInfo = t3lib_div::split_fileref($theTarget);		// Fetches info about path, name, extention of $theTarget

		if (!$this->checkPathAgainstMounts($fileInfo['path']))	{
			$this->writelog(9,1,121,'Destination path "%s" was not within your mountpoints!',array($fileInfo['path']), 'edit', $id);
			return;
		}

		if (!$this->actionPerms['editFile'])	{
			$this->writelog(9,1,104,'You are not allowed to edit files!', '', 'edit', $id);
			return;
		}

		if (!$this->checkIfAllowed($fileInfo['fileext'], $fileInfo['path'], $fileInfo['file'])) {
			$this->writelog(9,1,103,'Fileextension "%s" was not allowed!', array($fileInfo['fileext']), 'edit', $id);
			return;
		}

		if (!t3lib_div::inList($extList, $fileInfo['fileext']))	{
			$this->writelog(9,1,102,'Fileextension "%s" is not a textfile format! (%s)', array($fileInfo['fileext'], $extList), 'edit', $id);
			return;
		}

		if (!t3lib_div::writeFile($theTarget,$content))	{
			$this->writelog(9,1,100,'File "%s" was not saved! Write-permission problem in "%s"?', array($theTarget,$fileInfo['path']), 'edit', $id);
			return;
		}


		clearstatcache();
		$this->writelog(9,0,1,'File saved to "%s", bytes: %s, MD5: %s ', array($fileInfo['file'],@filesize($theTarget),md5($content)), 'edit', $id);
		return true;
	}


	/**
	 * Renaming files or foldes (action=5)
	 *
	 * @param	array		$cmds['data'] is the new name. $cmds['target'] is the target (file or dir).
	 * @param	string		$id: ID of the item
	 * @return	string		Returns the new filename upon success
	 */
	function func_rename($cmds, $id)	{

		if (!$this->isInit) return FALSE;


		$theNewName = tx_dam::file_makeCleanName($cmds['data']);
#		$theNewName = $this->cleanFileName($cmds['data']);

		if (empty($theNewName))	{ return; }


			// main log entry
		$this->log['cmd']['rename'][$id] = array(
				'errors' => array(),
				'orig_filename' => $cmds['target'],
				'target_file' => $theNewName,
				);


		if (!$this->checkFileNameLen($theNewName))	{
			$this->writelog(5,1,124,'New name "%s" was too long (max %s characters)',array($theNewName,$this->maxInputNameLen), 'rename', $id);
			return;
		}

		$theTarget = $cmds['target'];
		$type = filetype($theTarget);

			// $type MUST BE file or dir
		if (!($type=='file' || $type=='dir'))	{
			$this->writelog(5,2,123,'Target "%s" was neither a directory nor a file!',array($theTarget), 'rename', $id);
			return;
		}

			// Fetches info about path, name, extention of $theTarget
		$fileInfo = t3lib_div::split_fileref($theTarget);

			// The name should be different from the current. And the filetype must be allowed
		if ($fileInfo['file']==$theNewName)	{
			$this->writelog(5,1,122,'Old and new name is the same (%s)',array($theNewName), 'rename', $id);
			return;
		}

		$theRenameName = $fileInfo['path'].$theNewName;

			// check mountpoints
		if (!$this->checkPathAgainstMounts($fileInfo['path']))	{
			$this->writelog(5,1,121,'Destination path "%s" was not within your mountpoints!',array($fileInfo['path']), 'rename', $id);
			return;
		}
			// check if dest exists
		if (@file_exists($theRenameName))	{
			$this->writelog(5,1,120,'Destination "%s" existed already!',array($theRenameName), 'rename', $id);
			return;
		}

		if ($type=='file')	{

				// user have permissions for action
			if (!$this->actionPerms['renameFile'])	{
				$this->writelog(5,1,102,'You are not allowed to rename files!','', 'rename', $id);
				return;
			}

			$fI = t3lib_div::split_fileref($theRenameName);

			if (!$this->checkIfAllowed($fI['fileext'], $fileInfo['path'], $fI['file'])) {
				$this->writelog(5,1,101,'Fileextension "%s" was not allowed!',array($fI['fileext']), 'rename', $id);
				return;
			}

			if (!@rename($theTarget, $theRenameName))	{
				$this->writelog(5,1,100,'File "%s" was not renamed! Write-permission problem in "%s"?',array($theTarget,$fileInfo['path']), 'rename', $id);
				return;
			}
			$this->writelog(5,0,1,'File renamed from "%s" to "%s"',array($fileInfo['file'],$theNewName), 'rename', $id);

				// update meta data
			if ($this->processMetaUpdate) {
				tx_dam::notify_fileMoved($theTarget, $theRenameName);
			}

		} elseif ($type=='dir')	{

				// user have permissions for action
			if (!$this->actionPerms['renameFolder'])	{
				$this->writelog(5,1,111,'You are not allowed to rename directories!','', 'rename', $id);
				return;
			}

			if (!@rename($theTarget, $theRenameName))	{
				$this->writelog(5,1,110,'Directory "%s" was not renamed! Write-permission problem in "%s"?',array($theTarget,$fileInfo['path']), 'rename', $id);
				return;
			}

			$this->writelog(5,0,2,'Directory renamed from "%s" to "%s"',array($fileInfo['file'],$theNewName), 'rename', $id);

				// update meta data
			if ($this->processMetaUpdate) {
				tx_dam::notify_fileMoved($theTarget, $theRenameName);
			}

		} else {
			return;
		}


			// add file to log entry
		$this->log['cmd']['rename'][$id]['target_'.$type] = $theRenameName;

		return $theRenameName;
	}





	/**
	 * Logging actions
	 *
	 * @param	integer		The action number. See the functions in the class for a hint. Eg. edit is '9', upload is '1' ...
	 * @param	integer		The severity: 0 = message, 1 = error, 2 = System Error, 3 = security notice (admin)
	 * @param	integer		This number is unique for every combination of $type and $action. This is the error-message number, which can later be used to translate error messages.
	 * @param	string		This is the default, raw error message in english
	 * @param	array		Array with special information that may go into $details by "%s" marks / sprintf() when the log is shown
	 * @param	string		$actionName: rename, delete, ....
	 * @param	string		$id: ID of the item
	 * @return	void
	 * @see	class.t3lib_userauthgroup.php
	 */
	function writeLog($action,$error,$details_nr,$details,$data, $actionName='', $id='')	{
		$type = 2;	// Type value for tce_file.php
		if (is_object($GLOBALS['BE_USER']))	{
			$GLOBALS['BE_USER']->writelog($type,$action,$error,$details_nr,$details,$data);
		}

		if($error AND $actionName AND !((string)($id)=='')) {
			$this->log['errors']++;
			$this->log['cmd'][$actionName][$id]['errors'][] = array(
				'error' => $error,
				'errDetail' => $details_nr,
				'msg' => sprintf($details, $data[0],$data[1],$data[2],$data[3],$data[4]), // this should be set localized if available
				);
		}
	}


	/**
	 * Check if an error occured while processing
	 *
	 * @return	integer		Number of errors
	 */
	function errors() {
		return $this->log['errors'];
	}


	/**
	 * Extract the last error message from the log
	 *
	 * @param	boolean		$getFullErrorLogEntry If set the full error log entry will be returned as array
	 * @return	mixed		error message or error array
	 */
	function getLastError($getFullErrorLogEntry=FALSE) {
		$error = '';

		if($this->log['errors']) {
			$log = end($this->log['cmd']); // get action name
			$log = end($log); // get id
			$error = end($log['errors']);
			if(!$getFullErrorLogEntry) {
				$error = $error['msg'];
			}
		}

		return $error;
	}
}

/**
 * @ignore
 */
class tx_dam_extFileFunctions extends ux_t3lib_extFileFunctions	{
}


/**
 * TCE (TYPO3 Core Engine) file-handling
 *
 * Handling the calling of methods in the file admin classes.
 * This is a modified version for usage with the DAM. Might be merged with the TYPO3 core implementation at some point.
 * Used by the command modules in mod_cmd/.
 *
 * @author	Kasper Skaarhoj <kasper@typo3.com>
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Core
 */
class tx_dam_tce_file {

		// Internal, static: GP var
	var $file;						// Array of file-operations.
	var $redirect;					// Redirect URL
	var $CB;						// Clipboard operations array
	var $overwriteExistingFiles;	// If existing files should be overridden.
	var $vC;						// VeriCode - a hash of server specific value and other things which identifies if a submission is OK. (see $BE_USER->veriCode())

		// Internal, dynamic:
	var $fileProcessor;				// File processor object: tx_dam_extFileFunctions

	var $error = FALSE;

	/**
	 * Registering Incoming data
	 *
	 * @param	array		$file: Command map. Default: t3lib_div::_GP('file')
	 * @return	string	$this->error
	 */
	function init($file='')	{
		global $FILEMOUNTS, $TYPO3_CONF_VARS, $BE_USER;

			// GP vars:
		$this->file = is_array($file) ? $file : t3lib_div::_GP('file');
		$this->redirect = t3lib_div::_GP('redirect');
		$this->CB = t3lib_div::_GP('CB');
		$this->overwriteExistingFiles = t3lib_div::_GP('overwriteExistingFiles');
		$this->vC = t3lib_div::_GP('vC');

			// Initializing:
		# $this->fileProcessor = t3lib_div::makeInstance('t3lib_extFileFunctions');
		$this->fileProcessor = t3lib_div::makeInstance('tx_dam_extFileFunctions');
		$this->fileProcessor->init($FILEMOUNTS, $TYPO3_CONF_VARS['BE']['fileExtensions']);
		$this->fileProcessor->init_actionPerms($BE_USER->user['fileoper_perms']);
		$this->fileProcessor->dontCheckForUnique = $this->overwriteExistingFiles ? 1 : 0;

			// Checking referer / executing:
		$refInfo = parse_url(t3lib_div::getIndpEnv('HTTP_REFERER'));
		$httpHost = t3lib_div::getIndpEnv('TYPO3_HOST_ONLY');
		if ($httpHost!=$refInfo['host'] && $this->vC!=$BE_USER->veriCode() && !$TYPO3_CONF_VARS['SYS']['doNotCheckReferer'])	{
			$this->fileProcessor->writeLog(0,2,1,'Referer host "%s" and server host "%s" did not match!',array($refInfo['host'],$httpHost));

			$this->error = 'referer';
		}

		return $this->error;

	}

	/**
	 * Allow files to be overwritten
	 *
	 * @param 	boolean 	$overwriteExistingFiles If set files will be overwritten during upload for example.
	 * @return	void
	 */
	function overwriteExistingFiles($overwriteExistingFiles) {
		$this->fileProcessor->dontCheckForUnique = $overwriteExistingFiles;
	}


	/**
	 * Initializing file processing commands
	 *
	 * @param	array		The $file array with the commands to execute. See "TYPO3 Core API" document
	 * @return	void
	 */
	function setCmdmap($fileCmds)	{
		$this->file = $fileCmds;
	}

	/**
	 * Initialize the Clipboard. This will fetch the data about files to paste/delete if such an action has been sent.
	 *
	 * @return	void
	 */
	function initClipboard()	{
		global $TYPO3_CONF_VARS;

		if (is_array($this->CB))	{
			require_once(PATH_t3lib.'class.t3lib_clipboard.php');
			$clipObj = t3lib_div::makeInstance('t3lib_clipboard');
			$clipObj->initializeClipboard();
			if ($this->CB['paste'])	{
				$clipObj->setCurrentPad($this->CB['pad']);
				$this->file = $clipObj->makePasteCmdArray_file($this->CB['paste'],$this->file);
			}
			if ($this->CB['delete'])	{
				$clipObj->setCurrentPad($this->CB['pad']);
				$this->file = $clipObj->makeDeleteCmdArray_file($this->file);
			}
		}
	}

	/**
	 * Performing the file admin action:
	 * Initializes the objects, setting permissions, sending data to object.
	 *
	 * @return	array $this->fileProcessor->log
	 */
	function process()	{
		if (!$this->error) {
			$this->fileProcessor->start($this->file);
			$this->fileProcessor->processData();
			// should happen in module: t3lib_BEfunc::getSetUpdateSignal('updateFolderTree');
		}
		return $this->fileProcessor->log;
	}

		/**
 * Check if an error occured while processing
 *
 * @return	integer		Number of errors
 */
	function errors() {
		return $this->fileProcessor->errors();
	}

	/**
	 * Extract the last error message from the log
	 *
	 * @param	boolean		$getFullErrorLogEntry If set the full error log entry will be returned as array
	 * @return	mixed		error message or error array
	 */
	function getLastError($getFullErrorLogEntry=FALSE) {
		return $this->fileProcessor->getLastError($getFullErrorLogEntry);
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_tce_file.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_tce_file.php']);
}

?>
