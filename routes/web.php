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
    $router->get('sendroutinga20m', 'RoutingController@sendRoutingAlert20');

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
    
    // Get completed orders by sp id
    $router->get('getcompletedorders', 'RoutingController@getCompletedOrderBySPId');
    
    // Get orders details by id
    $router->get('getorderdetails', 'RoutingController@getOrderById');
    
    // Make completed routing status by subscription id
    $router->post('makecompleteorder', 'RoutingController@makeCompleteOrder');
    

    // Add payment for yesterday completed subscription
    $router->get('addpaymentcompletedsubscription', 'RoutingController@addPaymentForCompletedSubs');

    // Add payment for SP
    $router->post('addpayment', 'RoutingController@addSPPayment');
    
    // Get SP Payments
    $router->get('getsppayments', 'RoutingController@getSPPayments');

    // Get payment transactions
    $router->get('getsppaymenttransaction', 'RoutingController@getSPPaymentDetail');
    
    // Get payment receive transaction
    $router->get('getreceivepaymenttransaction', 'RoutingController@getSPPaymentReceive');
    
    // For SP Dashboard
    $router->get('getpaymentsforsp', 'RoutingController@getSPPaymentsForSPDas');
    $router->get('getpaymentdetailsforsp', 'RoutingController@getPaymentDetailForSPDas');
    $router->get('getreceivepaymentforsp', 'RoutingController@getPaymentReceiveForSPDas');
    
    
    // Token Verify
    $router->post('tokenverify', 'AuthController@tokenVerify');
});
