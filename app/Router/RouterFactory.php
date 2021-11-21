<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

/**
 * Továrna na routovací pravidla.
 * Řídí směrování a generovaní URL adres v celé aplikaci.
 * @package App
 */
final class RouterFactory
{
    use Nette\StaticClass;

    /**
    * Vytváří a vrací seznam routovacích pravidel pro aplikaci.
    * @return RouteList výsledný router pro aplikaci
    */
    public static function createRouter(): RouteList
    {
        $router = new RouteList;
        $router->addRoute('kontakt', 'Core:Contact:default');
        $router->addRoute('administrace', 'Core:Administration:default');

            $router->addRoute('<action>[/<url>]', [
                    'presenter' => 'Core:Company',
                    'action' => [
                         Route::FILTER_STRICT => true,
                         Route::FILTER_TABLE => [
                              // řetězec v URL => akce presenteru
                              'seznam-clanku' => 'list',
                              'editor' => 'editor',
                              'odstranit' => 'remove'
                              ]
                    ]
            ]);

            $router->addRoute('[<url>]', 'Core:Company:default');
            return $router;
    }
}