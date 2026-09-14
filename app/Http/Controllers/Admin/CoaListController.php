<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use DB;
use PDF;

class CoaListController extends Controller
{
	public function index()
	{
		$title = "Chart OF Account List";

		$coaLists = DB::table('tbl_coa')->get();

    	return view('admin.coaList.index')->with(compact('title','coaLists'));
	}

	public function print()
	{
		$title = "Print Chart OF Account List";

		$coaLists = DB::table('tbl_coa')->get();

        $pdf = PDF::loadView('admin.coaList.print',['title'=>$title,'coaLists'=>$coaLists],[],['orientation'=>'P']);
        return $pdf->stream('coa_list.pdf');   
	}
}
