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

	public static function createRouter(): RouteList
	{
		$router = new RouteList;

        /*$router->addRoute('/site-manager/<module>/<presenter>/<action>', [
            'module' => 'User',
            'presenter' => 'User',
            'action' => 'login'
        ]);*/

        $router->addRoute('/<module>/<presenter>/<action>[/<url .*>]', [
            'module' => 'App',
            'presenter' => 'Home',
            'action' => 'summary'
        ]);

        /*$router->addRoute('/<url .*>', [
            'presenter' => 'Home',
            'action' => 'summary'
        ]);*/

		return $router;
	}
}
