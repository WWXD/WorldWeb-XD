<?php
$title = __("IRC Page");
// The network hostname of your IRC channel
$net = "irc.yournetwork.net";
// The actual name of the channel without the beginning #
$chan = "YourChannel";
// The name prefix for anyone joining
$nameprefix = "IRCGuest";
?>
<html>
<div align="center">
<center>
<iframe src="https://kiwiirc.com/client/<?php echo $net; ?>/?nick=<?php echo $nameprefix; ?>/?#<?php echo $chan; ?>" style="border:0; width:125%; height:525px;"></iframe>
<p>If your IRC client allows irc:// links, click <a href="irc://<?php echo $net; ?>/<?php echo $chan; ?>">here</a> to join our channel!</p>
<p>Channel: #<?php echo $chan; ?></p>
<p>Server: <?php echo $net; ?></p>
</center>
</div>
</html>
