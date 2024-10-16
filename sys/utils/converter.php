<?php
namespace Serjeant;

use Exception;
use stdClass;

class Converter {
	public static function json_decode(string $json_object, bool $cast_to_array = false): stdClass|array {
		$result = new stdClass();
		
		try {
			$result = json_decode($json_object);
			
			if($cast_to_array) {
				$result = self::std2array($result);
			}
		} catch(Exception $e) {
			dd($e->getMessage());
		}
		
		return $result;
	}
	
	public static function std2array(stdClass $target): array {
		$result = [];
		
		try {
			foreach($target as $name => $value) {
				if($value instanceof stdClass) {
					$result[$name] = self::std2array($value);
				} else {
					$result[$name] = $value;
				}
			}
		} catch(Exception $e) {
			dd($e->getMessage());
		}
		
		return $result;
	}
	
	public static function capitalize(string $target): string {
		$words = explode(" ", $target);
		$temp = [];
		
		foreach($words as $word) {
			$word = strtolower($word);
			$temp[] = ucfirst($word);
		}
		
		return implode(" ", $temp);
	}
}