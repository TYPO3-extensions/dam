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
class Tx_Dam_Controller_CollectionController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * collectionRepository
	 *
	 * @var Tx_Dam_Domain_Repository_CollectionRepository
	 */
	protected $collectionRepository;

	/**
	 * injectCollectionRepository
	 *
	 * @param Tx_Dam_Domain_Repository_CollectionRepository $collectionRepository
	 * @return void
	 */
	public function injectCollectionRepository(Tx_Dam_Domain_Repository_CollectionRepository $collectionRepository) {
		$this->collectionRepository = $collectionRepository;
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$collections = $this->collectionRepository->findAll();
		$this->view->assign('collections', $collections);
	}

	/**
	 * action show
	 *
	 * @param $collection
	 * @return void
	 */
	public function showAction(Tx_Dam_Domain_Model_Collection $collection) {
		$this->view->assign('collection', $collection);
	}

	/**
	 * action new
	 *
	 * @param $newCollection
	 * @dontvalidate $newCollection
	 * @return void
	 */
	public function newAction(Tx_Dam_Domain_Model_Collection $newCollection = NULL) {
		$this->view->assign('newCollection', $newCollection);
	}

	/**
	 * action create
	 *
	 * @param $newCollection
	 * @return void
	 */
	public function createAction(Tx_Dam_Domain_Model_Collection $newCollection) {
		$this->collectionRepository->add($newCollection);
		$this->flashMessageContainer->add('Your new Collection was created.');
		$this->redirect('list');
	}

	/**
	 * action edit
	 *
	 * @param $collection
	 * @return void
	 */
	public function editAction(Tx_Dam_Domain_Model_Collection $collection) {
		$this->view->assign('collection', $collection);
	}

	/**
	 * action update
	 *
	 * @param $collection
	 * @return void
	 */
	public function updateAction(Tx_Dam_Domain_Model_Collection $collection) {
		$this->collectionRepository->update($collection);
		$this->flashMessageContainer->add('Your Collection was updated.');
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param $collection
	 * @return void
	 */
	public function deleteAction(Tx_Dam_Domain_Model_Collection $collection) {
		$this->collectionRepository->remove($collection);
		$this->flashMessageContainer->add('Your Collection was removed.');
		$this->redirect('list');
	}

}
?>