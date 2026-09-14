<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Admin;
use App\ShowroomSetup;
use DB;
use PDF;
use MPDF;

class UserListController extends Controller {

    public function index(Request $request) {
        $title = "User List";
        $searchFormLink = "userList.index";
        $printFormLink = "userList.print";

        $branch = $request->branch;
        $print = $request->print;


        $branches = ShowroomSetup::where('company_id', $this->company)->get();

        if ($branch) {
            $id = [];
            $count = count($branch);
            for ($i = 0; $i < $count; $i++) {
                $u = Admin::select('id')
                        ->where('company_id', $this->company)
                        ->whereRaw('FIND_IN_SET(showroomId, ' . $branch[$i] . ')')
                        ->get();

                foreach ($u as $user) {
                    $id[] = $user->id;
                }
            }

            $users = Admin::where('company_id', $this->company)
                    ->whereIn('id', $id)
                    ->orderBy('name', 'asc')
                    ->get();
        } else {
            $users = Admin::where('company_id', $this->company)->orderBy('name', 'asc')->get();
        }

        return view('admin.userList.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'branches', 'branch', 'print', 'users'));
    }

    public function print(Request $request) {
        $title = "Staff List";

        $branch = $request->branch;
        $print = $request->print;

        if ($branch) {
            $id = [];
            $count = count($branch);
            for ($i = 0; $i < $count; $i++) {
                $u = Admin::select('id')
                        ->where('company_id', $this->company)
                        ->whereRaw('FIND_IN_SET(showroomId, ' . $branch[$i] . ')')
                        ->get();

                foreach ($u as $user) {
                    $id[] = $user->id;
                }
            }

            $users = Admin::where('company_id', $this->company)
                    ->whereIn('id', $id)
                    ->orderBy('name', 'asc')
                    ->get();
        } else {
            $users = Admin::where('company_id', $this->company)->orderBy('name', 'asc')->get();
        }

        $pdf = PDF::loadView('admin.userList.print', compact('title', 'users'));

        return $pdf->stream('user_lists.pdf');
    }

}
