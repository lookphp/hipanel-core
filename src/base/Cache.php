<?php

/*
 * HiPanel core package
 *
 * @link      https://hipanel.com/
 * @package   hipanel-core
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\base;

use Closure;
use ReflectionFunction;
use Yii;

class Cache extends \yii\caching\FileCache
{
    /**
     * Caches a [[\Closure]] call, invalidates on timeout.
     *
     * @param int $timeout seconds
     * @param array $params an array of params that will be passed to the $func
     * @param Closure $function the function that will be called
     * @return mixed
     */
    public function getTimeCached($timeout, array $params, Closure $function)
    {
        $key = array_merge([__CLASS__, ReflectionFunction::export($function, true)], $params);
        $res = $this->get($key);
        if ($res === false) {
            $res = call_user_func_array($function, $params);
            $this->set($key, $res, $timeout);
        }

        return $res;
    }

    /**
     * Caches a [[\Closure]] call, invalidates on timeout.
     * Forcefully adds current logged in user ID to the params to be a part of the key.
     *
     * @param int $timeout seconds
     * @param array $params an array of params that will be passed to the $func
     * @param Closure $function the function that will be called
     * @return mixed
     */
    public function getAuthTimeCached($timeout, array $params, Closure $function)
    {
        $params[] = Yii::$app->user->id;
        return $this->getTimeCached($timeout, $params, $function);
    }

/* TRY to realize later
    public function init()
    {
        parent::init();
        if (!is_object($this->cache)) {
            $this->cache = Instance::ensure($this->cache, yiiCache::className());
        }
    }

    public function getTimeCached ($timeout, array $params, $func) {
        $key = array_merge([__CLASS__], $params);
        $res = $this->cache->get($key);
        if ($res === false) {
            $res = call_user_func_array($func, $params);
            $this->getCache()->set($key, $res, $timeout);
        }

        return $res;
    }
*/
}
