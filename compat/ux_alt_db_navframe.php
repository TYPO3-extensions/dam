<?php

class ux_localPageTree extends localPageTree {

	/**
	 * Initialize, setting what is necessary for browsing pages.
	 * Using the current user.
	 *
	 * @param	string		Additional clause for selecting pages.
	 * @return	void
	 */
	function init($clause='')	{
		parent::init($clause);

			// this will hide records from display - it has nothing todo with user rights!!
		$clauseExludePidList = '';
		if ($exludePidList = $GLOBALS['BE_USER']->getTSConfigVal('options.hideRecords.pages')) {

			if ($exludePidList = $GLOBALS['TYPO3_DB']->cleanIntList($exludePidList)) {
				$clauseExludePidList = ' AND pages.uid NOT IN ('.$exludePidList.')';
			}
		}

			// This is very important for making trees of pages: Filtering out deleted pages, pages with no access to and sorting them correctly:
		# done already in parent: parent::init(' AND '.$GLOBALS['BE_USER']->getPagePermsClause(1).' '.$clause.$clauseExludePidList, 'sorting');

			// remove mounts if pid is in hide list
		if ($exludePidList) {
			$exludePidList = t3lib_div::trimExplode(',', $exludePidList, 1);
			foreach ($this->MOUNTS as $mountKey => $mountPid) {
				if (in_array($mountPid, $exludePidList)) {
					unset($this->MOUNTS[$mountKey]);
				}
			}
		}

	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/ux_alt_db_navframe.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dam/compat/ux_alt_db_navframe.php']);
}

?>