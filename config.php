<?php
// Maybe edit these?
$config = array(
	'password' => '',  // only used when you're not running on localhost - cannot be left blank
	'save_path' => 'extensions', // relative path for storing the unpacked extension (default is probs fine)
	'timezone' => 'UTC', // https://secure.php.net/manual/en/timezones.php (default is probs fine)
);

// Okay, now go make some extensions!

// PHP version check
if (version_compare(PHP_VERSION, '5.5.7', '<')) {
  die('PHP version >= 5.5.7 required. You\'re running: ' . PHP_VERSION);
}

// Config defines
foreach ($config as $key => $value) {
	define(strtoupper($key), $value);
}

// Other defines
define('TPL_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'template_files' . DIRECTORY_SEPARATOR);
define('INCLUDED', true);

//
require_once('inc/permissions.php');
require_once('inc/functions.php');
require_once('inc/err.php'); $err = new err();
date_default_timezone_set(TIMEZONE);
ini_set('session.cookie_httponly', 1);
session_start();

// 
error_reporting(E_ALL);

//
define('VERSION', '1.0.0');
define('APP_NAME', 'Chrown');
define('APP_DOCO', 'https://github.com/sudosammy/chrown/');

// Headers
header('content-type: text/html; charset=utf-8');
header('x-content-type: nosniff');
header('x-xss-protection: 1; mode=block');
header('x-frame-options: DENY');

// Are we running on localhost?
if (stristr($_SERVER['HTTP_HOST'], 'localhost') || stristr($_SERVER['HTTP_HOST'], '127.0.0.1')) {
  define('EXTERNAL', false);
} else {
  define('EXTERNAL', true);
}
