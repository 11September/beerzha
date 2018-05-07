<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

//        $tables = ['dishes', 'types', 'beers'];

//        foreach ($tables as $table) {
//            DB::table($table)->truncate();
//        }

           $this->call(DishesTableSeeder::class);
           $this->call(TypeTableSeeder::class);
           $this->call(BeersTableSeeder::class);
           $this->call(OrdersTableSeeder::class);
           $this->call(BeergaTableSeeder::class);
    }
}
