<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Moxa;

class MoxaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $k = 1;
        for($i = 1; $i <= 3; $i++){
            for($j = 1; $j <= 2; $j++, $k++){
                $this->create($i, $j, $k);
            }
        }
    }

    public function create($i, $j, $k){
        $data = new Moxa();
        $data->category_id = $i;
        $data->user_id = 2;
        $data->serial = "D" . $k . "S";
        $data->name = "D" . $k . "N";
        $data->location = "D" . $k . "L";
        $data->floor = "D" . $k . "F";
        $data->utility = $j == 1 ? "Power" : "Water";
        $data->save();
    }
}