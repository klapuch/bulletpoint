<?php
namespace Bulletpoint\Router;

use Nette\Application\Routers\{RouteList, Route};

class RouterFactory {

    /**
     * @return \Nette\Application\IRouter
     */
    public static function createRouter() {
        $router = new RouteList;
        Route::$defaultFlags = Route::SECURED;
        $router[] = new Route('dokument/novy', 'Dokument:novy');
        $router[] = new Route(
            'dokument/upravit/<slug [-a-z0-9_]+>',
            'Dokument:upravit'
        );
        $router[] = new Route(
            'bulletpoint/pridat/<slug [-a-z0-9_]+>',
            'Bulletpoint:pridat'
        );
        $router[] = new Route(
            'dokument/<slug [-a-z0-9_]+>',
            'Dokument:default'
        );
        $router[] = new Route(
            'profil/<username [-a-z0-9_]+>',
            'Profil:default'
        );
        $router[] = new Route(
            '<presenter>[/<action>][/<id [0-9]+>]',
            'Default:default'
        );
        return $router;
    }

}
