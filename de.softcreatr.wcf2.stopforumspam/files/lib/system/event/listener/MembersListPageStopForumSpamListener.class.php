<?php
namespace wcf\system\event\listener;
use wcf\data\user\User;
use wcf\system\event\IEventListener;
use wcf\util\StopForumSpam;

/**
 * Hide spammers from members list (if enabled)
 * 
 * @author	Sascha Greuel <sascha@softcreatr.de>
 * @copyright	2013 Sascha Greuel
 * @license	Creative Commons BY-SA <http://creativecommons.org/licenses/by-sa/3.0/>
 * @package	de.softcreatr.wcf2.stopforumspam
 * @subpackage	system.event.listener
 * @category	Community Framework
 */
class MembersListPageStopForumSpamListener implements IEventListener {
	/**
	 * @see	wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		// Do nothing, if the module is disabled
		if (!defined('MODULE_STOPFORUMSPAM') || !MODULE_STOPFORUMSPAM) {
			return false;
		}

		// Hide all users, which are marked as spammer
		if (defined('STOPFORUMSPAM_HIDEFROMML') && STOPFORUMSPAM_HIDEFROMML) {
			$eventObj->objectList->getConditionBuilder()->add('(SELECT userOption' . User::getUserOptionID('stopforumspam_userstatus') . '
					FROM wcf' . WCF_N . '_user_option_value
					WHERE userID = user_table.userID) != ?', array(2)
			);
		}
	}
}
