<?php
Use Serjeant\Router;
?>
<nav id='navbar' class='navbar'>
	<div class="">
		<div class="nav-logo-container">
			<a href="<?php echo Router::generate_link("admin"); ?>">
				<img 
					src="<?php echo $params["img"];?>/icon-inversed.svg"
					title="Serjeant"
					alt="Immagine non disponibile" />
			</a>
		</div>
		<div class="nav-main-menu">
			<?php foreach($params["menu_items"] as $name => $item) {
				if($item["visible"]) {
					$link = Router::generate_link($name);
					$selected = $name === Router::get_current_route() ? "selected" : "";

					echo "<a href='$link'>
						<li class='navbar-item $selected'>
						" . $item["display"] . "	
						</li>
					</a>";
				}
			}?>
		</div>
	</div>
</nav>