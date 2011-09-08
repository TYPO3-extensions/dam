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
class Tx_Dam_Controller_FileController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * fileRepository
	 *
	 * @var Tx_Dam_Domain_Repository_FileRepository
	 */
	protected $fileRepository;

	/**
	 * injectFileRepository
	 *
	 * @param Tx_Dam_Domain_Repository_FileRepository $fileRepository
	 * @return void
	 */
	public function injectFileRepository(Tx_Dam_Domain_Repository_FileRepository $fileRepository) {
		$this->fileRepository = $fileRepository;
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$files = $this->fileRepository->findAll();
		$this->view->assign('files', $files);
	}

	/**
	 * action show
	 *
	 * @param $file
	 * @return void
	 */
	public function showAction(Tx_Dam_Domain_Model_File $file) {
		$this->view->assign('file', $file);
	}

	/**
	 * action new
	 *
	 * @param $newFile
	 * @dontvalidate $newFile
	 * @return void
	 */
	public function newAction(Tx_Dam_Domain_Model_File $newFile = NULL) {
		$this->view->assign('newFile', $newFile);
	}

	/**
	 * action create
	 *
	 * @param $newFile
	 * @return void
	 */
	public function createAction(Tx_Dam_Domain_Model_File $newFile) {
		$this->fileRepository->add($newFile);
		$this->flashMessageContainer->add('Your new File was created.');
		$this->redirect('list');
	}

	/**
	 * action edit
	 *
	 * @param $file
	 * @return void
	 */
	public function editAction(Tx_Dam_Domain_Model_File $file) {
		$this->view->assign('file', $file);
	}

	/**
	 * action update
	 *
	 * @param $file
	 * @return void
	 */
	public function updateAction(Tx_Dam_Domain_Model_File $file) {
		$this->fileRepository->update($file);
		$this->flashMessageContainer->add('Your File was updated.');
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param $file
	 * @return void
	 */
	public function deleteAction(Tx_Dam_Domain_Model_File $file) {
		$this->fileRepository->remove($file);
		$this->flashMessageContainer->add('Your File was removed.');
		$this->redirect('list');
	}

}
?>