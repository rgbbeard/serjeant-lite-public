<?php
namespace View;

use Exceptions\InvalidSessionException;
use Serjeant\Converter;
use Serjeant\SessionManager;
use Serjeant\Router;
use Service\ContentService;
use Service\JiraService;
use Service\UserService;

class PublicView extends BaseView {
	protected ContentService $contentService;
	protected UserService $userService;
	protected JiraService $jiraService;
	protected bool|string $navbar;
	protected bool|string $footer;
	
	public function __construct() {
		parent::__construct();

		$this->contentService = new ContentService();
		$this->userService = new UserService();
		$this->jiraService = new JiraService();
		
		$this->navbar = $this->get_navbar();
		$this->footer = $this->get_footer();
	}

	protected function get_navbar(): bool|string {
		$menu_items = parent::get_menu_methods();

		return parent::render("templates/public/navbar.php", [
			"menu_items" => $menu_items
		]);
	}

	protected function get_footer(): bool|string {
		$menu_items = [];

		return parent::render("templates/public/footer.php", [
			"menu_items" => $menu_items
		]);
	}
	
	protected function render(string $template_name, array $params = []): string {
		return parent::render($template_name, array_merge([
			"navbar" => $this->navbar,
			"footer" => $this->footer
		], $params));
	}

	/**
	 * @route(home)
	 * @menu(order: 1, visible: true, display: Home)
	 */
	public function get_home() {
		if(!$this->userService->has_jira_pat()) {
			Router::redirect_to("user_profile", [
				"requires_pat" => true
			]);
		}
		
		$pat = $this->userService->get_jira_pat();
		$issues = $this->jiraService->get_user_issues($pat);
		echo $this->render("templates/public/home.php", [
			"issues" => $issues
		]);
	}

	/**
	 * @route(about)
	 * @menu(order: 99, visible: true, display: About)
	 */
	public function get_about() {
		echo $this->render("templates/public/about.php");
	}
	
	public function get_user_registration(?string $params = "") {
		$result = false;
		$form_sent = false;
		$username = "";
		$password = "";
		$display = "";
		
		if(Router::has_post_data()) {
			$params = json_decode($params);
			$username = $params->username;
			$password = $params->password;
			$display = $params->display;
			
			$result = $this->userService->do_user_registration($username, $password, $display);
			
			if(!in_array($result, [0, 1])) {
				$_SESSION["ROLES"] = ["JIRA_USER"];
				$_SESSION["USERNAME"] = $username;
				Router::redirect_to("home");
			}
			
			$form_sent = true;
		}
		
		echo $this->render("templates/public/register.php", [
			"result" => $result,
			"form_sent" => $form_sent,
			"username" => $username,
			"password" => $password,
			"display" => $display
		]);
	}
	
	public function get_user_login(?string $params = "") {
		$success = false;
		$form_sent = false;
		$username = "";
		$password = "";
		
		if(Router::has_post_data()) {
			$params = json_decode($params);
			$username = $params->username;
			$password = $params->password;
			
			$success = $this->userService->attempt_user_login($username, $password);
			
			if($success) {
				$_SESSION["ROLES"] = ["JIRA_USER"];
				$_SESSION["USERNAME"] = $username;
				Router::redirect_to("home");
			}
			
			$form_sent = true;
		}
		
		echo $this->render("templates/public/login.php", [
			"login_succeeded" => $success,
			"form_sent" => $form_sent,
			"username" => $username,
			"password" => $password
		]);
	}
	
	/**
	 * @throws InvalidSessionException
	 */
	public function exec_user_logout() {
		SessionManager::reset_session();
		SessionManager::restart_session();
		
		Router::redirect_to("home");
	}
	
	public function get_user_profile(?string $params = "") {
		$infos = $this->userService->get_user_profile_infos();
		
		echo $this->render("templates/public/profile.php", [
			"update_succeeded" => SessionManager::consume_last_operation_result(),
			"requires_pat" => !$this->userService->has_jira_pat(),
			"infos" => $infos
		]);
	}
	
	public function exec_update_user_profile_infos(?string $params = "") {
		if(!Router::has_required_roles(["JIRA_USER"])) {
			Router::redirect_to("user_login");
		}
		
		if(Router::has_post_data()) {
			$data = Converter::json_decode($params, true);
			
			$result = $this->userService->update_user_profile_infos($_SESSION["USERNAME"], $data["display"])
				 && $this->userService->save_user_pat($_SESSION["USERNAME"], $data["pat"]);
			
			SessionManager::set_last_operation_result($result);
			
			Router::redirect_to("user_profile");
		}
	}
}