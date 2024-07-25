<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Device, Reading};

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
        $data = new Device();
        $data->category_id = $j == 1 ? 5 : 6;
        $data->user_id = 2;
        $data->serial = "D" . $k . "S";
        $data->name = $i + 5;
        $data->location = "D" . $k . "L";
        $data->floor = "D" . $k . "F";
        $data->utility = $j == 1 ? "Power" : "Water";
        $data->save();

        $data2 = new Reading();
        $data2->moxa_id = $data->id;
        $data2->total = 0;
        $data2->datetime = now()->subDays(16);
        $data2->save();
    }
}