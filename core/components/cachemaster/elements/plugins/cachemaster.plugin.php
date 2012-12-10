<?php
/**
 * CacheMaster plugin for CacheMaster extra
 *
 * Copyright 2012 by Bob Ray <http://bobsguides.com>
 * Created on 12-09-2012
 *
 * CacheMaster is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * CacheMaster is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * CacheMaster; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package cachemaster
 */

/**
 * Description
 * -----------
 * Clears the cache for a single resource when minor changes are made
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package cachemaster
 **/


if (!function_exists("my_debug")) {
    function my_debug($message, $clear = false)
    {
        global $modx;
        /* @var $chunk modChunk */
        $content = '';

        $chunk = $modx->getObject('modChunk', array('name' => 'debug'));
        if (!$chunk) {
            $chunk = $modx->newObject('modChunk', array('name' => 'debug'));
            $chunk->setContent('');
            $chunk->save();
            $chunk = $modx->getObject('modChunk', array('name' => 'debug'));
        } else {
            if ($clear) {
                $content = '';
            } else {
                $content = $chunk->getContent();
            }
        }
        $content .= $message . "\n";
        $chunk->setContent($content);
        $chunk->save();
    }
}

/* @var $modx modX  */
/* @var $resource modResource */
/* @var $oldResource modResource */
/* @var $scriptProperties array */
/* @var $mode int */
/* @var $tv modTemplateVar */


my_debug("In Plugin\n", true);

$emptyCache = $modx->getOption('syncsite', $_POST, false) == true;
$always = $modx->getOption('executeAlways', $scriptProperties, false);

/* don't execute for new documents or if Empty Cache checkbox is checked */
if ($mode == modSystemEvent::MODE_NEW) {
    return;
}

if ( (! $always) && $emptyCache ) {
    return;
}
/* Turn off automatic cache clearing */
$scriptProperties['resource']->_fields['syncsite'] = 0;
$_POST['syncsite'] = 0;

$checkTvs = $modx->getOption('checkTvs', $scriptProperties, true);
$allowed = $modx->getOption('AllowedFieldChanges',$scriptProperties,null);
$allowed = empty($allowed)? 'content,introtext,description' : $allowed;
$allowedChanges = explode(',', $allowed);

/* set resource context and id */
$ctx = $resource->get('context_key');
$docId = $resource->get('id');

/* set path to cache file */
$path = MODX_CORE_PATH . 'cache/resource/' . $ctx . '/resources/' . $docId . '.cache.php';


$oldResource = $modx->getObject('modResource', $docId);
  /*  just in case */
if ( (! $oldResource) || (! $resource)) {
  return;
}


$old = $oldResource->toArray();
$new = $resource->toArray();
$diffs = array_diff($old, $new);



foreach($allowedChanges as $field) {
  unset($diffs[$field]);
}
  
  $count = count($diffs);

  /*$res = $scriptProperties;
  ob_start();
  var_dump($res);
  $dump = ob_get_clean();
  my_debug($dump);*/

$ok = true;

/* See if any TVs have changed */
if ($checkTvs) {
    if (isset($scriptProperties['data']['tvs'])) {
        unset($scriptProperties['data']['tvs']);
        $tvs = array();
        foreach($scriptProperties['data'] as $k => $v) {
             if(strstr($k,'tvbrowser')) {
                 continue;
             }
             if (substr($k,0,2) == 'tv') {
                 $tvs[$k] = $v;
             }
        }
        $d = print_r($tvs, true);
        my_debug($d);

        foreach($tvs as $k => $v) {
            $tv = (integer) substr($k,2);
            $val = $resource->getTVValue($tv);

            if ($val != $v) {
                $ok = false;
                // my_debug('TV ' . $tv . ' Changed' );
                // my_debug('OLD: ' . $val . 'NEW: ' . $v);
                break;
            }
        }
    }
}

  /* editedon should be the only thing still set if only allowed changes have been made
   * so count will be 1 */
if (($count == 1) && $ok) {
    my_debug('Did Not Clear Site Cache');
    $ck = $resource->getCacheKey();
    my_debug('CK: ' . $ck);
    $modx->cacheManager->delete($resource->getCacheKey(), array(
            xPDO::OPT_CACHE_KEY => $modx->getOption('cache_resource_key', null, 'resource'),
            xPDO::OPT_CACHE_HANDLER => $modx->getOption('cache_resource_handler', null,
                $modx->getOption(xPDO::OPT_CACHE_HANDLER)),
            xPDO::OPT_CACHE_FORMAT => (integer)$modx->getOption('cache_resource_format', null,
                $modx->getOption(xPDO::OPT_CACHE_FORMAT, null, xPDOCacheManager::CACHE_PHP))
        )
    );
    /* the old-fashioned way, just in case */
    if (file_exists($path)) {
        unlink($path);
    }

} else { /* clear the whole site cache */
  my_debug('Cleared Site Cache');
  $cm = $modx->getCacheManager();
  $cm->refresh();
}

my_debug('ID = ' . $docId);
my_debug('Context = ' . $ctx . "\n");
my_debug('Path = ' . $path . "\n");