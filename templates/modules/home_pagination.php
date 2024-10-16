<?php
use Serjeant\Router;

$page = $params["page"];
?>
<div class="pagination">
	<?php
		$prev = $pages[0] == 1 ? 1 : $pages[0]-1;
		$next = end($pages)+1;

		echo "<a class=\"page\" href=\"" . Router::generate_link("home", ["page" => $prev]) . "\">Prev</a>";
		foreach($pages as $p) {
			$selected = "";

			if($p == $page) {
				$selected = "selected";
			}

			echo "<a class=\"page $selected\" href=\"" . Router::generate_link("home", ["page" => $p]) . "\">$p</a>";
		}
		echo "<a class=\"page\" href=\"" . Router::generate_link("home", ["page" => $next]) . "\">Next</a>";
	?>
</div>