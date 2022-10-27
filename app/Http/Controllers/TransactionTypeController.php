<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionType;
use DB;

class TransactionTypeController extends Controller
{
    public function __construct(){
        $this->table = "transaction_types";
    }

    public function index(){
        return $this->_view('index', [
            'title' => 'Transaction Type'
        ]);
    }

    public function get(Request $req){
        $array = TransactionType::select($req->select);

        // IF JOIN
        if($req->join){
            $array = $array->join('rhus as r', 'r.admin_id', '=', 'transaction_types.admin_id');
        }

        // IF HAS SORT PARAMETER $ORDER
        if($req->order){
            $array = $array->orderBy($req->order[0], $req->order[1]);
        }

        // IF HAS WHERE
        if($req->where){
            $array = $array->where($req->where[0], $req->where[1]);
        }

        // IF HAS WHERENOTNULL
        if($req->whereNotNull){
            $array = $array->whereNotNull($req->whereNotNull);
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
        $entry = new TransactionType();
        $entry->admin_id = auth()->user()->id;
        $entry->type = $req->type;
        $entry->operator = $req->operator;
        $entry->demand = $req->demand;
        $entry->rate = $req->rate;
        $entry->save();
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
        TransactionType::find($req->id)->delete();
    }

    private function _view($view, $data = array()){
        return view($this->table . "." . $view, $data);
    }
}
