<?php
namespace Service;

use Exceptions\ApiNotFoundException;
use Exceptions\InvalidApiConfigException;
use Serjeant\SessionManager;
use Serjeant\Whistle;
use Serjeant\Converter;

class JiraService extends BaseService {
	protected const apis_path = "sys/config/apis.ini";
	
	protected const jira_base = "";
	
	protected static ?array $apis = null;
	protected SessionManager $sessionManager;
	
	public function __construct() {
		parent::__construct();
		
		$this->sessionManager = new SessionManager();
	}
	
	public static function load_apis() {
		self::$apis = parse_ini_file(self::apis_path, true);
	}
	
	/**
	 * @throws ApiNotFoundException
	 * @throws InvalidApiConfigException
	 */
	private function get_headers_for(string $api_name): array {
		if(!isset(self::$apis[$api_name])) {
			throw new ApiNotFoundException("Invalid API name");
		}
		
		$api = self::$apis[$api_name];

		if(empty($api["headers"])) {
			throw new InvalidApiConfigException("API not correctly configured: $api_name(missing headers)");
		}
		
		return $api["headers"];
	}
	
	/**
	 * @throws ApiNotFoundException
	 */
	private function get_api(string $api_name): ?array {
		if(!isset(self::$apis[$api_name])) {
			throw new ApiNotFoundException("Invalid API name");
		}
		
		return self::$apis[$api_name];
	}
	
	/**
	 * @throws ApiNotFoundException
	 * @throws InvalidApiConfigException
	 */
	public function attempt_login_with_pat(?string $pat): bool {
		$api = $this->get_api("jira_pat_auth");
		$headers = self::get_headers_for("jira_pat_auth");
		
		$headers["Authorization"] = sprintf($headers["Authorization"], $pat);
		
		$whistle = new Whistle(
			self::jira_base . $api["path"],
			[
				"jql" => "assignee='$username'"
			],
			[
				"method" => CURLOPT_POST,
				"headers" => $headers
			]
		);
		
		$data = $whistle->play_and_listen();
		
		return false;
	}
	
	/**
	 * @throws ApiNotFoundException
	 */
	public function get_user_issues(?string $pat): array {
		$api = $this->get_api("jira_issues");
		
		$headers = self::get_headers_for("jira_issues");
		
		$headers["Authorization"] = sprintf($headers["Authorization"], $pat);

		$username = $_SESSION["USERNAME"];
		
		$whistle = new Whistle(
			self::jira_base . $api["path"],
			[
				"jql" => "assignee='$username'",
				"startAt" => 0,
				"maxResults" => 50
			],
			[
				"headers" => $headers,
				"json_encode" => true
			]
		);
		
		$issues = $whistle->play_and_listen();
		
		if(!$issues) {
			return [];
		}

		try {
			$issues = Converter::json_decode($issues, true);
			
			return $issues;
		} catch(Exception $e) {
			return [];
		}
	}
	
	public function get_user_infos(): array {
		$api = $this->get_api("jira_user");
		$username = $_SESSION["USERNAME"];
		
		$whistle = new Whistle(
			self::jira_base . $api["path"],
			[
				"username" => $username
			]
		);

		$infos = $whistle->play_and_listen();
		dd($whistle->show_settings(), $infos);
		
		return $infos;
	}
	
	public function validate_pat(string $token): bool {
		return true; # Debug
	}
}