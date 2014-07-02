<?php
require_once('./Services/GlobalCache/interfaces/interface.ilGlobalCacheWrapper.php');
require_once('./Services/GlobalCache/classes/Memcache/class.ilMemcache.php');
require_once('./Services/GlobalCache/classes/Xcache/class.ilXcache.php');
require_once('./Services/GlobalCache/classes/Shm/class.ilShm.php');
require_once('./Services/GlobalCache/classes/Apc/class.ilApc.php');

/**
 * Class ilGlobalCache
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilGlobalCache implements ilGlobalCacheWrapper {

	const TYPE_XCACHE = 1;
	const TYPE_MEMCACHED = 2;
	const TYPE_SHM = 3;
	const TYPE_APC = 4;
	const MSG = 'Global Cache not active, can not access cache';
	/**
	 * @var array
	 */
	protected static $types = array( self::TYPE_MEMCACHED, self::TYPE_XCACHE, self::TYPE_SHM, self::TYPE_APC );
	/**
	 * @var ilGlobalCache
	 */
	protected static $instance;
	/**
	 * @var ilGlobalCacheWrapper
	 */
	protected $global_cache;
	/**
	 * @var string
	 */
	protected $prefix = '';


	/**
	 * @return ilGlobalCache
	 */
	public static function getInstance() {
		if (! isset(self::$instance)) {
			$ilGlobalCache = new self(self::TYPE_XCACHE);
			$ilGlobalCache->setPrefix(substr(md5(ILIAS_HTTP_PATH), 0, 6) . '_');
			self::$instance = $ilGlobalCache;
		}

		return self::$instance;
	}


	/**
	 * @return ilGlobalCache[]
	 */
	public static function getAllInstallableTypes() {
		$types = array();
		foreach (self::$types as $type) {
			$obj = new self($type);
			if ($obj->isInstallable()) {
				$types[] = $obj;
			}
		}

		return $types;
	}


	/**
	 * @param $type
	 */
	protected function __construct($type) {
		switch ($type) {
			case self::TYPE_MEMCACHED:
				$this->global_cache = new ilMemcache();
				break;
			case self::TYPE_XCACHE:
				$this->global_cache = new ilXcache();
				break;
			case self::TYPE_SHM:
				$this->global_cache = new ilShm();
				break;
			case self::TYPE_APC:
				$this->global_cache = new ilApc();
				break;
		}
	}


	/**
	 * @return bool
	 */
	public function isActive() {
		$admin_setting = true; // TODO make Settings in Administration
//		return false;
		return $this->global_cache->isActive() AND $admin_setting;
	}


	/**
	 * @return bool
	 */
	public function isInstallable() {
		return count(self::getAllInstallableTypes()) > 0;
	}


	/**
	 * @param $key
	 *
	 * @throws RuntimeException
	 * @return bool
	 */
	public function exists($key) {
		if (! $this->global_cache->isActive()) {
			throw new RuntimeException(self::MSG);
		}

		return $this->global_cache->exists($this->getPrefix() . $key);
	}


	/**
	 * @param      $key
	 * @param      $value
	 * @param null $ttl
	 *
	 * @throws RuntimeException
	 * @return bool
	 */
	public function set($key, $value, $ttl = NULL) {
		if (! $this->global_cache->isActive()) {
			throw new RuntimeException(self::MSG);
		}

		return $this->global_cache->set($this->getPrefix() . $key, serialize($value), $ttl);
	}


	/**
	 * @param $key
	 *
	 * @throws RuntimeException
	 * @return mixed
	 */
	public function get($key) {
		if (! $this->global_cache->isActive()) {
			throw new RuntimeException(self::MSG);
		}

		return unserialize($this->global_cache->get($this->getPrefix() . $key));
	}


	/**
	 * @param $key
	 *
	 * @throws RuntimeException
	 * @return bool
	 */
	public function delete($key) {
		if (! $this->global_cache->isActive()) {
			throw new RuntimeException(self::MSG);
		}

		return $this->global_cache->delete($this->getPrefix() . $key);
	}


	/**
	 * @throws RuntimeException
	 * @return bool
	 */
	public function flush() {
		if (! $this->global_cache->isActive()) {
			throw new RuntimeException(self::MSG);
		}

		return $this->global_cache->flush();
	}


	/**
	 * @param string $prefix
	 */
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}


	/**
	 * @return string
	 */
	public function getPrefix() {
		return $this->prefix;
	}
}

?>
