<?php
namespace wcf\util;
use wcf\system\WCF;
use wcf\util\HTTPRequest;
use wcf\util\JSON;
use wcf\util\StringUtil;
use wcf\util\UserUtil;

/**
 * StopForumSpam API
 * 
 * @author	Sascha Greuel <sascha@softcreatr.de>
 * @copyright	2013 Sascha Greuel
 * @license	Creative Commons BY-SA <http://creativecommons.org/licenses/by-sa/3.0/>
 * @package	de.softcreatr.wcf2.stopforumspam
 * @subpackage	util
 * @category	Community Framework
 */
class StopForumSpam {
	const APIURL    = 'http://www.stopforumspam.com/api';
	const REPORTURL = 'http://www.stopforumspam.com/add.php';
	
	protected $username = null;
	protected $email = null;
	protected $ip = null;
	
	/**
	 * Prepares a new instance of StopForumSpam
	 */
	public function __construct($username = null, $email = null, $ip = null) {
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
		$requestResult = null;
		$avgConfidence = 0;
		$params = array(
			'unix' => 1,
			'confidence' => 1,
			'f' => 'json'
		);
		
		// Check E-Mail-Address?
		if (defined('STOPFORUMSPAM_CHECKEMAILADDRESS') && STOPFORUMSPAM_CHECKEMAILADDRESS && !empty($this->email)) {
			$check[] = 'email';
			$params['email'] = $this->email;
		}
		
		// Check IP-Address?
		if (defined('STOPFORUMSPAM_CHECKIPADDRESS') && STOPFORUMSPAM_CHECKIPADDRESS && !empty($this->ip)) {
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
					$avgConfidence += $result[$type]['confidence'];
				}
				
				$avgConfidence /= count($check);
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
	public function report($evidence = null) {
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
			$params['evidence'] .= "\n\n----------------\nStopforumspam for Woltlab Community Framework";
		}
	
		// Just continue, if there's anything to report
		if (!empty($this->username) && !empty($this->email) && !empty($this->ip)) {	
			// Perform API-Request
			$reportResult = $this->apiRequest(self::REPORTURL . $this->buildQuery($params));
		}
		
		return strip_tags($reportResult);
	}
	
	/**
	 * Confidence score calculation
	 *
	 * Based on the blog post at http://amix.dk/blog/post/19588
	 */
	public function confidence($reports = 0, $lastReport = 0) {
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
	
	public function log($msg = null, $className = null, $eventName = null) {		
		$sql = "INSERT INTO	wcf".WCF_N."_sfs_log
					(username, ipAddress, email, logDate, eventClassName, eventName, action)
			VALUES		(?, ?, ?, ?, ?, ?, ?)";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->username,
			$this->ip,
			$this->email,
			TIME_NOW,
			$className,
			$eventName,
			$msg
		));
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
		$request->addHeader('User-Agent', "HTTP.PHP (Stopforumspam; WoltLab Community Framework/" . WCF_VERSION . "; ".WCF::getLanguage()->languageCode . ")");
		$request->execute();
		
		$reply = $request->getReply();
		
		return $reply['body'];
	}
}
