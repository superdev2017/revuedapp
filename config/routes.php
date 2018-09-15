<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::prefix('admin', function ($routes) {
    // All routes here will be prefixed with `/admin`
    // And have the prefix => admin route element added.
    $routes->connect('/dashboard', ['controller' => 'Dashboard']);
    $routes->fallbacks(DashedRoute::class);
});

Router::prefix('reseller', function ($routes) {
    // All routes here will be prefixed with `/reseller`
    // And have the prefix => reseller route element added.
    $routes->connect('/dashboard', ['controller' => 'Dashboard']);
    $routes->connect('/account', ['controller' => 'Dashboard', 'action' => 'account']);
    $routes->connect('/dashboard', ['controller' => 'Dashboard', 'action' => 'index']);
    $routes->connect('/billing', ['controller' => 'Dashboard', 'action' => 'billing']);
    $routes->connect('/settings', ['controller' => 'Dashboard', 'action' => 'settings']);
    $routes->connect('/widget-settings', ['controller' => 'Dashboard', 'action' => 'widgetSettings']);
    $routes->connect('/get-code', ['controller' => 'Dashboard', 'action' => 'code']);
    $routes->connect('/request-review', ['controller' => 'Dashboard', 'action' => 'requestReview']);

    $routes->connect('/fetch-reviews', ['controller' => 'work', 'action' => 'manualFetch']);


    $routes->fallbacks(DashedRoute::class);
});

Router::scope('/', function (RouteBuilder $routes) {
    /**
     * Here, we are connecting '/' (base path) to a controller called 'Pages',
     * its action called 'display', and we pass a param to select the view file
     * to use (in this case, src/Template/Pages/home.ctp)...
     */
    $routes->connect('/', ['controller' => 'Auth', 'action' => 'login']);
    $routes->connect('/login', ['controller' => 'Auth', 'action' => 'login']);
    $routes->connect('/logout', ['controller' => 'Auth', 'action' => 'logout']);
    $routes->connect('/forgot-password', ['controller' => 'Auth', 'action' => 'recover']);

    $routes->connect('/register', ['controller' => 'Users', 'action' => 'register']);

    $routes->connect('/account', ['controller' => 'Dashboard', 'action' => 'account']);
    $routes->connect('/dashboard', ['controller' => 'Dashboard', 'action' => 'index']);
    $routes->connect('/billing', ['controller' => 'Dashboard', 'action' => 'billing']);
    $routes->connect('/settings', ['controller' => 'Dashboard', 'action' => 'settings']);
    $routes->connect('/widget-settings', ['controller' => 'Dashboard', 'action' => 'widgetSettings']);
    $routes->connect('/get-code', ['controller' => 'Dashboard', 'action' => 'code']);
    $routes->connect('/request-review', ['controller' => 'Dashboard', 'action' => 'requestReview']);
    $routes->connect('/fetch-reviews', ['controller' => 'work', 'action' => 'manualFetch']);
    $routes->connect('/request-video-review', ['controller' => 'Dashboard', 'action' => 'requestVideoReview']);
    $routes->connect('/embed/:id', ['controller' => 'Site', 'action' => 'embed'], ['id' => '\d+', 'pass' => ['id']]);
    $routes->connect('/record', ['controller' => 'Video', 'action' => 'record']);
    $routes->connect('/capture', ['controller' => 'Video', 'action' => 'capture']);
    $routes->connect('/store', ['controller' => 'Video', 'action' => 'store']);
    $routes->connect('/appStore', ['controller' => 'Video', 'action' => 'appStore']);


    $routes->connect('/mobileLogin', ['controller' => 'MobileApp', 'action' => 'login']);
    $routes->connect('/mobileListVideos', ['controller' => 'MobileApp', 'action' => 'listVideos']);
    $routes->connect('/mobileThumb', ['controller' => 'MobileApp', 'action' => 'thumb']);

    /**
     * ...and connect the rest of 'Pages' controller's URLs.
     */
    $routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);

    /**
     * Connect catchall routes for all controllers.
     *
     * Using the argument `DashedRoute`, the `fallbacks` method is a shortcut for
     *    `$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'DashedRoute']);`
     *    `$routes->connect('/:controller/:action/*', [], ['routeClass' => 'DashedRoute']);`
     *
     * Any route class can be used with this method, such as:
     * - DashedRoute
     * - InflectedRoute
     * - Route
     * - Or your own route class
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    $routes->fallbacks(DashedRoute::class);
});


/**
 * Load all plugin routes. See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
Plugin::routes();
