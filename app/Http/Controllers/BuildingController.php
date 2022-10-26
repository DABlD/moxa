<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Building, Medicine, Reorder};
use Image;
use DB;

class BuildingController extends Controller
{
    public function __construct(){
        $this->table = "medicines";
    }

    public function index(){
        return $this->_view('index', [
            'title' => 'SKU'
        ]);
    }

    public function get(Request $req){
        $array = Medicine::select($req->select);

        // IF HAS SORT PARAMETER $ORDER
        if($req->join){
            $array = $array->join('reorders as r', 'r.medicine_id', '=', 'medicine.id');
        }

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

    public function getCategories(Request $req){
        $array = Building::select($req->select);

        if(auth()->user()->role == "RHU"){
            $array = $array->join('sites as s', 's.id', '=', 'buildings.site_id');
            $array = $array->where('s.user_id', auth()->user()->id);
        }

        // IF JOIN
        if($req->join){
            $array = $array->join('rhus as r', 'r.admin_id', '=', 'buildings.admin_id');
        }

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

    public function getReorder(Request $req){
        $array = Medicine::select($req->select)
                    ->join("reorders as r", "r.medicine_id", '=', 'medicines.id')
                    ->where('r.deleted_at', null);

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
        $entry = new Medicine();

        if($req->hasFile('image')){
            $temp = $req->file('image');
            $image = Image::make($temp);

            $name = $req->name . '-' . time() . "." . $temp->getClientOriginalExtension();
            $destinationPath = public_path('uploads/');

            $image->resize(250, 250);
            $image->save($destinationPath . $name);
            $entry->image = 'uploads/' . $name;
        }

        $entry->user_id = $req->user_id ?? auth()->user()->id;
        $entry->category_id = $req->category_id;
        $entry->code = $req->code;
        $entry->brand = $req->brand;
        $entry->name= $req->name;
        $entry->packaging= $req->packaging;
        $entry->unit_price= $req->unit_price;
        $entry->cost_price= $req->cost_price;
        $entry->save();

        $reorder = new Reorder();
        $reorder->user_id = $entry->user_id;
        $reorder->medicine_id = $entry->id;
        $reorder->point = $req->reorder_point;
        $reorder->save();
    }

    public function storeCategory(Request $req){
        $entry = new Category();
        $entry->admin_id = auth()->user()->id;
        $entry->site_id = $req->site_id;
        $entry->name = $req->name;
        $entry->save();
    }

    public function update(Request $req){
        $query = DB::table($this->table);

        if($req->hasFile('images')){
            $temp = $req->file('images');
            $image = Image::make($temp);

            $name = $req->name . '-' . time() . "." . $temp->getClientOriginalExtension();
            $destinationPath = public_path('uploads/');

            $image->resize(250, 250);
            $image->save($destinationPath . $name);
            $req->merge(['image' => 'uploads/' . $name]);
        }

        if($req->where){
            $query = $query->where($req->where[0], $req->where[1])->update($req->except(['id', '_token', 'where', 'images']));
        }
        else{
            $query = $query->where('id', $req->id)->update($req->except(['id', '_token', 'images']));
        }
    }

    public function updateCategory(Request $req){
        $query = DB::table("buildings");

        if($req->where){
            $query = $query->where($req->where[0], $req->where[1])->update($req->except(['id', '_token', 'where']));
        }
        else{
            $query = $query->where('id', $req->id)->update($req->except(['id', '_token']));
        }
    }

    public function updateReorder(Request $req){
        $query = DB::table("reorders");

        $query = $query->where($req->where[0], $req->where[1]);
        $query = $query->where($req->where[2], $req->where[3])->update($req->except(['id', '_token', 'where']));
    }

    public function delete(Request $req){
        Medicine::find($req->id)->delete();
    }

    public function deleteReorder(Request $req){
        Reorder::where("medicine_id", $req->id)->delete();
    }

    public function deleteCategory(Request $req){
        Category::find($req->id)->delete();
    }

    public function assign(Request $req){
        if($req->ids){
            // DELETE REORDERS NOT SELECTED NOW
            Reorder::where('user_id', $req->id)->whereNotIn('medicine_id', $req->ids)->delete();
            // RESTORE DELETED THAT ARE SELECTED
            Reorder::where('user_id', $req->id)->whereIn('medicine_id', $req->ids)->whereNotNull('deleted_at')->restore();
            // CREATE REORDER FOR EACH MEDICINE_ID FOR USER THAT IS NOT YET CREATED
            $reorders = Reorder::where('user_id', $req->id)->whereIn('medicine_id', $req->ids)->pluck('medicine_id');
            foreach($req->ids as $id){
                if(!in_array($id, $reorders->toArray())){
                    $reorder = new Reorder();
                    $reorder->user_id = $req->id;
                    $reorder->medicine_id = $id;
                    $reorder->point = 0;
                    $reorder->save();
                }
            }
        }
        else{
            // DELETE  ALL
            Reorder::where('user_id', $req->id)->delete();
        }

    }

    private function _view($view, $data = array()){
        return view($this->table . "." . $view, $data);
    }
}