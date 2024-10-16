<?php
$issues = [];

if(!empty($params["issues"])) {
	$issues = $params["issues"]["issues"];
}

$pages = $params["pages"];
?>
<!doctype html>
<html lang="en-GB">
	<head>
		<title>Home - Serjeant(Lite)</title>
		<?php include "templates/common/headers.php"; ?>
		<script type="module" src="<?php echo $params["js"]; ?>home_search_issue.js"></script>
		<script type="module" src="<?php echo $params["js"]; ?>home.js"></script>
	</head>
	<body>
		<?php echo $params["navbar"]; ?>
		<main>
			<div class="input-group issue-finder">
				<label for="issue-finder">Find issue</label>
				<input type="text" id="issue-finder" />
			</div>
			<?php include "templates/modules/home_pagination.php"; ?>
  			<section class="issues-container">
  				<?php
	  				if(empty($issues)) {
	  					echo "<p>No issues here</p>";
	  				}

	  				foreach($issues as $issue) {
	  					$main = $issue->fields;

	  					try {
	  						$status =  "<img src='" . $main->status->iconUrl . "' title='" . $main->status->name . "'/>";
	  					} catch(Exception $e) {
	  						$status = "";
	  					}

	  					try {
	  						$priority = "<img src='" . $main->priority->iconUrl . "' title='" . $main->priority->name . "'/>";
	  					} catch(Exception $e) {
	  						$priority = "";
	  					}

	  					echo "<div class=\"issue\" data-id=\"{$issue->id}\">
	  						<h5 class=\"title\">{$main->summary}</h5>
	  						<h6 class=\"subtitle\">({$issue->key})</h6>
							<div class=\"infos\">
								<span class=\"status\">$status</span>
								<span class=\"priority\">$priority</span>
							</div>
	  					</div>";
	  				}
  				?>
  			</section>
			<?php include "templates/modules/home_pagination.php"; ?>
		</main>
	</body>
</html>