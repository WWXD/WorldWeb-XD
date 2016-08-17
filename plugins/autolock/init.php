<?php

//Autolock system
$locktime = time() - (2592000 * Settings::pluginGet("months"));
Query("UPDATE {threads} SET closed=1 WHERE closed=0 AND lastpostdate < {0}", $locktime);


