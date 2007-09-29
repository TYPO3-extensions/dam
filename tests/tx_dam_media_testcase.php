<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2006 Rene Fritz (r.fritz@colorcube.de)
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * @author	2005 Rene Fritz <r.fritz@colorcube.de>
 */

require_once (PATH_txdam.'lib/class.tx_dam_db.php');

class tx_dam_media_testcase extends tx_t3unit_testcase {




	/**
	 *
	 */
	public function test_base () {

		$fixture = $this->getFixtureMedia();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$media = $fixture['media'];

		self::assertTrue ((boolean)($media->isIndexed));
		self::assertEquals ($media->isExistent, @is_file($filename));
		$title = $media->getMeta ('title');
		self::assertTrue (!is_null($title), 'No title get from object');
	}


	/***************************************
	 *
	 *	 Get Meta data
	 *
	 ***************************************/


	/**
	 * media->getTypeAll()
	 */
	public function test_getTypeAll () {

		$fixture = $this->getFixtureMedia();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$media = $fixture['media'];

		$typeFile = tx_dam::file_getType ($filename);
		$type = $media->getTypeAll ();

		self::assertEquals ($type['file_type'], $typeFile['file_type'], 'Wrong file type: '.$type['file_type'].' ('.$typeFile['file_type'].')');
		self::assertEquals ($type['file_mime_type'], $typeFile['file_mime_type'], 'Wrong mime type: '.$type['file_mime_type'].' ('.$typeFile['file_mime_type'].')');
		self::assertEquals ($type['file_mime_subtype'], $typeFile['file_mime_subtype'], 'Wrong mime sub type: '.$type['file_mime_subtype'].' ('.$typeFile['file_mime_subtype'].')');
	}

	/**
	 * media->getType()
	 */
	public function test_getType () {

		$fixture = $this->getFixtureMedia();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$media = $fixture['media'];

		$typeFile = tx_dam::file_getType ($filename);
		$type = $media->getType ();

		self::assertEquals ($type, $typeFile['file_type'], 'Wrong file type: '.$type.' ('.$typeFile['file_type'].')');
	}

	/**
	 * media->getMeta()
	 */
	public function test_getMeta () {

		$fixture = $this->getFixtureMedia();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$media = $fixture['media'];

		$title = $media->getMeta ('title');
		self::assertEquals ($title, $meta['title'], 'Wrong data: '.$title.' ('.$meta['title'].')');

		$media->setMeta ('title', 'XXX');
		$title = $media->getMeta ('title');
		self::assertEquals ($title, 'XXX', 'Wrong data: '.$title.' (XXX)');
	}

	/**
	 * media->getDescription()
	 */
	public function test_getDescription () {

		$fixture = $this->getFixtureMedia();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$media = $fixture['media'];

		$title = $media->getDescription ('title');
		self::assertEquals ($title, $meta['title'], 'Wrong data: '.$title.' ('.$meta['title'].')');

		$media->setMeta ('title', 'XXX');
		$title = $media->getDescription ('title');
		self::assertEquals ($title, 'XXX', 'Wrong data: '.$title.' (XXX)');
	}

	/**
	 * media->getDownloadName()
	 */
	public function test_getDownloadName () {

		$fixture = $this->getFixtureMedia();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$media = $fixture['media'];

		$title = $media->getDownloadName ();
		self::assertEquals ($title, $meta['file_dl_name'], 'Wrong data: '.$title.' ('.$meta['file_dl_name'].')');

		$media->setMeta ('file_dl_name', 'XXX');
		$title = $media->getDownloadName ();
		self::assertEquals ($title, 'XXX', 'Wrong data: '.$title.' (XXX)');
	}

	/**
	 * media->getPathAbsolute()
	 */
	public function test_getPathAbsolute () {

		$fixture = $this->getFixtureMedia();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$media = $fixture['media'];

		$filepath = tx_dam::file_absolutePath ($filename);
		$path = $media->getPathAbsolute ();
		self::assertEquals ($path, $filepath, 'File path differs: '.$path.' ('.$filepath.')');
	}




	/***************************************
	 *
	 *	 Set Meta data
	 *
	 ***************************************/


	/**
	 * media->setMeta()
	 */
	public function test_setMeta () {

		$fixture = $this->getFixtureMedia();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$media = $fixture['media'];

		$media->setMeta ('title', 'XXX');
		$title = $media->getMeta ('title');
		self::assertEquals ($title, 'XXX', 'Wrong data: '.$title.' (XXX)');
	}


	/***************************************
	 *
	 *	 Fixtures
	 *
	 ***************************************/



	/**
	 *
	 * @return mixed Is false when no indexed file available or: array('meta' => $row, 'filename' => $filename)
	 */
	public function getFixtureMedia () {
		if ($fixture = $this->getFixtureIndexedFilename()) {
			$fixture['media'] = tx_dam::media_getByUid($fixture['meta']['uid']);
		}
		return $fixture;
	}


	/**
	 * Returns an fixture which is a random already indexed file.
	 * @return mixed Is false when no indexed file available or: array('meta' => $row, 'filename' => $filename)
	 */
	protected function getFixtureIndexedFilename() {
		$select_fields = '*'; #uid,file_name,file_path,file_hash';

		$where = array();
		$where['deleted'] = 'deleted=0';
		$where['pidList'] = 'pid IN ('.tx_dam_db::getPidList().')';
		$where['file_hash'] = 'file_hash';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
												$select_fields,
												'tx_dam',
												implode(' AND ', $where),
												'',
												'RAND()',
												50
											);

		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			if(is_file($filename = tx_dam::file_absolutePath ($row['file_path'].$row['file_name']))) {
				return array('meta' => $row, 'filename' => $filename);
			}
		}

		return false;
	}



}

//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_media_testcase.php'])	{
//	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_media_testcase.php']);
//}
?>