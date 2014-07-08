<?php
require_once('./Services/GlobalCache/interfaces/interface.ilGlobalCacheWrapper.php');

/**
 * Class ilMemcache
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilMemcache implements ilGlobalCacheWrapper {

	const PERSISTENT_ID = 'ilias_pers_cache';
	/**
	 * @var Memcached
	 */
	protected static $memcache_object;
	/**
	 * @var bool
	 */
	protected static $active = false;


	public function __construct() {
		if (! (self::$memcache_object instanceof Memcached)) {
			$memcached = new Memcached(self::PERSISTENT_ID);
			//$memcached->setOption(Memcached::OPT_CONNECT_TIMEOUT, 10);
			$memcached->addServer('127.0.0.1', 11211);
			// var_dump($memcached->getStats()); // FSX
			self::$memcache_object = $memcached;
			$stats = $memcached->getStats();
			self::$active = $stats['127.0.0.1:11211']['pid'] > 0;
		}
	}


	/**
	 * @return Memcached
	 */
	protected function getMemcacheObject() {
		return self::$memcache_object;
	}


	/**
	 * @return bool
	 */
	public function isActive() {
		return self::$active;
	}


	/**
	 * @return bool
	 */
	public function isInstallable() {
		return class_exists('Memcached');
	}


	/**
	 * @param $key
	 *
	 * @return bool
	 */
	public function exists($key) {
		return $this->getMemcacheObject()->get($key) != NULL;
	}


	/**
	 * @param      $key
	 * @param      $value
	 * @param null $ttl
	 *
	 * @return bool
	 */
	public function set($key, $value, $ttl = NULL) {
		return $this->getMemcacheObject()->set($key, $value, $ttl);
	}


	/**
	 * @param      $key
	 *
	 * @return mixed
	 */
	public function get($key) {
		return $this->getMemcacheObject()->get($key);
	}


	/**
	 * @param      $key
	 *
	 * @return bool
	 */
	public function delete($key) {
		return $this->getMemcacheObject()->delete($key);
	}


	/**
	 * @return bool
	 */
	public function flush() {
		return $this->getMemcacheObject()->flush();
	}
}

?>
