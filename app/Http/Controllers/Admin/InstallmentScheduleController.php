<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use PDF;
use App\Product;
use App\CustomerProduct;
use App\RetailSales;
use App\CustomerRegistrationSetup;
use App\CustomerRegistration;
use App\Installment;
use App\InstallmentSchedule;
use App\InvoiceSetup;
use App\RetailSale;
use App\StaffSetup;

class InstallmentScheduleController extends Controller {

    public function index() {
        $title = "Installment Schedule List";
        $installmentList = Installment::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('id', 'ASC')
                ->get();

        return view('admin.installmentSchedule.index')->with(compact('title', 'installmentList'));
    }

    public function add() {
        $title = "Prepare Installment Schedule";
        $formLink = "installmentSchedule.save";
        $buttonName = "Save";

        $collectorList = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        // customer_product_id is retail_sales_id From tbl_retail_sales (Change For Upgradation)

        $customerProducts = InvoiceSetup::select('tbl_invoice.*')
                ->leftJoin('tbl_installment', 'tbl_installment.customer_product_id', '=', 'tbl_invoice.retail_sales_id')
                ->whereNull('tbl_installment.customer_product_id')
                ->where('tbl_invoice.showroom_id', $this->showroomId)
                ->where('tbl_invoice.collection_type', '!=', 'Cash')
                ->where('tbl_invoice.status', '1')
                ->get();

        return view('admin.installmentSchedule.add')->with(compact('title', 'formLink', 'buttonName', 'customerProducts', 'collectorList'));
    }

    public function save(Request $request) {
        $getCustomerProduct = RetailSales::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('id', $request->customerProductId)
                ->first();
        $collector = StaffSetup::where('showroom_id', $this->showroomId)->where('id', $request->installmentCollectorId)->first();

        $installment = Installment::create([
                    'showroom_id' => $this->showroomId,
                    'customer_product_id' => $request->customerProductId,
                    'customer_id' => $getCustomerProduct->customer_id,
                    'product_id' => $getCustomerProduct->product_id,
                    'invoice_no' => $request->invoiceNo,
                    'installment_collector_id' => $request->installmentCollectorId,
                    'installment_collector_name' => $collector->name,
                    'customer_name' => $request->customerName,
                    'installment_price' => $request->productAmount,
                    'booking_amount' => $request->bookingAmount,
                    'installment_qty' => $request->installmentQty,
                    'installment_amount' => $request->installmentAmount,
                    'created_by' => $this->userId
        ]);

        if ($installment) {
            $countInstallmentSchedule = count($request->installmentScheduleDate);
            $postData = [];
            for ($i = 0; $i < $countInstallmentSchedule; $i++) {
                $installmentScheduleDate = date('Y-m-d', strtotime($request->installmentScheduleDate[$i]));
                $postData[] = [
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'installment_id' => $installment->id,
                    'invoice_no' => $request->invoiceNo,
                    'installment_schedule_date' => $installmentScheduleDate,
                    'installment_schedule_amount' => $request->installmentScheduleAmount[$i],
                    'created_by' => $this->userId
                ];
            }
            InstallmentSchedule::insert($postData);
        }

        return redirect(route('installmentSchedule.index'))->with('msg', 'Installment Schedule Created Successfully');
    }

    public function edit($id) {
        $title = "Edit Installment Schedule";
        $formLink = "installmentSchedule.update";
        $buttonName = "Update";
        $installment = Installment::where('id', $id)->first();
        $installmentScheduleList = InstallmentSchedule::where('installment_id', $id)->orderBy('installment_schedule_date', 'ASC')->get();
        $product = Product::where('id', $installment->product_id)->first();
        return view('admin.installmentSchedule.edit')->with(compact('title', 'formLink', 'buttonName', 'installment', 'installmentScheduleList', 'product'));
    }

    public function update(Request $request) {
        $installmentId = $request->installmentId;
        $countInstallmentSchedule = count($request->installmentScheduleDate);

        DB::table('tbl_installment_schedule')->where('installment_id', $installmentId)->delete();

        $postData = [];
        for ($i = 0; $i < $countInstallmentSchedule; $i++) {
            $installmentScheduleDate = date('Y-m-d', strtotime($request->installmentScheduleDate[$i]));
            $postData[] = [
                'showroom_id' => $this->showroomId,
                'installment_id' => $installmentId,
                'invoice_no' => $request->invoiceNo,
                'installment_schedule_date' => $installmentScheduleDate,
                'installment_schedule_amount' => $request->installmentScheduleAmount[$i],
                'updated_by' => $this->userId
            ];
        }
        InstallmentSchedule::insert($postData);

        return redirect(route('installmentSchedule.index'))->with('msg', 'Installment Schedule Created Successfully');
    }

    public function delete(Request $request) {
        Installment::where('id', $request->installmentId)->delete();
        InstallmentSchedule::where('installment_id', $request->installmentId)->delete();
    }

    public function print($id) {
        $title = "Installment Schedule Details";
        $installment = Installment::where('id', $id)->first();

        $customer = CustomerRegistrationSetup::where('showroom_id', $this->showroomId)->where('id', $installment->customer_id)->first();

        $retailSale = RetailSale::where('invoice_no', $installment->invoice_no)->with(['products.product', 'seller'])->first();

        $installmentScheduleList = InstallmentSchedule::where('installment_id', $id)->orderBy('installment_schedule_date', 'ASC')->get();

        $pdf = PDF::loadView('admin.installmentSchedule.print', ['title' => $title, 'installment' => $installment, 'installmentScheduleList' => $installmentScheduleList, 'customer' => $customer, 'retailSale' => $retailSale]);

        return $pdf->stream('installment_schedule_list.pdf');
    }

    public function getCustomerProductInfo(Request $request) {
        $customerProductId = $request->customerProductId;

        $invoice = InvoiceSetup::where('showroom_id', $this->showroomId)->where('retail_sales_id', $customerProductId)->first();
        $customerProduct = RetailSales::where('showroom_id', $this->showroomId)->where('id', $customerProductId)->first();
        $customer = CustomerRegistration::where('showroom_id', $this->showroomId)->where('id', $customerProduct->customer_id)->first();

        if ($request->ajax()) {
            return response()->json([
                        'invoice' => $invoice,
                        'customerProduct' => $customerProduct,
                        'customer' => $customer,
            ]);
        }
    }

}
