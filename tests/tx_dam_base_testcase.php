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



require_once (PATH_txdam.'lib/class.tx_dam.php');


/**
 * @author	2005 Rene Fritz <r.fritz@colorcube.de>
 */
class tx_dam_base_testcase extends tx_t3unit_testcase {




	/***************************************
	 *
	 *	 file_
	 *
	 ***************************************/



	/**
	 * tx_dam::file_isIndexed()
	 */
	public function test_file_isIndexed () {

		$fixture = $this->getFixtureIndexedFilename();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$uid = tx_dam::file_isIndexed($filename);
		self::assertEquals ($meta['uid'], $uid, 'File index not found: '.$filename);
		$uid = tx_dam::file_isIndexed($meta);
		self::assertEquals ($meta['uid'], $uid, 'File index not found: '.$filename);

		$filename = $this->getFixtureFilename();
		$uid = tx_dam::file_isIndexed($filename);
		self::assertFalse ($uid, 'File index found, but shouldn\'t');
	}


	/**
	 * tx_dam::file_compileInfo()
	 */
	public function test_file_compileInfo () {

		$filepath = $this->getFixtureFilename();
		$filename = basename($filepath);

		$ignoreExistence=true;
		for ($index = 0; $index < 2; $index++) {
			$fileinfo = tx_dam::file_compileInfo ($filepath, $ignoreExistence=false);

			self::assertEquals ($fileinfo['file_name'], $filename, 'Wrong file name: '.$fileinfo['file_name'].' ('.$filename.')');
			$testpath = tx_dam::path_makeAbsolute($fileinfo['file_path']).$fileinfo['file_name'];
			self::assertEquals ($testpath, $filepath, 'File path differs: '.$testpath.' ('.$filepath.')');
			$testpath = $fileinfo['file_path_absolute'].$fileinfo['file_name'];
			self::assertEquals ($testpath, $filepath, 'File path differs: '.$testpath.' ('.$filepath.')');

			$ignoreExistence=false;
		}

		self::assertTrue ($fileinfo['file_mtime']>0, 'No file_mtime');
		self::assertTrue ($fileinfo['file_ctime']>0, 'No file_ctime');
		self::assertTrue ($fileinfo['file_inode']>0, 'No file_inode');
		self::assertEquals ($fileinfo['file_size'], 2108, 'Wrong file size: '.$fileinfo['file_size'].' (2108)');
	}


	/**
	 * tx_dam::file_getType()
	 */
	public function test_file_getType () {

		$filepath = $this->getFixtureFilename();
		$type = tx_dam::file_getType ($filepath);

		self::assertEquals ($type['file_type'], 'txt', 'Wrong file type: '.$type['file_type'].' (txt)');
		self::assertEquals ($type['file_mime_type'], 'text', 'Wrong mime type: '.$type['file_mime_type'].' (text)');
		self::assertEquals ($type['file_mime_subtype'], 'plain', 'Wrong mime sub type: '.$type['file_mime_subtype'].' (plain)');

// TODO file without suffix
	}

	/**
	 * tx_dam::file_getType()
	 */
	public function test_file_getType_indexed () {

		$fixture = $this->getFixtureIndexedFilename();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];
		$type = tx_dam::file_getType ($filename);

