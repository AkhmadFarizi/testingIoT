<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sensors_data', function (Blueprint $table) {
            $table->id();
            $table->float('suhu', 5, 2); // 5 digit total, 2 digit desimal
            $table->float('kelembapan', 5, 2); // 5 digit total, 2 digit desimal
            $table->float('kelembapanTanah', 5, 2); // 5 digit total, 2 digit desimal
            $table->timestamps(); // created_at & updated_at
        });
    
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_sensors');
    }
};
