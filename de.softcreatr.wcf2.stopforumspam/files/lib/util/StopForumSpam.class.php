<?php
namespace wcf\util;
use wcf\data\stopforumspam\log\StopForumSpamLogEditor;
use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\system\exception\StopForumSpamException;
use wcf\system\WCF;
use wcf\util\HTTPRequest;
use wcf\util\JSON;
use wcf\util\StringUtil;
use wcf\util\UserUtil;

/**
 * StopForumSpam API
 * 
 * @author	Sascha Greuel <sascha@softcreatr.de>
 * @copyright	2010-2013 Sascha Greuel
 * @license	Creative Commons BY-SA <http://creativecommons.org/licenses/by-sa/3.0/>
 * @package	de.softcreatr.wcf2.stopforumspam
 * @subpackage	util
 * @category	Community Framework
 */
class StopForumSpam {
	const APIURL    = 'http://www.stopforumspam.com/api';
	const REPORTURL = 'http://www.stopforumspam.com/add.php';
	
	protected $username = '';
	protected $email = '';
	protected $ip = '';
	
	/**
	 * Prepares a new instance of StopForumSpam
	 */
	public function __construct($username = '', $email = '', $ip = '') {		
		$this->username = $username;
		$this->email = $email;
		$this->ip = (!empty($ip) ? $ip : UserUtil::convertIPv6To4(UserUtil::getIpAddress()));
	}
	
	/**
	 * Check, if the given data is listed on SFS
	 */
	public function check() {
		// Defaults
		$isChecked = $isSpammer = false;
		$check = $result = array();
		$requestResult = '';
		$avgConfidence = 0;
		$lastSeen = '0000-00-00 00:00:00';
		$params = array(
			'unix' => 1,
			'confidence' => 1,
			'f' => 'json'
		);
		
		// Check, if module is enabled and if the given information is whitelisted
		if (WCF::getUser()->userID && WCF::getUser()->stopforumspam_userstatus <> 1) {
			return false;
		} else if (!MODULE_STOPFORUMSPAM) {
			$this->log(false, 'wcf.stopforumspam.log.module_disabled');
			return false;
		} else if ($this->isWhitelisted()) {
			$this->log(false, 'wcf.stopforumspam.log.whitelisted');
			return false;
		} else {
			// Check E-Mail-Address?
			if (STOPFORUMSPAM_CHECKEMAILADDRESS && !empty($this->email)) {
				$check[] = 'email';
				$params['email'] = $this->email;
			}
			
			// Check IP-Address?
			if (STOPFORUMSPAM_CHECKIPADDRESS && !empty($this->ip)) {
				$check[] = 'ip';
				$params['ip'] = $this->ip;
			}
			
			// Just continue, if there's anything to check
			if (!empty($check)) {
				$isChecked = true;
				
				// Perform API-Request
				$requestResult = $this->apiRequest(self::APIURL . $this->buildQuery($params));
				
				$retArray = JSON::decode($requestResult, true);
				
				if (is_array($retArray) && isset($retArray['success']) && intval($retArray['success']) === 1) {
					foreach ($check as $type) {
						if (isset($retArray[$type]) && intval($retArray[$type]['appears']) === 1) {
							$isSpammer = true;
						}
						
						$result[$type]['type'] = $type;
						$result[$type]['value'] = (isset($retArray[$type]['value']) ? $retArray[$field]['value'] : $this->$type);
						$result[$type]['frequency'] = (isset($retArray[$type]['frequency']) ? $retArray[$type]['frequency'] : 0);
						$result[$type]['lastSeen'] = (isset($retArray[$type]['lastseen']) ? $retArray[$type]['lastseen'] : '0000-00-00 00:00:00');
						$result[$type]['confidence'] = ($isSpammer ? $this->confidence($result[$type]['frequency'], $result[$type]['lastSeen']) : 0);
						
						$lastSeen = ($lastSeen < $result[$type]['lastSeen'] ? $result[$type]['lastSeen'] : $lastSeen);
						$avgConfidence += $result[$type]['confidence'];
					}

					$avgConfidence /= count($check);
				}
			}
			
			// Identify spammer, using extended settings
			if (STOPFORUMSPAM_MAXAGE <> 0) {
				if (TIME_NOW - (STOPFORUMSPAM_MAXAGE * 24 * 60 * 60) >= $lastSeen) {
					$isSpammer = false;
				}
			}
			
			if (STOPFORUMSPAM_CONFIDENCE <> 0) {
				if ($avgConfidence < STOPFORUMSPAM_CONFIDENCE) {
					$isSpammer = false;
				}
			}
			
			// Mark user as spammer, if enabled
			if ($isSpammer) {
				$this->markAsSpammer(STOPFORUMSPAM_PRIORITIZECHECK);
			}
		}
		
		return array(
			'checked' => $isChecked,
			'spammer' => $isSpammer,
			'avgConfidence' => $avgConfidence,
			'result' => $result
		);
	}
	
