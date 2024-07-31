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
        $reading = new Reading();
        $reading->moxa_id = $req->moxa_id;
        $reading->total = $req->total;
        $reading->datetime = now();
        $reading->save();
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
        $data = Reading::where('moxa_id', $req->id)->orderBy('datetime', 'desc')->get()->first();
        if($data){
            $data->load('device.subscriber');
        }

        echo json_encode($data);
    }
}
