<?php
namespace View;

use Exceptions\TemplateNotFoundException;
use Service\HTTPService;
use Base\BaseInterface;
use \ReflectionClass;

class BaseView implements BaseInterface {
	protected string $http_template = "templates/public/http.php";

	protected string $css = "";
	protected string $js = "";
	protected string $img = "";
	protected string $misc = "";

	protected array $children = [];

	public function __construct() {
		$this->list_child_classes();
		$this->prepare_links();
	}
	
	public function list_child_classes() {
	    foreach (get_declared_classes() as $class) {
	        if (is_subclass_of($class, __CLASS__)) {
	            $this->children[] = $class;
	        }
	    }
	}

	public function find_owner_of(string $method) {
        if(!empty($this->children)) {
        	foreach($this->children as $child) {
        		if(method_exists($child, $method)) {
        			return $child;
        		}
        	}
        }
        
        return null;
    }

    private function get_caller() {
    	$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);

    	if(isset($trace[2])) {
    		$caller = $trace[2];
    		return $caller["class"];
    	}

    	return false;
    }

	/**
	 * automatically build menus
	 * 
	 * example return:
	 * 		[
	 * 			"home" => [
	 * 				"order" => 1,
	 * 				"visible" => true,
	 * 				"display" => "Homepage"	
	 * 			]
	 * 		]
	 */
    public function get_menu_methods(): array {
    	$caller = $this->get_caller();

    	$class = new ReflectionClass($caller);
		$methods = $class->getMethods();

		$annotatedMethods = [];

		try {
            $class = new ReflectionClass($caller);
            $methods = $class->getMethods();

            $annotatedMethods = [];

            foreach ($methods as $method) {
                $comment = $method->getDocComment();

                if ($comment !== false && str_contains($comment, "@menu")) {
                	$route_name = preg_match("/\@route\((.*)\)/", $comment, $matches);

                	# menu notations must have a @route attribute
                	if($matches && !empty($matches[1])) {
                		$route_name = $matches[1];
                	} else {
                		continue;
                	}

                	$menu_comment = preg_match("/\@menu\(.*\)/", $comment, $matches);
                	$notations = [];

                	if($matches && $matches[0]) {
                		$matches[0] = trim($matches[0]);

                		$matches[0] = preg_replace("/^\@menu\(/", "", $matches[0]);
                		$matches[0] = preg_replace("/\)$/", "", $matches[0]);

                		$attributes = explode(", ", $matches[0]);

                		foreach($attributes as $attribute) {
                			$n = explode(": ", $attribute);

                			$notations[$n[0]] = $n[1];
                		}
                	}

                    $annotatedMethods[$route_name] = $notations;
                } else {
                	continue;
                }
            }

            # order items
            uasort($annotatedMethods, function($a, $b) {
            	return $a["order"] > $b["order"];
            });

            return $annotatedMethods;
        } catch (ReflectionException $e) {
            echo "Reflection error: " . $e->getMessage() . "\n";
        }
    }

	public function prepare_links() {
		if(empty($this->css) || empty($this->js) || empty($this->img) || empty($this->misc)) {
			$this->css .= "/res/css/";
			$this->js .= "/res/js/";
			$this->img .= "/res/img/";
			$this->misc .= "/res/misc/";
		}

		if(defined("localhost_base") && !empty(localhost_base)) {
			if(!str_contains($this->css, localhost_base) 
				|| !str_contains($this->js, localhost_base) 
				|| !str_contains($this->img, localhost_base)) {
				$this->css = localhost_base . $this->css;
				$this->js = localhost_base . $this->js;
				$this->img = localhost_base . $this->img;
				$this->misc = localhost_base . $this->misc;
			}
		}
	}

	protected function render(string $template_name, array $params = []): bool|string {
		try {
			ob_start();
			
			$params = array_merge([
				"img" => $this->img,
				"css" => $this->css,
				"js" => $this->js,
				"misc" => $this->misc
			], $params);
			
			if(file_exists($template_name)) {
				require_once $template_name;
			} else {
				$template_name = $this->http_template;
				if(file_exists($template_name)) {
					$response = HTTPService::get_http_response(600);
					
					$params = array_merge([
						"response" => $response,
						"title" => $response->message
					], $params);
					
					require_once $template_name;
				}
			}
			
			return ob_get_clean();
		} catch(TemplateNotFoundException $e) {
			return false;
		}
	}

	public function render_http_response(HTTPService $response) {
		echo $this->render(
			$this->http_template, [
				"response" => $response, 
				"title" => $response->httpr->message
			]
		);
		die();
	}
}