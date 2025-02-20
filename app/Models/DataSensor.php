<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSensor extends Model
{
    use HasFactory;
    protected $table = 'sensors_data';
    protected $fillable = ['suhu', 'kelembapan', 'kelembapanTanah'];
}
