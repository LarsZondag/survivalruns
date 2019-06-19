<?php

namespace App\Http\Controllers;

use App\Run;
use Illuminate\Http\Request;

class RunsYearController extends Controller
{

    public function index(Request $request)
    {
        $runs = Run::where('year', $request->year)->orderBy('date', 'asc')->get();

        return view('runs_year', ['runs' => $runs]);
    }
}
