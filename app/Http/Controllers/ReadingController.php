<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{User, Reading, Category, Moxa, TransactionType};
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Report;
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
        $from = now()->parse($req->from)->sub(1, 'day')->toDateString();
        $to = $req->to;
        $type = $req->type;

        $data = Reading::select('readings.*', 'm.category_id', 'm.utility')
                        ->where('m.id', 'like', $req->moxa_id)
                        ->whereBetween('datetime', [$from, $to])
                        ->join('moxas as m', 'm.id', '=', 'readings.moxa_id')
                        ->get()->groupBy('moxa_id');
        
        $moxa = Moxa::where('id', $req->moxa_id)->first();

        $dataset = [];
        $labels = [];
        $temp = [];
        $values = [];

        if($req->fby == "Daily"){
            // FILL LABELS
            $temp2 = $req->from;
            while($temp2 <= $to){
                array_push($labels, $temp2);
                $temp2 = now()->parse($temp2)->addDay()->toDateString();
            }

            // DATA LOOP
            foreach($data as $id => $readings){
                $temp2 = $req->from;
                $ctr = 0;
                while($temp2 <= $to){
                    $temp[$id][$temp2] = 0;
                    $temp2 = now()->parse($temp2)->addDay()->toDateString();
                    $ctr++;
                }

                $temp[$id] = [];
                $values[$id] = [];

                $readings = $readings->sortBy('datetime');
                $start = 0;
                $startDate = $req->from;

                foreach($readings as $reading){
                    $temp3 = now()->parse($reading->datetime)->toDateString();

                    if($temp3 >= $startDate){
                        $temp[$id][$temp3] = $reading->total - $start;
                        $startDate = now()->parse($startDate)->addDay()->toDateString();
                    }
                    else{
                        $start = $reading->total;
                    }

                    $values[$id][$temp3] = [
                        "date" => $temp3,
                        "payload" => $reading->total,
                        "created_at" => $reading->created_at
                    ];
                }
            }
        }
        else{
            // FILL LABELS
            $temp2 = now()->parse($req->from)->startOfDay()->addHour()->toDateTimeString();
            $to = now()->parse($req->from)->addDay()->startOfDay()->toDateTimeString();
            while($temp2 <= $to){
                array_push($labels, $temp2);
                $temp2 = now()->parse($temp2)->add(1, 'hour')->toDateTimeString();
            }

            // DATA LOOP
            foreach($data as $id => $readings){
                $temp2 = now()->parse($req->from)->startOfDay()->addHour()->toDateTimeString();
                $to = now()->parse($req->from)->addDay()->startOfDay()->toDateTimeString();
                $ctr = 0;

                $readings = $readings->filter(function($value, $key) use($to){
                    return $value->datetime <= $to;
                });

                while($temp2 <= $to){
                    $temp[$id][$temp2] = 0;
                    $temp2 = now()->parse($temp2)->add(1, 'hour')->toDateTimeString();
                    $ctr++;
                }

                $temp[$id] = [];
                $values[$id] = [];

                $readings = $readings->sortBy('datetime');
                $start = 0;
                $startDate = now()->parse($req->from)->startOfDay()->addHour()->toDateTimeString();

                foreach($readings as $reading){
                    $temp3 = date("Y-m-d H:00:00",strtotime($reading->datetime . " + 1hour "));

                    if($temp3 >= $startDate){
                        $temp[$id][$temp3] = $reading->total - $start;
                        $start = $reading->total;
                    }
                    else{
                        $start = $reading->total;
                    }

                    if($temp3 >= $startDate){
                        $temp3 = date("Y-m-d H:00:00",strtotime($temp3. " + 1hour "));
                        $values[$id][$temp3] = [
                            "date" => $temp3,
                            "payload" => $reading->total,
                            "created_at" => $reading->created_at
                        ];
                        $startDate = now()->parse($startDate)->addHour()->toDateTimeString();
                    }
                }
            }
        }

        $multiplier = TransactionType::where('admin_id', auth()->user()->id)->where('type', $moxa->utility)->first()->demand;

        // FILL EMPTY DATES
        foreach($labels as $label){
            foreach($temp as $i => $temp2){
                if(!isset($temp2[$label])){
                    $temp[$i][$label] = 0;
                }
                elseif($multiplier > 0 && $req->type == "demand"){
                    $temp[$i][$label] += ($temp[$i][$label] * ($multiplier / 100));
                }
            }

            foreach($values as $id => $temp2){
                if(!isset($temp2[$label])){
                    $values[$i][$label] = [
                        "date" => $label,
                        "payload" => null,
                        "created_at" => null
                    ];
                }
                elseif($multiplier > 0 && $req->type == "demand"){
                    $values[$i][$label]['payload'] += ($values[$i][$label]['payload'] * ($values[$i][$label]['payload'] / 100));
                    $temp[$i][$label] += ($temp[$i][$label] * ($multiplier / 100));
                }
            }
        }

        foreach($temp as $i => $data){
            $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            array_push($dataset, [
                'label' => $moxa->name,
                'data' => $data,
                'borderColor' => $color,
                'backgroundColor' => $color,
                'hoverRadius' => 10,
                'values' => $values[$i],
                'bid' => $moxa->id
            ]);
        }

        // array_push($dataset, [
        //     'label' => $moxas[$id],
        //     'data' => $data,
        //     'borderColor' => $color,
        //     'backgroundColor' => $color,
        //     'hoverRadius' => 10,
        //     'values' => $temp3[$id],
        //     'bid' => $id
        // ]);

        // dd(['labels' => $labels, 'dataset' => $dataset]);
        echo json_encode(['labels' => $labels, 'dataset' => $dataset]);
    }

    public function exportPerBuilding(Request $req){
        // +1 IN FROM DATE TO GET INITIAL
        $from = now()->parse($req->from)->sub(1, 'day')->toDateString();
        $to = $req->to;

        $dates = $this->getDates($from, $to);
        $data = Reading::select('readings.*', 'm.category_id')
                        ->whereBetween('datetime', [$from, $to])
                        ->join('moxas as m', 'm.id', '=', 'readings.moxa_id');
                        // ->where('user_id', '>', 1)

        $data = $data->get();
        
        // $data->load('moxa');
        $data = $data->groupBy('moxa_id');
        $moxas = Moxa::whereIn('id', array_keys($data->toArray()))->pluck('name', 'id');
        $labels = [];

        if($req->fby == "Daily"){


            $temp = [];
            $temp2 = [];
            $ctr = 0;
            foreach(array_keys($data->toArray()) as $id){
                $from = now()->parse($req->from)->sub(1, 'day')->toDateTimeString();
                $to = $req->to;

                $temp[$id] = [];
                $labels[$id] = [];

                while($from <= $to){
                    $cur = $from;
                    $from = now()->parse($from)->add(1, 'day')->toDateTimeString();
                    
                    if($from >= $req->from){
                        array_push($labels[$id], $from);
                        $temp[$id][$from] = 0;
                    }

                    $ctr++;
                }
            }
            
            foreach($data as $id => $readings){
                $temp2[$id] = [];
                $prev = 0;
                foreach($readings as $key => $reading){
                    // $date = date("Y-m-d H:00:00",strtotime($reading->datetime . " + 1day "));
                    $date = now()->parse($reading->datetime)->startOfDay()->toDateTimeString();
                    if(now()->parse($date)->toDateString() >= $req->from){
                        // if($key == 0){
                        //     $temp[$id][$date] += $reading->total;
                        // }
                        // else{
                        // }
                        $temp[$id][$date] += $reading->total - $prev;

                        $temp3 = [
                            "date" => $date, 
                            "payload" => $reading->total,
                            "created_at" => $reading->created_at->toDateTimeString() ?? now()->toDateTimeString(),
                        ];
                        // array_push($temp2[$id], $temp3);
                        $temp2[$id][$date] = $temp3;
                    }

                    $prev = $reading->total;
                }
            }

            foreach($temp2 as $key => $temp3){
                $temp2[$key] = array_values($temp3);
            }
            // dd($temp2);

            $dataset = [];
            $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            foreach($temp as $id => $readings){
                array_push($dataset, [
                    'label' => $moxas[$id],
                    'data' => array_values($readings),
                    'borderColor' => $color,
                    'backgroundColor' => $color,
                    'hoverRadius' => 10,
                    'values' => $temp2[$id],
                    'bid' => $id
                ]);
            }
        }
        else{
            $temp = [];
            $temp2 = [];
            $ctr = 0;
            foreach(array_keys($data->toArray()) as $id){
                $from = now()->parse($req->from)->sub(1, 'day')->startOfDay()->toDateTimeString();
                $to = now()->parse($from)->addDay()->endOfDay()->toDateTimeString();

                $temp[$id] = [];
                $labels[$id] = [];

                while($from <= $to){
                    $cur = $from;
                    $from = now()->parse($from)->add(1, 'hour')->toDateTimeString();
                    
                    if($from >= $req->from){
                        array_push($labels[$id], $from);
                        $temp[$id][$from] = 0;
                    }

                    $ctr++;
                }
            }

            $to = now()->parse($req->from)->endOfDay()->toDateTimeString();
            foreach($data as $id => $readings){
                $temp2[$id] = [];
                $prev = 0;
                foreach($readings as $key => $reading){
                    $date = date("Y-m-d H:00:00",strtotime($reading->datetime . " + 1hour "));
                    if(now()->parse($date)->toDateString() >= $req->from && now()->parse($date)->toDateString() <= $to){
                        if($key == 0){
                            $temp[$id][$date] = $reading->total;
                        }
                        else{
                            $temp[$id][$date] = $reading->total - $prev;
                        }

                        $temp3 = [
                            "date" => $date, 
                            "payload" => $reading->total,
                            "created_at" => $reading->created_at->toDateTimeString() ?? now()->toDateTimeString(),
                        ];
                        array_push($temp2[$id], $temp3);
                    }

                    $prev = $reading->total;
                }
            }

            $dataset = [];
            $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            foreach($temp as $id => $readings){
                array_push($dataset, [
                    'label' => $moxas[$id],
                    'data' => array_values($readings),
                    'borderColor' => $color,
                    'backgroundColor' => $color,
                    'hoverRadius' => 10,
                    'values' => $temp2[$id],
                    'bid' => $id
                ]);
            }
        }

        // dd(['labels' => $labels, 'dataset' => $dataset]);
        $type = ucfirst($req->type);
        $title = now()->parse($req->from)->format("d-M-y") . " - " . now()->parse($req->to)->format("d-M-y") . "($req->fby $type)";
        return Excel::download(new Report($labels, $dataset), $title . ".xlsx");
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
