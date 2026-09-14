<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\StaffLoan;
use App\StaffLoanSchedule;
use App\StaffSetup;
use Carbon\Carbon;
use PDF;
use MPDF;

class StaffLoanController extends Controller
{
    public function index()
    {
        $title = "Staff Loan/Advance";

        $loans = StaffLoan::where('showroom_id', @$this->showroomId)->orderBy('id', 'desc')->get();

        return view('admin.staffLoan.index')->with(compact('title', 'loans'));
    }

    public function add()
    {
        $title = "Add Staff Loan/Advance";
        $formLink = "staffLoan.save";
        $buttonName = "Save";

        $staffs = StaffSetup::where('showroom_id', @$this->showroomId)->orderBy('name', 'asc')->get();

        return view('admin.staffLoan.add')->with(compact('title', 'formLink', 'buttonName', 'staffs'));
    }

    public function save(Request $request)
    {

        $loan = StaffLoan::create([
            'showroom_id' => $this->showroomId,
            'staff_id' => $request->staff_id,
            'date' => date('Y-m-d', strtotime($request->date)),
            'type' => $request->type,
            'total_installment' => $request->total_installment,
            'amount' => $request->amount,
            'created_by' => auth()->user()->id,
        ]);


        if ($request->schedule_date) {
            $countSchedule = count($request->schedule_date);
            $postData = [];
            for ($i = 0; $i < $countSchedule; $i++) {
                $postData[] = [
                    'showroom_id' => $this->showroomId,
                    'loan_id' => $loan->id,
                    'date' => date('Y-m', strtotime('01-' . $request->schedule_date[$i])),
                    'amount' => $request->schedule_amount[$i],
                ];
            }
            StaffLoanSchedule::insert($postData);
        }

        return redirect(route('staffLoan.index'))->with('msg', 'Staff Loan Added Successfully');
    }

    public function edit($id)
    {
        $title = "Edit Staff";
        $formLink = "staffLoan.update";
        $buttonName = "Update";

        $staffs = StaffSetup::where('showroom_id', @$this->showroomId)->orderBy('name', 'asc')->get();



        $loan = StaffLoan::with('schedules')->where('id', $id)->first();

        return view('admin.staffLoan.edit')->with(compact('title', 'formLink', 'buttonName', 'staffs', 'loan'));
    }

    public function update(Request $request)
    {

        $loan = StaffLoan::find($request->id);

        $loan->update([
            'staff_id' => $request->staff_id,
            'date' => date('Y-m-d', strtotime($request->date)),
            'type' => $request->type,
            'total_installment' => $request->total_installment,
            'amount' => $request->amount,
            'created_by' => auth()->user()->id,
        ]);

        StaffLoanSchedule::where('loan_id', $loan->id)->delete();

        if ($request->schedule_date) {
            $countSchedule = count($request->schedule_date);
            $postData = [];
            for ($i = 0; $i < $countSchedule; $i++) {
                $postData[] = [
                    'showroom_id' => $this->showroomId,
                    'loan_id' => $loan->id,
                    'date' => date('Y-m', strtotime('01-' . $request->schedule_date[$i])),
                    'amount' => $request->schedule_amount[$i],
                ];
            }
            StaffLoanSchedule::insert($postData);
        }


        return redirect(route('staffLoan.index'))->with('msg', 'Staff Loan Updated Successfully');
    }

    public function delete(Request $request)
    {
        StaffLoanSchedule::where('loan_id', $request->id)->delete();
        StaffLoan::where('id', $request->id)->delete();
    }


    public function schedule(Request $request)
    {

        $date = $request->date;
        $qty = $request->qty;
        $amount = $request->amount;
        $type = $request->type;
        $iamount = round($amount / $request->qty);

        $schedules = [];
        if ($type == 'Loan') {
            for ($i = 0; $i < $qty; $i++) {
                $startDate = new Carbon($date);
                $startDate->addMonths($i);


                $schedules[] = [
                    'date' => date('m-Y', strtotime($startDate)),
                    'amount' => number_format($iamount, 2, '.', '')
                ];
            }
        } else {
            $schedules[] = [
                'date' => date('m-Y', strtotime($date)),
                'amount' => number_format($amount, 2, '.', '')
            ];
        }

        return $schedules;
    }
}
