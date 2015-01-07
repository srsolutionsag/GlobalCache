<?php

require_once './Services/GlobalCache/classes/class.ilGlobalCacheService.php';

/**
 * Class ilFileCache
 *
 * Caching service that takes advantage of the file system. Useful for development instances that require caching
 * behaviour which spans across requests.
 *
 * This is adapted from CodeIgniter Caching by the ExpressionEngine Dev Team
 *
 * @author  Maximilian Becker <mbecker@databay.de>
 * @version 1.0.0
 */
class ilFileCache extends ilGlobalCacheService {

	/**
	 * @description Path to the cache-files.
	 *
	 * @var string
	 */
	protected $cache_path;
	/**
	 * @var bool
	 */
	protected $is_active = false;


	/**
	 * @param $service_id
	 * @param $component
	 */
	public function __construct($service_id, $component) {
		parent::__construct($service_id, $component);

		/** @var ILIAS $ilias */
		global $ilias;

		/** @var ilIniFile $ilias_ini */
		$ilias_ini = $ilias->ini_ilias;
		if ($ilias_ini instanceof ilIniFile) {
			$ext_path = $ilias_ini->GROUPS['clients']['datadir'] . DIRECTORY_SEPARATOR . 'cache';
			$this->cache_path = $ext_path . DIRECTORY_SEPARATOR . $component . DIRECTORY_SEPARATOR . $service_id . DIRECTORY_SEPARATOR;
			$this->is_active = true;
			if (!file_exists($this->cache_path)) {
				if (!mkdir($this->cache_path, 0777, true)) {
					$this->is_active = false;
				}
			}
		} else {
			$this->is_active = false;
		}
	}


	/**
	 * @return bool
	 */
	protected function getActive() {
		return $this->is_active;
	}


	/**
	 * @return bool
	 */
	protected function getInstallable() {
		return true;
	}


	/**
	 * @param $key
	 *
	 * @return bool
	 */
	public function exists($key) {
		return file_exists($this->cache_path . $key);
	}


	/**
	 * @param      $key
	 * @param      $serialized_value
	 * @param null $ttl
	 *
	 * @return bool
	 */
	public function set($key, $serialized_value, $ttl = NULL) {
		$contents = array(
			'time' => time(),
			'ttl' => $ttl,
			'data' => $serialized_value
		);

		if ($this->writeFile($this->cache_path . $key, serialize($contents))) {
			@chmod($this->cache_path . $key, 0777);

			return true;
		}

		return false;
	}


	/**
	 * @param      $key
	 *
	 * @return mixed
	 */
	public function get($key) {
		if (!file_exists($this->cache_path . $key)) {
			return false;
		}

		$data = $this->readFile($this->cache_path . $key);
		$data = unserialize($data);

		// I a future Version this will already be done by the ilGlobalCache-Class
		if (time() > $data['time'] + $data['ttl']) {
			unlink($this->cache_path . $key);

			return false;
		}

		return $data['data'];
	}


	/**
	 * @param      $key
	 *
	 * @return bool
	 */
	public function delete($key) {
		unlink($this->_cache_path . $key);
	}


	/**
	 * @return bool
	 */
	public function flush() {
		$files = glob($this->cache_path . '*');

		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
			}
		}

		return true;
	}


	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	public function serialize($value) {
		return ($value);
	}


	/**
	 * @param $serialized_value
	 *
	 * @return mixed
	 */
	public function unserialize($serialized_value) {
		return ($serialized_value);
	}


	/**
	 * @param $file
	 *
	 * @return string
	 */
	protected function readFile($file) {
		if (!file_exists($file)) {
			return false;
		}

		if (function_exists('file_get_contents')) {
			return file_get_contents($file);
		}

		if (!$fp = @fopen($file, 'r')) {
			return false;
		}

		flock($fp, LOCK_SH);

		$data = '';
		if (filesize($file) > 0) {
			$data =& fread($fp, filesize($file));
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return $data;
	}


	/**
	 * @param        $path
	 * @param        $data
	 * @param string $mode
	 *
	 * @return bool
	 */
	protected function writeFile($path, $data, $mode = 'w') {
		if (!$fp = @fopen($path, $mode)) {
			return false;
		}

		flock($fp, LOCK_EX);
		fwrite($fp, $data);
		flock($fp, LOCK_UN);
		fclose($fp);

		return true;
	}


	/**
	 * @return bool
	 */
	public function getShowInSetup() {
		return false;
	}
}
