<?php

namespace Webman;

/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
class Middleware
{

    /**
     * @var array
     */
    protected static $_instances = [];

    /**
     * @param $all_middlewares
     */
    public static function load($all_middlewares, $plugin)
    {
        foreach ($all_middlewares as $app_name => $middlewares) {
            if (!\is_array($middlewares)) {
                throw new \RuntimeException('Bad middleware config');
            }
            foreach ($middlewares as $class_name) {
                if (\method_exists($class_name, 'process')) {
                    static::$_instances[$plugin][$app_name][] = [$class_name, 'process'];
                } else {
                    // @todo Log
                    echo "middleware $class_name::process not exsits\n";
                }
            }
        }
    }

    /**
     * @param $app_name
     * @param bool $with_global_middleware
     * @return array
     */
    public static function getMiddleware($plugin, $app_name, $with_global_middleware = true)
    {
        $global_middleware = $with_global_middleware && isset(static::$_instances[$plugin]['']) ? static::$_instances[$plugin][''] : [];
        if ($app_name === '') {
            return \array_reverse($global_middleware);
        }
        $app_middleware = static::$_instances[$plugin][$app_name] ?? [];
        return \array_reverse(\array_merge($global_middleware, $app_middleware));
    }

}
