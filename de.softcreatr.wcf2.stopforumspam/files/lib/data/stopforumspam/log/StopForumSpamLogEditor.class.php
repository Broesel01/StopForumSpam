<?php
namespace wcf\data\stopforumspam\log;
use wcf\data\DatabaseObjectEditor;
use wcf\system\WCF;

/**
 * Provides functions to edit StopForumSpam logs.
 * 
 * @author	Sascha Greuel <sascha@softcreatr.de>
 * @copyright	2010-2013 Sascha Greuel
 * @license	Creative Commons BY-SA <http://creativecommons.org/licenses/by-sa/3.0/>
 * @package	de.softcreatr.wcf2.stopforumspam
 * @subpackage	data.stopforumspam.log
 * @category	Community Framework
 */
class StopForumSpamLogEditor extends DatabaseObjectEditor {
	/**
	 * @see	wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\stopforumspam\log\StopForumSpamLog';
	
	/**
	 * Deletes all StopForumSpam logs.
	 */
	public static function clearLogs() {
		// delete logs
		$sql = "DELETE FROM	wcf".WCF_N."_sfs_log";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
	}
}
