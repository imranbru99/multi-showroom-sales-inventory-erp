<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ServiceProductReceive;
use App\ServiceProductReceiveRemarks;
use App\Product;
use App\DealerSetup;
use App\ProductIssueList;

class ServiceProductReceiveController extends Controller {

    public function index() {
        $title = "Service Product Receive";

        $serviceProductReceives = ServiceProductReceive::with('dealer', 'product', 'remarks')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('id', 'desc')
                ->get();


        return view('admin.serviceProductReceive.index')->with(compact('title', 'serviceProductReceives'));
    }

    public function add() {
        $title = "Add Service Product";
        $formLink = "serviceProductReceive.save";
        $buttonName = "Save";


        $invoiceNo = $this->getInvoiceNo();

        $dealers = DealerSetup::where('status', '1')->get();

        return view('admin.serviceProductReceive.add')->with(compact('title', 'formLink', 'buttonName', 'invoiceNo', 'dealers'));
    }

    public function productInfo(Request $request) {
        $saleInfo = ProductIssueList::with('issue', 'issue.dealer', 'product', 'lifting.lifting.vendor')
                ->where('serial_no', $request->serialNo)
                ->first();

        $info = [
            'serialNo' => $saleInfo->serial_no,
            'productId' => $saleInfo->product_id,
            'productName' => @$saleInfo->product->name,
            'modelNo' => @$saleInfo->product->model_no,
            'dealerId' => @$saleInfo->issue->dealer_id,
            'dealerName' => @$saleInfo->issue->dealer->name,
            'liftingVendor' => @$saleInfo->lifting->lifting->vendor->name,
        ];

        return $info;
    }

    public function save(Request $request) {
        if ($request->sale_date) {
            $saleDate = date('Y-m-d', strtotime($request->sale_date));
        } else {
            $saleDate = '';
        }

        $serviceReceive = ServiceProductReceive::create([
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'invoice_no' => $request->invoice_no,
                    'receive_date' => date('Y-m-d', strtotime($request->date)),
                    'product_id' => $request->product_id,
                    'product_model' => $request->model_no,
                    'product_serial' => $request->product_serial,
                    'dealer_id' => $request->dealer_id,
                    'sale_date' => $saleDate,
                    'type' => $request->type,
                    'cn_no' => $request->cn_no,
                    'problem_list' => $request->problem_list,
                    'product_condition' => $request->problem_list,
                    'qty' => $request->qty,
                    'created_by' => $this->userId
        ]);

        if ($request->remarks) {
            ServiceProductReceiveRemarks::create([
                'showroom_id' => $this->showroomId,
                'invoice_no' => $serviceReceive->invoice_no,
                'service_receive_id' => $serviceReceive->id,
                'remarks' => $request->remarks,
                'created_by' => $this->userId
            ]);
        }

        return redirect(route('serviceProductReceive.index'))->with('msg', 'Service Product Receive Added Successfully');
    }

    public function edit($id) {
        $title = "Edit Service Product";
        $formLink = "serviceProductReceive.update";
        $buttonName = "Update";


        $serviceProductReceive = ServiceProductReceive::with('product', 'dealer', 'lifting.lifting.vendor', 'remarks')
                ->where('showroom_id', $this->showroomId)
                ->where('id', $id)
                ->first();

        return view('admin.serviceProductReceive.edit')->with(compact('title', 'formLink', 'buttonName', 'serviceProductReceive'));
    }

    public function update(Request $request) {

        $serviceReceive = ServiceProductReceive::find($request->id);
        $product = Product::where('id', $request->product_id)->first();
        $serviceReceive->update([
            'invoice_no' => $request->invoice_no,
            'receive_date' => date('Y-m-d', strtotime($request->date)),
            'product_id' => $product->id,
            'product_model' => $product->model_no,
            'product_serial' => $request->product_serial,
            'dealer_id' => $request->dealer_id,
            'problem_list' => $request->problem_list,
            'product_condition' => $request->problem_list,
            'qty' => $request->qty,
            'created_by' => $this->userId
        ]);

        $serviceReceiveRemarks = ServiceProductReceiveRemarks::where('service_receive_id', $request->id)->first();

        if ($request->remarks) {
            $serviceReceiveRemarks->update([
                'showroom_id' => $this->showroomId,
                'invoice_no' => $serviceReceive->invoice_no,
                'service_receive_id' => $serviceReceive->id,
                'remarks' => $request->remarks,
                'created_by' => $this->userId
            ]);
        }

        return redirect(route('serviceProductReceive.index'))->with('msg', 'Service Product Receive Updated Successfully');
    }

    public function delete(Request $request) {
        ServiceProductReceiveRemarks::where('service_receive_id', $request->serviceId)->delete();
        ServiceProductReceive::where('id', $request->serviceId)->delete();
    }

    public static function getInvoiceNo() {
        $maxId = ServiceProductReceive::max('id');
        $invoiceNoPrefix = date('ymd');
        // $invoiceNoPrefix = "inv-" . date('ymd') . "-";

        if ($maxId) {
            $invoiceNo = $invoiceNoPrefix . str_pad($maxId + 1, 0, '0', STR_PAD_LEFT);
        } else {
            $invoiceNo = $invoiceNoPrefix . "1";
        }

        return $invoiceNo;
    }

}
