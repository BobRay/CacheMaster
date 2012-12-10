<?php
/**
 * plugins transport file for CacheMaster extra
 *
 * Copyright 2012 by Bob Ray <http://bobsguides.com>
 * Created on 12-10-2012
 *
 * @package cachemaster
 * @subpackage build
 */

if (! function_exists('stripPhpTags')) {
    function stripPhpTags($filename) {
        $o = file_get_contents($filename);
        $o = str_replace('<' . '?' . 'php', '', $o);
        $o = str_replace('?>', '', $o);
        $o = trim($o);
        return $o;
    }
}
/* @var $modx modX */
/* @var $sources array */
/* @var xPDOObject[] $plugins */


$plugins = array();

$plugins[1] = $modx->newObject('modPlugin');
$plugins[1]->fromArray(array(
    'id' => '1',
    'property_preprocess' => '',
    'name' => 'CacheMaster',
    'description' => 'Clears the cache for a single resource when minor changes are made',
    'disabled' => '',
), '', true, true);
$plugins[1]->setContent(file_get_contents($sources['source_core'] . '/elements/plugins/cachemaster.plugin.php'));


$properties = include $sources['data'].'properties/properties.cachemaster.plugin.php';
$plugins[1]->setProperties($properties);
unset($properties);

return $plugins;
