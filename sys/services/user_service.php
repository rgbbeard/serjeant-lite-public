<?php
namespace Service;

use Exceptions\ApiNotFoundException;
use Exceptions\InvalidApiConfigException;
use Exceptions\DecryptionErrorException;
use \Exception;
use Serjeant\Hasher;

class UserService extends BaseService {
	private JiraService $jiraService;
	
	public function __construct() {
		parent::__construct();
		
		$this->jiraService = new JiraService();
	}
	
	public function do_admin_login(?string $username, ?string $password): bool {
		$password = Hasher::hash($password);
		
		# TODO: check for security issues
		$query = "select *
		from serjeant_administrators
		where username = '$username'
			and password = '$password'";
		
		$this->conn->execute($query);
		
		return $this->conn->get_rows() === 1;
	}
	
	/**
	 */
	public function attempt_user_login(?string $username, ?string $password): bool {
		$password = Hasher::hash($password);
		
		$query = "select *
		from serjeant_users
		where username = :username
		and password = :password";
		
		$this->conn->set_parameters([
			"username" => $username,
			"password" => $password
		]);
		
		return $this->conn->execute($query) && $this->conn->rows === 1;
	}
	
	/**
	 * @throws ApiNotFoundException
	 * @throws InvalidApiConfigException
	 */
	public function do_user_pat_login(?string $pat): bool {
		$success = $this->jiraService->attempt_login_with_pat($pat);
		
		return $success;
	}
	
	public function get_user_profile_infos(): array {
		$infos = [];
		
		$query = "select
			u.id,
			u.username,
			u.display,
			pat.encrypted_token
		from serjeant_users u
		left join serjeant_user_pat pat on pat.user_id = u.id
		where u.username = :username";
		
		$username = $_SESSION["USERNAME"];
		
		$this->conn->set_parameter("username", $username);
		
		if($this->conn->execute($query)) {
			if($this->conn->rows > 0) {
				foreach($this->conn->result as $r) {
					foreach($r as $name => $value) {
						$infos[$name] = $value;
					}
				}
			}
		}
		
		$infos["decrypted_token"] = "";
		
		if($this->has_jira_pat()) {
			if(!empty($infos["encrypted_token"])) {
				$infos["decrypted_token"] = Hasher::aes_decrypt($infos["encrypted_token"]);
			}
		}
		
		
		return $infos;
	}
	
	public function update_user_profile_infos(string $username, string $display): bool {
		$query = "update serjeant_users set
		display = :display
		where username = :username";
		
		$binds = [
			"display" => $display,
			"username" => $username
		];
		
		$this->conn->set_parameters($binds);
		
		return $this->conn->execute($query);
	}
	
	public function save_user_pat(string $username, string $pat): bool {
		$token = Hasher::aes_encrypt($pat);
		
		$query = "insert into serjeant_user_pat(user_id, encrypted_token, disabled, date_created, date_modified)
		select
		    u.id as user_id,
		    :token,
		    0 as disabled,
		    sysdate(0) as date_created,
		    sysdate(0) as date_modified
		from serjeant_users u
		where u.username = :username";
		
		if($this->has_jira_pat()) {
			$query = "update serjeant_user_pat set
			encrypted_token = :token,
			date_modified = sysdate(0)
			where user_id = (
			    select id
			    from serjeant_users
			    where username = :username
			)";
		}
		
		$this->conn->set_parameters([
			"token" => $token,
			"username" => $username
		]);
		
		return $this->conn->execute($query);
	}
	
	public function do_user_registration(string $username, string $password, string $display): int {
		$query_check = "select *
		from serjeant_users
		where username = :username";
		
		$this->conn->set_parameter(":username", $username);
		
		$check = $this->conn->execute($query_check);
		
		# transaction successfully executed and no result
		if($check && $this->conn->rows === 0) {
			$hashedpw = Hasher::hash($password);
			
			$query_insert = "insert into serjeant_users(username, password, display)
			values(:username, :password, :display)";
			
			$this->conn->set_parameters([
				":username" => $username,
				":password" => $hashedpw,
				":display" => $display
			]);
			
			return $this->conn->execute($query_insert) ? 2 : 1;
		}
		
		return 0;
	}
	
	public function has_jira_pat(): bool {
		$query = "select *
		from serjeant_user_pat
		where user_id = (
		    select id
		    from serjeant_users
		    where username = :username
		)";
		
		$this->conn->set_parameter("username", $_SESSION["USERNAME"]);
		
		return $this->conn->execute($query) && $this->conn->rows > 0;
	}
	
	public function has_valid_jira_pat(): bool {
		$query = "select encrypted_token
		from serjeant_user_pat
		where user_id = (
		    select id
		    from serjeant_users
		    where username = :username
		)
		order by id desc
		limit 1";
		
		$this->conn->set_parameter("username", $_SESSION["USERNAME"]);
		
		if($this->conn->execute($query) && $this->conn->rows === 1) {
			$token = $this->conn->result[0]["encrypted_token"];
			$this->jiraService->validate_pat($token);
		}
		
		return false;
	}

	public function get_jira_pat(): ?string {
		$query = "select encrypted_token
		from serjeant_user_pat
		where user_id = (
		    select id
		    from serjeant_users
		    where username = :username
		)
		order by id desc
		limit 1";
		
		$this->conn->set_parameter("username", $_SESSION["USERNAME"]);
		
		if($this->conn->execute($query)) {
			try {
				return Hasher::aes_decrypt($this->conn->result[0]["encrypted_token"]);
			} catch(Exception $e) {
				return null;
			}
		}

		return null;
	}
}