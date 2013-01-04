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

/* @var $modx modX  */
/* @var $scriptProperties array */
/* @var $mode int */

if (!function_exists("my_debug")) {
    function my_debug($message, $clear = false)
    {
        global $modx;
        $content = '';
        $chunk = $modx->getObject('modChunk', array('name' => 'Debug'));
        if (!$chunk) {
            $chunk = $modx->newObject('modChunk', array('name' => 'Debug'));
            $chunk->setContent('');
            $chunk->save();
            $chunk = $modx->getObject('modChunk', array('name' => 'Debug'));
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


/* don't execute for new documents */
if ($mode == modSystemEvent::MODE_NEW) {
    return;
}

$sp =& $scriptProperties;
$event = $modx->event->name;
$doResources = (bool) $modx->getOption('doResources', $sp, false);
$doPlugins = (bool) $modx->getOption('doPlugins', $sp, false);
$doSnippets = (bool) $modx->getOption('doSnippets', $sp, false);
$doChunks = (bool) $modx->getOption('doChunks', $sp, false);
$doTemplates = (bool) $modx->getOption('doTemplates', $sp, false);
$doTVs = (bool) $modx->getOption('doTVs', $sp, false);
$clearCheckbox = (bool) $modx->getOption('uncheckEmptyCache', $sp, false);


/* Bail out if we're not doing this object */
switch($event) {
    case 'OnDocFormPrerender':
    case 'OnBeforeDocFormSave':
        if (!$doResources) {
            return;
        }
        break;
    case 'OnSnipFormPrerender':
    case 'OnBeforeSnipFormSave':
        if (!$doSnippets) {
            return;
        }
        break;
    case 'OnPluginFormPrerender':
    case 'OnBeforePluginFormSave':
        if (!$doPlugins) {
            return;
        }
        break;
    case 'OnChunkFormPrerender':
    case 'OnBeforeChunkFormSave':
        if (!$doChunks) {
            return;
        }
        break;
    case 'OnTempFormPrerender':
    case 'OnBeforeTempFormSave':
        if (!$doTemplates) {
            return;
        }
        break;
    case 'OnTVFormPrerender':
    case 'OnBeforeTVFormSave':
        if (!$doTVs) {
            return;
        }
        break;
}

/* Clear Empty Cache checkbox if UncheckEmptyCache is set */
if (strstr($event, 'Prerender')) {
    $panel = array(
        'OnDocFormPrerender' => 'modx-resource-syncsite',
        'OnSnipFormPrerender' => 'modx-snippet-clear-cache',
        'OnPluginFormPrerender' => 'modx-plugin-clear-cache',
        'OnChunkFormPrerender' => 'modx-chunk-clear-cache',
        'OnTempFormPrerender' => 'modx-template-clear-cache',
        'OnTVFormPrerender' => 'modx-tv-clear-cache',
    );
    if (!$clearCheckbox) {
        $modx->regClientStartupHTMLBlock('<script type="text/javascript">
            Ext.onReady(function() {
                Ext.getCmp("' . $panel[$event] . '").setValue(false);
            });
            </script>');
    }
}


switch($event) {
    case 'OnDocFormPrerender':
        /* If &executeAlways is set, clear the Empty Cache checkbox */
        if (!empty($scriptProperties['executeAlways'])) {
/*        $modx->regClientStartupHTMLBlock('<script type="text/javascript">
            Ext.onReady(function() {
                Ext.getCmp("modx-resource-syncsite").setValue(false);
            });
            </script>');*/

        }
        break;

    case 'OnBeforeDocFormSave': 

        /* See if empty cache checkbox is checked */
        $emptyCache = $modx->getOption('syncsite', $_POST, false) == true;

        /* If EmptyCache is set, MODX will clear everything, if not,
         *  we clear the cache for just this resource */
        if (! $emptyCache) {

            /* get resource context and id */
            $ctx = $resource->get('context_key');
            $docId = $resource->get('id');

            /* set path to default cache file */
            $path = MODX_CORE_PATH . 'cache/resource/' . $ctx . '/resources/' . $docId . '.cache.php';

            $ck = $resource->getCacheKey();
            $mgrCtx = $modx->context->get('key');
            $cKey = str_replace($mgrCtx, $ctx, $ck);

            $modx->cacheManager->delete($cKey, array(
                xPDO::OPT_CACHE_KEY => $modx->getOption('cache_resource_key', null, 'resource'),
                xPDO::OPT_CACHE_HANDLER => $modx->getOption('cache_resource_handler', null,
                    $modx->getOption(xPDO::OPT_CACHE_HANDLER)),
                xPDO::OPT_CACHE_FORMAT => (integer)$modx->getOption('cache_resource_format', null,
                    $modx->getOption(xPDO::OPT_CACHE_FORMAT, null, xPDOCacheManager::CACHE_PHP))
                )
            );
            /* see if resource exists in any other contexts, and if so, clear those caches too */
            $ctxResources = $modx->getCollection('modContextResource', array('resource' => $docId));

            if (!empty ($ctxResources)) {
                foreach ($ctxResources as $ctxResource) {
                    /* @var $ctxResource modContextResource */
                    $key = $ctxResource->get('context_key');
                    $cKey = str_replace($mgrCtx, $key, $ck);
                    $modx->cacheManager->delete($cKey, array(
                        xPDO::OPT_CACHE_KEY => $modx->getOption('cache_resource_key', null, 'resource'),
                        xPDO::OPT_CACHE_HANDLER => $modx->getOption('cache_resource_handler', null,
                            $modx->getOption(xPDO::OPT_CACHE_HANDLER)),
                        xPDO::OPT_CACHE_FORMAT => (integer)$modx->getOption('cache_resource_format', null,
                            $modx->getOption(xPDO::OPT_CACHE_FORMAT, null, xPDOCacheManager::CACHE_PHP))
                       )
                    );

                }
            }
            /* clear the resource cache the old-fashioned way, just in case */
            if (file_exists($path)) {
                unlink($path);
            }
        }
        break;

}

return '';