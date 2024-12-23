<?php

namespace App\Http\Controllers;

use Illuminate\Http\{Request, JsonResponse};
use App\Models\{Reading, User, Device, Billing};
use Exception;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiController extends Controller
{
    public function receive(Request $req)
    {
        try{
            $device = Device::where('serial', $req->meter_id)->first();

            if($device){
                $reading = new Reading();
                $reading->moxa_id = $device->id;
                $reading->datetime = now()->parse($req->datetime)->toDateTimeString();
                $reading->total = $req->wh_total ?? $req->payload;
                $reading->save();

                return response()->json([
                    'data' => $reading,
                    'message' => 'Success'
                ], JsonResponse::HTTP_OK);
            }
            else{
                return response()->json([
                    'seriak' => $device->id,
                    'message' => 'Device serial not detected.'
                ], JsonResponse::HTTP_OK);
            }
        } catch (Exception $e) {
            return response()->json([
                'data' => [],
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
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

        if(isset(($req->device_id))){
            $billings->where('moxa_id', $req->device_id);
        }

        $billings = $billings->get();
        $billings->load('device.subscriber');

        return response()->json([
            'data' => $billings,
            'message' => "Success"
        ]);
    }

    public function getLatestBilling(Request $req){
        $billings = Billing::select('*')->where('billno', $req->billing_no);

        $billings = $billings->latest()->first();
        $billings->load('device.subscriber');

        return response()->json([
            'data' => $billings,
            'message' => "Success"
        ]);
    }

    public function pay(Request $req){
        $bill = Billing::where("billno", $req->billing_no)->first();
        $bill->mop = $req->mop;
        $bill->refno = $req->refno;
        $bill->invoice = "INV" . now()->format('Ymd') . sprintf('%06d', Billing::where('invoice', 'like', "INV" . now()->format('Ymd') . '%')->count() + 1);
        $bill->status = "Paid";
        $bill->date_paid = now();
        $bill->save();

        return response()->json([
            'data' => $bill,
            'message' => "Success"
        ]);
    }

    public function store(Request $req){
        $reading = new Reading();
        $reading->moxa_id = $req->device_id;
        $reading->total = $req->reading;
        $reading->datetime = now();
        $reading->save();

        return response()->json([
            'data' => $reading,
            'message' => "Success"
        ]);
    }

    public function subscribers(Request $req){
        $users = User::where('role', 'Subscriber')
                    ->select('id', 'name', 'email', 'address', 'contact');

        if(isset(($req->id))){
            $users->where('id', $req->id);
        }

        $users = $users->get();

        return response()->json([
            'data' => $users,
            'message' => "Success"
        ]);
    }
}