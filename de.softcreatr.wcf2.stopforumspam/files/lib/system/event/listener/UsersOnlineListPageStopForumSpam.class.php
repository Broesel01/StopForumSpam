<?php
namespace wcf\system\event\listener;
use wcf\data\user\User;
use wcf\system\event\IEventListener;

/**
 * Hide spammers from users online list (if enabled)
 * 
 * @author	Sascha Greuel <sascha@softcreatr.de>
 * @copyright	2010-2013 Sascha Greuel
 * @license	Creative Commons BY-SA <http://creativecommons.org/licenses/by-sa/3.0/>
 * @package	de.softcreatr.wcf2.stopforumspam
 * @subpackage	system.event.listener
 * @category	Community Framework
 */
class UsersOnlineListPageStopForumSpam implements IEventListener {
	/**
	 * @see	wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		// Do nothing, if the module or "Hide from users online list" is disabled
		if (!MODULE_STOPFORUMSPAM || !STOPFORUMSPAM_HIDEFROMUOL) {
			return false;
		}

		// Hide all users, which are marked as spammer
		$eventObj->objectList->getConditionBuilder()->add('session.userID IN (SELECT userID
				FROM wcf' . WCF_N . '_user_option_value
				WHERE userOption' . User::getUserOptionID('stopforumspam_userstatus') . ' != ?)', array(2)
		);
	}
}
