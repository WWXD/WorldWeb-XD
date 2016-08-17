<?php
$mcTitles = Settings::pluginGet("titles");
$mcTitles = explode("\n", $mcTitles);

//Remove empty strings
$mcTitles = array_map('trim', $mcTitles);
$mcTitles = array_filter($mcTitles); 

//Choose one randomly!
if(count($mcTitles))
	$mcTitle = $mcTitles[array_rand($mcTitles)];
else
	$mcTitle = "Minecraft Title!";


?>

	<script type="text/javascript" src="<?php print resourceLink("plugins/mctitles/makeTitle.js");?>"></script>
	<script type="text/javascript">
		window.addEventListener("load", function() {
			makeMcTitle(<?php print json_encode($mcTitle);?>);
		}, false);
	</script>
	<link rel="stylesheet" type="text/css" href="<?php print resourceLink("plugins/mctitles/mctitles.css");?>" />
