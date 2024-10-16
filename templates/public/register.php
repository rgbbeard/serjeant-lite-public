<?php
use Serjeant\Router;

$username = $params["username"];
$password = $params["password"];
$display = $params["display"];
$result = $params["result"];
$form_sent = $params["form_sent"];
?>
<!doctype html>
<html lang="en-GB">
	<head>
		<title>Register - Serjeant(Lite)</title>
		<?php include "templates/common/headers.php"; ?>
	</head>
	<body>
		<?php echo $params["navbar"]; ?>
		<main>
			<form action="" method="post">
				<a
					href="<?php echo Router::generate_link("home"); ?>"
                    class="btn candy link">
                    Do you already have an account? Go back
                </a>
				<h3>Register an account</h3>
				<div class="input-group">
					<label for="username">Create an username</label>
					<input
						id="username"
						type="text"
						name="username"
						placeholder="username"
                        autocomplete="off"
                        minlength="10"
	                    required
                    	value="<?php echo $username; ?>"/>
				</div>
				<div class="input-group">
					<label for="password">Choose a password</label>
					<input
						id="password"
						type="password"
						name="password"
						placeholder="password"
                    	autocomplete="off"
                        minlength="6"
                    	required
                        value="<?php echo $password; ?>"/>
				</div>
				<div class="input-group">
					<label for="display">Display (this will be your public name)</label>
					<input
						id="display"
						type="text"
						name=display
						placeholder="display"
                        minlength="3"
                    	required
                        value="<?php echo $display; ?>"/>
				</div>
				<input class="btn candy success" type="submit" name="register" value="Create" />
				<?php
				if($form_sent) {
                    $classes = ["alert", "matte"];
                    $message = "Registration error";
                    
                    switch($result) {
                        case 1:
                            $classes[] = "error";
                            break;
                        case 0:
                            $message = "An user already exists with this username";
                            $classes[] = "error";
                            break;
                        default:
                            $message = "Registration completed";
                            $classes[] = "success";
                            break;
                    }
                    
                    $classes = implode(" ", $classes);
                    include "templates/modules/alert.php";
				}
				?>
			</form>
		</main>
	</body>
</html>