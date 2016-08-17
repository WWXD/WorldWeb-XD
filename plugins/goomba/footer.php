<?php

echo "
	<script>
	(function () {
		var i = 0;
		for (; i < " . ((int) Settings::pluginGet('goombas')) . "; ++i) {
			setTimeout(function () {
				var goomba = new Goomba();
				goomba.startWalking();
			}, " . ((float) Settings::pluginGet('interval')) . " * i);
		}
	})();
	</script>
";

?>
