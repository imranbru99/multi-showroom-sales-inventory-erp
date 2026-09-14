<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\StaffSetup;
use App\EmployeeAllowance;
use App\EmployeeAllowanceList;
use App\AllowanceSetup;

class EmployeeAllowanceController extends Controller {

    public function index() {
        $title = "Employee Allowance";

        $employeeAllowances = EmployeeAllowance::select('tbl_employee_allowances.*', 'tbl_staffs.name as employeeName')
                ->leftJoin('tbl_staffs', 'tbl_staffs.id', '=', 'tbl_employee_allowances.staff_id')
                ->where('tbl_employee_allowances.company_id', $this->company)
                ->where('tbl_employee_allowances.showroom_id', $this->showroomId)
                ->orderBy('tbl_employee_allowances.id', 'dsc')
                ->get();

        return view('admin.employeeAllowance.index')->with(compact('title', 'employeeAllowances'));
    }

    public function add() {
        $title = "Add Employee Allowance";
        $formLink = "employeeAllowance.save";
        $buttonName = "Save";

        $employies = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', @$this->showroomId)
                ->where('status', '1')
                ->orderBy('id', 'asc')
                ->get();

        $allowances = AllowanceSetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', @$this->showroomId)
                ->orderBy('id', 'asc')
                ->get();

        return view('admin.employeeAllowance.add')->with(compact('title', 'formLink', 'buttonName', 'employies', 'allowances'));
    }

    public function save(Request $request) {

        $employeeAllowance = EmployeeAllowance::create([
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'staff_id' => $request->employeeId,
                    'total_amount' => $request->totalAdditionAmount,
                    'employee_designation' => $request->employeeDesignation,
                    'created_by' => $this->userId
        ]);

        $countAllowance = count($request->allowanceAmount);
        if ($request->allowanceAmount) {
            $postData = [];
            for ($i = 0; $i < $countAllowance; $i++) {
                $postData[] = [
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'employee_allowance_id' => $employeeAllowance->id,
                    'allowance_type' => $request->allowanceId[$i],
                    'allowance_amount' => $request->allowanceAmount[$i],
                    'allowance_name' => $request->allowanceName[$i],
                    'created_by' => $this->userId
                ];
            }
            EmployeeAllowanceList::insert($postData);
        }



        return redirect(route('employeeAllowance.index'))->with('msg', 'Employee Allowance Added Successfully');
    }

    public function edit($employeeAllowanceId) {
        $title = "Edit Employee Allowance";
        $formLink = "employeeAllowance.update";
        $buttonName = "Update";

        $employies = StaffSetup::where('status', '1')->where('showroom_id', @$this->showroomId)->orderBy('id', 'asc')->get();

        $allowances = AllowanceSetup::where('status', '1')->where('showroom_id', @$this->showroomId)->orderBy('id', 'asc')->get();
        $allow = EmployeeAllowanceList::where('status', '1')->where('showroom_id', @$this->showroomId)->orderBy('id', 'asc')->get();





        $employeeAllowance = EmployeeAllowance::select('tbl_employee_allowances.*', 'tbl_staffs.name as employeeName', 'tbl_employee_allowance_lists.allowance_name')
                ->leftJoin('tbl_staffs', 'tbl_staffs.id', '=', 'tbl_employee_allowances.staff_id')
                ->leftJoin('tbl_employee_allowance_lists', 'tbl_employee_allowance_lists.employee_allowance_id', '=', 'tbl_employee_allowances.id')
                ->where('tbl_employee_allowances.id', $employeeAllowanceId)
                ->first();


        $allowancesList = EmployeeAllowanceList::select('tbl_employee_allowance_lists.*',
                        'tbl_allowances.type as all_type')
                ->leftJoin('tbl_allowances', 'tbl_allowances.id', '=', 'tbl_employee_allowance_lists.allowance_type')
                ->where('tbl_employee_allowance_lists.employee_allowance_id', $employeeAllowanceId)
                ->first();

        $dealers = EmployeeAllowanceList::select('tbl_employee_allowance_lists.*',
                                'tbl_allowances.type as all_type', 'tbl_employee_allowances.total_amount')
                        ->leftJoin('tbl_allowances', 'tbl_allowances.id', '=', 'tbl_employee_allowance_lists.allowance_type')
                        ->leftJoin('tbl_employee_allowances', 'tbl_employee_allowances.id', '=', 'tbl_employee_allowance_lists.employee_allowance_id')
                        ->where('employee_allowance_id', $employeeAllowanceId)->get();


        return view('admin.employeeAllowance.edit')->with(compact('title', 'formLink', 'buttonName', 'employies', 'allowances', 'employeeAllowance', 'allowancesList', 'dealers'));
    }

    public function update(Request $request) {
        //dd($request->all());


        $employeeAllowance = EmployeeAllowance::where('id', $request->employeeAllowanceId)->first();


        $employeeAllowance->update([
            'showroom_id' => $this->showroomId,
            'staff_id' => $request->employeeId,
            'total_amount' => $request->totalAdditionAmount,
            'employee_designation' => $request->employeeDesignation,
            'created_by' => $this->userId
        ]);

        // $employeeAllowanceList = EmployeeAllowanceList::where('employee_allowance_id',$request->employeeAllowanceId)->first();

        $employeeAllowanceList = EmployeeAllowanceList::where('employee_allowance_id', $request->employeeAllowanceId)->delete();


        $countAllowance = count($request->allowanceAmount);
        if ($request->allowanceAmount) {
            $postData = [];
            for ($i = 0; $i < $countAllowance; $i++) {
                $postData[] = [
                    'showroom_id' => $this->showroomId,
                    'employee_allowance_id' => $employeeAllowance->id,
                    'allowance_amount' => $request->allowanceAmount[$i],
                    'allowance_name' => $request->allowanceName[$i],
                    'allowance_type' => $request->allowanceId[$i],
                    'created_by' => $this->userId
                ];
            }
            EmployeeAllowanceList::insert($postData);
        }
        // $employeeAllowanceList->update([
        // 	'showroom_id' => $this->showroomId,
        //           'employee_allowance_id' => $employeeAllowance->id,
        //           'allowance_amount'=>$request->allowanceAmount[0],
        //           'allowance_name'=>$request->allowanceName[0],
        //           'allowance_type'=>$request->allowanceId[0],
        //           'created_by' => $this->userId
        // ]);



        return redirect(route('employeeAllowance.index'))->with('msg', 'Employee Allowance Updated Successfully');
    }

    // public function getEmployeeInfo(Request $request)
    // {
    //     $employee = EmployeeSetup::select('tbl_employee.*','tbl_designation.name as designationName')
    //     	->leftJoin('tbl_designation','tbl_designation.id','=','tbl_employee.designation_id')
    //     	->where('tbl_employee.id',$request->employeeId)
    //     	->first();
    //     if($request->ajax()){
    //      return response()->json([
    //             'employee'=>$employee
    //         ]);
    //     }
    // }

    public function delete(Request $request) {
        EmployeeAllowance::where('id', $request->allowanceId)->delete();
    }

    public function status(Request $request) {
        $employeeAllowance = EmployeeAllowance::find($request->allowanceId);

        if ($employeeAllowance->status == 1) {
            $employeeAllowance->update([
                'status' => 0
            ]);
        } else {
            $employeeAllowance->update([
                'status' => 1
            ]);
        }
    }

    public function getTypeInfo(Request $request) {
        $allowance = AllowanceSetup::find($request->allowanceId);

        if ($request->ajax()) {
            return response()->json([
                        'allowance' => $allowance,
            ]);
        }
    }

}
