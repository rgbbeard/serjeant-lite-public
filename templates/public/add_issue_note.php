<?php
use Serjeant\Router;

$issue = $params["issue"];
$issue_id = $params["issue_id"];
?>
<!doctype html>
<html lang="en-GB">
	<head>
		<title>Add issue note - Serjeant(Lite)</title>
		<?php include "templates/common/headers.php"; ?>
	</head>
	<body>
		<?php echo $params["navbar"]; ?>
		<main>
  			<section class="issue-container">
  				<form action="<?php echo Router::generate_link("exec_add_issue_note"); ?>" method="post">
  					<input type="hidden" name="issue_id" value="<?php echo $issue_id; ?>">
  					<div class="input-group">
  						<textarea id="note" name="note" placeholder="note"></textarea>
  						<label for="note">Note</label>
  					</div>
  				</form>
  			</section>
		</main>
	</body>
</html>