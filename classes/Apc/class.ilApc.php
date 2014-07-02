<?php
require_once('./Services/GlobalCache/interfaces/interface.ilGlobalCacheWrapper.php');

/**
 * Class ilApc
 *
 * @beta
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilApc implements ilGlobalCacheWrapper {

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
		self::$active = function_exists('apc_store');
	}


	/**
	 * @return bool
	 */
	public function isInstallable() {
		return function_exists('apc_store');
	}


	/**
	 * @param $key
	 *
	 * @return bool
	 */
	public function exists($key) {
		return apc_exists($key);
	}


	/**
	 * @param      $key
	 * @param      $value
	 * @param null $ttl
	 *
	 * @return bool
	 */
	public function set($key, $value, $ttl = NULL) {
		return apc_store($key, $value, $ttl);
	}


	/**
	 * @param      $key
	 *
	 * @return mixed
	 */
	public function get($key) {
		return apc_fetch($key);
	}


	/**
	 * @param      $key
	 *
	 * @return bool
	 */
	public function delete($key) {
		return apc_delete($key);
	}


	/**
	 * @return bool
	 */
	public function flush() {
		return apc_clear_cache();
	}
}

?>
