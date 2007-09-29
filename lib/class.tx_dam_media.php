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
 *   92: class tx_dam_media
 *
 *              SECTION: Initialization
 *  186:     function tx_dam_media ()
 *  201:     function __construct($file = null, $hash=false, $autoIndex=true)
 *  217:     function fetchIndexFromFilename ($file, $hash=false, $autoIndex=true)
 *  237:     function fetchIndexFromMetaUID ($uid)
 *  253:     function fetchFileinfo ($fileInfo=NULL, $ignoreExistence=true)
 *
 *              SECTION: Meta data
 *  292:     function fetchFullIndex ($uid=NULL)
 *  309:     function setMetaData ($meta)
 *
 *              SECTION: Get Meta data
 *  345:     function getTypeAll ()
 *  364:     function getType ()
 *  381:     function getMeta ($field)
 *  403:     function getDescription ($field)
 *  424:     function getDownloadName ()
 *  435:     function getPathAbsolute ()
 *  445:     function getPathForSite ()
 *
 *              SECTION: Set Meta data
 *  473:     function setMeta ($field, $value)
 *
 *              SECTION: Update DB meta data
 *  495:     function updateIndex ()
 *  506:     function updateIndexFileinfo ()
 *  521:     function updateAuto ()
 *  531:     function updateHash ()
 *
 *              SECTION: Indexing
 *  551:     function index ()
 *  558:     function reindex ()
 *  565:     function autoIndex()
 *
 * TOTAL FUNCTIONS: 22
 * (This index is automatically created/updated by the script "update-class-index")
 *
 */


require_once(PATH_txdam.'lib/class.tx_dam.php');
require_once(PATH_t3lib.'class.t3lib_basicfilefunc.php');


/**
 * DAM media object
 *
 * This is an object representing a file/media item.
 * This is the prefered method to access media items from the DAM.
 *
 * @author	Rene Fritz <r.fritz@colorcube.de>
 * @package DAM-Core
 * @subpackage Lib
 */
class tx_dam_media {

	/**
	 * Holds the meta data record from DB
	 */
	var $meta = NULL;

	/**
	 * Holds the meta data which was changed/set/updated.
	 */
	var $metaUpdated = array();

	/**
	 * The file info like ctime, mtime.
	 * Mainly the same format like in the meta array
	 */
	var $info = NULL;

	/**
	 * filename (basename)
	 */
	var $filename = NULL;

	/**
	 * Path to file in normalized format which is relative if possible and is like the stored path in the meta data.
	 */
	var $pathNormalized = NULL;

	/**
	 * The absolute path to the file.
	 */
	var $pathAbsolute = NULL;



	/**
	 * If the file is already indexed or not.
	 */
	var $isIndexed = NULL;

	/**
	 * If the file is automatically indexed (sometimes).
	 */
	var $isAutoIndexed = NULL;

/**
 * If the file meta data was automatically updated for some reasons.
 */
var $isAutoUpdated = NULL;

	/**
	 * If the file is JUST automatically indexed.
	 */
	var $isJustAutoIndexed = NULL;

	/**
	 * If the file exists
	 */
	var $isExistent = NULL;



	/**
	 * If set the file will be autoindexed if needed.
	 */
	var $doAutoIndexing = true;

	/**
	 * If set the file info will be updated in the index automatically.
	 */
	var $doAutoFileinfoUpdate = true;

/**
 * If set the meta data will be updated automatically if needed.
 */
var $doAutoMetaUpdate = false;


// TODO what todo with non-existing files??

	/***************************************
	 *
	 *	 Initialization
	 *
	 ***************************************/


	/**
	 * Initialize the object
	 * PHP4 constructor
	 *
	 * @return	void
	 * @see __construct()
	 */
	function tx_dam_media () {
			// call php5 constructor
		$args = func_get_args();
		return call_user_func_array(array(&$this, '__construct'), $args);
	}


	/**
	 * Initialize the object by a given filename
	 *
	 * @param	string		$file Filepath to file. Should probably be absolute. If not set the object is undefined but can be initialized by UID or meta data record with initFrom* methods.
	 * @param	string		$hash If set the hash value can be used to identify the file if the file name was not found. That can happen if the file was renamed or moved without index update.
	 * @param	boolean		$autoIndex If set (default) the file will be indexed automatically.
	 * @return	void
	 */
	function __construct($file = null, $hash=false, $autoIndex=true) {
		if($file) {
			$this->fetchIndexFromFilename ($file, $hash, $autoIndex);
			return $this->isExistent;
		}
	}


