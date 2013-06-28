<?php
namespace wcf\system\event\listener;
use wcf\system\event\IEventListener;
use wcf\util\StopForumSpam;

/**
 * StopForumSpam integration (registration form)
 * 
 * @author	Sascha Greuel <sascha@softcreatr.de>
 * @copyright	2010-2013 Sascha Greuel
 * @license	Creative Commons BY-SA <http://creativecommons.org/licenses/by-sa/3.0/>
 * @package	de.softcreatr.wcf2.stopforumspam
 * @subpackage	system.event.listener
 * @category	Community Framework
 */
class RegisterFormStopForumSpamListener implements IEventListener {
	/**
	 * @see	wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		// Init
		$sfs = new StopForumSpam($eventObj->username, $eventObj->email);

		// Avoid multiple checks on multiple events in the same class
		if ((STOPFORUMSPAM_PRIORITIZECHECK && $eventName != 'saved') || (!STOPFORUMSPAM_PRIORITIZECHECK && $eventName == 'saved')) {
			// "Check registration" enabled?
			if (!STOPFORUMSPAM_CHECKREGISTRATION) {
				$sfs->log(false, 'wcf.stopforumspam.log.checkregister_disabled', $className, $eventName);
				return false;
			}
			
			// Perform check against StopForumSpam-API
			$result = $sfs->check();
			
			// If user is a spammer, perform actions based on the settings
			if (isset($result['checked']) && $result['checked']) {
				$sfs->log((isset($result['spammer']) && $result['spammer']), null, $className, $eventName);
			}
		}
	}
}
