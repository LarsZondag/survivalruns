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
        $diff = $current_members->diffByKeys(['first_name', 'last_name'], $new_members);
        $diff->each(function($object) {
            $object->delete();
        });
        $new_members_to_save = $new_members->diffByKeys(['first_name', 'last_name'], Member::all());
        $new_members_to_save->each(function($model) {
            $model->save();
        });
        return redirect('admin');
    }
}
