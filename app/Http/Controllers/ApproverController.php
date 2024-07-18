<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;

class LocationController extends Controller
{
    public function __construct(){
        $this->table = "locations";
    }

    public function index(){
        return $this->_view('index', [
            'title' => 'Location'
        ]);
    }

    public function get(Request $req){
        $array = Location::select($req->select);

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
        $data = new Location();
        
        $data->admin_id = auth()->user()->id;
        $data->location = $req->location;
        $data->contact = $req->contact;

        $data->save();
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
        Location::find($req->id)->delete();
    }

    private function _view($view, $data = array()){
        return view("locations" . "." . $view, $data);
    }
}
