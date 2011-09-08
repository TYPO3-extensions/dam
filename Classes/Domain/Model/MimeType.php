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
class Tx_Dam_Domain_Model_MimeType extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * MIME type
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $mimeType;

	/**
	 * MIME type name
	 *
	 * @var string
	 */
	protected $mimeTypeName;

	/**
	 * Asset type
	 *
	 * @var Tx_Dam_Domain_Model_AssetType
	 */
	protected $assetType;

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {

	}

	/**
	 * Returns the mimeType
	 *
	 * @return string $mimeType
	 */
	public function getMimeType() {
		return $this->mimeType;
	}

	/**
	 * Sets the mimeType
	 *
	 * @param string $mimeType
	 * @return void
	 */
	public function setMimeType($mimeType) {
		$this->mimeType = $mimeType;
	}

	/**
	 * Returns the mimeTypeName
	 *
	 * @return string $mimeTypeName
	 */
	public function getMimeTypeName() {
		return $this->mimeTypeName;
	}

	/**
	 * Sets the mimeTypeName
	 *
	 * @param string $mimeTypeName
	 * @return void
	 */
	public function setMimeTypeName($mimeTypeName) {
		$this->mimeTypeName = $mimeTypeName;
	}

	/**
	 * Returns the assetType
	 *
	 * @return Tx_Dam_Domain_Model_AssetType $assetType
	 */
	public function getAssetType() {
		return $this->assetType;
	}

	/**
	 * Sets the assetType
	 *
	 * @param Tx_Dam_Domain_Model_AssetType $assetType
	 * @return void
	 */
	public function setAssetType(Tx_Dam_Domain_Model_AssetType $assetType) {
		$this->assetType = $assetType;
	}

}
?>