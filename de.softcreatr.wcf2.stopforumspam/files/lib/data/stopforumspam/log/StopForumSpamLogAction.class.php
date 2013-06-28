<?php
namespace wcf\data\stopforumspam\log;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;

/**
 * Executes StopForumSpam log-related actions.
 * 
 * @author	Sascha Greuel <sascha@softcreatr.de>
 * @copyright	2010-2013 Sascha Greuel
 * @license	Creative Commons BY-SA <http://creativecommons.org/licenses/by-sa/3.0/>
 * @package	de.softcreatr.wcf2.stopforumspam
 * @subpackage	data.stopforumspam.log
 * @category	Community Framework
 */
class StopForumSpamLogAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'wcf\data\stopforumspam\log\StopForumSpamLogEditor';
	
	/**
	 * Validates the clear all action.
	 */
	public function validateClearAll() {
		WCF::getSession()->checkPermissions(array('admin.system.canViewLog'));
	}

	/**
	 * Deletes the entire StopForumSpam log.
	 */
	public function clearAll() {
		StopForumSpamLogEditor::clearLogs();
	}
}
