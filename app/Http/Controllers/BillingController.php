<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Billing};
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
        $bill = new Billing();
        $bill = $req->user_id;
        $bill = $req->moxa_id;
        $bill = $req->from;
        $bill = $req->to;
        $bill = $req->reading;
        $bill = $req->rate;
        $bill = $req->total;
        $bill = $req->status;
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
