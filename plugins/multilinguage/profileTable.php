<?php
if ($user['linguage'] === '-default')
	$profileParts[__('Presentation')][__('Language')] = 'Board default';
else
	$profileParts[__('Presentation')][__('Language')] = $user['linguage'];
