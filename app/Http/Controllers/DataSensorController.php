<?php

namespace App\Http\Controllers;

use App\Models\DataSensor;
use App\Http\Requests\StoreDataSensorRequest;
use App\Http\Requests\UpdateDataSensorRequest;

use Carbon\Carbon;
use Illuminate\Http\Request;

class DataSensorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('chart');
    }

    public function getData(Request $request)
    {
        $filter = $request->input('filter', '12h'); // Default 12 jam terakhir

        // Query berdasarkan filter
        $query = DataSensor::orderBy('created_at', 'asc');

        if ($filter === '6h') {
            $query->where('created_at', '>=', Carbon::now()->subHours(6));
        } elseif ($filter === '12h') {
            $query->where('created_at', '>=', Carbon::now()->subHours(12));
        } elseif ($filter === '1month') {
            $query->where('created_at', '>=', Carbon::now()->subMonth());
        } elseif ($filter === 'date' && $request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $data = $query->get();

        return response()->json([
            'labels' => $data->pluck('created_at')->map(fn($date) => $date->format('H:i')),
            'suhu' => $data->pluck('suhu'),
            'kelembapan' => $data->pluck('kelembapan'),
            'kelembapanTanah' => $data->pluck('kelembapanTanah'),
        ]);
    }

    public function getSensorData(Request $request) {
        $query = DataSensor::query();
    
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }
    
        if ($request->hours && $request->hours !== 'all') {
            $query->where('created_at', '>=', Carbon::now()->subHours($request->hours));
        }
    
        return response()->json(['data' => $query->orderBy('created_at', 'desc')->get()]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDataSensorRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDataSensorRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DataSensor  $dataSensor
     * @return \Illuminate\Http\Response
     */
    public function show(DataSensor $dataSensor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DataSensor  $dataSensor
     * @return \Illuminate\Http\Response
     */
    public function edit(DataSensor $dataSensor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDataSensorRequest  $request
     * @param  \App\Models\DataSensor  $dataSensor
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDataSensorRequest $request, DataSensor $dataSensor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DataSensor  $dataSensor
     * @return \Illuminate\Http\Response
     */
    public function destroy(DataSensor $dataSensor)
    {
        //
    }
}
