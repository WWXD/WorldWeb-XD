<?php
if (!defined('BLARG')) die();

$aff = "Affected by ";

if (Settings::get('syndromes') == "0") {
	$syndromes = [];
} else if (Settings::get('syndromes') == "1") {
	$syndromes = [
		10 => ["Actually active", "#53C373"],
		20 => ["$aff'Activity Syndrome'", "#69D989"],
		30 => ["$aff'Activity Syndrome' +", "#83F3A3"],

		50 => ["$aff'Mountain Dew Voltage Syndrome'", "#8E83EE"],

		100 => ["{$aff}'Polari Syndrome'", '#89ff8d'],
		150 => ["{$aff}'Polari Syndrome' +", '#ffbb35'],
		200 => ["{$aff}'Mega Goomba Syndrome'", '#ff212f'],

		// this one can't be reached unless the minimum interval between posts is 9 or less
		9001 => ["$aff'Goku Syndrome'", "#FF5353"],
	];
} else if (Settings::get('syndromes') == "2") {
	$syndromes = [
		75 => ["$aff'Reinfors Syndrome'", "#83F3A3"],
		100 => ["$aff'Reinfors Syndrome' +", "#FFE323"],
		150 => ["$aff'Reinfors Syndrome' ++", "#FF5353"],
		200 => ["$aff'Reinfors Syndrome' +++", "#CE54CE"],
		250 => ["$aff'Reinfors Syndrome' ++++", "#8E83EE"],
		300 => ["$aff'Wooster Syndrome'!!", "#BBAAFF"],
		350 => ["$aff'Wooster Syndrome' +!!", "#FFB0FF"],
		400 => ["$aff'Wooster Syndrome' ++!!", "#FFB070"],
		450 => ["$aff'Wooster Syndrome' +++!!", "#C8C0B8"],
		500 => ["$aff'Wooster Syndrome' ++++!!", "#A0A0A0"],
		600 => ["$aff'Anya Syndrome'!!!", "#C762F2"],
		800 => ["$aff'Something higher than Anya Syndrome' +++++!!", "#D06030"],
	];
} else if (Settings::get('syndromes') == "3") {
	$syndromes = [
		25 => ["$aff'Posting Syndrome'", "#53C373"],
		50 => ["$aff'Posting Syndrome' +", "#69D989"],
		75 => ["$aff'Geno Syndrome'", "#83F3A3"],
		100 => ["$aff'Geno Syndrome' +", "#FFE323"],
		150 => ["$aff'Geno Syndrome' ++", "#FF5353"],
		200 => ["$aff'pieguy1372 Syndrome'!", "#CE54CE"],
		250 => ["$aff'pieguy1372 Syndrome' +!", "#8E83EE"],
		300 => ["$aff'pieguy1372 Syndrome' ++!!", "#BBAAFF"],
		350 => ["$aff'Wooster Syndrome' +!!", "#FFB0FF"],
		400 => ["$aff'Wooster Syndrome' ++!!", "#FFB070"],
		450 => ["$aff'Wooster Syndrome' +++!!", "#C8C0B8"],
		500 => ["$aff'Wooster Syndrome' ++++!!", "#A0A0A0"],
		600 => ["$aff'Anya Syndrome'!!!", "#C762F2"],
		800 => ["$aff'Xkeeper Syndrome'!!!!", "#D06030"],
		1000 => ["$aff'Wtf?! Syndrome'!~", "#FF2277"],
	];
} else if (Settings::get('syndromes') == "4") {
	$syndromes = [
		0 => ["$aff'Laziness'", "#ff0000"],
		1 => ["$aff'Carpal Tunnel Syndrome'", "#76655c"],
		5 => ["$aff'Trooperness'", "#6d9a6d"],
		25 => ["$aff'1/35th Vizzed Syndrome'", "#716f96"],
		75 => ["$aff'Trooperness A+'", "#459a45"],
		100 => ["$aff'Official Post Spree Syndrome'", "#ffa200"],
		150 => ["$aff'Ravering Syndrome'", "#c585cf"],
		200 => ["$aff'Ravering Syndrome+'", "#cb6ad9"],
		300 => ["$aff'Veneeval Syndrome'", "#76cbce"],
		350 => ["$aff'Veneeval Syndrome+'", "#3ac9ce"],
		555 => ["$aff'Yahmonners Syndrome'", "#c9c9c9"],
		777 => ["$aff'Lucky 777 Syndrome'", "#06ff00"],
		875 => ["$aff'<b>VIZZED</b> Syndrome'", "#483fff"],
	];
};