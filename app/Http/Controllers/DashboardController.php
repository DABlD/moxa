<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Request as Req, Data, Alert, Moxa};
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

        $moxas = Moxa::all();

        return $this->_view('dashboard', [
            'title'         => 'Dashboard',
            'moxas'         => $moxas
        ]);
    }

    private function _view($view, $data = array()){
        return view($view, $data);
    }
}
