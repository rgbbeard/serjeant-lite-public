<?php
namespace Base;

interface BaseInterface {
	public function find_owner_of(string $method);
	
	public function list_child_classes();
}
