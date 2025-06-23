<?php

namespace App\Http\Controllers;

use App\Services\OtpCodeServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    protected $otp_code_services;
public function __construct(OtpCodeServices $otp_code_services)
{
$this->otp_code_services=$otp_code_services;
}
    public function sendOtp(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'phoneNumber' => 'required|unique:students,phoneNumber|regex:/^09\d{8}$/',
                'user' => 'required|string'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }

        $response = $this->otp_code_services->SendOtp($request);
        // return response()->json([$response]);
        if ($response->successful()) {
            return response()->json(['message' => 'the code is sent']);
        } else {
            return response()->json(['message' => 'the code is failed to send', 'error' => $response->json()], 500);
        }
    }
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phoneNumber' => 'required|regex:/^09\d{8}$/',
            'otp' => 'required|digits:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        $response = $this->otp_code_services->VerifyOtp($request);
        if($response==null){
            return response()->json(['message'=>'user type dont send']);
        }
        // return response()->json([$response]);
        if ($response['verify'] == false) {
            return response()->json(["message" => $response['message']]);
        } else
            return response()->json(["message" => $response['message'], "token" => $response["token"], "refresh_token" => $response["refresh_token"]]);
    }
    public function Refresh_token(Request $request)
    {
        $validate = Validator::make($request->all(), ['refresh_token' => 'required|string']);
        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()]);
        }
        return $this->otp_code_services->refresh_token($request);
    }
}
