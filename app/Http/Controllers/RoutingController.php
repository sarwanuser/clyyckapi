<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use  App\Models\Subscription;
use  App\Models\Routing;
use  App\Models\CartPackage;
use  App\Models\Cart;
use  App\Models\SPServiceSettings;
use  App\Models\SPRoutingAlert;
use  App\Models\SPRoutingAlertDetail;
use  App\Models\UserAlert;
use  App\Models\SPAlert;
use  App\Models\SPPayment;
use  App\Models\SPPaymentTransaction;
use  App\Models\SPPaymentHistory;
use  App\Models\SubCategoryServiceRule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;
use DB;
use Validator;
use Illuminate\Contracts\Queue\Queue;

class RoutingController extends Controller {
    


    /**
     * This function use for send routing alert.
     *
     * @return Response
     */
    public function sendRoutingAlert(Request $request, Queue $queue){
        try {
            
            //$Subscriptions = Subscription::where('status', 'scheduled')->get()->toArray();

            $queue_status = $queue->push(new \App\Jobs\RoutingAlert());

            // $log_string = '';
            // foreach($Subscriptions as $Subscription){
            //     $timetaken .= 'Loop start <br>'.date("d-m-Y, h:i:s")."<br>";
            //     if($Subscription->service_time == ''){continue;}
            //     $cartpackage = CartPackage::where('cartID', $Subscription->cart_id)->first();

            //     $timetaken .= '2dn query complete <br>'.date("d-m-Y, h:i:s")."<br>";
            //     $cart = Cart::where('id', $cartpackage->cartID)->first();

            //     $timetaken .= '3 query complete <br>'.date("d-m-Y, h:i:s")."<br>";
            //     $Routing = Routing::where('subcategory_id', $cartpackage->sub_category_id)->first();

            //     $timetaken .= '4 query complete <br>'.date("d-m-Y, h:i:s")."<br>";

            //     // Current Date Time
            //     $current = Carbon::now();
            //     $current->subMinutes($Routing->route_before);

            //     // Service Date Time
            //     $service_time = explode(':',$Subscription->service_time);                
            //     $service_date = Carbon::create($Subscription->service_date);
            //     $service_date->setTime($service_time[0], $service_time[1]);
            //     $service_date->subMinutes($Routing->route_before);

            //     $timetaken .= 'time calculate complete <br>'.date("d-m-Y, h:i:s")."<br>";
           
            //     // Log String
            //     $route_year = '';
            //     $route_month = '';
            //     $route_day = '';
            //     $route_hour = '';
            //     $route_minute = '';
            //     $route_sp_ids = '';

            //     $timetaken .= 'check schedule time start <br>'.date("d-m-Y, h:i:s")."<br>";

            //     if($service_date->year == $current->year){
            //         $route_year =  $current->year;
            //         if($service_date->month == $current->month){
            //             $route_month = $current->month;
            //             $timetaken .= 'month maich <br>'.date("d-m-Y, h:i:s")."<br>";
            //             if($service_date->day == $current->day){
            //                 $route_day = $current->day;
            //                 if($service_date->hour == $current->hour){
            //                     $route_hour = $current->hour;
            //                     $timetaken .= 'hour maich <br>'.date("d-m-Y, h:i:s")."<br>";
            //                     if($service_date->minute == $current->minute){
            //                         $route_minute = $current->minute;
            //                         $timetaken .= 'minuts maich <br>'.date("d-m-Y, h:i:s")."<br>";
            //                         // Search service provider by sub_category and rule id
            //                         $subcategory_id = $Routing->subcategory_id;
            //                         $rule_id = $this->getRuleCode($Routing->rule_id);

            //                         $timetaken .= '5 query start <br>'.date("d-m-Y, h:i:s")."<br>";
            //                         $providers = SPServiceSettings::where('subcategory_id', $subcategory_id)->where('enabled', '1')->where($rule_id, '1')->get();

            //                         $timetaken .= '5 query end <br>'.date("d-m-Y, h:i:s")."<br>";

            //                         $timetaken .= '6 query start <br>'.date("d-m-Y, h:i:s")."<br>";
            //                         // Log String
            //                         $sp_ids = SPServiceSettings::where('subcategory_id', $subcategory_id)->where('enabled', '1')->where($rule_id, '1')->get()->pluck('sp_id')->toArray();
            //                         $route_sp_ids = implode(',',$sp_ids);

            //                         $timetaken .= '6 query end <br>'.date("d-m-Y, h:i:s")."<br>";

            //                         $timetaken .= '7 query start SPRoutingAlert <br>'.date("d-m-Y, h:i:s")."<br>";
            //                         DB::beginTransaction();
            //                         $SPRoutingAlert = new SPRoutingAlert();
            //                         $SPRoutingAlert->user_id = $cart->user_id;
            //                         $SPRoutingAlert->subscription_id = $Subscription->id;
            //                         $SPRoutingAlert->commission_from = $Routing->min_commission;
            //                         $SPRoutingAlert->commission_to = $Routing->max_commission;
            //                         $SPRoutingAlert->routing_before = 0;
            //                         $SPRoutingAlert->cart_id = $Subscription->cart_id;
            //                         $SPRoutingAlert->sub_category_id = $subcategory_id;
            //                         $SPRoutingAlert->no_of_route = 1;
            //                         $SPRoutingAlert->save();

            //                         $timetaken .= '7 query end SPRoutingAlert <br>'.date("d-m-Y, h:i:s")."<br>";

            //                         $timetaken .= 'Provider loop start <br>'.date("d-m-Y, h:i:s")."<br>";
            //                         foreach($providers as $provider){
            //                             $timetaken .= 'query start SPRoutingAlertDetail <br>'.date("d-m-Y, h:i:s")."<br>";
            //                             $commission = rand($Routing->min_commission,$Routing->max_commission);
            //                             $SPRoutingAlertDetail = new SPRoutingAlertDetail();
            //                             $SPRoutingAlertDetail->provider_id = $provider->sp_id;
            //                             $SPRoutingAlertDetail->sp_routing_id = $SPRoutingAlert->id;
            //                             $SPRoutingAlertDetail->user_id = $cart->user_id;
            //                             $SPRoutingAlertDetail->subscription_id = $Subscription->id;
            //                             $SPRoutingAlertDetail->commission = $commission;
            //                             $SPRoutingAlertDetail->routing_before = 0;
            //                             $SPRoutingAlertDetail->cart_id = $Subscription->cart_id;
            //                             $SPRoutingAlertDetail->sub_category_id = $subcategory_id;
            //                             $SPRoutingAlertDetail->no_of_route = 1;
            //                             $SPRoutingAlertDetail->save();

            //                             $timetaken .= 'query end SPRoutingAlertDetail <br>'.date("d-m-Y, h:i:s")."<br>";

            //                             $timetaken .= 'Start addAlert <br>'.date("d-m-Y, h:i:s")."<br>";
            //                             $this->addAlert($SPRoutingAlertDetail->id, 'New Order', 'OrderRouting', $provider->sp_id);

            //                             $timetaken .= 'end addAlert <br>'.date("d-m-Y, h:i:s")."<br>";

            //                             $timetaken .= 'Start updateOrderStatus <br>'.date("d-m-Y, h:i:s")."<br>";
            //                             // Update order status
            //                             $this->updateOrderStatus('Routed',$Subscription->id);

            //                             $timetaken .= 'end updateOrderStatus <br>'.date("d-m-Y, h:i:s")."<br>";
            //                         }
            //                         $timetaken .= 'Provider loop end <br>'.date("d-m-Y, h:i:s")."<br>";
            //                         DB::commit();
            //                     }
            //                 }
            //             }
            //         }
            //     }

            //     $timetaken .= 'start addAlertForCust <br>'.date("d-m-Y, h:i:s")."<br>";

            //     // Send alert to customer
            //     $this->addAlertForCust($Subscription->id, 'Order', 'Pending', $cart->user_id, 'Finding Service Provider For Your Order');

            //     $timetaken .= 'end addAlertForCust <br>'.date("d-m-Y, h:i:s")."<br>";

            //     $timetaken .= 'start make log array <br>'.date("d-m-Y, h:i:s")."<br>";

            //     // Log String
            //     $log_string .= 'Subscription Id:'.$Subscription->id.', Service Date:'.$Subscription->service_date.', Service Time:'.$Subscription->service_time.', Cart Id: '.$Subscription->cart_id.', Package Name: '.$cartpackage->package_name.', Base Price: '.$cartpackage->base_price.', Selling Price:'.$cartpackage->selling_price.', Sub Category Id: '.$cartpackage->sub_category_id.', Service Rule: '.$this->getRuleCode($cartpackage->service_rule_id).', Route Before:'.$Routing->route_before.', Service Duration:'.$Routing->service_duration.', Open Time:'.$Routing->open_time.', Close Time:'.$Routing->close_time.', Min Commission:'.$Routing->min_commission.', Max Commission:'.$Routing->max_commission.', Route Year:'.$route_year.', Route Month:'.$route_month.', Route Day:'.$route_day.', Route Hour:'.$route_hour.', Route Minute:'.$route_minute.', Route SP Ids:'.$route_sp_ids.'
                
            //     ';
            //     $timetaken .= 'end make log array <br>'.date("d-m-Y, h:i:s")."<br>";
            // }
            // //$Subscription = Subscription::All();
            // //$Subscription = Routing::All();

            // $timetaken .= 'start make log <br>'.date("d-m-Y, h:i:s")."<br>";
            // Log::info('Subscription routing successfull! - '.$log_string);
            // $timetaken .= 'end make log <br>'.date("d-m-Y, h:i:s")."<br>";
            
            //return $timetaken;
            return response()->json(['error' => 0,'message' => 'Subscription routing successfull!', 'data' => $queue_status], 200);

        }catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }


