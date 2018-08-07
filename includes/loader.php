<?php
/** ------------ */
/**  Oli Loader  */
/** ------------ */

/** Define PHP_VERSION_ID if not defined (PHP < 5.2.7) */
if(!defined('PHP_VERSION_ID')) {
	$phpVersion = explode('.', PHP_VERSION);
	define('PHP_VERSION_ID', $phpVersion[0] * 10000 + $phpVersion[1] * 100 + $phpVersion[2]);
}

/** Load files in includes/ */
foreach(glob(INCLUDESPATH . '*.php') as $filename) {
	if($filename != __FILE__) include $filename;
}
?>