<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use Illuminate\Http\Request;

class ForecastController extends Controller
{
    /**
     * Show the price forecasting dashboard.
     */
    public function index()
    {
        $crops = Crop::all();
        return view('forecast.index', compact('crops'));
    }
}
