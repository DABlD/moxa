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
            ["Power", null, 0, 0],
            ["Water", null, 0, 0],
            ["Electricity", null, 0, 0],
            ["Air", null, 0, 0]
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
        $tType->save();
    }
}