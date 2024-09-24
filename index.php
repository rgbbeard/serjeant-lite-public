<?php
/*
 *  ____               _                  _
 * / ___|  ___ _ __   | | ___  __ _ _ __ | |_
 * \___ \ / _ \ '__|  | |/ _ \/ _` | '_ \| __|
 *  ___) |  __/ | | |_| |  __/ (_| | | | | |_
 * |____/ \___|_|  \___/ \___|\__,_|_| |_|\__|
 *
 * @Author https://github.com/rgbbeard
 */

/**
 * Used for development with localhost
 * regardless of whatever port the server will use
 * example: http://localhost/path/to/source
 * 			localhost_base: path/to/source/
 */
define("localhost_base", (str_contains($_SERVER["HTTP_HOST"], "localhost") ? "" : ""));
define("is_localhost", (defined("localhost_base") && !empty(localhost_base)));

require_once "sys/utils.php";
require_once "sys/router.php";
require_once "sys/base_interface.php";

walk_autoload("sys/utils");
walk_autoload("sys/views");
walk_autoload("sys/services");

use Exceptions\InvalidSessionException;
use Database\MySQL;
use Serjeant\SessionManager;
use Serjeant\Router;
use Service\JiraService;

# Check for session availability and start a new session if possible
try {
	SessionManager::check_session();
} catch(InvalidSessionException $e) {
	throw new InvalidSessionException("Cannot use sessions");
}

# Use localhost credentials
MySQL::is_localhost(is_localhost);

JiraService::load_apis();

# Get routes
Router::set_routes();
$router = new Router();