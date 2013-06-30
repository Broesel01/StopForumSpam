<?php
namespace wcf\system\event\listener;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\system\event\IEventListener;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * Restrict access to a spammers userprofile (if enabled)
 * 
 * @author	Sascha Greuel <sascha@softcreatr.de>
 * @copyright	2010-2013 Sascha Greuel
 * @license	Creative Commons BY-SA <http://creativecommons.org/licenses/by-sa/3.0/>
 * @package	de.softcreatr.wcf2.stopforumspam
 * @subpackage	system.event.listener
 * @category	Community Framework
 */
class UserPageStopForumSpamListener implements IEventListener {
	/**
	 * @see	wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		// Do nothing, if the module or "Block profiles" is disabled
		if (!MODULE_STOPFORUMSPAM || !STOPFORUMSPAM_BLOCKPROFILES) {
			return false;
		}
		
		// Get user information
		$user = UserProfile::getUserProfile($eventObj->userID);
		$option = 'userOption' . User::getUserOptionID('stopforumspam_userstatus');

		// Block access to the spammer's profile, if we don't have the required permission
		if ($user->$option == 2) {
			if (!WCF::getSession()->getPermission('admin.user.canEditUser')) {
				throw new PermissionDeniedException();
			}
		}
	}
}
