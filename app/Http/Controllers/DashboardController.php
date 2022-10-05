<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Request as Req, Data, Alert, Category};
use Auth;

class DashboardController extends Controller
{
    function index(){
        if(auth()->user()->role == "Approver"){
            return redirect()->route('request.request')->send();
        }
        elseif(auth()->user()->role == "Super Admin"){
            return redirect()->route('admin.admin')->send();
        }

        $buildings = Category::all();

        return $this->_view('dashboard', [
            'title'         => 'Dashboard',
            'buildings'         => $buildings
        ]);
    }

    private function _view($view, $data = array()){
        return view($view, $data);
    }
}