	/**
	 * Submit a new report, based on the given informations
	 */
	public function report($evidence = '') {
		$params = array();
		$reportResult = 'Error';

		if (!empty($this->username)) {
			$params['username'] = $this->username;
		}

		if (!empty($this->email)) {
			$params['email'] = $this->email;
		}

		if (!empty($this->ip)) {
			$params['ip'] = $this->ip;
		}
		
		// Please note, that it's recommended to submit an evidence
		// on every report (see SFS removal policy)
		if (!empty($evidence)) {
			$params['evidence'] = StringUtil::trim($evidence);
			$params['evidence'] .= "\n\n----------------\nStopforumspam for WoltLab Community Framework";
		}
	
		// Just continue, if there's anything to report
		if (!empty($this->username) && !empty($this->email) && !empty($this->ip)) {	
			// Perform API-Request
			$reportResult = $this->apiRequest(self::REPORTURL . $this->buildQuery($params));
		}
		
		return strip_tags($reportResult);
	}
	
	/**
	 * Mark user as spammer
	 */
	public function markAsSpammer($exception = true) {
		WCF::getSession()->register('stopforumspam_userstatus', 2);
		WCF::getSession()->update();

		if (WCF::getUser()->userID) {
			$userEditor = new UserEditor(WCF::getUser());
			$userEditor->updateUserOptions(array(
				User::getUserOptionID('stopforumspam_userstatus') => 2
			));
		}
		
		if ($exception) {
			throw new StopForumSpamException();
		}
	}
	
	/**
	 * Log
	 */
	public function log($status = false, $message = '', $className = '', $eventName = '') {
		list(, $caller) = debug_backtrace(false);

		StopForumSpamLogEditor::create(array(
			'userid' => (WCF::getUser()->userID ? WCF::getUser()->userID : 0),
			'username' => (!empty($this->username) ? $this->username : (WCF::getUser()->userID ? WCF::getUser()->username : '')),
			'ipAddress' => $this->ip,
			'email' => (!empty($this->email) ? $this->email : (WCF::getUser()->userID ? WCF::getUser()->email : '')),
			'logDate' => TIME_NOW,
			'eventClassName' => (empty($className) ? $caller['class'] : $className),
			'eventName' => (empty($eventName) ? $caller['function'] : $eventName),
			'status' => ($status ? 1 : 0),
			'error' => $message
		));
	}
	
	/**
	 * Confidence score calculation
	 *
	 * Based on the blog post at http://amix.dk/blog/post/19588
	 */
	protected function confidence($reports = 0, $lastReport = 0) {
		$ups = $reports / 2;
		$gracePeriod = 7;
		
		$downs = floor((time() - $lastReport) / 86400) - $gracePeriod;
		
		if ($downs < 0) {
			$downs = 0;
		}
		
		$numReports = $ups + ($downs * 2);
		
		if (!$numReports) {
			return 0;
		}
		
		// Begin with the Wilson score interval
		// http://en.wikipedia.org/wiki/Binomial_proportion_confidence_interval#Wilson_score_interval
		$z = 1; // 1.0 = 85%, 1.6 = 95%
		$phat = $ups / $numReports;
		$confidence = round(sqrt($phat + $z * $z / (2 * $numReports) - $z * (($phat * (1 - $phat) + $z * $z / (4 * $numReports)) / $numReports)) / (1 + $z * $z / $numReports) * 100, 2);
		
		return $confidence;
	}
	
	/**
	 * Whitelist check
	 */
	protected function isWhiteListed() {
		if (STOPFORUMSPAM_WHITELIST != '') {
			if (!empty($this->username) && !StringUtil::executeWordFilter($this->username, STOPFORUMSPAM_WHITELIST)) {
				return true;
			}

			if (!empty($this->ip) && !StringUtil::executeWordFilter($this->ip, STOPFORUMSPAM_WHITELIST)) {
				return true;
			}

			if (!empty($this->email) && !StringUtil::executeWordFilter($this->email, STOPFORUMSPAM_WHITELIST)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Build Request-Query
	 */
	private function buildQuery($params = array()) {
		$paramsJoined = array();
		
		// Append API-Key (if avaiable) on every call
		$paramsJoined[] = 'api_key=' . (defined('STOPFORUMSPAM_APIKEY') ? StringUtil::trim(STOPFORUMSPAM_APIKEY) : '');
		
		foreach ($params as $param => $value) {
			$paramsJoined[] = rawurlencode(StringUtil::trim($param)) . '=' . rawurlencode(StringUtil::trim($value));
		}
		
		return '?' . implode('&', $paramsJoined);
	}
	
	/**
	 * Make request
	 */
	private function apiRequest($apiURL) {
		$request = new HTTPRequest($apiURL);
		$request->execute();
		
		$reply = $request->getReply();
		
		return $reply['body'];
	}
}
