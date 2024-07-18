<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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

    private function _view($view, $data = array()){
        return view("subscribers" . "." . $view, $data);
    }
}
