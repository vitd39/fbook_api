<?php

use App\Eloquent\Office;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OfficesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('offices')->truncate();

        app(Office::class)->insert([
            [
                'name' => 'Ha Noi Branch',
                'area' => 'Ha Noi',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Da Nang Branch',
                'area' => 'Da Nang',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Ho Chi Minh Branch',
                'area' => 'Ho Chi Minh',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
