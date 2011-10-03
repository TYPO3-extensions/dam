<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Media development team
 <typo3-project-media@lists.typo3.org>, TYPO3 Association
 *  			
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
 * Test case for class Tx_Media_Domain_Model_MimeType.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage tx_media
 *
 * @author Media development team
 <typo3-project-media@lists.typo3.org>
 */
class Tx_Media_Domain_Model_MimeTypeTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_Media_Domain_Model_MimeType
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new Tx_Media_Domain_Model_MimeType();
	}

	public function tearDown() {
		unset($this->fixture);
	}
	
	
	/**
	 * @test
	 */
	public function getMimeTypeReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setMimeTypeForStringSetsMimeType() { 
		$this->fixture->setMimeType('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getMimeType()
		);
	}
	
	/**
	 * @test
	 */
	public function getMimeTypeNameReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setMimeTypeNameForStringSetsMimeTypeName() { 
		$this->fixture->setMimeTypeName('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getMimeTypeName()
		);
	}
	
	/**
	 * @test
	 */
	public function getAssetTypeReturnsInitialValueFortx_mediaType() {
		$this->assertEquals(
			NULL,
			$this->fixture->getAssetType()
		);
	}

	/**
	 * @test
	 */
	public function setAssetTypeFortx_mediaTypeSetsAssetType() {
		$dummyObject = new tx_mediaType();
		$this->fixture->setAssetType($dummyObject);

		$this->assertSame(
			$dummyObject,
			$this->fixture->getAssetType()
		);
	}
	
}
?>