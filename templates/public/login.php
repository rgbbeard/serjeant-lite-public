<?php
use Serjeant\Router;

$login_succeeded = $params["login_succeeded"];
$form_sent = $params["form_sent"];
?>
<!doctype html>
<html lang="en-GB">
	<head>
		<title>Login - Serjeant(Lite)</title>
		<?php include "templates/common/headers.php"; ?>
	</head>
	<body>
		<?php echo $params["navbar"]; ?>
		<main>
			<form action="" method="post">
                <h3>Login to Serjeant</h3>
				<div class="input-group">
					<label for="username">Username</label>
					<input
                        id="username"
                        type="text"
                        name="username"
                        placeholder="username"
                        value="<?php echo $params["username"]; ?>"/>
				</div>
				<div class="input-group">
					<label for="password">Password</label>
					<input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="password"
                        value="<?php echo $params["password"]; ?>"/>
				</div>
				<button class="btn candy primary">Login</button>
				<?php
				if(!$login_succeeded && $form_sent) {
					?>
					<p class="alert error matte">Invalid credentials</p>
					<?php
				}
				?>
                <hr ignore/>
                <a
                    href="<?php echo Router::generate_link("user_registration"); ?>"
                    class="btn candy link">
                    Are you new to Serjeant? Register an account now!
                </a>
			</form>
		</main>
	</body>
</html>