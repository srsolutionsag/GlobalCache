<?php
require_once('./Services/GlobalCache/classes/Memcache/class.ilMemcache.php');
require_once('./Services/GlobalCache/classes/Xcache/class.ilXcache.php');
require_once('./Services/GlobalCache/classes/Shm/class.ilShm.php');
require_once('./Services/GlobalCache/classes/Apc/class.ilApc.php');
require_once('./Services/GlobalCache/classes/Static/class.ilStaticCache.php');

/**
 * Class ilGlobalCache
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilGlobalCache {

	const ACTIVE = true;
	const TYPE_STATIC = - 1;
	const TYPE_XCACHE = 1;
	const TYPE_MEMCACHED = 2;
	const TYPE_SHM = 3;
	const TYPE_APC = 4;
	const TYPE_FALLBACK = self::TYPE_APC;
	const MSG = 'Global Cache not active, can not access cache';
	/**
	 * @var array
	 */
	protected static $types = array( self::TYPE_MEMCACHED, self::TYPE_XCACHE, self::TYPE_SHM, self::TYPE_APC );
	/**
	 * @var array
	 */
	protected static $registred_components = array(
		'lng',
		'obj_def',
		'set',
		'tpl',
	);
	/**
	 * @var array
	 */
	protected static $registred_types = array(
		'lng' => self::TYPE_APC,
		'obj_def' => self::TYPE_APC,
		'set' => self::TYPE_APC,
		'tpl' => self::TYPE_APC,
	);
	/**
	 * @var ilGlobalCache
	 */
	protected static $instances;
	/**
	 * @var ilGlobalCacheService
	 */
	protected $global_cache;
	/**
	 * @var string
	 */
	protected $service_id = '';
	/**
	 * @var string
	 */
	protected $component;


	/**
	 * @param $component
	 *
	 * @return int
	 */
	protected static function getComponentType($component) {
		$comp_setting = self::$registred_types[$component];

		return $comp_setting ? $comp_setting : self::TYPE_FALLBACK;
	}


	/**
	 * @param null $component
	 *
	 * @return ilGlobalCache
	 */
	public static function getInstance($component = NULL) {
		if (! isset(self::$instances[$component])) {
			$type = self::getComponentType($component);
			$ilGlobalCache = new self($type, $component);

			self::$instances[$component] = $ilGlobalCache;
		}

		return self::$instances[$component];
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
	 * @param $component
	 */
	protected function __construct($type, $component = NULL) {
		$this->setComponent($component);
		$this->setServiceid(substr(md5('global_cache'), 0, 6));
		switch ($type) {
			case self::TYPE_APC:
				$this->global_cache = new ilApc($this->getServiceid(), $this->getComponent());
				break;
			case self::TYPE_MEMCACHED:
				$this->global_cache = new ilMemcache($this->getServiceid(), $this->getComponent());
				break;
			case self::TYPE_XCACHE:
				$this->global_cache = new ilXcache($this->getServiceid(), $this->getComponent());
				break;
			case self::TYPE_SHM:
				$this->global_cache = new ilShm($this->getServiceid(), $this->getComponent());
				break;
			case self::TYPE_STATIC:
				$this->global_cache = new ilStaticCache($this->getServiceid(), $this->getComponent());
				break;
		}
	}


	/**
	 * @return bool
	 */
	public function isActive() {
		$admin_setting = true; // TODO make Settings in Administration
		if (! self::ACTIVE) {
			return false;
		}

		return ($this->global_cache->isActive() AND $admin_setting);
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

		return $this->global_cache->exists($key);
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
		$this->global_cache->setValid($key);

		return $this->global_cache->set($key, $this->global_cache->serialize($value), $ttl);
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
		$unserialized_return = $this->global_cache->unserialize($this->global_cache->get($key));
		if ($unserialized_return) {
			if ($this->global_cache->isValid($key)) {
				return $unserialized_return;
			}
		}

		return NULL;
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

		// $this->global_cache->setInvalid($key);

		return $this->global_cache->delete($key);
	}


	/**
	 * @param bool $complete
	 *
	 * @return bool
	 * @throws RuntimeException
	 */
	public function flush($complete = false) {
		if (! $this->global_cache->isActive()) {
			// throw new RuntimeException(self::MSG);
		}
		if ($this->global_cache->isActive()) {
			if ($complete) {
				return $this->global_cache->flush();
			} else {
				return $this->global_cache->setInvalid();
			}
		}

		return false;
	}


	public function getInfo() {
		return $this->global_cache->getInfo();
	}


	/**
	 * @param string $prefix
	 */
	public function setServiceid($prefix) {
		$this->service_id = $prefix;
	}


	/**
	 * @return string
	 */
	public function getServiceid() {
		return $this->service_id;
	}


	/**
	 * @param string $component
	 */
	public function setComponent($component) {
		$this->component = $component;
	}


	/**
	 * @return string
	 */
	public function getComponent() {
		return $this->component;
	}
}

?>
