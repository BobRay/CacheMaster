<?php
/**
 * Properties file for CacheMaster plugin
 *
 * Copyright 2012 by Bob Ray <http://bobsguides.com>
 * Created on 01-04-2013
 *
 * @package cachemaster
 * @subpackage build
 */




$properties = array( 
    array( 
        'name' => 'doChunks',
        'desc' => 'cm_do_chunks_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => '0',
        'lexicon' => 'cachemaster:properties',
        'area' => 'CacheMaster',
        ),
    array( 
        'name' => 'doPlugins',
        'desc' => 'cm_do_plugins_desc~~Execute for Plugins',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => '0',
        'lexicon' => 'cachemaster:properties',
        'area' => 'CacheMaster',
        ),
    array( 
        'name' => 'doResources',
        'desc' => 'cm_do_resources_desc~~Execute for Resources',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => '1',
        'lexicon' => 'cachemaster:properties',
        'area' => 'CacheMaster',
        ),
    array( 
        'name' => 'doSnippets',
        'desc' => 'cm_do_snippets_desc~~Execute for Snippets',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => '0',
        'lexicon' => 'cachemaster:properties',
        'area' => 'CacheMaster',
        ),
    array( 
        'name' => 'doTVs',
        'desc' => 'cm_do_tvs_desc~~Execute for Template Variables',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => '0',
        'lexicon' => 'cachemaster:properties',
        'area' => 'CacheMaster',
        ),
    array( 
        'name' => 'doTemplates',
        'desc' => 'cm_do_templates_desc~~Execute for Templates',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => '0',
        'lexicon' => 'cachemaster:properties',
        'area' => 'CacheMaster',
        ),
    array( 
        'name' => 'uncheckEmptyCache',
        'desc' => 'cm_uncheck_empty_cache_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => '1',
        'lexicon' => 'cachemaster:properties',
        'area' => 'CacheMaster',
        ),

);

return $properties;

