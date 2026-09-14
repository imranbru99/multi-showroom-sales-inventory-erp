<?php

namespace App\Http\Controllers\Admin;

use App\BankLoan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\BankLoanPayment;
use App\BankLoanSchedule;
use Carbon\Carbon;

class BankLoanPaymentController extends Controller
{
    public function index()
    {
        $title = "Bank Loan Payment";

        $bankLoanPayments = BankLoanPayment::where('showroom_id', @$this->showroomId)->orderBy('id', 'desc')->get();

        return view('admin.bankLoanPayment.index')->with(compact('title', 'bankLoanPayments'));
    }

    public function add()
    {
        $title = "Add Bank Loan Payment";
        $formLink = "bankLoanPayment.save";
        $buttonName = "Save";

        $loans = BankLoan::get();

        return view('admin.bankLoanPayment.add')->with(compact('title', 'formLink', 'buttonName', 'loans'));
    }

    public function save(Request $request)
    {

        if (empty($request->schedule_id) && count($request->schedule_id) <= 0) {
            return redirect(route('bankLoanPayment.add'))->with('msg', 'Select a schedule.');
        }

        $loan = BankLoan::find($request->loan_id);
        $bankLoanPayment = BankLoanPayment::create([
            'showroom_id' => $this->showroomId,
            'bank_name' => $loan->bank_name,
            'loan_id' => $loan->id,
            'loan_no' => $loan->loan_no,
            'payment_date' => date('Y-m-d', strtotime($request->payment_date)),
            'paid_amount' => $request->payment_amount,
            'created_by' => $this->userId,
        ]);


        foreach ($request->schedule_id as $schedule_id) {
            $schedule = BankLoanSchedule::find($schedule_id);

            $schedule->update([
                'payment_id' => $bankLoanPayment->id,
                'paid_amount' => $schedule->amount,
                'payment_date' => date('Y-m-d', strtotime($request->payment_date)),
            ]);
        }

        return redirect(route('bankLoanPayment.index'))->with('msg', 'Bank Loan Added Successfully');
    }

    public function edit($id)
    {
        $title = "Edit Bank Loan Payment";
        $formLink = "bankLoanPayment.update";
        $buttonName = "Update";

        $loans = BankLoan::get();

        $loanPayment = BankLoanPayment::with('payments')->where('id', $id)->first();

        return view('admin.bankLoanPayment.edit')->with(compact('title', 'formLink', 'buttonName', 'loanPayment', 'loans'));
    }

    public function update(Request $request)
    {

        $bankLoanPayment = BankLoanPayment::find($request->id);

        if (empty($request->schedule_id) && count($request->schedule_id) <= 0) {
            return redirect(route('bankLoanPayment.edit', $request->id))->with('msg', 'Select a schedule.');
        }

        $loan = BankLoan::find($request->loan_id);
        $bankLoanPayment->update([
            'showroom_id' => $this->showroomId,
            'bank_name' => $loan->bank_name,
            'loan_id' => $loan->id,
            'loan_no' => $loan->loan_no,
            'payment_date' => date('Y-m-d', strtotime($request->payment_date)),
            'paid_amount' => $request->payment_amount,
            'created_by' => $this->userId,
        ]);


        foreach ($request->schedule_id as $schedule_id) {
            $schedule = BankLoanSchedule::find($schedule_id);

            $schedule->update([
                'payment_id' => $bankLoanPayment->id,
                'paid_amount' => $schedule->amount,
                'payment_date' => date('Y-m-d', strtotime($request->payment_date)),
            ]);
        }

        return redirect(route('bankLoanPayment.index'))->with('msg', 'Bank Loan Payment Updated Successfully');
    }

    public function delete(Request $request)
    {
        $loan = BankLoanPayment::where('id', $request->id)->first();
        $schedules = BankLoan::where('payment_id', $loan)->update([
            'payment_id' => '',
            'paid_amount' => 0,
            'payment_date' => '',
        ]);
    }

    public function schedule(Request $request)
    {
        $schedules = BankLoanSchedule::where('loan_id', $request->loan)->whereNull('payment_id')->orderBy('date', 'asc')->get();

        return $schedules;
    }
}
