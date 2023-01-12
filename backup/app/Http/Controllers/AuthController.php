<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    

    /**
     * This function use for token verify 
     *
     * @param  Request  $request
     * @return Response
     */
    public function tokenVerify(Request $request){
        $token = str_replace('Bearer ', '', $request->header('authorization'));

        // $this->validate($request, [
        //     'token' => 'required'
        // ]);

        try{
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://ec2-3-108-220-85.ap-south-1.compute.amazonaws.com:8080/verify/token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                    "token": "'.$token.'"
                }',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return json_decode($response, true);

        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * This function use for get user info by token
     *
     * @param  Request  $request
     * @return Response
     */
    public function getUserInfo(Request $request){
        $token = str_replace('Bearer ', '', $request->header('authorization'));

        try{
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://ec2-3-108-220-85.ap-south-1.compute.amazonaws.com:8089/clykk/ext/um/v1/get-profile',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$token
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return json_decode($response, true);

        }catch(Exception $e){
            return $e->getMessage();
        }
    }


}