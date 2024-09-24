<?php
$http = $params["response"];
?>
<!doctype html>
<html lang="en-GB">
	<head>
		<title><?php echo $params["title"]; ?> - Serjeant(Lite)</title>
		<?php include "templates/common/headers.php"; ?>
		<link href="<?php echo $params["css"] . "error.css";?>" rel="stylesheet"/>
	</head>
	<body>
		<?php echo $params["navbar"]; ?>
		<main>
            <section class="error-container">
                <article>
                    <h10 class="text-center"><?php echo $http->code; ?></h10>
                    <h7 class="text-center"><?php echo $http->message; ?></h7>
                    <div class="error-icon mt-15">
                        <img src="<?php echo $params["misc"] . $http->icon; ?>" alt="<?php echo $http->code; ?>">
                    </div>
                </article>
            </section>
		</main>
	</body>
</html>