<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User, Device, Billing};
use DB;

class SubscriberController extends Controller
{
    public function __construct(){
        $this->table = "users";
    }

    public function index(){
        return $this->_view('index', [
            'title' => 'Subscribers'
        ]);
    }

    public function getSubscriberDetails(Request $req){
        $user = User::find($req->id);

        $devices = Device::where('name', $req->id)->get();
        $devices->load('category');

        $ids = $devices->pluck('id');
        $billings = Billing::whereIn('id', $ids)->get();
        $billings->load('device');

        echo json_encode([
            "user" => $user,
            "devices" => $devices,
            "billings" => $billings
        ]);
    }

    private function _view($view, $data = array()){
        return view("subscribers" . "." . $view, $data);
    }
}
