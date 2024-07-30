<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\{Billing, Device, Reading};

class SampleController extends Controller
{
    public function readAndBill(){
        return view('readAndBill', [
            "title" => "Read And Bill"
        ]);
    }

    public function store(Request $req){
        $device = Device::find($req->moxa_id);

        // $readings = Reading::where('moxa_id', $req->moxa_id)->whereBetween('datetime', [$req->from . ' 00:00:00', $req->to . ' 11:59:59'])->orderBy('datetime', 'desc')->get();
        $readings = Reading::where('moxa_id', $req->moxa_id)->orderBy('datetime', 'desc')->first();

        $bill = new Billing();
        $bill->user_id = $device->name;
        $bill->moxa_id = $req->moxa_id;
        $bill->billno = "MB" . now()->format('Ymd') . sprintf('%06d', Billing::count() + 1);
        $bill->reading = $req->reading;
        $bill->initReading = $readings->total;
        $bill->consumption = $req->reading - $bill->initReading;
        $bill->from = $req->from;
        $bill->to = $req->to;
        $bill->rate = $device->category->rate;
        $bill->late_interest = $device->category->late_interest;
        $bill->total = $bill->consumption * $device->category->rate;
        $bill->status = "Unpaid";
        $bill->save();
    }

    public function getDevices(Request $req){
        $array = Device::select($req->select);

        // IF HAS SORT PARAMETER $ORDER
        if($req->order){
            $array = $array->orderBy($req->order[0], $req->order[1]);
        }

        // IF HAS WHERE
        if($req->where){
            $array = $array->where($req->where[0], $req->where[1]);
        }

        // IF HAS LIKE
        if($req->like){
            $array = $array->where($req->like[0], 'LIKE' ,$req->like[1]);
        }

        $array = $array->get();

        // IF HAS LOAD
        if($array->count() && $req->load){
            foreach($req->load as $table){
                $array->load($table);
            }
        }

        // IF HAS GROUP
        if($req->group){
            $array = $array->groupBy($req->group);
        }

        echo json_encode($array);
    }

    public function getLatestReading(Request $req){
        echo Reading::where('moxa_id', $req->id)->orderBy('datetime', 'desc')->get()->first();
    }
}
