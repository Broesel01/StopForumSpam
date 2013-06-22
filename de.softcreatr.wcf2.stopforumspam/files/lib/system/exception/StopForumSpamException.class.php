<?php
namespace wcf\system\exception;
use wcf\system\WCF;

/**
 * StopForumSpamException shows an error page, if the user has been blocked
 * 
 * @author	Sascha Greuel <sascha@softcreatr.de>
 * @copyright	2013 Sascha Greuel
 * @license	Creative Commons BY-SA <http://creativecommons.org/licenses/by-sa/3.0/>
 * @package	de.softcreatr.wcf2.stopforumspam
 * @subpackage	util
 * @category	Community Framework
 */
class StopForumSpamException extends UserException {
	/**
	 * @see	wcf\system\exception\LoggedException::$ignoreDebugMode
	 */
	protected $ignoreDebugMode = true;
	
	public function show() {
		$exceptionString = '';
		
		if (defined('STOPFORUMSPAM_BLOCKMESSAGE') && STOPFORUMSPAM_BLOCKMESSAGE != '') {
			$exceptionString = WCF::getLanguage()->get(STOPFORUMSPAM_BLOCKMESSAGE, true);
		} else {
			$exceptionString = WCF::getLanguage()->get('wcf.acp.option.stopforumspam_defaultblockmessage');
		}
		
		WCF::getTPL()->assign(array(
			'exceptionString' => $exceptionString,
			'name' => get_class($this),
			'file' => $this->getFile(),
			'line' => $this->getLine(),
			'stacktrace' => $this->getTraceAsString(),
			'templateName' => 'stopForumSpam'
		));

		WCF::getTPL()->display('stopForumSpam');
	}
}
