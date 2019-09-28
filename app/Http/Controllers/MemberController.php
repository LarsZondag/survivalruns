<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Member;

class MemberController extends Controller
{
    public function replace_all(Request $request) {
        $request->validate([
            "new_members" => "string|required",
        ]);
        
        $new_members = preg_split('/\r\n|\r|\n/', $request->new_members);
        $new_members = array_map(function($string) {
            [$first_name, $last_name] = preg_split("/[\t]/", $string);
            return new Member([
                "first_name" => $first_name,
                "last_name" => $last_name
            ]);
        }, $new_members);
        $new_members = collect($new_members);
        $current_members = Member::all();
        dd($current_members->pluck(['first_name', 'last_name']));
        $diff = $current_members->diff($new_members);
        dd($diff);
        // dd($new_members);
        dd($new_members->diff($current_members));

    }
}
