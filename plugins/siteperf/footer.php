<?php
$footerExtensionsB .= format(__("Page rendered in {0} seconds with {1}") . "<br>", sprintf('%1.3f', usectime()-$timeStart), Plural($queries, __("MySQL query")));
?>
