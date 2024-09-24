<?php
namespace View;

use HashTools\utils\SessionManager;
use Serjeant\Router;
use Service\ContentService;
use Service\HTTPService;
use Service\UserService;

class AdminView extends BaseView {
	protected ContentService $contentService;
	protected UserService $userService;
	
	public function __construct() {
		parent::__construct();

		$this->contentService = new ContentService();
		$this->userService = new UserService();
	}

	protected function get_navbar() {
		$menu_items = [
			"admin" => [
				"order" => 0,
				"visible" => true,
				"display" => "Home"
			],
			"home" => [
				"order" => 2,
				"visible" => true,
				"display" => "Torna al sito"
			]
		];

		return parent::render("templates/admin/navbar.php", [
			"menu_items" => $menu_items
		]);
	}

	protected function check_login() {
		$allowed = Router::has_required_roles(["ROLE_ADMIN"]);

		if(!$allowed) {
			# access denied
			info_log("Access denied to:" . $_SERVER["REQUEST_URI"]);
			$this->render_http_response(new HTTPService(http_response_code(401)));
		}

		return $allowed;
	}

	public function get_admin_home() {
		$navbar = $this->get_navbar();

		echo parent::render("templates/admin/home.php", [
			"navbar" => $navbar
		]);
	}

	public function get_admin_login(?string $params = "") {
		if(Router::has_required_roles(["ROLE_ADMIN"])) {
			Router::redirect_to("admin");
		}

		$navbar = $this->get_navbar();
		$success = false;
		$form_sent = false;
		$username = "";
		$password = "";

		if(Router::has_post_data()) {
			$params = json_decode($params);
			$username = $params->username;
			$password = $params->password;

			$success = $this->userService->do_admin_login($username, $password);

			if($success) {
				$_SESSION["ROLES"] = ["ROLE_ADMIN"];
				$_SESSION["USERNAME"] = $username;
				Router::redirect_to("admin");
			}

			$form_sent = true;
		}

		echo parent::render("templates/admin/login.php", [
			"navbar" => $navbar,
			"login_succeeded" => $success,
			"form_sent" => $form_sent,
			"username" => $username,
			"password" => $password
		]);
	}

	public function exec_admin_logout() {
		if($this->check_login()) {
			SessionManager::restart_session();
		}

		Router::redirect_to("admin_login");
	}
}