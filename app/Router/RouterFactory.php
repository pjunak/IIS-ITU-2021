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
        $router->addRoute('administrace', 'Administration:default');
        $router->addRoute('prihlaseni', 'Sign:in');
        $router->addRoute('odhlaseni', 'Sign:out');

        // Firma
        $router->addRoute('firma/<action>[/<url>]', [
                'presenter' => 'Core:Company',
                'action' => [
                        Route::FILTER_STRICT => true,
                        Route::FILTER_TABLE => [
                            'seznam-firem' => 'list',
                            'editor-firma' => 'editor',
                            'odstranit-firmu' => 'remove'
                            ]
                ]
        ]);
        $router->addRoute('firma/[<url>]', 'Core:Company:default');

        // Osoba
        $router->addRoute('osoba/<action>[/<url>]', [
            'presenter' => 'Core:User',
            'action' => [
                    Route::FILTER_STRICT => true,
                    Route::FILTER_TABLE => [
                        // řetězec v URL => akce presenteru
                        'seznam-uzivatelu' => 'list',
                        'editor-uzivatel' => 'editor',
                        'odstranit-uzivatele' => 'remove'
                        ]
            ]
        ]);
        $router->addRoute('osoba/[<url>]', 'Core:User:default');

        // Pozadavek
        $router->addRoute('pozadavek/<action>[/<url>]', [
            'presenter' => 'Core:Request',
            'action' => [
                    Route::FILTER_STRICT => true,
                    Route::FILTER_TABLE => [
                        // řetězec v URL => akce presenteru
                        'seznam-pozadavku' => 'list',
                        'editor-pozadavku' => 'editor',
                        'odstranit-pozadavek' => 'remove'
                        ]
            ]
        ]);
        $router->addRoute('pozadavek/[<url>]', 'Core:Request:default');

        // Výpis
        $router->addRoute('vypis/<action>[/<url>]', [
            'presenter' => 'Core:Report',
            'action' => [
                    Route::FILTER_STRICT => true,
                    Route::FILTER_TABLE => [
                        // řetězec v URL => akce presenteru
                        'seznam-vypisu' => 'list',
                        'editor-vypisu' => 'editor',
                        'odstranit-vypis' => 'remove'
                        ]
            ]
        ]);
        $router->addRoute('vypis/[<url>]', 'Core:Report:default');

        // Vyrobna
        $router->addRoute('vyrobna/<action>[/<url>]', [
            'presenter' => 'Core:Factory',
            'action' => [
                    Route::FILTER_STRICT => true,
                    Route::FILTER_TABLE => [
                        // řetězec v URL => akce presenteru
                        'seznam-vyroben' => 'list',
                        'editor-vyroben' => 'editor',
                        'odstranit-vyrobnu' => 'remove'
                        ]
            ]
        ]);
        $router->addRoute('vyrobna/[<url>]', 'Core:Factory:default');

        // Route pro uvodni stranku
        $router->addRoute('', 'Sign:in');
        return $router;
    }
}