<?php
namespace wcf\system\exception;
use wcf\system\WCF;

/**
 * Shows the StopForumSpam error page
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
		WCF::getTPL()->assign(array(
			'name' => get_class($this),
			'file' => $this->getFile(),
			'line' => $this->getLine(),
			'stacktrace' => $this->getTraceAsString(),
			'templateName' => 'stopForumSpam'
		));

		WCF::getTPL()->display('stopForumSpam');
	}
}
