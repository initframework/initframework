<?php
namespace Library\Http;

class Cache
{
	private static $expire;

	// private static function __instance($expire = CACHE_EXPIRE) {
	// 	self::$expire = $expire;

	// 	$files = glob(CACHE_DIR . 'cache.*');

	// 	if ($files) {
	// 		foreach ($files as $file) {
	// 			$time = substr(strrchr($file, '.'), 1);

	// 			if ($time < time()) {
	// 				if (file_exists($file)) {
	// 					unlink($file);
	// 				}
	// 			}
	// 		}
	// 	}
	// }

	// public static function get($key) {
	// 	self::__instance();
	// 	$files = glob(CACHE_DIR . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

	// 	if ($files) {
	// 		$handle = fopen($files[0], 'r');

	// 		flock($handle, LOCK_SH);

	// 		$data = fread($handle, filesize($files[0]));

	// 		flock($handle, LOCK_UN);

	// 		fclose($handle);

	// 		return json_decode($data, true);
	// 	}

	// 	return false;
	// }

	// public static function set($key, $value) {
	// 	self::__instance();
	// 	self::delete($key);

	// 	$file = CACHE_DIR . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.' . (time() + self::$expire);

	// 	$handle = fopen($file, 'w');

	// 	flock($handle, LOCK_EX);

	// 	fwrite($handle, json_encode($value));

	// 	fflush($handle);

	// 	flock($handle, LOCK_UN);

	// 	fclose($handle);
	// }

	// private static function delete($key) {
	// 	$files = glob(CACHE_DIR . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

	// 	if ($files) {
	// 		foreach ($files as $file) {
	// 			if (file_exists($file)) {
	// 				unlink($file);
	// 			}
	// 		}
	// 	}
	// }
}