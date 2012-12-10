<?php
/**
 * Properties file for CacheMaster plugin
 *
 * Copyright 2012 by Bob Ray <http://bobsguides.com>
 * Created on 12-10-2012
 *
 * @package cachemaster
 * @subpackage build
 */




$properties = array( 
    array( 
        'name' => 'executeAlways',
        'desc' => 'execute_always_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => '',
        'lexicon' => 'cachemaster:properties',
        'area' => '',
        ),
    array( 
        'name' => 'allowedFieldChanges',
        'desc' => 'allowed_field_changes_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'content,introtext,description',
        'lexicon' => 'cachemaster:properties',
        'area' => 'CacheMaster',
        ),
    array( 
        'name' => 'checkTVs',
        'desc' => 'check_tvs_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => '1',
        'lexicon' => 'cachemaster:properties',
        'area' => 'CacheMaster',
        ),

);

return $properties;

