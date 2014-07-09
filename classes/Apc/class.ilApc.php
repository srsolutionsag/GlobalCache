<?php

require_once('./Services/GlobalCache/classes/class.ilGlobalCacheService.php');

/**
 * Class ilApc
 *
 * @beta
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilApc extends ilGlobalCacheService {

	/**
	 * @param $key
	 *
	 * @return bool|string[]
	 */
	public function exists($key) {
		return apc_exists($this->returnKey($key));
	}


	/**
	 * @param      $key
	 * @param      $serialized_value
	 * @param null $ttl
	 *
	 * @return array|bool
	 */
	public function set($key, $serialized_value, $ttl = NULL) {
		return apc_store($this->returnKey($key), $serialized_value, $ttl);
	}


	/**
	 * @param $key
	 *
	 * @return mixed
	 */
	public function get($key) {
		return (apc_fetch($this->returnKey($key)));
	}


	/**
	 * @param $key
	 *
	 * @return bool|string[]
	 */
	public function delete($key) {
		return apc_delete($this->returnKey($key));
	}


	/**
	 * @return bool
	 */
	public function flush() {
		return apc_clear_cache('user');
	}


	/**
	 * @param $value
	 *
	 * @return mixed|string
	 */
	public function serialize($value) {
		return $value;
	}


	/**
	 * @param $serialized_value
	 *
	 * @return mixed
	 */
	public function unserialize($serialized_value) {
		return $serialized_value;
	}


	/**
	 * @return array
	 */
	public function getInfo() {
		$iter = new APCIterator('user');
		$return = array();
		$match = "/" . $this->getServiceId() . "_" . $this->getComponent() . "_([a-zA-Z0-9_.]*)/uism";
		foreach ($iter as $item) {
			$key = $item['key'];
			if (preg_match($match, $key, $matches)) {
				if ($matches[1]) {
					if ($this->isValid($matches[1])) {
						$return[$matches[1]] = $this->unserialize($item['value']);
					}
				}
			}
		}

		return $return;
	}


	protected function getActive() {
		return function_exists('apc_store');
	}


	/**
	 * @description set self::$installable
	 */
	protected function getInstallable() {
		return function_exists('apc_store');
	}
}

?>
