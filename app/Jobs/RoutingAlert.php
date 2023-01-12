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

class RoutingAlert extends Job
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
        $log_string = '';
        //$Subscriptions = Subscription::where('status', 'Scheduled')->orwhere('status', 'ReScheduled')->get();
        
        $Subscriptions = DB::connection('cart_management')->select("SELECT * FROM subscription where service_date BETWEEN '".date('Y-m-d', strtotime("-5 day"))."' and '".date('Y-m-d', strtotime("+1 day"))."' and status ='Scheduled' or status ='ReScheduled' ORDER BY service_date DESC");
        $routingcontroller = new RoutingController();
        foreach($Subscriptions as $Subscription){
            if($Subscription->service_time == ''){continue;}
            
            $cartpackage = DB::connection('cart_management')->select("SELECT * FROM cart_package where cartID ='".$Subscription->cart_id."'")[0];

            $cart = DB::connection('cart_management')->select("SELECT * FROM cart where id ='".$cartpackage->cartID."'")[0];

            $Routing = DB::connection('routing')->select("SELECT * FROM routing_setup where subcategory_id ='".$cartpackage->sub_category_id."' and rule_id='".$cartpackage->service_rule_id."'")[0];
                
            // $cartpackage = CartPackage::where('cartID', $Subscription->cart_id)->first();
            // $cart = Cart::where('id', $cartpackage->cartID)->first();
            // $Routing = Routing::where('subcategory_id', $cartpackage->sub_category_id)->where('rule_id', $cartpackage->service_rule_id)->first();

            // Current Date Time
            $current = Carbon::now();
            //$current->subMinutes($Routing->route_before);

            // Service Date Time
            $service_time = explode(':',$Subscription->service_time);                
            $service_date = Carbon::create($Subscription->service_date);
            $service_date->setTime($service_time[0], $service_time[1],0);
            $service_date->subMinutes($Routing->route_before);
       
            // Log String
            $route_year = '';
            $route_month = '';
            $route_day = '';
            $route_hour = '';
            $route_minute = '';
            $route_sp_ids = '';

            if($service_date->year == $current->year){
                $route_year =  $current->year;
                if($service_date->month == $current->month){
                    $route_month = $current->month;
                    if($service_date->day == $current->day){
                        $route_day = $current->day;
                        if($service_date->hour == $current->hour){
                            $route_hour = $current->hour;
                            if($service_date->minute == $current->minute){
                                $route_minute = $current->minute;
                                // Search service provider by sub_category and rule id
                                $subcategory_id = $Routing->subcategory_id;
                                $rule_id = $routingcontroller->getRuleCode($Routing->rule_id);

                                //$providers = SPServiceSettings::where('subcategory_id', $subcategory_id)->where('enabled', '1')->where($rule_id, '1')->get();
                                
                                $providers = DB::connection('sp_management')->select("SELECT spss.*, sd.`role` FROM sp_service_settings spss JOIN sp_detail sd on sd.id = spss.sp_id where spss.subcategory_id = '".$subcategory_id."' and spss.".$rule_id." = '1' and spss.enabled = '1' and sd.`role` = 'provider'");

                                // Log String
                                $sp_ids = SPServiceSettings::where('subcategory_id', $subcategory_id)->where('enabled', '1')->where($rule_id, '1')->get()->pluck('sp_id')->toArray();
                                $route_sp_ids = implode(',',$sp_ids);

                                DB::beginTransaction();
                                $SPRoutingAlert = new SPRoutingAlert();
                                $SPRoutingAlert->user_id = $cart->user_id;
                                $SPRoutingAlert->subscription_id = $Subscription->id;
                                $SPRoutingAlert->commission_from = $Routing->min_commission;
                                $SPRoutingAlert->commission_to = $Routing->max_commission;
                                $SPRoutingAlert->routing_before = 0;
                                $SPRoutingAlert->cart_id = $Subscription->cart_id;
                                $SPRoutingAlert->sub_category_id = $subcategory_id;
                                $SPRoutingAlert->no_of_route = 1;
                                $SPRoutingAlert->save();

                                foreach($providers as $provider){
                                    //$sp_type = $routingcontroller->checkProviderOrSubProviderByID($provider->sp_id);
                                    //if(!$sp_type){continue;}
                                    $commission = rand($Routing->min_commission,$Routing->max_commission);
                                    $SPRoutingAlertDetail = new SPRoutingAlertDetail();
                                    $SPRoutingAlertDetail->provider_id = $provider->sp_id;
                                    $SPRoutingAlertDetail->sp_routing_id = $SPRoutingAlert->id;
                                    $SPRoutingAlertDetail->user_id = $cart->user_id;
                                    $SPRoutingAlertDetail->subscription_id = $Subscription->id;
                                    $SPRoutingAlertDetail->commission = $commission;
                                    $SPRoutingAlertDetail->routing_before = 0;
                                    $SPRoutingAlertDetail->cart_id = $Subscription->cart_id;
                                    $SPRoutingAlertDetail->sub_category_id = $subcategory_id;
                                    $SPRoutingAlertDetail->no_of_route = 1;
                                    $SPRoutingAlertDetail->save();

                                    $routingcontroller->addAlert($SPRoutingAlertDetail->id, 'New Order', 'OrderRouting', $provider->sp_id);
                                    
                                    // Update order status
                                    $routingcontroller->updateOrderStatus('Routed',$Subscription->id);
                                }
                                DB::commit();
                            }
                        }
                    }
                }
            }
            

            // Send alert to customer
            $routingcontroller->addAlertForCust($Subscription->id, 'Order', 'Pending', $cart->user_id, 'Finding Service Provider For Your Order');

            // Log String
            //$log_string .= 'Subscription Id:'.$Subscription->id.', Service Date:'.$Subscription->service_date.', Service Time:'.$Subscription->service_time.', Cart Id: '.$Subscription->cart_id.', Package Name: '.$cartpackage->package_name.', Base Price: '.$cartpackage->base_price.', Selling Price:'.$cartpackage->selling_price.', Sub Category Id: '.$cartpackage->sub_category_id.', Service Rule: '.$routingcontroller->getRuleCode($cartpackage->service_rule_id).', Route Before:'.$Routing->route_before.', Service Duration:'.$Routing->service_duration.', Open Time:'.$Routing->open_time.', Close Time:'.$Routing->close_time.', Min Commission:'.$Routing->min_commission.', Max Commission:'.$Routing->max_commission.', Route Year:'.$route_year.', Route Month:'.$route_month.', Route Day:'.$route_day.', Route Hour:'.$route_hour.', Route Minute:'.$route_minute.', Route SP Ids:'.$route_sp_ids.'';
            
            // Log String
            $log_string .= 'Subscription Id:'.$Subscription->id;
            $log_string .= 'Service Date:'.$Subscription->service_date;
            $log_string .= 'Service Time:'.$Subscription->service_time;
            $log_string .= 'Cart Id: '.$Subscription->cart_id;
            $log_string .= 'Package Name: '.$cartpackage->package_name;
            $log_string .= 'Base Price: '.$cartpackage->base_price;
            $log_string .= 'Selling Price:'.$cartpackage->selling_price;
            $log_string .= 'Sub Category Id: '.$cartpackage->sub_category_id;
            $log_string .= 'Service Rule: '.$routingcontroller->getRuleCode($cartpackage->service_rule_id);
            $log_string .= 'Route Before:'.$Routing->route_before;
            $log_string .= 'Service Duration:'.$Routing->service_duration;
            $log_string .= 'Open Time:'.$Routing->open_time;
            $log_string .= 'Close Time:'.$Routing->close_time;
            $log_string .= 'Min Commission:'.$Routing->min_commission;
            $log_string .= 'Max Commission:'.$Routing->max_commission;
            $log_string .= 'Route Year:'.$route_year;
            $log_string .= 'Route Month:'.$route_month;
            $log_string .= 'Route Day:'.$route_day;
            $log_string .= 'Route Hour:'.$route_hour;
            $log_string .= 'Route Minute:'.$route_minute;
            $log_string .= 'Route SP Ids:'.$route_sp_ids;
        }
        //$Subscription = Subscription::All();
        //$Subscription = Routing::All();

        Log::info('Subscription routing successfull! - '.$log_string);
        return true;
    }
}
