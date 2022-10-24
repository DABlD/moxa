<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, Site};

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

        for($i = 1; $i <= 3; $i++){
            $user = new User();
            $user->name = "Site $i";

            $user->username = "site$i";
            $user->password = "123456";
            $user->admin_id = "2";
            $user->role = "RHU";

            $user->save();
            $user->login_link = "?u=" . $user->id;
            $user->save();
        }

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
