<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\AppModule\Router;

use Nette;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
    use Nette\StaticClass;

    protected static $modules = [
        'dashboard'
    ];

    public static function createRouter() : RouteList
    {
        $router = new RouteList;

        foreach (self::$modules as $module) {
            $uModule = ucfirst($module);
            $router->addRoute("/site-manager/{$module}/<presenter>/<action>[/<id \d+>]", [
                'module' => $uModule,
                'presenter' => $uModule,
                'action' => 'summary'
            ]);
        }

        $router->addRoute('/<module>/<presenter>/<action>[/<url .*>]', [
            'module' => 'Homepage',
            'presenter' => 'Homepage',
            'action' => 'summary'
        ]);

        return $router;
    }
}
