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
    function my_debug($message, $clear = false) {
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
/* @var $scriptProperties array */
/* @var $mode int */


/* See if empty cache checkbox is checked */
$emptyCache = $modx->getOption('syncsite', $_POST, false) == true;

/* See if &executeAlways is set */
$always = $modx->getOption('executeAlways', $scriptProperties, false);

/* don't execute for new documents or if empty cache is set
 * and &executeAlways is not */
if ( ($mode == modSystemEvent::MODE_NEW) || ((!$always) && $emptyCache)) {
    return '';
}

/* Turn off automatic cache clearing */
$scriptProperties['resource']->_fields['syncsite'] = 0;
$_POST['syncsite'] = 0;

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
/* the old-fashioned way, just in case */
if (file_exists($path)) {
    unlink($path);
}