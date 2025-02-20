<?php

namespace Database\Seeders;

use App\Models\DataSensor;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DataSensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 1000; $i++) {
            DataSensor::create([
                'suhu' => $faker->randomFloat(2, 20, 40), // Suhu antara 20°C - 40°C
                'kelembapan' => $faker->randomFloat(2, 30, 90), // Kelembapan antara 30% - 90%
                'kelembapanTanah' => $faker->randomFloat(2, 10, 80), // Kelembapan Tanah antara 10% - 80%
                'created_at' => Carbon::now()
                    ->subDays(rand(0, 30)) // Acak dalam 30 hari terakhir
                    ->setTime(rand(0, 23), rand(0, 59), rand(0, 59)), // Waktu acak (jam:menit:detik)
                'updated_at' => Carbon::now()
                    ->subDays(rand(0, 30))
                    ->setTime(rand(0, 23), rand(0, 59), rand(0, 59))
            ]);
        }
    }
}
