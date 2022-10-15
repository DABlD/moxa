<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Theme;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ["app_name", "AMR"],
            ["logo_img", 'images/images/moxa_logo1.png'],
            ["login_banner_img", "images/images/moxa_logo1.png"],
            ["login_bg_img", null],
            ["sidebar_bg_color", "#343a40"],
            ["sidebar_font_color", "#c2c7d0"],
            ["table_header_color", "#b96666"],
            ["table_header_font_color", "#ffffff"],
            ["table_group_color", "#66b966"],
            ["table_group_font_color", "#ffffff"],
        ];

        for($i = 1; $i <= 2; $i++){ 
            foreach($array as $theme){
                $this->seed($theme[0], $theme[1], $i);
            }
        }
    }

    private function seed($name, $value, $aid){
        $data = new Theme();
        $data->admin_id = $aid;
        $data->name = $name;
        $data->value = $value;
        $data->save();
    }
}
