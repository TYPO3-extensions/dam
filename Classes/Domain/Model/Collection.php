<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Fabien Udriot <fabien.udriot@typo3.org>
 *  Lorenz Ulrich <lorenz.ulrich@visol.ch>
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 *
 *
 * @package dam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Dam_Domain_Model_Collection extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * Collection name
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $collectionName;

	/**
	 * Description
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Visibility
	 *
	 * @var integer
	 */
	protected $visibility;

	/**
	 * Assets
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_Dam_Domain_Model_Asset>
	 */
	protected $assets;

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Initializes all Tx_Extbase_Persistence_ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		/**
		 * Do not modify this method!
		 * It will be rewritten on each save in the extension builder
		 * You may modify the constructor of this class instead
		 */
		$this->assets = new Tx_Extbase_Persistence_ObjectStorage();
	}

	/**
	 * Returns the description
	 *
	 * @return string $description
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Sets the description
	 *
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Adds a Asset
	 *
	 * @param Tx_Dam_Domain_Model_Asset $asset
	 * @return void
	 */
	public function addAsset(Tx_Dam_Domain_Model_Asset $asset) {
		$this->assets->attach($asset);
	}

	/**
	 * Removes a Asset
	 *
	 * @param Tx_Dam_Domain_Model_Asset $assetToRemove The Asset to be removed
	 * @return void
	 */
	public function removeAsset(Tx_Dam_Domain_Model_Asset $assetToRemove) {
		$this->assets->detach($assetToRemove);
	}

	/**
	 * Returns the assets
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_Dam_Domain_Model_Asset> $assets
	 */
	public function getAssets() {
		return $this->assets;
	}

	/**
	 * Sets the assets
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_Dam_Domain_Model_Asset> $assets
	 * @return void
	 */
	public function setAssets(Tx_Extbase_Persistence_ObjectStorage $assets) {
		$this->assets = $assets;
	}

	/**
	 * Returns the collectionName
	 *
	 * @return string $collectionCollectionName
	 */
	public function getCollectionName() {
		return $this->collectionName;
	}

	/**
	 * Sets the collectionName
	 *
	 * @param string $collectionCollectionName
	 * @return void
	 */
	public function setCollectionName($collectionName) {
		$this->collectionName = $collectionName;
	}

	/**
	 * Returns the visibility
	 *
	 * @return integer $visibility
	 */
	public function getVisibility() {
		return $this->visibility;
	}

	/**
	 * Sets the visibility
	 *
	 * @param integer $visibility
	 * @return void
	 */
	public function setVisibility($visibility) {
		$this->visibility = $visibility;
	}

}
?>