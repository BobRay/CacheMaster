<?php
/**
 * Properties file for CacheMaster plugin
 *
 * Copyright 2012 by Bob Ray <http://bobsguides.com>
 * Created on 12-09-2012
 *
 * @package cachemaster
 * @subpackage build
 */




$properties = array( 
    array( 
        'name' => 'allowedFieldChanges',
        'desc' => '_allowed_field_changes_desc~~When fields other than these are changed, the whole site cache is cleared',
        'type' => 'textfield',
        'options' => '',
        'value' => 'content,introtext,description',
        'lexicon' => 'cachemaster:properties',
        'area' => 'CacheMaster',
        ),
    array( 
        'name' => 'checkTVs',
        'desc' => 'check_tvs_desc~~If this is set, any change in a TV will cause the site cache to be cleared',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => '1',
        'lexicon' => 'cachemaster:properties',
        'area' => 'CacheMaster',
        ),

);

return $properties;

