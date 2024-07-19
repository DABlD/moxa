<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;

class SubscriberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 1; $i <= 3; $i++){
            $this->addSubscriber($i);
        }
    }

    public function addSubscriber($i){
        $user = new User();
        $user->username = bin2hex(random_bytes(4));
        $user->name = "SUBSCRIBER $i";
        $user->role = "Subscriber";
        $user->email = "subscriber$i@pharmacy.com";
        $user->address = "SUB$i ADDRESS";
        $user->contact = "09" . rand(100000000, 999999999);
        $user->password = "12345678";
        $user->save();
    }
}
