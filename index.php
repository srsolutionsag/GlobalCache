<?php
chdir(strstr(__FILE__, 'Services', true));
require_once('./Services/GlobalCache/classes/class.ilGlobalCache.php');
$ilGlobalCache = ilGlobalCache::getInstance('lng');
// $ilGlobalCache->flush();
//echo '<pre>' . print_r($ilGlobalCache->getInfo(), 1) . '</pre>';
$ilGlobalCache = ilGlobalCache::getInstance('set');
echo '<pre>' . print_r($ilGlobalCache->getInfo(), 1) . '</pre>';
?>
