<?php
namespace wcf\system\event\listener;
use wcf\system\event\IEventListener;
use wcf\system\exception\StopForumSpamException;
use wcf\util\StopForumSpam;

/**
 * StopForumSpam integration (registration form)
 * 
 * @author	Sascha Greuel <sascha@softcreatr.de>
 * @copyright	2013 Sascha Greuel
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
		$sfs = new StopForumSpam($eventObj->username, $eventObj->email);
		
		// Perform check against sfs api
		$result = $sfs->check();
		
		// If user is a spammer, perform actions based on the settings
		if (isset($result['spammer']) && $result['spammer'] === true) {
			throw new StopForumSpamException();
		}
	}
}
