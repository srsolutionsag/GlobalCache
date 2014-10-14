GlobalCache
===========
Some ILIAS tables have mostly static content that rarely be changed, but at each page view must be read.
By using memcache the data can be read directly from the memory and reduce the data base load.

A new Service for ILIAS , which supports the following requirements:
*Support multiple Caching-APIs such as xCache, APC and Memcached
*Fallback to Static PHP-Class-Cache
*Methods to be implemented: set($key, $value, $ttl = NULL), get($key), exists($key), delete($key) $ttl = Time To Live
*An expiration time can be set for a value (Time to Live)
*Namespaces for components (e.g. lng for Language)
*Flush a Namespace

ilGlobalCache currently supports APC. Memcached, and xCache will follow in a future release.

Usage
-----
This service is part of ILIAS 5 (www.ilias.de).

Contact
----
studer + raimann ag  
Waldeggstrasse 72  
3097 Liebefeld  
info@studer-raimann.ch  
www.studer-raimann.ch  
