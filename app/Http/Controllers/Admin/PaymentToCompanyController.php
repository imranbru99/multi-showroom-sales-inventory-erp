<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VendorSetup;
use App\PaymentToCompany;
use App\Lifting;

class PaymentToCompanyController extends Controller {

    public function index() {
        $title = "Vendor Payment";


        if ($this->userRole == 1) {
            $paymentToCompany = PaymentToCompany::select('tbl_payment_to_company.*', 'tbl_vendors.name as vendorName')
                    ->join('tbl_vendors', 'tbl_vendors.id', '=', 'tbl_payment_to_company.vendor_id')
                    ->orderBy('tbl_vendors.name', 'asc')
                    ->orderBY('tbl_payment_to_company.payment_date', 'asc')
                    ->get();
        } else {
            $paymentToCompany = PaymentToCompany::select('tbl_payment_to_company.*', 'tbl_vendors.name as vendorName')
                    ->join('tbl_vendors', 'tbl_vendors.id', '=', 'tbl_payment_to_company.vendor_id')
                    ->where('tbl_payment_to_company.company_id', $this->company)
//                    ->where('tbl_payment_to_company.showroom_id', $this->showroomId)
                    ->orderBy('tbl_vendors.name', 'asc')
                    ->orderBY('tbl_payment_to_company.payment_date', 'asc')
                    ->get();
        }

        return view('admin.paymentToCompany.index')->with(compact('title', 'paymentToCompany'));
    }

    public function add() {
        $title = "Add Payment To Company";
        $formLink = "paymentToCompany.save";
        $buttonName = "Save";

        $vendors = VendorSetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        return view('admin.paymentToCompany.add')->with(compact('title', 'formLink', 'buttonName', 'vendors'));
    }

    public function save(Request $request) {
        // dd($request->all());

        $paymentDate = date('Y-m-d', strtotime($request->paymentDate));

        $this->validate(request(), [
            'vendorId' => 'required',
            'paymentType' => 'required',
        ]);

        $paymentToCompany = PaymentToCompany::create([
                    'company_id' => $this->company,
                    'vendor_id' => $request->vendorId,
                    'showroom_id' => $this->showroomId,
                    'payment_no' => $request->paymentNo,
                    'payment_date' => $paymentDate,
                    'current_due' => $request->currentDue,
                    'payment_now' => $request->paymentNow,
                    'balance' => $request->balance,
                    'money_receipt' => $request->moneyReceipt,
                    'payment_type' => $request->paymentType,
                    'remarks' => $request->remarks,
                    'created_by' => $this->userId
        ]);

        return redirect(route('paymentToCompany.index'))->with('msg', 'Payment To Company Complete Successfully');
    }

    public function edit($paymentToCompanyId) {
        $title = "Edit Payment To Company";
        $formLink = "paymentToCompany.update";
        $buttonName = "Update";

        $paymentToCompany = PaymentToCompany::where('id', $paymentToCompanyId)->first();

        $vendor = VendorSetup::where('id', $paymentToCompany->vendor_id)->first();


        return view('admin.paymentToCompany.edit')->with(compact('title', 'formLink', 'buttonName', 'vendor', 'paymentToCompany'));
    }

    public function update(Request $request) {
        // dd($request->all());

        $paymentDate = date('Y-m-d', strtotime($request->paymentDate));

        $this->validate(request(), [
            'vendorId' => 'required',
            'paymentType' => 'required',
        ]);

        $paymentToCompanyId = $request->paymentToCompanyId;
        $paymentToCompany = PaymentToCompany::find($paymentToCompanyId);

        $paymentToCompany->update([
            'company_id' => $this->company,
            'vendor_id' => $request->vendorId,
            'showroom_id' => $this->showroomId,
            'payment_no' => $request->paymentNo,
            'payment_date' => $paymentDate,
            'current_due' => $request->currentDue,
            'payment_now' => $request->paymentNow,
            'balance' => $request->balance,
            'money_receipt' => $request->moneyReceipt,
            'payment_type' => $request->paymentType,
            'remarks' => $request->remarks,
            'updated_by' => $this->userId
        ]);

        return redirect(route('paymentToCompany.index'))->with('msg', 'Payment To Company Updated Successfully');
    }

    public function getVendorInfo(Request $request) {
        // echo $vendorId; die();
        $lifting = Lifting::where('vendor_id', $request->vendorId)->sum('total_price');
        $liftingReturn = 0;
        $currentDue = PaymentToCompany::where('vendor_id', $request->vendorId)->sum('payment_now');
        $data = ['lifting' => $lifting, 'liftingReturn' => $liftingReturn, 'currentDue' => $currentDue];

        return $data;
    }

    public function delete(Request $request) {
        PaymentToCompany::where('id', $request->paymentToCompanyId)->delete();
    }

}
