<?php
namespace Service;

use Base\BaseInterface;
use Database\MySQL;

class BaseService implements BaseInterface
{
	protected MySQL $conn;
	
	public function __construct() {
		$this->conn = new MySQL();
	}
	
	public function list_child_classes() {}
	
	public function find_owner_of(string $method) {}
}