<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Billing, Device, Reading};
use DB;

class BillingController extends Controller
{
    public function __construct(){
        $this->table = "billings";
    }

    public function index(){
        return $this->_view('index', [
            'title' => 'Billings'
        ]);
    }

    public function get(Request $req){
        $array = Billing::select($req->select);

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

    public function store(Request $req){
        $device = Device::find($req->moxa_id);

        $readings = Reading::where('moxa_id', $req->moxa_id)->whereBetween('datetime', [$req->from . ' 00:00:00', $req->to . ' 11:59:59'])->orderBy('datetime', 'desc')->get();

        $bill = new Billing();
        $bill->user_id = $device->name;
        $bill->moxa_id = $req->moxa_id;
        $bill->billno = "MB" . now()->format('Ymd') . sprintf('%06d', Billing::count() + 1);
        $bill->reading = $req->reading;
        $bill->from = $req->from;
        $bill->to = $req->to;
        $bill->rate = $device->category->rate;
        $bill->late_interest = $device->category->late_interest;
        $bill->total = ($req->reading - $readings->last()->total) * $device->category->rate;
        $bill->status = "Unpaid";
        $bill->save();
    }

    public function pay(Request $req){
        $bill = Billing::find($req->id);
        $bill->mop = $req->mop;
        $bill->refno = $req->refno;
        $bill->invoice = "INV" . now()->format('Ymd') . sprintf('%06d', Billing::where('invoice', 'like', "INV" . now()->format('Ymd') . '%')->count() + 1);
        $bill->status = "Paid";
        $bill->date_paid = now();
        $bill->save();
    }

    public function update(Request $req){
        $query = DB::table($this->table);

        if($req->where){
            $query = $query->where($req->where[0], $req->where[1])->update($req->except(['id', '_token', 'where']));
        }
        else{
            $query = $query->where('id', $req->id)->update($req->except(['id', '_token']));
        }
    }

    public function delete(Request $req){
        Billing::find($req->id)->delete();
    }

    private function _view($view, $data = array()){
        return view('billings' . "." . $view, $data);
    }
}
