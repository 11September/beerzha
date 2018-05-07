<?php

use Illuminate\Database\Seeder;

class BeergaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setting = \App\Beerga::firstOrNew(['key' => 'code']);
        if (!$setting->exists) {
            $setting->fill([
                'key'          => 'code',
                'display_name' => 'Code of the day',
                'details'      => '',
                'value'        => 654,
            ])->save();
        }

        $setting = \App\Beerga::firstOrNew(['key' => 'tables']);
        if (!$setting->exists) {
            $setting->fill([
                'key'          => 'tables',
                'display_name' => 'Tables in restaurant',
                'details'      => '',
                'value'        => 50,
            ])->save();
        }

        $setting = \App\Beerga::firstOrNew(['key' => 'ticker']);
        if (!$setting->exists) {
            $setting->fill([
                'key'          => 'ticker',
                'display_name' => 'This is line for text bottom on the site',
                'details'      => '',
                'value'        => "Hello, we are beerga",
            ])->save();
        }
    }
}
