<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 
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
class Tx_Dam_Domain_Model_Asset extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * Title
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $title;

	/**
	 * Description
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Keywords
	 *
	 * @var string
	 */
	protected $keywords;

	/**
	 * MIME type
	 *
	 * @var string
	 */
	protected $mimeType;

	/**
	 * File extension
	 *
	 * @var string
	 */
	protected $extension;

	/**
	 * File creation date
	 *
	 * @var DateTime
	 */
	protected $creationDate;

	/**
	 * File modification date
	 *
	 * @var DateTime
	 */
	protected $modificationDate;

	/**
	 * Creator tool
	 *
	 * @var string
	 */
	protected $creatorTool;

	/**
	 * Download name
	 *
	 * @var string
	 */
	protected $downloadName;

	/**
	 * Identifier
	 *
	 * @var string
	 */
	protected $identifier;

	/**
	 * Creator
	 *
	 * @var string
	 */
	protected $creator;

	/**
	 * Source
	 *
	 * @var string
	 */
	protected $source;

	/**
	 * Alternative title
	 *
	 * @var string
	 */
	protected $alternative;

	/**
	 * Caption
	 *
	 * @var string
	 */
	protected $caption;

	/**
	 * FAL
	 *
	 * @var Tx_Dam_Domain_Model_File
	 */
	protected $fal;

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
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Returns the title
	 *
	 * @return string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the title
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Returns the identifier
	 *
	 * @return string $identifier
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * Sets the identifier
	 *
	 * @param string $identifier
	 * @return void
	 */
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
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
	 * Returns the extension
	 *
	 * @return string $extension
	 */
	public function getExtension() {
		return $this->extension;
	}

	/**
	 * Sets the extension
	 *
	 * @param string $extension
	 * @return void
	 */
	public function setExtension($extension) {
		$this->extension = $extension;
	}

	/**
	 * Returns the creator
	 *
	 * @return string $creator
	 */
	public function getCreator() {
		return $this->creator;
	}

	/**
	 * Sets the creator
	 *
	 * @param string $creator
	 * @return void
	 */
	public function setCreator($creator) {
		$this->creator = $creator;
	}

	/**
	 * Returns the keywords
	 *
	 * @return string $keywords
	 */
	public function getKeywords() {
		return $this->keywords;
	}

	/**
	 * Sets the keywords
	 *
	 * @param string $keywords
	 * @return void
	 */
	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}

	/**
	 * Initializes all Tx_Extbase_Persistence_ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		// empty
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
	 * Returns the creationDate
	 *
	 * @return DateTime $creationDate
	 */
	public function getCreationDate() {
		return $this->creationDate;
	}

	/**
	 * Sets the creationDate
	 *
	 * @param DateTime $creationDate
	 * @return void
	 */
	public function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
	}

	/**
	 * Returns the modificationDate
	 *
	 * @return DateTime $modificationDate
	 */
	public function getModificationDate() {
		return $this->modificationDate;
	}

	/**
	 * Sets the modificationDate
	 *
	 * @param DateTime $modificationDate
	 * @return void
	 */
	public function setModificationDate($modificationDate) {
		$this->modificationDate = $modificationDate;
	}

	/**
	 * Returns the creatorTool
	 *
	 * @return string $creatorTool
	 */
	public function getCreatorTool() {
		return $this->creatorTool;
	}

	/**
	 * Sets the creatorTool
	 *
	 * @param string $creatorTool
	 * @return void
	 */
	public function setCreatorTool($creatorTool) {
		$this->creatorTool = $creatorTool;
	}

	/**
	 * Returns the downloadName
	 *
	 * @return string $downloadName
	 */
	public function getDownloadName() {
		return $this->downloadName;
	}

	/**
	 * Sets the downloadName
	 *
	 * @param string $downloadName
	 * @return void
	 */
	public function setDownloadName($downloadName) {
		$this->downloadName = $downloadName;
	}

	/**
	 * Returns the source
	 *
	 * @return string $source
	 */
	public function getSource() {
		return $this->source;
	}

	/**
	 * Sets the source
	 *
	 * @param string $source
	 * @return void
	 */
	public function setSource($source) {
		$this->source = $source;
	}

	/**
	 * Returns the fal
	 *
	 * @return Tx_Dam_Domain_Model_File $fal
	 */
	public function getFal() {
		return $this->fal;
	}

	/**
	 * Sets the fal
	 *
	 * @param Tx_Dam_Domain_Model_File $fal
	 * @return void
	 */
	public function setFal(Tx_Dam_Domain_Model_File $fal) {
		$this->fal = $fal;
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

	/**
	 * Returns the alternative
	 *
	 * @return string $alternative
	 */
	public function getAlternative() {
		return $this->alternative;
	}

	/**
	 * Sets the alternative
	 *
	 * @param string $alternative
	 * @return void
	 */
	public function setAlternative($alternative) {
		$this->alternative = $alternative;
	}

	/**
	 * Returns the caption
	 *
	 * @return string $caption
	 */
	public function getCaption() {
		return $this->caption;
	}

	/**
	 * Sets the caption
	 *
	 * @param string $caption
	 * @return void
	 */
	public function setCaption($caption) {
		$this->caption = $caption;
	}

}
?>