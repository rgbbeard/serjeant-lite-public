<?php
use Serjeant\Router;

$infos = $params["infos"];
?>
<!doctype html>
<html lang="en-GB">
<head>
	<title>Profile - Serjeant(Lite)</title>
	<?php include "templates/common/headers.php"; ?>
</head>
	<body>
		<?php echo $params["navbar"]; ?>
		<main>
			<section class="profile-container">
                <?php
                if($params["requires_pat"]) {
                    $classes = "alert matte error";
                    $message = "You need to add a Jira PAT to continue using this program";
					include "templates/modules/alert.php";
				}
				
				if($params["update_succeeded"]) {
					$classes = "alert matte success";
					$message = "Profile data updated successfully";
					include "templates/modules/alert.php";
				}
                ?>
				<form action="<?php echo Router::generate_link("update_user_infos"); ?>" method="post">
					<h2>User: <?php echo $_SESSION["USERNAME"]; ?></h2>
					<div class="input-group">
						<label for="display">Display</label>
						<input id="display" type="text" name="display" value="<?php echo $infos["display"]; ?>" required>
					</div>
                    <div class="input-group password-input mb-10">
                        <label for="pat">Jira PAT</label>
                        <input id="pat" type="password" name="pat" value="<?php echo $infos["decrypted_token"]; ?>" required>
                        <span></span>
                    </div>
					<input class="btn candy primary" type="submit" name="save" value="Save infos">
				</form>
			</section>
		</main>
	</body>
</html>