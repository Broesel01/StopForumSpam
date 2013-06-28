<?php
namespace wcf\data\stopforumspam\log;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of StopForumSpam log entries.
 * 
 * @author	Sascha Greuel <sascha@softcreatr.de>
 * @copyright	2010-2013 Sascha Greuel
 * @license	Creative Commons BY-SA <http://creativecommons.org/licenses/by-sa/3.0/>
 * @package	de.softcreatr.wcf2.stopforumspam
 * @subpackage	data.stopforumspam.log
 * @category	Community Framework
 */
class StopForumSpamLogList extends DatabaseObjectList {
	/**
	 * @see	wcf\data\DatabaseObjectList::$className
	 */
	public $className = 'wcf\data\stopforumspam\log\StopForumSpamLog';
}
