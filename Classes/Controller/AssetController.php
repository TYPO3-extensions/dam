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
class Tx_Dam_Controller_AssetController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * assetRepository
	 *
	 * @var Tx_Dam_Domain_Repository_AssetRepository
	 */
	protected $assetRepository;

	/**
	 * injectAssetRepository
	 *
	 * @param Tx_Dam_Domain_Repository_AssetRepository $assetRepository
	 * @return void
	 */
	public function injectAssetRepository(Tx_Dam_Domain_Repository_AssetRepository $assetRepository) {
		$this->assetRepository = $assetRepository;
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$assets = $this->assetRepository->findAll();
		$this->view->assign('assets', $assets);
	}

	/**
	 * action show
	 *
	 * @param $asset
	 * @return void
	 */
	public function showAction(Tx_Dam_Domain_Model_Asset $asset) {
		$this->view->assign('asset', $asset);
	}

	/**
	 * action new
	 *
	 * @param $newAsset
	 * @dontvalidate $newAsset
	 * @return void
	 */
	public function newAction(Tx_Dam_Domain_Model_Asset $newAsset = NULL) {
		$this->view->assign('newAsset', $newAsset);
	}

	/**
	 * action create
	 *
	 * @param $newAsset
	 * @return void
	 */
	public function createAction(Tx_Dam_Domain_Model_Asset $newAsset) {
		$this->assetRepository->add($newAsset);
		$this->flashMessageContainer->add('Your new Asset was created.');
		$this->redirect('list');
	}

	/**
	 * action edit
	 *
	 * @param $asset
	 * @return void
	 */
	public function editAction(Tx_Dam_Domain_Model_Asset $asset) {
		$this->view->assign('asset', $asset);
	}

	/**
	 * action update
	 *
	 * @param $asset
	 * @return void
	 */
	public function updateAction(Tx_Dam_Domain_Model_Asset $asset) {
		$this->assetRepository->update($asset);
		$this->flashMessageContainer->add('Your Asset was updated.');
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param $asset
	 * @return void
	 */
	public function deleteAction(Tx_Dam_Domain_Model_Asset $asset) {
		$this->assetRepository->remove($asset);
		$this->flashMessageContainer->add('Your Asset was removed.');
		$this->redirect('list');
	}

}
?>