<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 The DAM development team <typo-project-dam@lists.typo3.org>, TYPO3 Association
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
 * Test case for class Tx_Dam_Domain_Model_Collection.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage DAM
 *
 * @author The DAM development team <typo-project-dam@lists.typo3.org>
 */
class Tx_Dam_Domain_Model_CollectionTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_Dam_Domain_Model_Collection
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new Tx_Dam_Domain_Model_Collection();
	}

	public function tearDown() {
		unset($this->fixture);
	}
	
	
	/**
	 * @test
	 */
	public function getCollectionNameReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setCollectionNameForStringSetsCollectionName() { 
		$this->fixture->setCollectionName('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getCollectionName()
		);
	}
	
	/**
	 * @test
	 */
	public function getDescriptionReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setDescriptionForStringSetsDescription() { 
		$this->fixture->setDescription('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getDescription()
		);
	}
	
	/**
	 * @test
	 */
	public function getVisibilityReturnsInitialValueForInteger() { 
		$this->assertSame(
			0,
			$this->fixture->getVisibility()
		);
	}

	/**
	 * @test
	 */
	public function setVisibilityForIntegerSetsVisibility() { 
		$this->fixture->setVisibility(12);

		$this->assertSame(
			12,
			$this->fixture->getVisibility()
		);
	}
	
	/**
	 * @test
	 */
	public function getAssetsReturnsInitialValueForObjectStorageContainingTx_Dam_Domain_Model_Asset() { 
		$newObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->fixture->getAssets()
		);
	}

	/**
	 * @test
	 */
	public function setAssetsForObjectStorageContainingTx_Dam_Domain_Model_AssetSetsAssets() { 
		$asset = new Tx_Dam_Domain_Model_Asset();
		$objectStorageHoldingExactlyOneAssets = new Tx_Extbase_Persistence_ObjectStorage();
		$objectStorageHoldingExactlyOneAssets->attach($asset);
		$this->fixture->setAssets($objectStorageHoldingExactlyOneAssets);

		$this->assertSame(
			$objectStorageHoldingExactlyOneAssets,
			$this->fixture->getAssets()
		);
	}
	
	/**
	 * @test
	 */
	public function addAssetToObjectStorageHoldingAssets() {
		$asset = new Tx_Dam_Domain_Model_Asset();
		$objectStorageHoldingExactlyOneAsset = new Tx_Extbase_Persistence_ObjectStorage();
		$objectStorageHoldingExactlyOneAsset->attach($asset);
		$this->fixture->addAsset($asset);

		$this->assertEquals(
			$objectStorageHoldingExactlyOneAsset,
			$this->fixture->getAssets()
		);
	}

	/**
	 * @test
	 */
	public function removeAssetFromObjectStorageHoldingAssets() {
		$asset = new Tx_Dam_Domain_Model_Asset();
		$localObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
		$localObjectStorage->attach($asset);
		$localObjectStorage->detach($asset);
		$this->fixture->addAsset($asset);
		$this->fixture->removeAsset($asset);

		$this->assertEquals(
			$localObjectStorage,
			$this->fixture->getAssets()
		);
	}
	
}
?>