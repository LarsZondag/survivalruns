<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Member;

class AdminController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $members = Member::all();
        return view('admin', ["members" => $members]);
    }
}
