<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $location = ["Pasay", "Manila", "Cavite"];

        foreach($location as $key => $loc){
            $this->create($key, $loc);
        }
    }

    private function create($i, $location){
        $data = new Site();
        $data->admin_id = "2";
        $data->name = "Site $i";
        $data->site_location = $location;
        $data->save();
    }
}
