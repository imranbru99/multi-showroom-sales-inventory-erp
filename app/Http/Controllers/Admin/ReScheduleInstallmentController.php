<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Installment;
use App\InstallmentSchedule;
use Illuminate\Http\Request;
use App\CustomerRegistrationSetup;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReScheduleInstallmentController extends Controller {

    public function index(Request $request) {
        $title = "ReSchedule Installment";
        $customers = CustomerRegistrationSetup::where('company_id', $this->company)
                ->where('showroom_id', $this->showroomId)
                ->get();

        $customer = $request->customer;
        $customerID = $request->customer;

        $data = [];

        if ($request->searched) {
            $data['installments'] = Installment::where('customer_id', $customer)->with(['schedule'])->get();
            $data['customer'] = CustomerRegistrationSetup::findOrFail($customer);

            // dd($data);
        }

        return view('admin.ReScheduleInstallment.index', compact('title', 'customers', 'data', 'customerID'));
    }

    public function UpDateSingle(Request $request) {

        InstallmentSchedule::where('id', $request->id)->update([
            'installment_schedule_date' => date('Y-m-d', strtotime($request->date)),
            'installment_schedule_amount' => $request->amount,
        ]);


        // return true;
    }

    public function rescheduleAuto(Request $request) {

        // divide total with partial amount
        $totalAmount = $request->totalAmount;
        $partial = $request->amount;

        $iterator = $totalAmount / $partial;

        // generate dates

        $startDate = new Carbon($request->start_date);

        $dates = [];

        $dates[] = [
            'date' => $startDate->format('d-m-Y'),
            'day' => $startDate->format('l'),
        ];

        // for ($i = 1; $i < $iterator; $i++) {
        //     $a = $startDate->addDays($request->type);
        //     array_push($dates, [
        //         'date' => $a->format('d-m-Y'),
        //         'day' => $a->format('l'),
        //     ]);
        // }

        $i = 1;

        while ($i < $iterator) {

            $a = $startDate->addDays($request->type);

            if ($request->type == 1) {
                if ($a->format('l') == 'Friday') {
                    continue;
                }
            }

            array_push($dates, [
                'date' => $a->format('d-m-Y'),
                'day' => $a->format('l'),
            ]);

            $i++;
        }


        $data = [];

        foreach ($dates as $date) {
            $data[] = [
                'date' => $date,
                'amount' => $partial,
            ];
        }

        return $data;
    }

    public function rescheduleAutoSave(Request $request) {
        // dd($request);
        // get Schedules
        $customerInstallments = Installment::where('customer_id', $request->customerId)->select(['id', 'invoice_no'])->get();

        $customerInstallmentsids = $customerInstallments->pluck('id')->toArray();


        // delete Schedules

        InstallmentSchedule::whereIn('installment_id', $customerInstallmentsids)->delete();

        // add new Schedules

        foreach ($request->newDate as $key => $date) {

            InstallmentSchedule::create([
                'company_id' => $this->company,
                'showroom_id' => $this->showroomId,
                'installment_id' => $customerInstallments[0]->id,
                'invoice_no' => $customerInstallments[0]->invoice_no,
                'installment_schedule_date' => date('Y-m-d', strtotime($date)),
                'installment_schedule_amount' => $request->newAmount[$key],
                'status' => 1,
                'created_by' => Auth::guard('admin')->id()
            ]);
        }

        // dd($request);

        return back();
    }

}
