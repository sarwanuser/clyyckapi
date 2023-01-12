<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use  App\Models\Subscription;


class Controller extends BaseController{
    
    //Add this method to the Controller class
    protected function respondWithToken($token){
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }

    // Update the order status
    public function updateOrderStatus($status, $order_id){
        $Subscription = Subscription::where('id', $order_id)->first();
        $Subscription->status = $status;
        $Subscription->save();
        
        return true;
    }
}
