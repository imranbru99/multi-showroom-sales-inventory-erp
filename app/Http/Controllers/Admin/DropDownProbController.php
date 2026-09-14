<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;

class DropDownProbController extends Controller
{
    public function index()
    {
        $title = "Drop Down Problem";
        $alphabets = DB::table('alphabets')
        	->orderBy('name','ASC')->get();
        return view('admin.dropDownProb.index')->with(compact('title','alphabets'));
    }
}
