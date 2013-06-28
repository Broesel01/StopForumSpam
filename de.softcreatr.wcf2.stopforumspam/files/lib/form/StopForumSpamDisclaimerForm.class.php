<?php
namespace wcf\form;
use wcf\system\exception\NamedUserException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Shows the StopForumSpam disclaimer
 * 
 * @author	Sascha Greuel <sascha@softcreatr.de>
 * @copyright	2010-2013 Sascha Greuel
 * @license	Creative Commons BY-SA <http://creativecommons.org/licenses/by-sa/3.0/>
 * @package	de.softcreatr.wcf2.stopforumspam
 * @subpackage	form
 * @category	Community Framework
 */
class StopForumSpamDisclaimerForm extends AbstractForm {
	/**
	 * true, if the user has accepted the disclaimer
	 * @var	boolean
	 */
	public $accept = false;
	
	/**
	 * @see	wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// user is already registered
		if (WCF::getUser()->userID) {
			throw new PermissionDeniedException();
		}
		
		// registration disabled
		if (REGISTER_DISABLED) {
			throw new NamedUserException(WCF::getLanguage()->get('wcf.user.register.error.disabled'));
		}
	}
	
	/**
	 * @see	wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['accept'])) $this->accept = true;
	}
	
	/**
	 * @see	wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		if (!$this->accept) throw new UserInputException('accept');
	}
	
	/**
	 * @see	wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		WCF::getSession()->register('stopForumSpamDisclaimerAccepted', true);
		$this->saved();
		WCF::getSession()->update();
		
		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('Register'));
		exit;
	}
}
