<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RunsYearController extends Controller
{

    public function index(Request $request)
    {
        echo 'hello' . $request->year;
    }
}