		self::assertEquals ($type['file_type'], $meta['file_type'], 'Wrong file type: '.$type['file_type'].' ('.$meta['file_type'].')');
		self::assertEquals ($type['file_mime_type'], $meta['file_mime_type'], 'Wrong mime type: '.$type['file_mime_type'].' ('.$meta['file_mime_type'].')');
		self::assertEquals ($type['file_mime_subtype'], $meta['file_mime_subtype'], 'Wrong mime sub type: '.$type['file_mime_subtype'].' ('.$meta['file_mime_subtype'].')');


	}

	/**
	 * tx_dam::file_calcHash()
	 */
	public function test_file_calcHash() {

		$filename = $this->getFixtureFilename();

		$hash = array();

		$hash['file_calcHash'] = tx_dam::file_calcHash($filename);
		$compareHash = '4e231415019b6593f0266b99b7704bc2';


		if (function_exists('md5_file')) {
			$hash['md5_file'] = @md5_file($filename);
		}

		$cmd = t3lib_exec::getCommand('md5sum');
		$output = array();
		$retval = '';
		exec($cmd.' -b "'.escapeshellcmd($filename).'"', $output, $retval);
		$output = explode(' ',$output[0]);
		$match = array();
		if (preg_match('#[0-9a-f]{32}#', $output[0], $match)) {
			$hash['md5sum'] = $match[0];
		}

		$file_string = t3lib_div::getUrl($filename);
		$hash['md5'] = md5($file_string);


		foreach ($hash as $key => $value)  {
			self::assertEquals ($compareHash, $value, 'Wrong hash: '.$value.' ('.$key.')');
		}

	}


	/**
	 * tx_dam::file_absolutePath()
	 */
	public function test_file_absolutePath () {

		$filepath = $this->getFixtureFilename();
		$filename = basename($filepath);
		$testpath = dirname($filepath).'/';


		$path = tx_dam::path_makeClean ($testpath);
		$path = tx_dam::path_makeRelative ($path);
		$path = tx_dam::file_absolutePath ($path.$filename);

		self::assertEquals ($path, $filepath, 'File path differs: '.$path.' ('.$filepath.')');


		$filepath = $this->getFixtureFilename();
		$fileinfo = tx_dam::file_compileInfo ($filepath, $ignoreExistence=false);

		$path = tx_dam::file_absolutePath ($fileinfo);

		self::assertEquals ($path, $filepath, 'File path differs: '.$path.' ('.$filepath.')');
	}



	/**
	 * tx_dam::file_relativeSitePath()
	 */
	public function test_file_relativeSitePath () {

		$filepath = $this->getFixtureFilename();
		$filename = basename($filepath);
		$testpath = dirname($filepath).'/';

		$relPath = t3lib_extmgm::siteRelPath('dam').'tests/fixtures/'.$filename;

		$path = tx_dam::path_makeClean ($testpath);
		$path = tx_dam::path_makeRelative ($path);
		$path = tx_dam::file_relativeSitePath ($path.$filename);

		self::assertEquals ($path, $relPath, 'File path differs: '.$path.' ('.$relPath.')');


		$filepath = $this->getFixtureFilename();
		$fileinfo = tx_dam::file_compileInfo ($filepath, $ignoreExistence=false);

		$path = tx_dam::file_relativeSitePath ($fileinfo);

		self::assertEquals ($path, $relPath, 'File path differs: '.$path.' ('.$relPath.')');
	}


	/***************************************
	 *
	 *	 path_
	 *
	 ***************************************/


	/**
	 * tx_dam::path_makeXXX()
	 */
	public function test_path_makeXXX () {

		$filepath = $this->getFixtureFilename();
		$filename = basename($filepath);
		$testpath = dirname($filepath).'/';


		$path = tx_dam::path_makeClean ($testpath);
		$path = tx_dam::path_makeRelative ($path);
		$path = tx_dam::path_makeAbsolute ($path);

		self::assertEquals ($path, $testpath, 'File path differs: '.$path.' ('.$testpath.')');


		$path = tx_dam::path_makeClean ($testpath);
		$path = tx_dam::path_makeRelative ($path);
		$path = tx_dam::path_makeClean ($testpath);
		$path = tx_dam::path_makeRelative ($path);
		$path = tx_dam::path_makeRelative ($path);
		$path = tx_dam::path_makeAbsolute ($path);
		$path = tx_dam::path_makeClean ($testpath);
		$path = tx_dam::path_makeRelative ($path);
		$path = tx_dam::path_makeAbsolute ($path);

		self::assertEquals ($path, $testpath, 'Path differs: '.$path.' ('.$testpath.')');
// TODO /./ path should be resolved in t3lib_div::resolveBackPath
		$testpath = '/aaa/../bbb/./ccc//ddd';
		$testpathClean = '/bbb/./ccc/ddd/';
		$path = tx_dam::path_makeClean ($testpath);
		$path = tx_dam::path_makeRelative ($path);
		$path = tx_dam::path_makeAbsolute ($path);
		self::assertEquals ($path, $testpathClean, 'Path differs: '.$path.' ('.$testpathClean.')');


		$testpath = PATH_site.'/aaa/../bbb/./ccc//ddd';
		$testpathClean = 'bbb/./ccc/ddd/';
		$path = tx_dam::path_makeClean ($testpath);
		$path = tx_dam::path_makeRelative ($path);
		self::assertEquals ($path, $testpathClean, 'Path differs: '.$path.' ('.$testpathClean.')');

	}


	/**
	 * tx_dam::path_compileInfo()
	 */
	public function test_path_compileInfo () {

		$filepath = $this->getFixtureFilename();
		$filename = basename($filepath);
		$testpath = dirname($filepath).'/';

		$pathInfo = tx_dam::path_compileInfo($testpath);

		self::assertTrue (is_array($pathInfo), 'Path not found: '.$testpath);
		self::assertTrue ((boolean)$pathInfo['dir_readable'], 'Path not readable: '.$testpath);
		self::assertFALSE ((boolean)$pathInfo['mount_id'], 'Impossible mount found: '.$pathInfo['mount_path'].' ('.$testpath.')');

		$pathInfo = tx_dam::path_compileInfo(PATH_site.'fileadmin/');

		self::assertTrue (is_array($pathInfo), 'Path not found: '.$testpath);
		self::assertTrue ((boolean)$pathInfo['dir_readable'], 'Path not readable: '.$testpath);
		self::assertTrue ((boolean)$pathInfo['mount_id'], 'No mount found: '.$pathInfo['mount_path'].' ('.$testpath.')');


	}

	/***************************************
	 *
	 *	 meta_
	 *
	 ***************************************/


	/**
	 * tx_dam::meta_getDataForFile()
	 */
	public function test_meta_getDataForFile () {
		$fixture = $this->getFixtureIndexedFilename();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];

		$data = tx_dam::meta_getDataForFile($filename);
		self::assertEquals ($data['uid'], $meta['uid'], 'Wrong index for '.$filename);
	}

	/**
	 * tx_dam::meta_getDataByUid()
	 */
	public function test_meta_getDataByUid () {
		$fixture = $this->getFixtureIndexedFilename();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];

		$data = tx_dam::meta_getDataByUid($meta['uid']);
		self::assertEquals ($data['file_name'], $meta['file_name'], 'Wrong index for '.$filename);
	}

	/**
	 * tx_dam::meta_getDataByHash()
	 */
	public function test_meta_getDataByHash () {
		$fixture = $this->getFixtureIndexedFilename();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];

		$data = tx_dam::meta_getDataByHash($meta['file_hash']);
		self::assertEquals ($data[$meta['uid']]['uid'], $meta['uid'], 'Wrong index for '.$filename);
	}

	/**
	 * tx_dam::meta_findDataForFile()
	 */
	public function test_meta_findDataForFile () {
		$fixture = $this->getFixtureIndexedFilename();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];

		$data = tx_dam::meta_findDataForFile($filename);
		self::assertEquals ($data[$meta['uid']]['uid'], $meta['uid'], 'Wrong index for '.$filename);
