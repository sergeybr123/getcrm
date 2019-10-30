<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('services')->insert([
            'type' => 'bot',
            'name' => 'Дополнительный авточат',
            'price' => 500,
            'quantity' => 1,
            'active' => 1,
            'created_at' => Carbon::today(),
            'updated_at' => Carbon::today(),
        ]);
        DB::table('services')->insert([
            'type' => 'bonus',
            'name' => 'Бесплатный месяц',
            'price' => 0,
            'quantity' => 30,
            'active' => 1,
            'created_at' => Carbon::today(),
            'updated_at' => Carbon::today(),
        ]);
    }
}