    /**
     * This function use for get route rule code by route rule id.
     *
     * @return Response
     */
    public function getRuleCode($rule_id){
        
        try {
            if($rule_id == 1){
                return 's2_c';
            }elseif($rule_id == 2){
                return 'c2_s';
            }elseif($rule_id == 3){
                return 's2_vc';
            }elseif($rule_id == 4){
                return 's2_c2_c';
            }else{
                return '';
            }
        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }

     /**
     * This function use for increase the routing time
     *
     * @return Response
     */
    public function increaseRouteTime(){
        try {
            $searching_alerts = SPRoutingAlert::where('status', 'SEARCHING')->where('routing_before','<=','40')->get();
            foreach($searching_alerts as $searching_alert){
                SPRoutingAlert::where('id', $searching_alert->id)->update(['routing_before' => DB::raw('routing_before+1')]);
            }
            
            Log::info('Subscription routing time update');
            return response()->json(['error' => 0,'message' => 'Searchig Alert Routing Time Is Increament successfull!', 'data' => $searching_alerts], 200);
        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }

    /**
     * This function use for send routing alert After 10m.
     *
     * @return Response
     */
    public function sendRoutingAlert10(Request $request, Queue $queue){
        try {
            $queue_status = $queue->push(new \App\Jobs\RoutingAlert10m());
            // $log_string ='';
            // $route_sp_ids = SPRoutingAlert::select('id','subscription_id','sub_category_id','routing_before','status')->where('status', 'SEARCHING')->orWhere('status', 'ACCEPTED')->where('routing_before', '10')->get()->unique('subscription_id');
            
            // foreach($route_sp_ids as $route_sp_id){
            //     $Routing = Routing::where('subcategory_id', $route_sp_id->sub_category_id)->first();
            //     $Subscription = Subscription::where('id', $route_sp_id->subscription_id)->first();
            //     $cartpackage = CartPackage::where('cartID', $Subscription->cart_id)->first();
            //     $cart = Cart::where('id', $Subscription->cart_id)->first();

            //     $rule_id = $this->getRuleCode($Routing->rule_id);
            //     $providers = SPServiceSettings::where('subcategory_id', $route_sp_id->sub_category_id)->where('enabled', '1')->where($rule_id, '1')->get();

            //     // Log String
            //     $sp_ids = SPServiceSettings::where('subcategory_id', $route_sp_id->sub_category_id)->where('enabled', '1')->where($rule_id, '1')->get()->pluck('sp_id')->toArray();
            //     $route_sp_ids = implode(',',$sp_ids);

                
            //     DB::beginTransaction();
            //     $SPRoutingAlert = SPRoutingAlert::where('id', $route_sp_id->id)->first();
            //     $SPRoutingAlert->no_of_route = 2;
            //     $SPRoutingAlert->save();

            //     foreach($providers as $provider){
            //         $commission = rand($Routing->min_commission,$Routing->max_commission);
            //         $SPRoutingAlertDetail = new SPRoutingAlertDetail();
            //         $SPRoutingAlertDetail->provider_id = $provider->sp_id;
            //         $SPRoutingAlertDetail->sp_routing_id = $SPRoutingAlert->id;
            //         $SPRoutingAlertDetail->user_id = $cart->user_id;
            //         $SPRoutingAlertDetail->subscription_id = $Subscription->id;
            //         $SPRoutingAlertDetail->commission = $commission;
            //         $SPRoutingAlertDetail->routing_before = 10;
            //         $SPRoutingAlertDetail->cart_id = $Subscription->cart_id;
            //         $SPRoutingAlertDetail->sub_category_id = $route_sp_id->sub_category_id;
            //         $SPRoutingAlertDetail->no_of_route = 2;
            //         $SPRoutingAlertDetail->save();
            //         $this->addAlert($SPRoutingAlertDetail->id, 'New Order', 'OrderRouting', $provider->sp_id);
            //     }
            //     DB::commit();

            //     // $commission = rand($Routing->min_commission,$Routing->max_commission);
            //     // $SPRoutingAlert = SPRoutingAlert::where('id', $route_sp_id->id)->first();
            //     // $SPRoutingAlert->commission = $commission;
            //     // $SPRoutingAlert->no_of_route = 2;
            //     // $SPRoutingAlert->save();

            //     // Send alert to customer
            //     //$this->addAlertForCust($Subscription->id, 'Order', 'Pending', $cart->user_id, 'Finding Service Provider Again, Please Wait A Moment');

            //     // Log String
            //     $log_string .= 'Subscription Id:'.$SPRoutingAlert->subscription_id.', Service Date:'.$Subscription->service_date.', Service Time:'.$Subscription->service_time.', Cart Id: '.$Subscription->cart_id.', Package Name: '.$cartpackage->package_name.', Base Price: '.$cartpackage->base_price.', Selling Price:'.$cartpackage->selling_price.', Sub Category Id: '.$cartpackage->sub_category_id.', Service Rule: '.$this->getRuleCode($cartpackage->service_rule_id).', Route Before:'.$Routing->route_before.', Service Duration:'.$Routing->service_duration.', Open Time:'.$Routing->open_time.', Close Time:'.$Routing->close_time.', Min Commission:'.$Routing->min_commission.', Max Commission:'.$Routing->max_commission.', Route SP Ids:'.$route_sp_ids.'
                
            //     ';
            // }
            // Log::info('Subscription routing successfull After 10M Route - '.$log_string);
            return response()->json(['error' => 0,'message' => 'Subscription routing successfull!', 'data' => ''], 200);

        }catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }

    /**
     * This function use for send routing alert After 20m.
     *
     * @return Response
     */
    public function sendRoutingAlert20(Request $request, Queue $queue){
        try {
           $queue_status = $queue->push(new \App\Jobs\RoutingAlert20m());


            // $log_string ='';
            // $route_sp_ids = SPRoutingAlert::where('status', 'SEARCHING')->orWhere('status', 'ACCEPTED')->where('routing_before', '20')->get();
            // foreach($route_sp_ids as $route_sp_id){
            //     $Routing = Routing::where('subcategory_id', $route_sp_id->sub_category_id)->first();
            //     $Subscription = Subscription::where('id', $route_sp_id->subscription_id)->first();
            //     $cartpackage = CartPackage::where('cartID', $Subscription->cart_id)->first();

            //     $cart = Cart::where('id', $Subscription->cart_id)->first();

            //     $rule_id = $this->getRuleCode($Routing->rule_id);
            //     $providers = SPServiceSettings::where('subcategory_id', $route_sp_id->sub_category_id)->where('enabled', '1')->where($rule_id, '1')->get();

            //     // Log String
            //     $sp_ids = SPServiceSettings::where('subcategory_id', $route_sp_id->sub_category_id)->where('enabled', '1')->where($rule_id, '1')->get()->pluck('sp_id')->toArray();
            //     $route_sp_ids = implode(',',$sp_ids);

            //     DB::beginTransaction();
            //     $SPRoutingAlert = SPRoutingAlert::where('id', $route_sp_id->id)->first();
            //     $SPRoutingAlert->no_of_route = 3;
            //     $SPRoutingAlert->save();

            //     foreach($providers as $provider){
            //         $commission = rand($Routing->min_commission,$Routing->max_commission);
            //         $SPRoutingAlertDetail = new SPRoutingAlertDetail();
            //         $SPRoutingAlertDetail->provider_id = $provider->sp_id;
            //         $SPRoutingAlertDetail->sp_routing_id = $SPRoutingAlert->id;
            //         $SPRoutingAlertDetail->user_id = $cart->user_id;
            //         $SPRoutingAlertDetail->subscription_id = $Subscription->id;
            //         $SPRoutingAlertDetail->commission = $commission;
            //         $SPRoutingAlertDetail->routing_before = 20;
            //         $SPRoutingAlertDetail->cart_id = $Subscription->cart_id;
            //         $SPRoutingAlertDetail->sub_category_id = $route_sp_id->sub_category_id;
            //         $SPRoutingAlertDetail->no_of_route = 3;
            //         $SPRoutingAlertDetail->save();
            //         $this->addAlert($SPRoutingAlertDetail->id, 'New Order', 'OrderRouting', $provider->sp_id);
            //     }
            //     DB::commit();

            //     // $commission = rand($Routing->min_commission,$Routing->max_commission);
            //     // $SPRoutingAlert = SPRoutingAlert::where('id', $route_sp_id->id)->first();
            //     // $SPRoutingAlert->commission = $commission;
            //     // $SPRoutingAlert->no_of_route = 3;
            //     // $SPRoutingAlert->save();

            //     // Send alert to customer
            //     //$this->addAlertForCust($Subscription->id, 'Order', 'Pending', $cart->user_id, 'Finding Service Provider Again, Please Wait A Moment');

            //     // Log String
            //     $log_string .= 'Subscription Id:'.$SPRoutingAlert->subscription_id.', Service Date:'.$Subscription->service_date.', Service Time:'.$Subscription->service_time.', Cart Id: '.$Subscription->cart_id.', Package Name: '.$cartpackage->package_name.', Base Price: '.$cartpackage->base_price.', Selling Price:'.$cartpackage->selling_price.', Sub Category Id: '.$cartpackage->sub_category_id.', Service Rule: '.$this->getRuleCode($cartpackage->service_rule_id).', Route Before:'.$Routing->route_before.', Service Duration:'.$Routing->service_duration.', Open Time:'.$Routing->open_time.', Close Time:'.$Routing->close_time.', Min Commission:'.$Routing->min_commission.', Max Commission:'.$Routing->max_commission.', Route SP Ids:'.$route_sp_ids.'
                
            //     ';
            // }
            // Log::info('Subscription routing successfull After 20M Route - '.$log_string);
            return response()->json(['error' => 0,'message' => 'Subscription routing successfull!', 'data' => ''], 200);

        }catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }

    /**
     * This function use for send routing alert After 30m.
     *
     * @return Response
     */
    public function sendRoutingAlert30(Request $request, Queue $queue){

        try {
            $queue_status = $queue->push(new \App\Jobs\RoutingAlert30m());

        //     $log_string ='';
        //     $route_sp_ids = SPRoutingAlert::where('status', 'SEARCHING')->orWhere('status', 'ACCEPTED')->where('routing_before', '10')->get();
        //     foreach($route_sp_ids as $route_sp_id){
        //         $Routing = Routing::where('subcategory_id', $route_sp_id->sub_category_id)->first();
        //         $Subscription = Subscription::where('id', $route_sp_id->subscription_id)->first();
        //         $cartpackage = CartPackage::where('cartID', $Subscription->cart_id)->first();

        //         $cart = Cart::where('id', $Subscription->cart_id)->first();

        //         $rule_id = $this->getRuleCode($Routing->rule_id);
        //         $providers = SPServiceSettings::where('subcategory_id', $route_sp_id->sub_category_id)->where('enabled', '1')->where($rule_id, '1')->get();

        //         // Log String
        //         $sp_ids = SPServiceSettings::where('subcategory_id', $route_sp_id->sub_category_id)->where('enabled', '1')->where($rule_id, '1')->get()->pluck('sp_id')->toArray();
        //         $route_sp_ids = implode(',',$sp_ids);

        //         DB::beginTransaction();
        //         $SPRoutingAlert = SPRoutingAlert::where('id', $route_sp_id->id)->first();
        //         $SPRoutingAlert->no_of_route = 4;
        //         $SPRoutingAlert->save();

        //         foreach($providers as $provider){
        //             $commission = rand($Routing->min_commission,$Routing->max_commission);
        //             $SPRoutingAlertDetail = new SPRoutingAlertDetail();
        //             $SPRoutingAlertDetail->provider_id = $provider->sp_id;
        //             $SPRoutingAlertDetail->sp_routing_id = $SPRoutingAlert->id;
        //             $SPRoutingAlertDetail->user_id = $cart->user_id;
        //             $SPRoutingAlertDetail->subscription_id = $Subscription->id;
        //             $SPRoutingAlertDetail->commission = $commission;
        //             $SPRoutingAlertDetail->routing_before = 30;
        //             $SPRoutingAlertDetail->cart_id = $Subscription->cart_id;
        //             $SPRoutingAlertDetail->sub_category_id = $route_sp_id->sub_category_id;
        //             $SPRoutingAlertDetail->no_of_route = 4;
        //             $SPRoutingAlertDetail->save();
        //             $this->addAlert($SPRoutingAlertDetail->id, 'New Order', 'OrderRouting', $provider->sp_id);
        //         }
        //         DB::commit();

        //         // $commission = rand($Routing->min_commission,$Routing->max_commission);
        //         // $SPRoutingAlert = SPRoutingAlert::where('id', $route_sp_id->id)->first();
        //         // $SPRoutingAlert->commission = $commission;
        //         // $SPRoutingAlert->no_of_route = 4;
        //         // $SPRoutingAlert->save();

        //         // Send alert to customer
        //         $this->addAlertForCust($Subscription->id, 'Order', 'Pending', $cart->user_id, 'Finding Service Provider Again, Please Wait A Moment');

        //         // Log String
        //         $log_string .= 'Subscription Id:'.$SPRoutingAlert->subscription_id.', Service Date:'.$Subscription->service_date.', Service Time:'.$Subscription->service_time.', Cart Id: '.$Subscription->cart_id.', Package Name: '.$cartpackage->package_name.', Base Price: '.$cartpackage->base_price.', Selling Price:'.$cartpackage->selling_price.', Sub Category Id: '.$cartpackage->sub_category_id.', Service Rule: '.$this->getRuleCode($cartpackage->service_rule_id).', Route Before:'.$Routing->route_before.', Service Duration:'.$Routing->service_duration.', Open Time:'.$Routing->open_time.', Close Time:'.$Routing->close_time.', Min Commission:'.$Routing->min_commission.', Max Commission:'.$Routing->max_commission.', Route SP Ids:'.$route_sp_ids.'
                
        //         ';
        //     }
        //     Log::info('Subscription routing successfull After 30M Route - '.$log_string);
            return response()->json(['error' => 0,'message' => 'Subscription routing successfull!', 'data' => ''], 200);

        }catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }


    /**
     * This function use for add alert for user.
     *
     * @return Response
     */
    public function addAlert($refrence_id, $alert_type, $sub_type, $sp_id){
        try {
            $SPRoutingAlertdetai = SPRoutingAlertDetail::where('id', $refrence_id)->first();
            $sub_details = Subscription::where('id', $SPRoutingAlertdetai->subscription_id)->with('getCartPackageDetails')->first();
            
            $SPAlerts = new SPAlert();
            $SPAlerts->alert_type = $alert_type;
            $SPAlerts->sub_type = $sub_type;
            $SPAlerts->refrence_id = $refrence_id;
            $SPAlerts->title = @$sub_details->getCartPackageDetails->package_name;
            $SPAlerts->description = @$sub_details->getCartPackageDetails->short_description;
            $SPAlerts->alert_image = @$sub_details->getCartPackageDetails->package_image;
            $SPAlerts->status = 0;
            $SPAlerts->sp_id = $sp_id;
            $SPAlerts->save();
            return true;
        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }

    /**
     * This function use for add alert for customer.
     *
     * @return Response
     */
    public function addAlertForCust($refrence_id, $alert_type, $sub_type, $user_id, $title){
        try {
            $UserAlerts = new UserAlert();
            $UserAlerts->refrence_id = $refrence_id;
            $UserAlerts->alert_type = $alert_type;
            $UserAlerts->sub_type = $sub_type;            
            $UserAlerts->title = $title;
            $UserAlerts->create_by = $user_id;
            $UserAlerts->user_id = $user_id;
            $UserAlerts->save();
            return true;
        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }

    /**
     * This function use for order accept.
     *
     * @return Response
     */
    public function acceptOrder(Request $request){
        
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if($token_status['status'] == '200'){
                
                // Update Route
                //SPRoutingAlertDetail::where('sp_routing_id', $request->r_id)->update(['status' => $request->status]);
                

                // Update Route Details
                $SPRoutingAlertdetai = SPRoutingAlertDetail::where('id', $request->r_id)->first();
                $SPRoutingAlert = SPRoutingAlert::where('id', $SPRoutingAlertdetai->sp_routing_id)->first();
                
                // If same sp_id accepte gain
                if($SPRoutingAlert->accept_provider_id == $request->accept_provider_id){
                    return response()->json(['error' => 0,'message' => 'You Are Accepted This Order!', 'data' => $SPRoutingAlert], 200);
                }
                
                // Check order already accepted or not
                if($SPRoutingAlert->status == 'ACCEPTED'){
                    return response()->json(['error' => 0,'message' => 'Order Already Accepted, Sorry you are late', 'data' => $SPRoutingAlert], 200);
                }
                
                $SPRoutingAlert->accept_provider_id = $request->accept_provider_id;
                $SPRoutingAlert->status = 'ACCEPTED';
                $SPRoutingAlert->save();

                // Add Alert
                $this->addAlert($request->r_id, 'New Order', 'OrderAccepted', $request->accept_provider_id);

                // Send alert to customer
                //$this->addAlertForCust($request->r_id, 'Order', 'ProviderAcceptedOrder', $SPRoutingAlert->user_id, 'Order Accepted');
                
                return response()->json(['error' => 0,'message' => 'Order Accepted!', 'data' => $SPRoutingAlert], 200);
            }else{
                return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }

    /**
     * This function use for order details for accept.
     *
     * @return Response
     */
    public function acceptOrderInfo(Request $request){
        
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if($token_status['status'] == '200'){
                
                // Update Route
                $routing_details = SPRoutingAlertDetail::where('id', $request->r_id)->where('provider_id', $request->sp_id)->first();
                
                return response()->json(['error' => 0, 'message' => 'Order Accept Info', 'data' => $routing_details], 200);
            }else{
                return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }

    /**
     * This function use for order assign.
     *
     * @return Response
     */
    public function assignOrder(Request $request){
        
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if($token_status['status'] == '200'){

                // Update Route
                $sp_routing_detai = SPRoutingAlertDetail::where('id', $request->r_id)->first();
                
                // Check already assign or not
                if($sp_routing_detai->status == 'CLOSE'){
                    return response()->json(['error' => 0,'message' => 'Order Already Assigned!', 'data' => ''], 401);
                }
                
                
                $sp_routing_detai->status = 'CLOSE';
                $sp_routing_detai->save();                

                // Update Route Details
                $SPRoutingAlert = SPRoutingAlert::where('id', $sp_routing_detai->sp_routing_id)->first();
                $SPRoutingAlert->assigned_provider_id = $request->assigned_provider_id;
                $SPRoutingAlert->status = 'CLOSE';
                $SPRoutingAlert->save();

                $sub_id = $sp_routing_detai->subscription_id;
                $r_id = $request->r_id;
                $sp_id = $request->assigned_provider_id;
                $commission = $sp_routing_detai->commission;
                
                // Add Commission payment for SP
                //$this->addPaymentForSP($sub_id, $r_id, $sp_id,$commission,'Admin');

                // Send alert to customer
                $this->addAlertForCust($request->r_id, 'Order', 'ProviderAssignedOrder', $SPRoutingAlert->user_id, 'Order Assigned');

                // Add Alert
                $this->addAlert($request->r_id, 'New Order', 'OrderAssigned', $request->assigned_provider_id);
                
                // Update order status
                $this->updateOrderStatus('ProviderAssigned',$SPRoutingAlert->subscription_id);

                return response()->json(['error' => 0,'message' => 'Order Assigned!', 'data' => $SPRoutingAlert], 200);
            }else{
                return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }

    /**
     * This function use for add payment for sp by subscription.
     *
     * @return Response
     */
    public function addPaymentForSP($sub_id, $r_id, $sp_id, $commission, $create_by){
        $sub_details = Subscription::where('id', $sub_id)->with('getCartPackageDetails')->first();

        $selling_price = $sub_details->getCartPackageDetails->selling_price;
        $amount = $selling_price*$commission/100;

        $SPPayment = new SPPayment();
        $SPPayment->subscription_id = $sub_id;
        $SPPayment->sp_id = $sp_id;
        $SPPayment->amount = $amount;
        $SPPayment->commission = $commission;
        $SPPayment->payment_status = 'pending';
        $SPPayment->create_by = $create_by;
        $SPPayment->save();
        
        return true;
    }


    /**
     * This function use for get routing details by subscription id.
     *
     * @return Response
     */
    public function getRoutingDetailsBySubsId(Request $request){
        $validator = Validator::make($request->all(), [ 
            'subscription_id' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if($token_status['status'] == '200'){
                $routing_details = SPRoutingAlert::where('subscription_id', $request->subscription_id)->with('getroutngdetails')->get();
                if($routing_details->count() > 0){
                    return response()->json(['status' => 1,'message' => 'Subscription routing details', 'data' => $routing_details], 200);
                }else{
                    return response()->json(['status' => 0,'message' => 'Subscription routing details not found', 'data' => $routing_details], 200);
                }
                
            }else{
                return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }

    }


    /**
     * This function use for get vendor list by subscription id.
     *
     * @return Response
     */
    public function getVendorListBySubsId(Request $request){
        $validator = Validator::make($request->all(), [ 
            'subscription_id' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if($token_status['status'] == '200'){
                $Subscriptions = DB::connection('cart_management')->select("select sub.*, cp.sub_category_id  from cart_management.subscription sub join cart_package cp on cp.cartID = sub.cart_id  where sub.id=".$request->subscription_id)[0];

                $service_rule = SubCategoryServiceRule::where('sub_category_id', $Subscriptions->sub_category_id)->first();
                $rule_code = $this->getRuleCode($service_rule->service_rule_id);

                $vendor_list = DB::connection('sp_management')->select("SELECT * FROM sp_service_settings spss JOIN sp_detail sd on sd.id = spss.sp_id where spss.subcategory_id = '".$Subscriptions->sub_category_id."' and spss.".$rule_code." = '1' and spss.enabled = '1' and sd.`role` = 'provider'");

                //$vendor_list = SPServiceSettings::where('subcategory_id', $routing_details->sub_category_id)->where('enabled', '1')->where($rule_code, '1')->with('getSPdetails')->get();
                return response()->json(['status' => 1,'message' => 'SP List For Manual Routing', 'data' => $vendor_list], 200);                
            }else{
                return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }

    }

    /**
     * This function use for update alert for user.
     *
     * @return Response
     */
    public function updateAlert(Request $request){
        
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
                        
            if($token_status['status'] == '200'){
                $SPAlerts = SPAlerts::where('id', $request->id)->first();
                $SPAlerts->status = $request->status;
                $SPAlerts->save();
                return response()->json(['error' => 0,'message' => 'Alert save successfull!', 'data' => $SPAlerts], 200);
            }else{
                return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }

    }

    /**
     * This function use for get alert count only unread.
     *
     * @return Response
     */
    public function getSPAlertsCount(Request $request){
        
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if($token_status['status'] == '200'){
                $SPAlerts = SPAlerts::where('sp_id', $request->sp_id)->where('status', '!=', '2')->count();
                
                return response()->json(['error' => 0,'message' => 'SP total unread alerts count here!', 'count' => $SPAlerts], 200);
            }else{
                return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }

    }


    /**
     * This function use for send manual routing alert.
     *
     * @return Response
     */
    public function sendManualRoutingAlert(Request $request){
        $validator = Validator::make($request->all(), [ 
            'subscription_id' => 'required',
            'sp_ids' => 'required',
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
                        
            if($token_status['status'] == '200'){
                $Subscription = Subscription::where('id', $request->subscription_id)->first();
                if(!$Subscription){
                    return response()->json(['error' => 1,'message' => 'Subscription Not Found!', 'data' => ''], 201);
                }
                
                $cartpackage = CartPackage::where('cartID', $Subscription->cart_id)->first();
                $cart = Cart::where('id', $cartpackage->cartID)->first();
                $Routing = Routing::where('subcategory_id', $cartpackage->sub_category_id)->first();
                
                $providers = $request->sp_ids;
                $log_string = '';

                $route_sp_ids = implode(',',$request->sp_ids);
                DB::beginTransaction();
                $SPRoutingAlert = new SPRoutingAlert();
                $SPRoutingAlert->user_id = $cart->user_id;
                $SPRoutingAlert->subscription_id = $Subscription->id;
                $SPRoutingAlert->commission_from = $Routing->min_commission;
                $SPRoutingAlert->commission_to = $Routing->max_commission;
                $SPRoutingAlert->routing_before = 0;
                $SPRoutingAlert->cart_id = $Subscription->cart_id;
                $SPRoutingAlert->sub_category_id = $cartpackage->sub_category_id;
                $SPRoutingAlert->no_of_route = 1;
                $SPRoutingAlert->type = 'manual';
                $SPRoutingAlert->save();
            
                foreach($providers as $provider){
                    $commission = rand($Routing->min_commission,$Routing->max_commission);
                    $SPRoutingAlertDetail = new SPRoutingAlertDetail();
                    $SPRoutingAlertDetail->provider_id = $provider;
                    $SPRoutingAlertDetail->sp_routing_id = $SPRoutingAlert->id;
                    $SPRoutingAlertDetail->user_id = $cart->user_id;
                    $SPRoutingAlertDetail->subscription_id = $Subscription->id;
                    $SPRoutingAlertDetail->commission = $commission;
                    $SPRoutingAlertDetail->routing_before = 0;
                    $SPRoutingAlertDetail->cart_id = $Subscription->cart_id;
                    $SPRoutingAlertDetail->sub_category_id = $cartpackage->sub_category_id;
                    $SPRoutingAlertDetail->no_of_route = 1;
                    $SPRoutingAlertDetail->save();

                    $this->addAlert($SPRoutingAlertDetail->id, 'New Order', 'OrderRouting', $provider);

                    // Send alert to customer
                    $this->addAlertForCust($Subscription->id, 'Order', 'Pending', $cart->user_id, 'Finding Service Provider For Your Order');

                    // Log String
                    $log_string .= 'Subscription Id:'.$Subscription->id.', Service Date:'.$Subscription->service_date.', Service Time:'.$Subscription->service_time.', Cart Id: '.$Subscription->cart_id.', Package Name: '.$cartpackage->package_name.', Base Price: '.$cartpackage->base_price.', Selling Price:'.$cartpackage->selling_price.', Sub Category Id: '.$cartpackage->sub_category_id.', Service Rule: '.$this->getRuleCode($cartpackage->service_rule_id).', Route Before:'.$Routing->route_before.', Service Duration:'.$Routing->service_duration.', Open Time:'.$Routing->open_time.', Close Time:'.$Routing->close_time.', Min Commission:'.$Routing->min_commission.', Max Commission:'.$Routing->max_commission.', Route SP Ids:'.$route_sp_ids.'
                    
                    ';
                }
                DB::commit();

                Log::info('Subscription manual routing successfull! - '.$log_string);

                // Update order status
                $this->updateOrderStatus('Routed',$Subscription->id);
                
                return response()->json(['error' => 0,'message' => 'Subscription manual routing successfull!', 'data' => ''], 200);
            }else{
                return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }
        }catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }


    /**
     * This function use for get routing details by routing id.
     *
     * @return Response
     */
    public function getRoutingDetailsById(Request $request){
        $validator = Validator::make($request->all(), [ 
            'routing_id' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if($token_status['status'] == '200'){
                $routing_details = SPRoutingAlert::where('id', $request->routing_id)->first();
                if($routing_details->count() > 0){
                    $routing_list = SPRoutingAlertDetail::where('sp_routing_id', $request->routing_id)->get();
                    return response()->json(['status' => 1,'message' => 'Routing details', 'data' => $routing_list], 200);
                }else{
                    return response()->json(['status' => 0,'message' => 'Routing details not found', 'data' => $routing_details], 200);
                }
                
            }else{
                return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }

    }


    /**
     * This function use for get accepted and assign order list by sp_id
     *
     * @return Response
     */
    public function getUpcomingOrderBySPId(Request $request){
        $validator = Validator::make($request->all(), [ 
            'sp_id' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if($token_status['status'] == '200'){
                //$routing_details = SPRoutingAlert::orWhere('accept_provider_id', $request->sp_id)->orWhere('assigned_provider_id', $request->sp_id)->with('getsubsdetails')->with('getcartdetails')->with('getCartPackageDetails')->with('getAddressDetails')->with('getSPDetails')->with('getUserDetails')->get();
                $routing_details = SPRoutingAlert::where('status', '!=', 'Completed')->where('accept_provider_id', $request->sp_id)->with('getsubsdetails')->with('getcartdetails')->with('getCartPackageDetails')->with('getAddressDetails')->with('getSPDetails')->with('getUserDetails')->get();
                if($routing_details->count() > 0){
                    return response()->json(['status' => 1,'message' => 'Upcoming orders', 'data' => $routing_details], 200);
                }else{
                    return response()->json(['status' => 0,'message' => 'Upcoming order not found', 'data' => $routing_details], 200);
                }
                
            }else{
                 return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }

    }
    
    
    /**
     * This function use for get completed order list by sp_id
     *
     * @return Response
     */
    public function getCompletedOrderBySPId(Request $request){
        $validator = Validator::make($request->all(), [ 
            'sp_id' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            if($token_status['status'] == '200'){
                $sp_ids = SPRoutingAlert::select('subscription_id')->orWhere('accept_provider_id', $request->sp_id)->pluck('subscription_id')->toArray();

                $complete_subscriptions = Subscription::whereIn('id', $sp_ids)->where('status', 'Completed')->with('getcartdetails')->with('getCartPackageDetails')->with('getAddressDetails')->get();
                
                if($complete_subscriptions->count() > 0){
                    return response()->json(['status' => 1,'message' => 'Completed orders', 'data' => $complete_subscriptions], 200);
                }else{
                    return response()->json(['status' => 0,'message' => 'Completed order not found', 'data' => $complete_subscriptions], 200);
                }
                
            }else{
                 return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }

    }
    
    // Check provider or sub-provider by sp_id
    public function checkProviderOrSubProviderByID($sp_id){
        $provider = DB::connection('sp_management')->select("SELECT role FROM sp_detail where id ='".$sp_id."'")[0]->role;
        if($provider == 'provider'){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * This function use for get subscription details by subscription id
     *
     * @return Response
     */
    public function getOrderById(Request $request){
        $validator = Validator::make($request->all(), [ 
            'sub_id' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if($token_status['status'] == '200'){
                $routing_details = SPRoutingAlert::where('subscription_id', $request->sub_id)->with('getsubsdetails')->with('getcartdetails')->with('getCartPackageDetails')->with('getAddressDetails')->with('getSPAssignDetails')->with('getUserDetails')->get();
                
                if($routing_details->count() > 0){
                    return response()->json(['status' => 1,'message' => 'Order Details', 'data' => $routing_details], 200);
                }else{
                    return response()->json(['status' => 0,'message' => 'Order Details not found', 'data' => $routing_details], 200);
                }
                
            }else{
                 return response()->json(['error' => 1,'message' => 'Unauthorized auth token'], 401);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'error: '.$e], 500);
        }

    }
    
    
    /**
     * This function use for increase the routing time
     *
     * @return Response
     */
    public function makeCompleteOrder(Request $request){
        $validator = Validator::make($request->all(), [ 
            'sub_id' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $searching_alerts = SPRoutingAlert::where('status', 'CLOSE')->where('subscription_id', $request->sub_id)->update(['status' => 'COMPLETED']);
            
            Log::info('Subscription maked a completed in routing table');
            return response()->json(['error' => 0,'message' => 'Searchig Alert Routing Update Stats Successfull!', 'data' => ''], 200);
        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }
    
    /**
     * This function use for add payment for all yesterday completed subscription.
     *
     * @return Response
     */
    public function addPaymentForCompletedSubs(Request $request){

        DB::beginTransaction();
        try {

            $sub_details = Subscription::where('status', 'Completed')->where('service_date', date('Y-m-d', strtotime("-1 day")))->with('getCartPackageDetails')->with('getcartdetails')->get();
            
            foreach($sub_details as $sub_detail){
                $routing_details = DB::connection('sp_management')->select("SELECT * FROM routing.sp_routing_alert_details WHERE subscription_id IN (".$sub_detail->id.") and status ='CLOSE' LIMIT 1")[0];
                
                $transactions = DB::connection('cart_management')->select("SELECT * FROM cart_management.`transaction` WHERE subscription_id IN (".$sub_detail->id.")");

                $commission = $routing_details->commission;
                $sp_id = $routing_details->provider_id;

                $total_amount = 0;
                foreach($transactions as $transaction){
                    
                    // Add Payment transaction
                    $SPPayment = new SPPaymentTransaction();
                    $SPPayment->subscription_id = $transaction->subscription_id;
                    $SPPayment->sp_id = $sp_id;
                    $SPPayment->amount = $transaction->total;
                    if($transaction->description == 'order'){
                        $SPPayment->commission = $commission;
                        $SPPayment->commission_mount = $transaction->total*$commission/100;
                        $total_amount += $transaction->total*$commission/100;
                    }else{
                        $SPPayment->commission = 100;
                        $total_amount += $transaction->total;
                    }
                    //$SPPayment->payment_status = 'pending';
                    $SPPayment->description = $transaction->description;
                    $SPPayment->create_by = 'Admin';
                    $SPPayment->save();                    
                }
                
                // Add Payment
                $SPPayment = new SPPayment();
                $SPPayment->subscription_id = $sub_detail->id;
                $SPPayment->sp_id = $sp_id;
                $SPPayment->amount = $total_amount;
                $SPPayment->payment_status = 'pending';
                $SPPayment->create_by = 'Admin';
                $SPPayment->save();
                
            }
            DB::commit();
            return true;
        }catch(\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'error: '.$e], 500);
        }
    }
    
    /**
     * This function use for add sp payment.
     *
     * @return Response
     */
    public function addSPPayment(Request $request){
        $validator = Validator::make($request->all(), [ 
            'subscription_id' => 'required',
            'sp_id' => 'required',
            'transaction_id' => 'required',
            'amount' => 'required',
            'create_by' => 'required'
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){

                // Check sp payment availble or not
                $SPPayment = SPPayment::where('subscription_id', $request->subscription_id)->where('sp_id', $request->sp_id)->first();
                if($SPPayment->count() > 0){
                    if($SPPayment->payment_status == 'done'){
                        return response()->json(['status' => 0,'message' => 'payment has already been paid!', 'data' => ''], 401);
                    }

                    if($SPPayment->amount != $request->amount){
                        return response()->json(['status' => 0,'message' => 'Payment amount should be same as generated payment: '.$SPPayment->amount, 'data' => ''], 401);
                    }
                }

                // add payment
                $SPPaymentHistory = new SPPaymentHistory();
                $SPPaymentHistory->subscription_id = $request->subscription_id;
                $SPPaymentHistory->sp_id = $request->sp_id;
                $SPPaymentHistory->transaction_id = $request->transaction_id;
                $SPPaymentHistory->amount = $request->amount;
                $SPPaymentHistory->comment = $request->comment;
                $SPPaymentHistory->payment_status = 'done';
                $SPPaymentHistory->create_by = $request->create_by;
                $SPPaymentHistory->payment_date = date('Y-m-d, H:i:s');
                $SPPaymentHistory->save();  
                
                // Update the payment status of sp payment table
                $SPPayment->payment_status = 'done';
                $SPPayment->save();

                return response()->json(['status' => 1,'message' => 'Payment Done!', 'data' => $SPPaymentHistory], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'unexpected signing method in auth token'], 500);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }
    
    /**
     * This function use for get sp payments.
     *
     * @return Response
     */
    public function getSPPayments(Request $request){
        
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){

                // Check sp payment availble or not
                $SPPayment = SPPayment::with('getSubscriptionDetails')->get();

                return response()->json(['status' => 1,'message' => 'SP Payments!', 'data' => $SPPayment], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'unexpected signing method in auth token'], 500);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }

    /**
     * This function use for get sp payments.
     *
     * @return Response
     */
    public function getSPPaymentDetail(Request $request){
        $validator = Validator::make($request->all(), [ 
            'payment_id' => 'required',
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){

                // Check sp payment availble or not
                $SPPayment = SPPayment::where('id', $request->payment_id)->with('getPaymentTransaction')->first();

                return response()->json(['status' => 1,'message' => 'SP Payments!', 'data' => $SPPayment], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'unexpected signing method in auth token'], 500);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }
    
    /**
     * This function use for get sp payments receive details.
     *
     * @return Response
     */
    public function getSPPaymentReceive(Request $request){
        $validator = Validator::make($request->all(), [ 
            'sub_id' => 'required',
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){

                // Check sp payment availble or not
                $SPPaymentHistory = SPPaymentHistory::where('subscription_id', $request->sub_id)->first();

                return response()->json(['status' => 1,'message' => 'SP Payments Transaction!', 'data' => $SPPaymentHistory], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'unexpected signing method in auth token'], 500);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }
    
    
    /**
     * This function use for get sp payments for sp dashboard.
     *
     * @return Response
     */
    public function getSPPaymentsForSPDas(Request $request){
        $validator = Validator::make($request->all(), [ 
            'sp_id' => 'required',
        ]);

        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }

        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){

                // Check sp payment availble or not 
                $SPPayment = SPPayment::where('sp_id', $request->sp_id)->with('getSubscriptionDetails')->get();

                return response()->json(['status' => 1,'message' => 'SP Payments!', 'data' => $SPPayment], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'unexpected signing method in auth token'], 500);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }
    
    /**
     * This function use for get sp payments for sp dashboard.
     *
     * @return Response
     */
    public function getPaymentDetailForSPDas(Request $request){
        $validator = Validator::make($request->all(), [ 
            'sp_id' => 'required',
            'payment_id' => 'required',
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){

                // Check sp payment availble or not
                $SPPayment = SPPayment::where('id', $request->payment_id)->where('sp_id', $request->sp_id)->with('getPaymentTransaction')->first();

                return response()->json(['status' => 1,'message' => 'SP Payments!', 'data' => $SPPayment], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'unexpected signing method in auth token'], 500);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }
    
    /**
     * This function use for get sp payments receive details for sp dashboard.
     *
     * @return Response
     */
    public function getPaymentReceiveForSPDas(Request $request){
        $validator = Validator::make($request->all(), [ 
            'sp_id' => 'required',
            'sub_id' => 'required',
        ]);
        if ($validator->fails()) { 
            $result = ['type'=>'error', 'message'=>$validator->errors()->all()];
            return response()->json($result);            
        }
        try {
            $AuthController = new AuthController();
            $token_status = $AuthController->tokenVerify($request);
            
            if(@$token_status['status'] == '200'){

                // Check sp payment availble or not
                $SPPaymentHistory = SPPaymentHistory::where('subscription_id', $request->sub_id)->where('sp_id', $request->sp_id)->first();

                return response()->json(['status' => 1,'message' => 'SP Payments Transaction!', 'data' => $SPPaymentHistory], 200);
            }else{
                return response()->json(['status' => 0, 'error' => 1,'message' => 'unexpected signing method in auth token'], 500);
            }

        }catch(\Exception $e) {
            return response()->json(['message' => 'Error: '.$e], 500);
        }

    }
    
    
}