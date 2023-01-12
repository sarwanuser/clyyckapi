<?php

namespace App\Jobs;

use  App\Models\Subscription;
use  App\Models\Routing;
use  App\Models\CartPackage;
use  App\Models\Cart;
use  App\Models\SPServiceSettings;
use  App\Models\SPRoutingAlert;
use  App\Models\SPRoutingAlertDetail;
use  App\Models\UserAlert;
use  App\Models\SPAlert;
use  App\Models\SubCategoryServiceRule;
use App\Http\Controllers\RoutingController;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;
use DB;
use Validator;

class RoutingAlert10m extends Job
{

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(){
        //$this->subscriptions = $Subscriptions;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){
        $log_string ='';
        $routingcontroller = new RoutingController();

        $route_sp_ids = SPRoutingAlert::select('id','subscription_id','sub_category_id','routing_before','status')->where('status', 'SEARCHING')->orWhere('status', 'ACCEPTED')->where('routing_before', '10')->get()->unique('subscription_id');
        
        foreach($route_sp_ids as $route_sp_id){
            $Routing = Routing::where('subcategory_id', $route_sp_id->sub_category_id)->first();
            $Subscription = Subscription::where('id', $route_sp_id->subscription_id)->first();
            $cartpackage = CartPackage::where('cartID', $Subscription->cart_id)->first();
            $cart = Cart::where('id', $Subscription->cart_id)->first();

            $rule_id = $routingcontroller->getRuleCode($Routing->rule_id);
            $providers = SPServiceSettings::where('subcategory_id', $route_sp_id->sub_category_id)->where('enabled', '1')->where($rule_id, '1')->get();

            // Log String
            $sp_ids = SPServiceSettings::where('subcategory_id', $route_sp_id->sub_category_id)->where('enabled', '1')->where($rule_id, '1')->get()->pluck('sp_id')->toArray();
            $route_sp_ids = implode(',',$sp_ids);

            
            DB::beginTransaction();
            $SPRoutingAlert = SPRoutingAlert::where('id', $route_sp_id->id)->first();
            $SPRoutingAlert->no_of_route = 2;
            $SPRoutingAlert->save();

            foreach($providers as $provider){
                $commission = rand($Routing->min_commission,$Routing->max_commission);
                $SPRoutingAlertDetail = new SPRoutingAlertDetail();
                $SPRoutingAlertDetail->provider_id = $provider->sp_id;
                $SPRoutingAlertDetail->sp_routing_id = $SPRoutingAlert->id;
                $SPRoutingAlertDetail->user_id = $cart->user_id;
                $SPRoutingAlertDetail->subscription_id = $Subscription->id;
                $SPRoutingAlertDetail->commission = $commission;
                $SPRoutingAlertDetail->routing_before = 10;
                $SPRoutingAlertDetail->cart_id = $Subscription->cart_id;
                $SPRoutingAlertDetail->sub_category_id = $route_sp_id->sub_category_id;
                $SPRoutingAlertDetail->no_of_route = 2;
                $SPRoutingAlertDetail->save();
                $routingcontroller->addAlert($SPRoutingAlertDetail->id, 'New Order', 'OrderRouting', $provider->sp_id);
            }
            DB::commit();

            // $commission = rand($Routing->min_commission,$Routing->max_commission);
            // $SPRoutingAlert = SPRoutingAlert::where('id', $route_sp_id->id)->first();
            // $SPRoutingAlert->commission = $commission;
            // $SPRoutingAlert->no_of_route = 2;
            // $SPRoutingAlert->save();

            // Send alert to customer
            //$routingcontroller->addAlertForCust($Subscription->id, 'Order', 'Pending', $cart->user_id, 'Finding Service Provider Again, Please Wait A Moment');

            // Log String
            $log_string .= 'Subscription Id:'.$SPRoutingAlert->subscription_id.', Service Date:'.$Subscription->service_date.', Service Time:'.$Subscription->service_time.', Cart Id: '.$Subscription->cart_id.', Package Name: '.$cartpackage->package_name.', Base Price: '.$cartpackage->base_price.', Selling Price:'.$cartpackage->selling_price.', Sub Category Id: '.$cartpackage->sub_category_id.', Service Rule: '.$routingcontroller->getRuleCode($cartpackage->service_rule_id).', Route Before:'.$Routing->route_before.', Service Duration:'.$Routing->service_duration.', Open Time:'.$Routing->open_time.', Close Time:'.$Routing->close_time.', Min Commission:'.$Routing->min_commission.', Max Commission:'.$Routing->max_commission.', Route SP Ids:'.$route_sp_ids.'
            
            ';
        }
        Log::info('Subscription routing successfull After 10M Route - '.$log_string);
        return true;
    }
}
