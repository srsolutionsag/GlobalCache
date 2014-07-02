<?php

/**
 * Interface ilGlobalCacheWrapper
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
interface ilGlobalCacheWrapper {

	/**
	 * @return bool
	 */
	public function isActive();


	/**
	 * @return bool
	 */
	public function isInstallable();


	/**
	 * @param $key
	 *
	 * @return bool
	 */
	public function exists($key);


	/**
	 * @param      $key
	 * @param      $value
	 * @param null $ttl
	 *
	 * @return bool
	 */
	public function set($key, $value, $ttl = NULL);


	/**
	 * @param      $key
	 *
	 * @return mixed
	 */
	public function get($key);


	/**
	 * @param      $key
	 *
	 * @return bool
	 */
	public function delete($key);


	/**
	 * @return bool
	 */
	public function flush();
}

?>
