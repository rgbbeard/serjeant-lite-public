<?php
Use Serjeant\Router;
$img = $params["img"];
$user_is_logged_in = !@empty($_SESSION["USERNAME"]);
?>
<nav id='navbar' class='navbar'>
	<div class="container">
		<div class="nav-logo-container">
			<a href="<?php echo Router::generate_link("home"); ?>">
				<img 
					src="<?php echo $img;?>/named-icon-inversed.svg"
					title="Serjeant"
					alt="Immagine non disponibile"/>
			</a>
		</div>
		<div class="nav-main-menu">
			<?php foreach($params["menu_items"] as $name => $item) {
				if($item["visible"]) {
					if(
                        !@empty($item["authenticated"])
                        && !Router::has_required_roles(Router::get_allowed_roles($name))
                    ) {
						continue;
					}

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
        <div class="nav-profile-container mt-20">
            <profile>
                <?php echo $user_is_logged_in ? strtoupper(substr($_SESSION["USERNAME"], 0, 1)) : "?"; ?>
            </profile>
            <label for="username" class="ml-20">
				<?php echo $user_is_logged_in ? $_SESSION["USERNAME"] : "Unknown user"; ?>
            </label>
            <input type="checkbox" name="username" id="username" hidden/>
            <ul>
                <?php
                if($user_is_logged_in) {
                    echo "<a href=\"" . Router::generate_link("user_profile") . "\">
                        <li>
                            Your profile
                        </li>
                    </a>
                    <a href=\"" . Router::generate_link("user_logout") . "\">
                        <li>
                            Logout
                        </li>
                    </a>";
                }
                ?>
            </ul>
        </div>
	</div>
</nav>