	/**
	 * Initialize the object by a given filename
	 *
	 * @param	string		$file Filepath to file. Should probably be absolute.
	 * @param	string		$hash If set the hash value can be used to identify the file if the file name was not found. That can happen if the file was renamed or moved without index update.
	 * @param	boolean		$autoIndex If set (default) the file will be indexed automatically.
	 * @return	void
	 */
	function fetchIndexFromFilename ($file, $hash=false, $autoIndex=true) {

		$this->fetchFileinfo($file);
		if ($this->isExistent) {
			if ($row = tx_dam::meta_getDataForFile($this->info, '', true)) {
				$this->setMetaData ($row);
			} elseif ($autoIndex) {
// TODO search for hash
				$this->autoIndex();
			}
		}
	}


	/**
	 * Init the object by the UID of the meta data record
	 *
	 * @param	integer		$uid UID of the wanted meta data record.
	 * @return	void
	 */
	function fetchIndexFromMetaUID ($uid) {
		if ($row = tx_dam::meta_getDataByUid($uid)) {
			$this->setMetaData ($row);
		}
	}



	/**
	 * Collects physical informations about the file.
	 * This means the file must be existent.
	 *
	 * @param	mixed		$fileInfo Is a file path or an array containing a file info from tx_dam::file_compileInfo().  Default from getPathAbsolute().
	 * @param	boolean		$ignoreExistence The existence of the file will not be checked and only the file path will be splitted.
	 * @return	boolean		If the file exists and the info could be fetched.
	 */
	function fetchFileinfo ($fileInfo=NULL, $ignoreExistence=true) {
		$this->isExistent = false;

		if (is_array($fileInfo) AND $fileInfo['file_name'] AND $fileInfo['file_path_absolute']) {
			$info = $fileInfo;

		} else {
			$fileInfo = $fileInfo ? $fileInfo : $this->getPathAbsolute();
			$info = tx_dam::file_compileInfo ($fileInfo, $ignoreExistence);
		}

		if (is_array($info)) {
			$this->info = $info;
			$this->name = $this->info['file_name'];
			$this->pathNormalized = $this->info['file_path'];
			$this->pathAbsolute = $this->info['file_path_absolute'];
			$this->isExistent = $this->info['__exists'];
		}

		return $this->isExistent;
	}




	/***************************************
	 *
	 *	 Meta data
	 *
	 ***************************************/


	/**
	 * Reads all data from the index.
	 * By default only the limited amount of fields called "info fields" are fetched from the index.
	 *
	 * @param	integer		$uid Optional UID of the wanted meta data record. Default: $this->meta['uid']
	 * @return	void
	 */
	function fetchFullIndex ($uid=NULL) {
		$uid = $uid ? $uid : $this->meta['uid'];
		if ($uid) {
			if ($row = tx_dam::meta_getDataByUid($this->meta['uid'], '*')) {
				$this->setMetaData ($row);
			}
		}
	}


	/**
	 * Init the object by a passed meta data record array.
	 * It is assumed that the data is really from the index and therefore the file "isIndexed".
	 *
	 * @param	array		$uid $meta Array of a meta data record
	 * @return	void
	 */
	function setMetaData ($meta) {
		if (is_array($meta)) {
			$this->meta = $meta;
			$this->isIndexed = is_array($meta);

			$this->name = $this->meta['file_name'];
			$this->pathNormalized = $this->meta['file_path'];
			$this->pathAbsolute = tx_dam::path_makeAbsolute($this->meta['file_path']);
			if ($this->isExistent==NULL) {
				$info = $this->fetchFileinfo();
			}
		}
	}





	/***************************************
	 *
	 *	 Get Meta data
	 *
	 ***************************************/


	/**
	 * Returns an array which describes the type of a file.
	 *
	 * example:
	 * $mimeType = array();
	 * $mimeType['file_mime_type'] = 'audio';
	 * $mimeType['file_mime_subtype'] = 'x-mpeg';
	 * $mimeType['file_type'] = 'mp3';
	 *
	 * @return	array		Describes the type of a file
	 */
	function getTypeAll () {
		$mimeType = false;

		if ($this->meta) {
			$mimeType = array();
			$mimeType['file_mime_type'] = $this->meta['file_mime_type'];
			$mimeType['file_mime_subtype'] = $this->meta['file_mime_subtype'];
			$mimeType['file_type'] = $this->meta['file_type'];
		}

		return $mimeType;
	}


