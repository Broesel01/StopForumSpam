<?php
namespace wcf\system\event\listener;
use wcf\system\event\IEventListener;
use wcf\system\exception\StopForumSpamException;
use wcf\system\WCF;

/**
 * StopForumSpam integration (overall block page)
 * 
 * @author	Sascha Greuel <sascha@softcreatr.de>
 * @copyright	2010-2013 Sascha Greuel
 * @license	Creative Commons BY-SA <http://creativecommons.org/licenses/by-sa/3.0/>
 * @package	de.softcreatr.wcf2.stopforumspam
 * @subpackage	system.event.listener
 * @category	Community Framework
 */
class StopForumSpamLockedListener implements IEventListener {
	/**
	 * @see	wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		// Do nothing, if the module or "Enable notification" is disabled
		if (!MODULE_STOPFORUMSPAM || !STOPFORUMSPAM_ENABLENOTIFICATION) {
			return false;
		}
		
		// Show error message and prevent further access
		if ((WCF::getUser()->userID && WCF::getUser()->stopforumspam_userstatus == 2) || WCF::getSession()->getVar('stopforumspam_userstatus') == 2) {
			throw new StopForumSpamException();
		}
	}
}
