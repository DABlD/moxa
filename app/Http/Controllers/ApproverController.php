<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;

class ApproverController extends Controller
{
    public function __construct(){
        $this->table = "users";
    }

    public function index(){
        return $this->_view('index', [
            'title' => 'Approver'
        ]);
    }

    public function get(Request $req){
        $array = User::select($req->select);

        // IF HAS SORT PARAMETER $ORDER
        if($req->order){
            $array = $array->orderBy($req->order[0], $req->order[1]);
        }

        // IF HAS WHERE
        if($req->where){
            $array = $array->where($req->where[0], $req->where[1]);
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
        foreach($req->data as $temp){
            $temp = (object)$temp;

            $data = new User();
            $data->name = $temp->name;
            $data->email = $temp->email;
            $data->contact = $temp->contact;
            $data->save();
        }
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
        User::find($req->id)->delete();
    }

    private function _view($view, $data = array()){
        return view("approvers" . "." . $view, $data);
    }
}
