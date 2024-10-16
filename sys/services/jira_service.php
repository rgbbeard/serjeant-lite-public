<?php
namespace Service;

use Exceptions\ApiNotFoundException;
use Exceptions\InvalidApiConfigException;
use Serjeant\SessionManager;
use Serjeant\Whistle;
use Serjeant\Converter;

class JiraService extends BaseService {
	protected const apis_path = "sys/config/apis.ini";
	
	protected const jira_base = "{your jira server}";
	
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

		$api = self::$apis[$api_name];
		$api["name"] = $api_name;

		return $api;
	}

	private function make_call(
		string $path,
		array $params = [], 
		array $options = [],
		bool $basic_query_inclusion = true,
		bool $debug = false
	) {
		$username = $_SESSION["USERNAME"];

		if($basic_query_inclusion) {
			if(!empty($params["jql"])) {
				$params["jql"] = "assignee='$username' and " . $params["jql"]; 
			} else {
				$params["jql"] = "assignee='$username'";
			}
		}

		$whistle = new Whistle(
			self::jira_base . $path,
			$params,
			$options
		);

		$response = $whistle->play_and_listen();

		if($debug) {
			dump($whistle->show_settings());
		}
		
		return $response;
	}
	
	/**
	 * @throws ApiNotFoundException
	 * @throws InvalidApiConfigException
	 */
	public function attempt_login_with_pat(?string $pat): bool {
		$api = $this->get_api("jira_pat_auth");
		$headers = self::get_headers_for($api["name"]);
		
		$headers["Authorization"] = sprintf($headers["Authorization"], $pat);
		
		$data = $this->make_call(
			$api["path"],
			[],
			[
				"method" => CURLOPT_POST,
				"headers" => $headers
			]
		);
		
		return false;
	}
	
	/**
	 * @throws ApiNotFoundException
	 * @throws InvalidApiConfigException
	 */
	public function get_user_issues(?string $pat, ?int $page = 1): array {
		$api = $this->get_api("jira_issues");
		$headers = self::get_headers_for($api["name"]);
		
		$headers["Authorization"] = sprintf($headers["Authorization"], $pat);
		
		$maxResults = 50;
		$startAt = ($page ?? 1) * $maxResults;

		$issues = $this->make_call(
			$api["path"],
			[
				"startAt" => $startAt,
				"maxResults" => $maxResults,
				"fields" => ["summary", "priority", "status"] 
			],
			[
				"headers" => $headers,
				"json_encode" => true
			]
		);
		
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

	/**
	 * @throws ApiNotFoundException
	 * @throws InvalidApiConfigException
	 */
	public function get_single_issue(?string $pat, int $id): array {
		$api = $this->get_api("jira_issue");
		$api["path"] = sprintf($api["path"], $id);
		$headers = self::get_headers_for($api["name"]);
		
		$headers["Authorization"] = sprintf($headers["Authorization"], $pat);

		$issue = $this->make_call(
			$api["path"],
			[
				"fields" => ["summary", "priority", "status"]
			],
			[
				"headers" => $headers,
				"json_encode" => true
			],
			false,
			true
		);

		dd($issue);
		
		if(!$issue) {
			return [];
		}

		try {
			$issue = Converter::json_decode($issue, true);
			
			return $issue;
		} catch(Exception $e) {
			return [];
		}
	}
	
	/**
	 * @throws ApiNotFoundException
	 */
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