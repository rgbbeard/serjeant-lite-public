<?php
$login_succeeded = $params["login_succeeded"];
$form_sent = $params["form_sent"];
?>
<!doctype html>
<html>
	<head>
		<title>Login - Serjeant(Lite)</title>
		<?php include "templates/common/headers.php"; ?>
		<style>
			form {
				width: 80%;
				margin: 20px auto 0px;
			}
		</style>
	</head>
	<body>
		<?php echo $params["navbar"]; ?>
		<div>
			<form action="" method="post">
				<div class="input-group">
					<label for="username">Username</label>
					<input id="username" type="text" name="username" placeholder="username" autocomplete="off"/>
				</div>
				<div class="input-group">
					<label for="password">Password</label>
					<input id="password" type="password" name="password" placeholder="password" autocomplete="off"/>
				</div>
				<button class="btn candy primary">Login</button>
				<?php 
				if(!$login_succeeded && $form_sent) {
					?>
					<p class="banner error">Wrong username or password</p>
					<?php
				}
				?>
			</form>
		</div>
	</body>
</html>