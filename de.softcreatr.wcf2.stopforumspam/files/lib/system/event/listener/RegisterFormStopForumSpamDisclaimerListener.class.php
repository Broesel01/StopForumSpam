<?php
namespace wcf\system\event\listener;
use wcf\system\event\IEventListener;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * A disclaimer for StopForumSpam
 * 
 * @author	Sascha Greuel <sascha@softcreatr.de>
 * @copyright	2013 Sascha Greuel
 * @license	Creative Commons BY-SA <http://creativecommons.org/licenses/by-sa/3.0/>
 * @package	de.softcreatr.wcf2.stopforumspam
 * @subpackage	system.event.listener
 * @category	Community Framework
 */
class RegisterFormStopForumSpamDisclaimerListener implements IEventListener {
	/**
	 * @see	wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		// avoid misorder of disclaimer pages
		if (REGISTER_ENABLE_DISCLAIMER && !WCF::getSession()->getVar('disclaimerAccepted')) {
			return false;
		}
		
		// check StopForumSpam disclaimer
		if (defined('STOPFORUMSPAM_ENABLEDISCLAIMER') && STOPFORUMSPAM_ENABLEDISCLAIMER && !WCF::getSession()->getVar('stopForumSpamDisclaimerAccepted')) {
			HeaderUtil::redirect(LinkHandler::getInstance()->getLink('StopForumSpamDisclaimer'));
			exit;
		}
	}
}