	/**
	 * Returns just the file type like mp3, txt, pdf.
	 *
	 * @return	string		The file type like mp3, txt, pdf.
	 */
	function getType () {
		$type = false;

		if ($this->meta) {
			$type = $this->meta['file_type'];
		}

		return $type;
	}


	/**
	 * Returns raw meta data from the database record.
	 *
	 * @param	string		$field Field name to get meta data from. These are database fields.
	 * @return	mixed		Meta data value.
	 */
	function getMeta ($field) {
		$value = false;
		if (isset($this->metaUpdated[$field])) {
			$value = $this->metaUpdated[$field];
		} else {
			$value = $this->meta[$field];
		}
		return $value;
	}




	/**
	 * Returns meta data which might be processed.
	 * That means some fields are known and will be substituted by other fields values if the requested field is empty.
	 * Example if you request a caption but the field is empty you will get the description field value.
	 * This function will be improved by time and the processing will be configurable.
	 *
	 * @param	mixed		$field Field name to get meta data from. These are database fields.
	 * @return	mixed		Meta data value.
	 */
	function getDescription ($field) {
		switch ($field) {
			case value:
// TODO
				break;

			default:
				$value = $this->getMeta($field);
				break;
		}
		return $value;
	}


	/**
	 * Returns the download name for the file.
	 * This don't have to be the real file name. For usage with "Content-Disposition" HTTP header.
	 * header("Content-Disposition: attachment; filename=$downloadFilename");
	 *
	 * @return	string		File name for download.
	 */
	function getDownloadName () {
		$dlName = $this->getMeta('file_dl_name');
		return $dlName ? $dlName : $this->name;
	}


	/**
	 * Returns a file path with absolute path.
	 *
	 * @return	string		Absolute path to file
	 */
	function getPathAbsolute () {
		return $this->pathAbsolute.$this->name;
	}


	/**
	 * Returns a file path relative to PATH_site or getIndpEnv('TYPO3_SITE_URL').
	 *
	 * @return	string		Relative path to file
	 */
	function getPathForSite () {
			// for now path_makeRelative() do what we want but that may change
		$file_path = tx_dam::path_makeRelative ($this->pathAbsolute, PATH_site);

		return $file_path.$this->name;
	}







	/***************************************
	 *
	 *	 Set Meta data
	 *
	 ***************************************/


	/**
	 * Raw meta data can be set for database storage.
	 * The data will be written when update() will be called.
	 *
	 * @param	string		$field Field name to get meta data from. These are database fields.
	 * @param	mixed		$value Meta data value.
	 * @return	void
	 */
	function setMeta ($field, $value) {
// TODO check read only
		$this->metaUpdated[$field] = $value;
	}






	/***************************************
	 *
	 *	 Update DB meta data
	 *
	 ***************************************/


	/**
	 * Updates the index when meta data was changed or the fileinfo is not in sync.
	 *
	 * @return	void
	 */
	function updateIndex () {
		if (count($this->metaUpdated)) {
// TODO write index    $this->fileinfo + $this->metaUpdated;
		}
	}

	/**
	 * Updates the fileinfo in the index if needed.
	 *
	 * @return	void
	 */
	function updateIndexFileinfo () {
// TODO write index    $this->fileinfo + $this->metaUpdated;
	}


	/**
	 * Update/Cleanup the index for the object.
	 * This can be following:
	 * - update index
	 * - auto index for new file
	 * - reconnect index with moved/renamed file
	 * - reconnect file with removed (auto deleted) index entry: recover
	 *
	 * @return	void
	 */
	function updateAuto () {
	}


	/**
	 * Calculates a hash value from a file and updates the database.
	 * The hash is used to identify file changes.
	 *
	 * @return	void
	 */
	function updateHash () {
		if ($hash = tx_dam::file_calcHash(getPathAbsolute())) {
			$this->metaUpdated['file_hash'] = $hash;
		}
		$this->updateIndex();
	}




	/***************************************
	 *
	 *	 Indexing
	 *
	 ***************************************/


	/**
	 *
	 */
	function index () {
	}


	/**
	 *
	 */
	function reindex () {
	}


	/**
	 *
	 */
	function autoIndex() {
	}






}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_media.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/lib/class.tx_dam_media.php']);
}
?>