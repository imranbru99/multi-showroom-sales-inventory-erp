<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ServiceProductReceive;
use App\ServiceProductReceiveRemarks;
use App\Product;
use App\CustomerRegistrationSetup;
use App\StaffSetup;
use App\ServiceAllocation;
use App\ServiceSpareAllocation;
use App\LiftingProduct;
use DB;
use PDF;

class ServiceAllocationController extends Controller {

    public function index() {
        $title = "Service Allocation";

        $invoiceNo = $this->getInvoiceNo();

        $serviceAllocation = ServiceProductReceive::with('dealer', 'product', 'remarks')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('id', 'desc')
                ->get();

        $staffs = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', 1)
                ->get();

        return view('admin.serviceAllocation.index')->with(compact('title', 'serviceAllocation', 'staffs', 'invoiceNo'));
    }

    public function save(Request $request) {

        $allocation = ServiceAllocation::create([
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'service_receive_id' => $request->service_receive_id,
                    'issue_date' => date('Y-m-d', strtotime($request->issue_date)),
                    'invoice_no' => $request->invoice_no,
                    'product_id' => $request->product_id,
                    'product_model' => $request->product_model,
                    'product_serial' => $request->product_serial,
                    'qty' => $request->qty,
                    'employee_id' => $request->employee_id,
                    'created_by' => $this->userId,
                    'status' => 'Initial'
        ]);

        if (!empty($request->remarks)) {
            ServiceProductReceiveRemarks::create([
                'showroom_id' => $this->showroomId,
                'invoice_no' => $allocation->invoice_no,
                'job_allocation_id' => $allocation->id,
                'remarks' => $request->remarks,
                'created_by' => $this->userId
            ]);
        }


        return redirect()->back()->with('msg', 'Engineer Assigned Successfully');
    }

    public function runningAllocation() {
        $title = "Service Running Allocation";

        $products = Product::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('pdtType_status', 1)
                ->get();

        $initials = ServiceAllocation::with('serviceProduct', 'product', 'staff', 'remarks')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', 'Initial')
                ->get();

        $stockOutProducts = DB::table('view_out_of_stock')
                ->select('productId', 'tbl_products.name', 'tbl_products.model_no')
                ->leftjoin('tbl_products', 'tbl_products.id', '=', 'view_out_of_stock.productId')
                ->where('showroomId', $this->showroomId)
                ->where('pdtType_status', 1)
                ->groupBy('categoryId', 'productId')
                ->orderBy('productName', 'asc')
                ->get();

//        dd($liftingProducts);

        return view('admin.serviceAllocation.runningAllocation')->with(compact('title', 'initials', 'products'));
    }

    public function runningAllocationsave(Request $request) {
        $product = ServiceAllocation::where('id', $request->id)->first();

        $product->update([
            'finish_date' => $request->finish_date,
            'status' => 'Complete'
        ]);

        if (!empty($request->remarks)) {
            ServiceProductReceiveRemarks::create([
                'showroom_id' => $this->showroomId,
                'invoice_no' => $product->invoice_no,
                'job_allocation_id' => $product->id,
                'remarks' => $request->remarks,
                'created_by' => $this->userId
            ]);
        }

        $countProduct = count($request->spare_products);
        if ($request->spare_products) {
            $postData = [];
            for ($i = 0; $i < $countProduct; $i++) {
                $productModel = Product::where('id', $request->product_id)->first();
                $productLifting = LiftingProduct::where('serial_no', $request->spare_serial_no[$i])->first();
                $postData[] = [
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'service_allocation_id' => $product->id,
                    'invoice_no' => $product->invoice_no,
                    'product_id' => $request->spare_products[$i],
                    'model_no' => $productModel->model_no,
                    'serial_no' => $request->spare_serial_no[$i],
                    'qty' => 1,
                    'sale_price' => $request->spare_price[$i],
                    'created_by' => $this->userId
                ];
            }
            ServiceSpareAllocation::insert($postData);
        }
        return redirect()->back()->with('msg', 'Status Updated Successfully');
    }

    public function completeAllocation() {
        $title = "Service Complete Allocation";

        $completes = ServiceAllocation::with('serviceProduct', 'product', 'staff', 'spareProducts', 'spareProducts.product')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', 'Complete')
                ->get();

        return view('admin.serviceAllocation.completeAllocation')->with(compact('title', 'completes'));
    }

    public function completeAllocationPrint() {
        $title = "Service Complete Allocation";

        $completes = ServiceAllocation::with('serviceProduct', 'product', 'staff', 'spareProducts', 'spareProducts.product')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', 'Complete')
                ->get();

        $pdf = PDF::loadView('admin.serviceAllocation.completeAllocationPrint', ['title' => $title, 'completes' => $completes], [], ['format' => 'A4', 'orientation' => 'L']);
        return $pdf->stream('complete_allocation.pdf');
    }

    public function getSerialNo(Request $request) {


        $usedSpares = ServiceSpareAllocation::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->select('serial_no')
                ->get()
                ->pluck('serial_no')
                ->toArray();

        $liftingProducts = LiftingProduct::select('serial_no')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('product_id', $request->product_id)
                ->whereNotIn('serial_no', $usedSpares)
                ->get();

        if ($request->ajax()) {
            return response()->json([
                        'serial_nos' => $liftingProducts,
            ]);
        }
    }

    public static function getInvoiceNo() {
        $maxId = ServiceAllocation::max('id');
        $invoiceNoPrefix = date('ymd') . '12';
        // $invoiceNoPrefix = "inv-" . date('ymd') . "-";

        if ($maxId) {
            $invoiceNo = $invoiceNoPrefix . str_pad($maxId + 1, 0, '0', STR_PAD_LEFT);
        } else {
            $invoiceNo = $invoiceNoPrefix . "1";
        }

        return $invoiceNo;
    }

    public static function getRunningInvoiceNo() {
        $maxId = ServiceAllocation::max('id');
        $invoiceNoPrefix = date('ymd') . '15';
        // $invoiceNoPrefix = "inv-" . date('ymd') . "-";

        if ($maxId) {
            $invoiceNo = $invoiceNoPrefix . str_pad($maxId + 1, 0, '0', STR_PAD_LEFT);
        } else {
            $invoiceNo = $invoiceNoPrefix . "1";
        }

        return $invoiceNo;
    }

}
