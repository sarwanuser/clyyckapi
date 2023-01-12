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

class RoutingAlert20m extends Job
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
        //$route_sp_ids = SPRoutingAlert::where('status', 'SEARCHING')->orWhere('status', 'ACCEPTED')->where('routing_before', '20')->get()->unique('subscription_id');

        $route_sp_ids = DB::select("SELECT DISTINCT `subscription_id`, `id`,`sub_category_id`,`routing_before`,`status`,`no_of_route` FROM routing.sp_routing_alerts where routing_before = '20' and no_of_route = '2' and (status ='SEARCHING' or status ='ACCEPTED')");

        if(count($route_sp_ids) <= 0){
            return response()->json(['error' => 0,'message' => 'Subscription routing not found!', 'data' => ''], 200);
        }

        foreach($route_sp_ids as $route_sp_id){
            $Subscription = Subscription::where('id', $route_sp_id->subscription_id)->first();
            $cartpackage = DB::connection('cart_management')->select("SELECT * FROM cart_package where cartID ='".$Subscription->cart_id."'")[0];

            $Routing = DB::connection('routing')->select("SELECT * FROM routing_setup where subcategory_id ='".$cartpackage->sub_category_id."' and rule_id='".$cartpackage->service_rule_id."'")[0];
            
            $cart = DB::connection('cart_management')->select("SELECT * FROM cart where id ='".$Subscription->cart_id."'")[0];

            $rule_id = $routingcontroller->getRuleCode($Routing->rule_id);
            //$providers = SPServiceSettings::where('subcategory_id', $route_sp_id->sub_category_id)->where('enabled', '1')->where($rule_id, '1')->get();
            
            $subcategory_id = $route_sp_id->sub_category_id;
            $providers = DB::connection('sp_management')->select("SELECT spss.*, sd.`role` FROM sp_service_settings spss JOIN sp_detail sd on sd.id = spss.sp_id where spss.subcategory_id = '".$subcategory_id."' and spss.".$rule_id." = '1' and spss.enabled = '1' and sd.`role` = 'provider'");
            

            // Log String
            $sp_ids = SPServiceSettings::where('subcategory_id', $route_sp_id->sub_category_id)->where('enabled', '1')->where($rule_id, '1')->get()->pluck('sp_id')->toArray();
            $route_sp_ids = implode(',',$sp_ids);

            DB::beginTransaction();
            $SPRoutingAlert = SPRoutingAlert::where('id', $route_sp_id->id)->first();
            $SPRoutingAlert->no_of_route = 3;
            $SPRoutingAlert->save();

            foreach($providers as $provider){
                
                $commission = rand($Routing->min_commission,$Routing->max_commission);
                $SPRoutingAlertDetail = new SPRoutingAlertDetail();
                $SPRoutingAlertDetail->provider_id = $provider->sp_id;
                $SPRoutingAlertDetail->sp_routing_id = $SPRoutingAlert->id;
                $SPRoutingAlertDetail->user_id = $cart->user_id;
                $SPRoutingAlertDetail->subscription_id = $Subscription->id;
                $SPRoutingAlertDetail->commission = $commission;
                $SPRoutingAlertDetail->routing_before = 20;
                $SPRoutingAlertDetail->cart_id = $Subscription->cart_id;
                $SPRoutingAlertDetail->sub_category_id = $route_sp_id->sub_category_id;
                $SPRoutingAlertDetail->no_of_route = 3;
                $SPRoutingAlertDetail->save();
                $routingcontroller->addAlert($SPRoutingAlertDetail->id, 'New Order', 'OrderRouting', $provider->sp_id);
            }
            DB::commit();

            // $commission = rand($Routing->min_commission,$Routing->max_commission);
            // $SPRoutingAlert = SPRoutingAlert::where('id', $route_sp_id->id)->first();
            // $SPRoutingAlert->commission = $commission;
            // $SPRoutingAlert->no_of_route = 3;
            // $SPRoutingAlert->save();

            // Send alert to customer
            //$this->addAlertForCust($Subscription->id, 'Order', 'Pending', $cart->user_id, 'Finding Service Provider Again, Please Wait A Moment');

            // Log String
            $log_string .= 'Subscription Id:'.$SPRoutingAlert->subscription_id.', Service Date:'.$Subscription->service_date.', Service Time:'.$Subscription->service_time.', Cart Id: '.$Subscription->cart_id.', Package Name: '.$cartpackage->package_name.', Base Price: '.$cartpackage->base_price.', Selling Price:'.$cartpackage->selling_price.', Sub Category Id: '.$cartpackage->sub_category_id.', Service Rule: '.$routingcontroller->getRuleCode($cartpackage->service_rule_id).', Route Before:'.$Routing->route_before.', Service Duration:'.$Routing->service_duration.', Open Time:'.$Routing->open_time.', Close Time:'.$Routing->close_time.', Min Commission:'.$Routing->min_commission.', Max Commission:'.$Routing->max_commission.', Route SP Ids:'.$route_sp_ids.'
            
            ';
        }
        Log::info('Subscription routing successfull After 20M Route - '.$log_string);

        return true;
    }
}
