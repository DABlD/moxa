<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reading;

class ReadingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 1; $i <= 6; $i++){
            $total = rand(10,20);
            for($j = 0; $j < 336; $j++){
                $temp = rand(10,20);
                $total += $temp;
                $this->create($i, $j, $total);
            }
        }
    }

    private function create($i, $j, $total){
        $datetime = now()->sub(15,'days');

        $data = new Reading();
        $data->moxa_id = $i;
        $data->total = $total;
        $data->datetime = now()->sub(15, 'days')->add($j * 60, 'minutes');
        $data->save();
    }
}
