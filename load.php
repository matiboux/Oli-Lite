<?php
/** Define Initial Timestamp & Absolute Path */
if(!defined('INITTIME')) define('INITTIME', $initTimestamp = microtime(true));
if(!defined('ABSPATH')) define('ABSPATH', dirname(__FILE__) . '/');

/** Form Data (FILES, POST and GET arguments) */
$_ = array_merge($_GET, $_POST, $_FILES);

/** Get Oli Source Files Path */
$oliPath = file_exists(ABSPATH . '.olipath') ? file_get_contents(ABSPATH . '.olipath') : null;
if($oliPath AND !file_exists($oliPath . 'includes/')) $oliPath = null;

/** Define Oli Path & Main Paths Constants */
if(!defined('OLIPATH')) define('OLIPATH', $oliPath ?: ABSPATH); unset($oliPath);
if(!defined('INCLUDESPATH')) define('INCLUDESPATH', OLIPATH . 'includes/');
if(!defined('CONTENTPATH')) define('CONTENTPATH', ABSPATH . 'content/');

/** Load Oli Lite */
if(file_exists(INCLUDESPATH . 'loader.php')) require INCLUDESPATH . 'loader.php';
else die('Error: The <b>loader.php</b> file countn\'t be found! (in "' . INCLUDESPATH . 'loader.php")');
$_Oli = new \Oli\OliLite(INITTIME);

/** Load Config */
if(file_exists(ABSPATH . 'config.php')) include ABSPATH . 'config.php';
?>