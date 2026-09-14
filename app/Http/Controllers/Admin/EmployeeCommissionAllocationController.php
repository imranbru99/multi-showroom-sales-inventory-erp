<?php

namespace App\Http\Controllers\Admin;

use App\EmployeeCommissionAllocation;
use App\EmployeeCommissionAllocationList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\StaffSetup;

class EmployeeCommissionAllocationController extends Controller
{
    public function index()
    {
        $title = "Dealer & Retail";

        $commissions = EmployeeCommissionAllocation::get();


        return view('admin.employeeCommissionAllocation.index')->with(compact('title', 'commissions'));
    }


    public function create()
    {

        $title = "Add Dealer & Retail";

        $formLink = "employeecommissionallocation.store";
        $buttonName = "Save";

        $staffs = StaffSetup::get();


        return view('admin.employeeCommissionAllocation.add')->with(compact('title', 'buttonName', 'formLink', 'staffs'));
    }

    public function staffInfo(Request $request)
    {
        $staff = StaffSetup::find($request->staff);

        return $staff;
    }


    public function store(Request $request)
    {
        $this->validate(request(), [
            // 'month' => 'required|unique:employee_commission_allocations',
        ]);

        $allocation = EmployeeCommissionAllocation::create([
            'showroom_id' => $this->showroomId,
            'month' => $request->month
        ]);


        if (!empty($request->staff) && count($request->staff) > 0) {
            $count = count($request->staff);
            for ($i = 0; $i < $count; $i++) {
                EmployeeCommissionAllocationList::create(
                    [
                        'allocation_id' => $allocation->id,
                        'staff_id' => $request->staff[$i],
                        'showroom_id' => $this->showroomId,
                        // 'dealer_collection_target' => $request->dealer_collection_target[$i],
                        'dealer_collection' => $request->dealer_collection[$i],
                        // 'retail_cash_collection_target' => $request->retail_cash_collection_target[$i],
                        'retail_cash_collection' => $request->retail_cash_collection[$i],
                        // 'retail_hire_collection_target' => $request->retail_hire_collection_target[$i],
                        'retail_hire_collection' => $request->retail_hire_collection[$i],
                        // 'recovery_cash_collection_target' => $request->recovery_cash_collection_target[$i],
                        'recovery_cash_collection' => $request->recovery_cash_collection[$i],
                        // 'recovery_hire_collection_target' => $request->recovery_hire_collection_target[$i],
                        'recovery_hire_collection' => $request->recovery_hire_collection[$i],
                        'msp_target' => $request->msp_target[$i],
                        'msp' => $request->msp[$i],
                    ]
                );
            }
        }


        return redirect(route('employeecommissionallocation.index'))->with('msg', 'Dealer & Retail Added Successfully');
    }



    public function edit($id)
    {

        $title = "Edit Dealer & Retail";
        $commission = EmployeeCommissionAllocation::with('allocations', 'allocations.staff')->where('id', $id)->first();
        $formLink = 'employeecommissionallocation.updateThis';
        $buttonName = "Update";
        $staffs = StaffSetup::get();
        return view('admin.employeeCommissionAllocation.edit')->with(compact('title', 'buttonName', 'formLink', 'commission', 'staffs'));
    }




    public function update(Request $request)
    {
        $allocation = EmployeeCommissionAllocation::find($request->id);
        $allocation->update([
            'month' => $request->month,
            'dealer_target' => $request->dealer_target,
            'retail_target' => $request->retail_target,
            'hire_target' => $request->hire_target
        ]);


        EmployeeCommissionAllocationList::where('allocation_id', $allocation->id)->delete();

        if (!empty($request->staff) && count($request->staff) > 0) {
            $count = count($request->staff);
            for ($i = 0; $i < $count; $i++) {
                EmployeeCommissionAllocationList::create(
                    [
                        'allocation_id' => $allocation->id,
                        'staff_id' => $request->staff[$i],
                        'showroom_id' => $this->showroomId,
                        // 'dealer_collection_target' => $request->dealer_collection_target[$i],
                        'dealer_collection' => $request->dealer_collection[$i],
                        // 'retail_cash_collection_target' => $request->retail_cash_collection_target[$i],
                        'retail_cash_collection' => $request->retail_cash_collection[$i],
                        // 'retail_hire_collection_target' => $request->retail_hire_collection_target[$i],
                        'retail_hire_collection' => $request->retail_hire_collection[$i],
                        // 'recovery_cash_collection_target' => $request->recovery_cash_collection_target[$i],
                        'recovery_cash_collection' => $request->recovery_cash_collection[$i],
                        // 'recovery_hire_collection_target' => $request->recovery_hire_collection_target[$i],
                        'recovery_hire_collection' => $request->recovery_hire_collection[$i],
                        // 'msp_target' => $request->msp_target[$i],
                        'msp' => $request->msp[$i],
                    ]
                );
            }
        }


        return redirect(route('employeecommissionallocation.index'))->with('msg', 'Dealer & Retail Updated Successfully');
    }


    public function destroy(Request $request)
    {
        EmployeeCommissionAllocationList::where('allocation_id', $request->id)->delete();
        EmployeeCommissionAllocation::where('id', $request->id)->delete();
        print_r(1);
        return;
    }
}
