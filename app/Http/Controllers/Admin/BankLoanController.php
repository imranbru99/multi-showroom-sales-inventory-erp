<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\BankLoan;
use App\BankLoanSchedule;
use Carbon\Carbon;

class BankLoanController extends Controller
{
    public function index()
    {
        $title = "Bank Loan";

        $bankLoans = BankLoan::where('showroom_id', @$this->showroomId)->orderBy('id', 'desc')->get();

        return view('admin.bankLoan.index')->with(compact('title', 'bankLoans'));
    }

    public function add()
    {
        $title = "Add Bank Loan";
        $formLink = "bankLoan.save";
        $buttonName = "Save";

        return view('admin.bankLoan.add')->with(compact('title', 'formLink', 'buttonName'));
    }

    public function save(Request $request)
    {

        $loan =  BankLoan::create([
            'showroom_id' => $this->showroomId,
            'bank_name' => $request->bank_name,
            'loan_no' => $request->loan_no,
            'loan_date' => date('Y-m-d', strtotime($request->loan_date)),
            'loan_amount' => $request->loan_amount,
            'total_amount' => $request->total_amount,
            'interest' => $request->interest,
            'total_installment' => $request->total_installment,
            'insatllment_amount' => $request->insatllment_amount,
            'created_by' => $this->userId,
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
            BankLoanSchedule::insert($postData);
        }

        return redirect(route('bankLoan.index'))->with('msg', 'Bank Loan Added Successfully');
    }

    public function edit($id)
    {
        $title = "Edit Bank Loan";
        $formLink = "bankLoan.update";
        $buttonName = "Update";

        $bankLoan = BankLoan::with('schedules')->where('id', $id)->first();

        return view('admin.bankLoan.edit')->with(compact('title', 'formLink', 'buttonName', 'bankLoan'));
    }

    public function update(Request $request)
    {

        $bankLoan = BankLoan::find($request->id);

        $bankLoan->update([
            'showroom_id' => $this->showroomId,
            'bank_name' => $request->bank_name,
            'loan_date' => date('Y-m-d', strtotime($request->loan_date)),
            'loan_no' => $request->loan_no,
            'loan_amount' => $request->loan_amount,
            'total_amount' => $request->total_amount,
            'interest' => $request->interest,
            'total_installment' => $request->total_installment,
            'insatllment_amount' => $request->insatllment_amount,
            'created_by' => $this->userId,
        ]);

        BankLoanSchedule::where('loan_id', $bankLoan->id)->delete();

        if ($request->schedule_date) {
            $countSchedule = count($request->schedule_date);
            $postData = [];
            for ($i = 0; $i < $countSchedule; $i++) {
                $postData[] = [
                    'showroom_id' => $this->showroomId,
                    'loan_id' => $bankLoan->id,
                    'date' => date('Y-m', strtotime('01-' . $request->schedule_date[$i])),
                    'amount' => $request->schedule_amount[$i],
                ];
            }
            BankLoanSchedule::insert($postData);
        }

        return redirect(route('bankLoan.index'))->with('msg', 'Bank Loan Updated Successfully');
    }

    public function delete(Request $request)
    {
        BankLoanSchedule::where('loan_id', $request->id)->delete();
        BankLoan::where('id', $request->id)->delete();
    }

    public function schedule(Request $request)
    {

        $date = $request->date;
        $qty = $request->qty;
        $amount = $request->amount;
        $iamount = round($amount / $request->qty);

        $schedules = [];
        for ($i = 0; $i < $qty; $i++) {
            $startDate = new Carbon($date);
            $startDate->addMonths($i);
            $sl = $i + 1;

            $schedules[] = [
                'sl' => $sl,
                'date' => date('m-Y', strtotime($startDate)),
                'amount' => number_format($iamount, 2, '.', '')
            ];
        }

        return $schedules;
    }
}
