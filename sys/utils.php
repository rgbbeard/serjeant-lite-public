<?php
/*
/ Minimum PHP version 7.x
/ Using PHP version 8.0.1
/ Author - Davide
/ Git - github.com/rgbbeard/
*/

const TIMEZONE_ROME = "Europe/Rome";
const TIMEZONE_UTC = "UTC";

function dump(...$items) {
    $debug = "<pre style='background-color:#000;color:#0f0;padding:10px;font-size:1.1em;'>";

    foreach($items as $item) {
        $value = print_r($item, true);

        if(is_bool($item)) {
            $debug .= "bool(" . (intval($value) ? "true" : "false") . ")";
        } elseif(is_numeric($item)) {
            $debug .= "number(" . $value . ")";
        } elseif(is_array($item)) {
			$debug .=  "array(" . replace(
				[
					"{" => "{\n",
					"}" => "\n}",
					",\"" => ",\n\""
				],
				json_encode($item)
			) . ")";
		} else {
			$debug .= $value . "\n";
		}
	}

    $debug .= "</pre>";

    echo $debug;

    $backtrace_array = debug_backtrace();
    $backtrace = "<pre style='background-color:#f0f3;padding:10px;'>";
    foreach($backtrace_array as $stack => $trace) {
    	$file = @$trace["file"];
    	$function = @$trace["function"];
    	$line = @$trace["line"];
    	$class = @$trace["class"];

    	$tmp = "";

    	if($file || $class) {
    		$tmp .= "<i>";

    		if($file) {
    			$tmp .= "$file";
    		}

    		if($file && $class) {
    			$tmp .= "/$class";
    		} elseif($class) {
    			$tmp .= "$class";
    		}

    		$tmp .= "</i>->";
    	}

    	if($function) {
    		$tmp .= "<b>$function</b>";
    	}

    	if($line) {
    		$tmp .= " at line $line";
    	}

    	$backtrace .= "<p style='margin:0;padding:0;font-size:13px;'>[$stack] $tmp</p>\n";
    }
    $backtrace .= "</pre>";
    echo $backtrace;
}

function dd(...$items) {
    dump(...$items);
    die();
}

function info_log(string $message, int $type = 1) {
    $log_type = "INFO";

    switch($type) {
        case 2:
            $log_type = "ERROR";
            break;
        case 3:
            $log_type = "WARNING";
            break;
    }

    error_log("[$log_type] $message");
}

/**
 * Includes files in a directory recursively
 * returns an array with all the files found
 *
 * @param string $folder
 * @return array
 */
function walk_autoload(string $folder): array {
	$filesdebug = [];
	$files = [];
	$basefiles = [];
	$directories = [];
	
	$iterator = new DirectoryIterator($folder);
	
	# find files
	foreach($iterator as $item) {
		$path = $item->getPathname();
		
		if($item->isFile() && $item->getExtension() === "php") {
			if(preg_match("/base_.*/", $path)) {
				$basefiles[] = $path;
			} else {
				$files[] = $path;
			}
		} elseif($item->isDir() && !$item->isDot()) {
			$directories[] = "$folder/" . $item->getFilename();
		}
	}
	
	foreach($basefiles as $file) {
		$filesdebug[] = $file;
		require_once $file;
	}
	
	foreach($files as $file) {
		$filesdebug[] = $file;
		require_once $file;
	}
	
	foreach($directories as $directory) {
		$filesdebug = array_merge(
			$filesdebug,
			walk_autoload($directory)
		);
	}
	
	return $filesdebug;
}

function replace(array $chars, string $target) {
	foreach($chars as $char => $replacement) {
		$target = str_replace($char, $replacement, $target);
	}
	
	return $target;
}

function first(array $target) {
    return array_shift($target);
}

function array_delast(array $target): array {
    array_pop($target);
    return $target;
}

function array_exclude(array $target, $element): array {
    $temp = [];
    for ($x = 0; $x < count($target); $x++) {
        if($x == $element) {
            continue;
        }
        $temp[] = $target[$x];
    }
    return $temp;
}

function array_clear(array $target): array {
    $temp = [];
    foreach($target as $item) {
        if(!empty($item)) {
            $temp[] = $item;
        }
    }
    return $temp;
}

function get_host(): string {
    $host = $_SERVER["HTTP_HOST"];

    $httpx = !@empty($_SERVER["HTTPS"]) ? "https" : "http";

    return "$httpx://$host";
}

# relative path
function relpath(string $file, string $localhostBase = ""): string {
    $current_url = $_SERVER["REQUEST_URI"];

    # Remove parameters
    $paramIndex = strpos($current_url, '?');
    if($paramIndex !== false) {
        $current_url = substr($current_url, 0, $paramIndex);
    }

    # Remove leading slash
    $current_url = ltrim($current_url, '/');

    # Traverse directories
    $levelUp = str_repeat('../', substr_count($current_url, '/'));
    $file = $levelUp . $file;

    # Append localhost base if applicable
    if(defined("localhost_base") && !empty(localhost_base)) {
        $localhostBase = localhost_base;
        $file = $localhostBase . $file;
    } elseif(!empty($localhostBase)) {
        $localhostBase = localhost_base;
        $file = $localhostBase . $file;
    }

    return $file;
}

function is_email(string $target): bool {
    return preg_match("/([a\.\--z\_]*[a0-z9]+@)([a-z]+\.)([a-z]{2,6})/", $target);
}

function is_it_phone(string $target, bool $type = false): bool {
    if($type === true) {
        #Code to detect if it's mobile or phone
    }
    
    return preg_match("/(([30])([0-9]+){9,})/", $target);
}

function is_date(string $target): bool {
    return preg_match("/([\d]+){1,2}[\-|\/]([\d]+){1,2}[\-|\/]([\d]+){4}/", $target);
}