<?php 
use Serjeant\Router;

$css = $params["css"];
?>
<!doctype html>
<html>
	<head>
		<title>Administration - Serjeant(Lite)</title>
		<?php include "templates/common/headers.php"; ?>
		<link href="<?php echo $css . "admin.css"; ?>" rel="stylesheet">
	</head>
	<body>
		<?php echo $params["navbar"]; ?>
		<main>
		
		</main>
	</body>
</html>