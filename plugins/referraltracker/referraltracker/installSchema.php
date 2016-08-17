<?php

$tables['referrals'] = array
	(
		'fields' => array
		(
			'ref_hash' => $var256,
			'referral' => $text,
			'count' => $genericInt,
		),
		'special' => 'primary key (`ref_hash`)'
	);
	
