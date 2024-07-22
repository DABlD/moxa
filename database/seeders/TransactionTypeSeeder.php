<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionType;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            ["Power", "kw", 0, 1],
            ["Water", "cfs", 0, 1],
            ["Air", "psi", 0, 1],
            ["Gas", "m3", 0, 1]
        ];

        for($i = 1; $i <= 2; $i++){
            foreach($array as $type){
                $this->addTransactionType($type[0], $type[1], $type[2], $type[3], $i);
            }
        }
    }

    public function addTransactionType($type, $operator, $inDashboard, $canDelete, $admin_id){
        $tType = new TransactionType();
        $tType->admin_id = $admin_id;
        $tType->type = $type;
        $tType->operator = $operator;
        $tType->inDashboard = $inDashboard;
        $tType->canDelete = $canDelete;
        $tType->rate = rand(1,3);
        $tType->save();
    }
}