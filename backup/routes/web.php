<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
        
    // Send Routing Alert
    $router->get('sendrouting', 'RoutingController@sendRoutingAlert');

    // Send Manual Routing Alert
    $router->post('sendmanualrouting', 'RoutingController@sendManualRoutingAlert');

    // Send Routing Alert After 10m
    $router->get('sendroutinga10m', 'RoutingController@sendRoutingAlert10');

    // Send Routing Alert After 20
    $router->get('sendroutinga20', 'RoutingController@sendRoutingAlert20');

    // Send Routing Alert After 30
    $router->get('sendroutinga30m', 'RoutingController@sendRoutingAlert30');

    // Routing Time Increament
    $router->get('updateroutetime', 'RoutingController@increaseRouteTime');

    // Accept Order
    $router->post('acceptorder', 'RoutingController@acceptOrder');

    // Accept Order Info
    $router->post('acceptOrderInfo', 'RoutingController@acceptOrderInfo');
    $router->post('alertdetail', 'RoutingController@acceptOrderInfo');

    // Assign Order
    $router->post('assignOrder', 'RoutingController@assignOrder');

    // Get routing details
    $router->get('getroutingdetails', 'RoutingController@getRoutingDetailsBySubsId');

    // Get routing details by id
    $router->get('getroutingdetailsbyid', 'RoutingController@getRoutingDetailsById');

    // Get SP list by subscription id
    $router->get('getsplist', 'RoutingController@getVendorListBySubsId');

    // Get upcoming orders by sp id
    $router->get('getupcomingorders', 'RoutingController@getUpcomingOrderBySPId');
    


    // Token Verify
    $router->post('tokenverify', 'AuthController@tokenVerify');

    // Add alert
    $router->post('addalert', 'SPAlertController@addAlert');
    $router->post('updatealert', 'SPAlertController@updateAlert');
    $router->get('getalerts', 'SPAlertController@getSPAlerts');
    $router->get('getalertscount', 'SPAlertController@getSPAlertsCount');
});
