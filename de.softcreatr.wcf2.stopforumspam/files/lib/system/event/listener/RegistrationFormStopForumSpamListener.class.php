<?php
namespace wcf\system\event\listener;
use wcf\system\event\IEventListener;
use wcf\system\exception\PermissionDeniedException;
use wcf\util\StopForumSpam;
use wcf\util\UserUtil;

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
class RegistrationFormStopForumSpamListener implements IEventListener {
	/**
	 * @see	wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		/*
		if (!defined('MODULE_STOPFORUMSPAM') || !MODULE_STOPFORUMSPAM) {
			return false;
		}
		*/
		
		$sfs = new StopForumSpam($eventObj->username, $eventObj->email);
		$result = $sfs->check();
		
		if ($result['spammer'] === true) {
			throw new PermissionDeniedException();
		}
	}
}
