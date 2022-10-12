<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User, Reading, Category, Moxa};
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
                        ["utility" => $medicine[0]->moxa->utility], 
                        $tempDates
                    ),
                    ["total" => $grandtotal]
                )
            );
        }

        echo json_encode($array);
    }

    public function perBuilding(Request $req){
        // +1 IN FROM DATE TO GET INITIAL
        $from = now()->subDays(14)->startOfDay()->toDateTimeString();
        $to = now()->endOfDay()->toDateTimeString();

        $dates = $this->getDates($from, $to);
        $data = Reading::select('readings.*', 'm.category_id')
                        ->where('category_id', 'like', $req->building_id)
                        ->whereBetween('datetime', [$from, $to])
                        ->join('moxas as m', 'm.id', '=', 'readings.moxa_id');
                        // ->where('user_id', '>', 1)

        $data = $data->get();
        
        // $data->load('moxa');
        $data = $data->groupBy('category_id');
        $buildings = Category::whereIn('id', array_keys($data->toArray()))->pluck('name', 'id');

        $initDate = null;
        $labels = [];
        $temp = [];
        foreach($data as $id => $a){
            foreach($dates as $index => $date){ 
                if($index){
                    $date = now()->parse($date)->toDateString();
                    $temp[$id][$date] = 0;
                }
                else{
                    $initDate = $date;
                }
            }
        }

        $temp3 = [];
        foreach($data as $id => $readings){
            $readings = $readings->sortBy('datetime');
            $start = 0;
            foreach($readings as $reading){
                if($reading->datetime->toDateTimeString() == $initDate){
                    $start = $reading->total;
                    $temp3[$id] = [];
                    array_push($temp3[$id], ["date" => $reading->datetime, "payload" => $reading->total]);
                }
                else{
                    $temp[$id][now()->parse($reading->datetime)->toDateString()] = $reading->total - $start;
                    array_push($temp3[$id], ["date" => $reading->datetime, "payload" => $reading->total]);
                    $start = $reading->total;
                }
            }
        }

        $labels = [];
        foreach($dates as $date){
            array_push($labels, now()->parse($date)->format('M d'));
        }

        $dataset = [];
        foreach($temp as $id => $data){
            $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            array_push($dataset, [
                'label' => $buildings[$id],
                'data' => array_values($data),
                'borderColor' => $color,
                'backgroundColor' => $color,
                'hoverRadius' => 10,
                'values' => $temp3[$id],
                'bid' => $id
            ]);
        }

        // dd(['labels' => $labels, 'dataset' => $dataset]);
        echo json_encode(['labels' => $labels, 'dataset' => $dataset]);
    }

    public function perBuilding2(Request $req){
        $from = now()->subDays(14)->startOfDay()->toDateTimeString();
        $to = now()->endOfDay()->toDateTimeString();

        $dates = $this->getDates($from, $to);
        $data = Reading::select('readings.*', 'm.category_id')
                        ->whereBetween('datetime', [$from, $to])
                        ->join('moxas as m', 'm.id', '=', 'readings.moxa_id');
                        // ->where('user_id', '>', 1)

        $data = $data->get();
        
        // $data->load('moxa');
        $data = $data->groupBy('category_id');
        $buildings = Category::whereIn('id', array_keys($data->toArray()))->pluck('name', 'id');

        $labels = [];
        $temp = [];
        foreach($data as $id => $a){
            foreach($dates as $date){
                $date = now()->parse($date)->toDateString();
                $temp[$id][$date] = 0;
            }
        }

        foreach($data as $id => $readings){
            foreach($readings as $reading){
                $temp[$id][now()->parse($reading->datetime)->toDateString()] += $reading->total;
            }
        }

        $labels = [];
        foreach($dates as $date){
            array_push($labels, now()->parse($date)->format('M d'));
        }

        $dataset = [];
        foreach($temp as $id => $data){
            $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            array_push($dataset, [
                'label' => $buildings[$id],
                'data' => array_values($data),
                'borderColor' => $color,
                'backgroundColor' => $color,
                'hoverRadius' => 10
            ]);
        }

        echo json_encode(['labels' => $labels, 'dataset' => $dataset]);
    }

    public function moxaPerBuilding(Request $req){
        $from = now()->subDays(14)->startOfDay()->toDateTimeString();
        $to = now()->endOfDay()->toDateTimeString();

        $dates = $this->getDates($from, $to);
        $data = Reading::select('readings.*', 'm.category_id')
                        ->where('category_id', 'like', $req->building_id)
                        ->whereBetween('datetime', [$from, $to])
                        ->join('moxas as m', 'm.id', '=', 'readings.moxa_id');
                        // ->where('user_id', '>', 1)

        $data = $data->get();
        
        // $data->load('moxa');
        $data = $data->groupBy('moxa_id');
        $buildings = Moxa::whereIn('id', array_keys($data->toArray()))->pluck('name', 'id');

        $labels = [];
        $temp = [];
        foreach($data as $id => $a){
            foreach($dates as $date){
                $date = now()->parse($date)->toDateString();
                $temp[$id][$date] = 0;
            }
        }

        foreach($data as $id => $readings){
            foreach($readings as $reading){
                $temp[$id][now()->parse($reading->datetime)->toDateString()] += $reading->total;
            }
        }

        $labels = [];
        foreach($dates as $date){
            array_push($labels, now()->parse($date)->format('M d'));
        }

        $dataset = [];
        foreach($temp as $id => $data){
            $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            array_push($dataset, [
                'label' => $buildings[$id],
                'data' => array_values($data),
                'borderColor' => $color,
                'backgroundColor' => $color,
                'hoverRadius' => 10
            ]);
        }

        echo json_encode(['labels' => $labels, 'dataset' => $dataset]);
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
