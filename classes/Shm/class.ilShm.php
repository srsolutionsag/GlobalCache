<?php
require_once('./Services/GlobalCache/interfaces/interface.ilGlobalCacheWrapper.php');

/**
 * Class ilShm
 *
 * @beta
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilShm implements ilGlobalCacheWrapper {

	/**
	 * @var bool
	 */
	protected static $active = false;
	/**
	 * @var int
	 */
	protected static $id = 0;
	/**
	 * @var ressource
	 */
	protected static $ressource = NULL;


	/**
	 * @return bool
	 */
	public function isActive() {
		return self::$active;
	}


	public function __construct() {
		$tmp = tempnam('/tmp', 'PHP');
		$key = ftok($tmp, 'a');
		self::$ressource = shm_attach($key);;
		self::$id = (int)self::$ressource;
		self::$active = function_exists('shm_put_var');
	}


	/**
	 * @return bool
	 */
	public function isInstallable() {
		return function_exists('shm_put_var');
	}


	/**
	 * @param $key
	 *
	 * @return bool
	 */
	public function exists($key) {
		return shm_has_var(self::$ressource, $key);
	}


	/**
	 * @param      $key
	 * @param      $value
	 * @param null $ttl
	 *
	 * @return bool
	 */
	public function set($key, $value, $ttl = NULL) {
		return shm_put_var(self::$ressource, $key, serialize($value));
	}


	/**
	 * @param      $key
	 *
	 * @return mixed
	 */
	public function get($key) {
		return unserialize(shm_get_var(self::$ressource, $key));
	}


	/**
	 * @param      $key
	 *
	 * @return bool
	 */
	public function delete($key) {
		return shm_remove_var(self::$ressource, $key);
	}


	/**
	 * @return bool
	 */
	public function flush() {
		shmop_delete(self::$id);

		return true;
	}
}

?>
