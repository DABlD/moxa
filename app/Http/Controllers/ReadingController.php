<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User, Reading};
use DB;

class ReadingController extends Controller
{
    public function __construct(){
        $this->table = "readings";
    }

    public function index(){
        return $this->_view('index', [
            'title' => 'Reading'
        ]);
    }

    public function get(Request $req){
        $array = Reading::select($req->select);

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

    public function getReading(Request $req){
        $from = now()->parse($req->from)->startOfDay()->toDateTimeString();
        $to = now()->parse($req->to)->endOfDay()->toDateTimeString();

        // category id is building id
        $temp = Reading::select('readings.*')
            ->where('m.category_id', 'like', $req->building)
            ->join('moxas as m', 'm.id', '=', 'readings.moxa_id')
            ->whereBetween('datetime', [$from, $to]);

        $temp = $temp->get();

        // $temp->load('reorder.medicine');
        $temp->load('moxa');
        $temp = $temp->groupBy('moxa_id');

        $from = $req->from;
        $to = $req->to;

        $dates = $this->getDates($from, $to);
        $array = [];

        foreach($temp as $medicine){
            $grandtotal = 0;
            $tempDates = [];
            foreach($dates as $date){
                $total = 0;
                foreach($medicine as $data){
                    if(now()->parse($data->datetime)->startOfDay()->toDateTimeString() == $date){
                        $total += $data->total;
                        $grandtotal += $data->total;
                    }
                }

                $tempDates[now()->parse($date)->format('M d')] = $total;
            }

            array_push($array, 
                array_merge(
                    array_merge(
                        ["item" => $medicine[0]->moxa->name], 
                        $tempDates
                    ),
                    ["total" => $grandtotal]
                )
            );
        }

        echo json_encode($array);
    }

    private function getDates($from, $to){
        $dates = [];

        while($from <= $to){
            $tempDate = now()->parse($from);
            array_push($dates, $tempDate->toDateTimeString());
            $from = $tempDate->addDay()->toDateString();
        }

        return $dates;
    }

    public function store(Request $req){
        $reading = new Reading();
        $reading->moxa_id = $req->moxa_id;
        $reading->total = $req->total;
        $reading->datetime = $req->datetime;
        $reading->save();
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
        Reading::find($req->id)->delete();
    }

    private function _view($view, $data = array()){
        return view('readings' . "." . $view, $data);
    }
}
