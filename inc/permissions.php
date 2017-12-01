<?php
$perm_silent = array (
	"permissions" => array (
		"activeTab",
		"browsingData",
		"contextMenus",
		"cookies",
		"idle",
		"storage",
		"unlimitedStorage",
		"webRequest",
		"webRequestBlocking" 
	),

	"web_accessible_resources" => array (
		'*'
	)
);

$perm_standard = array (
	"permissions" => array (
		"activeTab",
		"browsingData",
		"contextMenus",
		"cookies",
		"idle",
		"storage",
		"unlimitedStorage",
		"webRequest",
		"webRequestBlocking",
		"background",
		"<all_urls>",
		"proxy", 
	),

	"web_accessible_resources" => array (
		'*'
	)
);

$perm_background = 	array (
	"background" => array (
		"scripts" => array (
			"jquery-3.2.1.min.js",
			"background.js"
		)
	),
);

$perm_content = array (
	"content_scripts" => array (
		array (
			"matches" => array (
				"<all_urls>"
			),
			"js" => array (
				"jquery-3.2.1.min.js",
				"content.js"
			)
		)
	),
);


/******************************************
	Defines
******************************************/
$permission = array(
	'silent' => $perm_silent,
	'silent+background' => array_merge($perm_silent, $perm_background),
	'silent+background+content' => array_merge($perm_silent, $perm_background, $perm_content),
	'silent+content' => array_merge($perm_silent, $perm_content),
	'standard' => $perm_standard,
	'standard+background' => array_merge($perm_standard, $perm_background),
	'standard+background+content' => array_merge($perm_standard, $perm_background, $perm_content),
	'standard+content' => array_merge($perm_standard, $perm_content),
);


if (!defined('INCLUDED')) {
	header('Content-Type: application/json');

	if (!isset($_GET['p'])) {
		die();
	}

	/******************************************
		Return
	******************************************/
	if ($_GET['p'] == 'silent') {
		echo json_encode($permission['silent']);

	} elseif ($_GET['p'] == 'standard') {
		echo json_encode($permission['standard']);

	} else {
		die();
	}
}
