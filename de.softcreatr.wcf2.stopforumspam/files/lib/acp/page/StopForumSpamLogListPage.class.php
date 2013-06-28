<?php
namespace wcf\acp\page;
use wcf\page\SortablePage;

/**
 * Shows StopForumSpam log information.
 * 
 * @author	Sascha Greuel <sascha@softcreatr.de>
 * @copyright	2010-2013 Sascha Greuel
 * @license	Creative Commons BY-SA <http://creativecommons.org/licenses/by-sa/3.0/>
 * @package	de.softcreatr.wcf2.stopforumspam
 * @subpackage	acp.page
 * @category	Community Framework
 */
class StopForumSpamLogListPage extends SortablePage {
	/**
	 * @see	wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.log.stopforumspam';
	
	/**
	 * @see	wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.system.canViewLog');
	
	/**
	 * @see	wcf\page\MultipleLinkPage::$itemsPerPage
	 */
	public $itemsPerPage = 100;
	
	/**
	 * @see	wcf\page\SortablePage::$defaultSortField
	 */
	public $defaultSortField = 'logID';
	
	/**
	 * @see	wcf\page\SortablePage::$defaultSortOrder
	 */
	public $defaultSortOrder = 'DESC';
	
	/**
	 * @see	wcf\page\SortablePage::$validSortFields
	 */
	public $validSortFields = array('logID', 'username', 'ipAddress', 'email', 'logDate', 'eventClassName', 'eventName', 'status');
	
	/**
	 * @see	wcf\page\MultipleLinkPage::$objectListClassName
	 */
	public $objectListClassName = 'wcf\data\stopforumspam\log\StopForumSpamLogList';
}
