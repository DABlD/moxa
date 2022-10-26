<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Request as Req, Data, Alert, Device};
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

        $moxas = null;

        if(auth()->user()->role == "Admin"){
            $moxas = Device::all();
        }
        else{
            $moxas = Device::select('devices.*')
                        ->join('buildings as c', 'c.id', '=', 'devices.id')
                        ->join('sites as s', 's.id', '=', 'c.site_id')
                        ->where('s.user_id', auth()->user()->id)
                        ->get();
        }

        return $this->_view('dashboard', [
            'title'         => 'Dashboard',
            'moxas'         => $moxas
        ]);
    }

    private function _view($view, $data = array()){
        return view($view, $data);
    }
}
