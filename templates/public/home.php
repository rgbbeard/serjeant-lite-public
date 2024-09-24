<?php
$issues = [];

if(!empty($params["issues"])) {
	$issues = $params["issues"]["issues"];
}
?>
<!doctype html>
<html lang="en-GB">
	<head>
		<title>Home - Serjeant(Lite)</title>
		<?php include "templates/common/headers.php"; ?>
	</head>
	<body>
		<?php echo $params["navbar"]; ?>
		<main>
  			<section class="issues-container">
  				<?php foreach($issues as $issue) {
  					# display single issue
  				} ?>
  			</section>
		</main>
	</body>
</html>