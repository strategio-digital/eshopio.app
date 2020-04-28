<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;

        // Example: http://localhost/analytics/open-rate/7/970/8686b351-c983-4f61-b9e0-86b90db550f0/a790282f-8fda-42c6-879b-69fe8ccce3ad/signature.png
        $router->addRoute('/analytics/open-rate/<emailCampaignId \d+>/<sentEmailId \d+>/<emailCampaignSecretKey>/<sentEmailSecretKey>/signature.png', [
            'presenter' => 'Analytics',
            'action' => 'openRate'
        ]);

        // Example: http://localhost/analytics/click-rate/7/970/8686b351-c983-4f61-b9e0-86b90db550f0/a790282f-8fda-42c6-879b-69fe8ccce3ad?targetUrl=https://google.com
        $router->addRoute('/analytics/click-rate/<emailCampaignId \d+>/<sentEmailId \d+>/<emailCampaignSecretKey>/<sentEmailSecretKey>?targetUrl=<targetUrl .*>', [
            'presenter' => 'Analytics',
            'action' => 'clickRate'
        ]);

        $router->addRoute('<presenter>/<action>[/<secretKey>/<id \d+>]', [
            'presenter' => 'Landing',
            'action' => 'summary'
        ]);

		return $router;
	}
}