// TODO test hash
	}


	/***************************************
	 *
	 *	 media_
	 *
	 ***************************************/

	/**
	 * tx_dam::media_getForFile()
	 */
	public function test_media_getForFile () {
		$fixture = $this->getFixtureIndexedFilename();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];

		$media = tx_dam::media_getForFile($meta);

		self::assertTrue (is_object($media), 'Object not created for '.$filename);
		self::assertTrue ($media->isIndexed);
	}

	/**
	 * tx_dam::media_getByUid()
	 */
	public function test_media_getByUid () {
		$fixture = $this->getFixtureIndexedFilename();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];

		$media = tx_dam::media_getByUid($meta['uid']);

		self::assertTrue (is_object($media), 'Object not created for '.$filename);
		self::assertTrue ($media->isIndexed);
	}

	/**
	 * tx_dam::media_getByHash()
	 */
	public function test_media_getByHash () {
		$fixture = $this->getFixtureIndexedFilename();
		$filename = $fixture['filename'];
		$meta = $fixture['meta'];

		$mediaList = tx_dam::media_getByHash($meta['file_hash']);

		self::assertTrue (is_array($mediaList), 'Is not Object array for '.$filename);
		$media = current($mediaList);
		self::assertTrue (is_object($media), 'Object not created for '.$filename);
		self::assertTrue ($media->isIndexed);
	}




	/***************************************
	 *
	 *	 Fixtures
	 *
	 ***************************************/


	/**
	 * Returns an fixture from an example file which contains some utf-8 characters.
	 */
	protected function getFixtureContent() {
		$content = file_get_contents($this->getFixtureFilename());
		return $content;
	}

	/**
	 * Returns an fixture from an example file which contains some utf-8 characters.
	 */
	protected function getFixtureFilename() {
		$testFile = PATH_txdam.'tests/fixtures/example-content.txt';
		return $testFile;
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

//if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_base_testcase.php'])	{
//	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/tests/tx_dam_base_testcase.php']);
//}
?>