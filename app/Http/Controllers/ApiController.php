<?php

namespace App\Http\Controllers;

use Illuminate\Http\{Request, JsonResponse};
use App\Models\{Reading, User, Device};
use Exception;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiController extends Controller
{
    public function receive(Request $req)
    {
        try{
            $reading = new Reading();
            $reading->moxa_id = $req->id;
            $reading->datetime = $req->datetime;
            $reading->total = $req->payload;
            $reading->save();
        } catch (Exception $e) {
            return response()->json([
                'data' => [],
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        return response()->json([
            'data' => $reading,
            'message' => 'Success'
        ], JsonResponse::HTTP_OK);
    }

    public function getToken(Request $request){
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }
     
        return $user->createToken($request->device_name)->plainTextToken;
    }

    public function revokeToken(Request $request){
        $request->user()->currentAccessToken()->delete();

        return ["message" => "Success"];
    }

    public function getDevices(Request $req){
        $devices = Device::select('*');

        if(isset(($req->user_id))){
            $devices->where('user_id', $req->user_id);
        }

        $devices = $devices->get();
        $devices->load('category');

        return response()->json([
            'data' => $devices,
            'message' => "Success"
        ]);
    }

    public function getBillings(Request $req){
        $billings = Billing::select('*');

        // if(isset(($req->user_id))){
        //     $devices->where('user_id', $req->user_id);
        // }

        $billings = $billings->get();
        $billings->load('device.subscriber');

        return response()->json([
            'data' => $billings,
            'message' => "Success"
        ]);
    }

    public function getLatestBilling(Request $req){
        $billings = Billing::select('*');

        // if(isset(($req->user_id))){
        //     $devices->where('user_id', $req->user_id);
        // }

        $billings = $billings->latest()->first();
        $billings->load('device.subscriber');

        return response()->json([
            'data' => $billings,
            'message' => "Success"
        ]);
    }
}
