<?php
require_once('./Services/GlobalCache/interfaces/interface.ilGlobalCacheWrapper.php');

/**
 * Class ilXcache
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilXcache implements ilGlobalCacheWrapper {

	/**
	 * @var bool
	 */
	protected static $active = false;


	/**
	 * @return bool
	 */
	public function isActive() {
		return self::$active;
	}


	public function __construct() {
		$function_exists = function_exists('xcache_set');
		$var_size = ini_get('xcache.var_size') != '0M';
		$var_count = ini_get('xcache.var_count') > 0;
		$api = (php_sapi_name() !== 'cli');
		self::$active = ($function_exists AND $var_size AND $var_count AND $api);
	}


	/**
	 * @return bool
	 */
	public function isInstallable() {
		return self::$active;
	}


	/**
	 * @param $key
	 *
	 * @return bool
	 */
	public function exists($key) {
		return xcache_isset($key);
	}


	/**
	 * @param      $key
	 * @param      $value
	 * @param null $ttl
	 *
	 * @return bool
	 */
	public function set($key, $value, $ttl = NULL) {
		return xcache_set($key, ($value), $ttl);
	}


	/**
	 * @param      $key
	 *
	 * @return mixed
	 */
	public function get($key) {
		return (xcache_get($key));
	}


	/**
	 * @param      $key
	 *
	 * @return bool
	 */
	public function delete($key) {
		return xcache_unset($key);
	}


	/**
	 * @return bool
	 */
	public function flush() {
		xcache_clear_cache(- 1);

		return true;
	}
}

?>
