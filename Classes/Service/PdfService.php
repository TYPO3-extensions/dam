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
class Tx_Dam_PdfService extends t3lib_svbase {
	protected $prefixId = 'tx_dam_pdfservice';		// Same as class name
	protected $scriptRelPath = 'Classes/Service/PdfService.php';	// Path to this script relative to the extension dir.
	protected $extKey = 'dam';	// The extension key.

	/**
	 * Performs the service processing
	 *
	 * @return	boolean
	 */
	public function process()	{

		$includePath = t3lib_extMgm::extPath($this->extKey) . 'Classes/Utility/';
		set_include_path($includePath);
		require_once(t3lib_extMgm::extPath($this->extKey) . 'Classes/Utility/Zend/Pdf.php');

		$this->out = array();
		$this->out['fields'] = array();

		if($inputFile = $this->getInputFile()) {

			$pdf = Zend_Pdf::load($inputFile);

			if (is_object($pdf)) {
				//ToDO: Check possible encoding problems, think about security aspects (SQL injection through metadata)
				$this->out['fields']['title'] = $pdf->properties['Title'];
				$this->out['fields']['creator'] = $pdf->properties['Author'];
				$this->out['fields']['description'] = $pdf->properties['Subject'];
				$this->out['fields']['keywords'] = $pdf->properties['Keywords'];
				$this->out['fields']['creator_tool'] = $pdf->properties['Creator'];
				$this->out['fields']['creation_date'] = $this->parsePdfDate($pdf->properties['CreationDate']);
				$this->out['fields']['modification_date'] = $this->parsePdfDate($pdf->properties['ModDate']);

			}

		} else {
			$this->errorPush(T3_ERR_SV_NO_INPUT, 'No or empty input.');
		}

		return $this->getLastError();
	}


	/**
	 * Convert a PDF date string into a timestamp
	 * PDF date: D:YYYYMMDDHHmmSSOHH'mm'
	 */
	protected function parsePdfDate($pdfDate)	{

		// Remove starting D: if exists
		$pdfDate = preg_replace("/D:/", "", $pdfDate);
		// Split the PDF Date into two parts if a timezone indication exists (Z = time is indicated in UTC)
		$pdfDateArray = preg_split("/(?=[-+Z]\d{2}'\d{2}')/", $pdfDate, -1);

		// Check if timezone indication exists
		if (isset($pdfDateArray[1])) {

			$timeOffset = preg_replace('[\D]', '', $pdfDateArray[1]);

			switch (substr($pdfDateArray[1], 0, 1)) {
				case '-':
					$timeOffset = '-' . $timeOffset;
				case '+':
					$timeOffset = '+' . $timeOffset;
			}
			
		}

		// Build an interpretable datetime
		if (isset($timeOffset)) {
			$pdfDate = $pdfDateArray[0] . $timeOffset;
			$pdfDateTimeFormat = DateTime::createFromFormat('YmdGisO', $pdfDate);
		} else {
			$pdfDateTimeFormat = DateTime::createFromFormat('YmdGis', $pdfDateArray[0]);
		}

		// Form it to a UNIX timestamp
		$pdfDateTime = $pdfDateTimeFormat->format('U');

		return $pdfDateTime;

	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/Classes/Service/PdfService.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/Classes/Service/PdfService.php']);
}

?>