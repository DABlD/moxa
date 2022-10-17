<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            [1, "Metropoint"],
            [1, "Empire Mall"],
            [2, "Green Mall"],
            [2, "Robinson Manila"],
            [3, "SM Manila"],
            [3, "Evia"]
        ];

        foreach($array as $entry){
            $this->addCategory($entry[0], $entry[1]);
        }
    }

    public function addCategory($id, $location){
        $category = new Category();
        $category->admin_id = 2;
        $category->site_id = $id;
        $category->name = $location;
        $category->save();
    }
}