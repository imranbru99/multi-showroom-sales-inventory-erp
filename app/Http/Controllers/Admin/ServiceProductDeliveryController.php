<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ServiceDelivery;
use App\ServiceAllocation;
use DB;
use PDF;

class ServiceProductDeliveryController extends Controller {

    public function index() {
        $title = "Service Delivery";

        $serviceDeliveries = ServiceDelivery::with('serviceAllocation', 'serviceAllocation.product', 'serviceAllocation.serviceProduct.dealer', 'spareProducts', 'staff')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('id', 'desc')
                ->get();

        return view('admin.serviceDelivery.index')->with(compact('title', 'serviceDeliveries'));
    }

    public function add() {
        $title = 'Add Service Delivey Product';

        $formLink = 'serviceDelivery.save';
        $buttonName = 'Save';

        $serviceDelivery = ServiceDelivery::select('service_allocation_id')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get()
                ->pluck('service_allocation_id')
                ->toArray();

        $completeAllocations = ServiceAllocation::where('status', 'Complete')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->whereNotIn('id', $serviceDelivery)
                ->get();

        return view('admin.serviceDelivery.add')->with(compact('title', 'formLink', 'buttonName', 'completeAllocations'));
    }

    public function save(Request $request) {
        $serviceAllocation = ServiceAllocation::find($request->job_id);

        ServiceDelivery::create([
            'company_id' => $this->company,
            'showroom_id' => $this->showroomId,
            'service_allocation_id' => $serviceAllocation->id,
            'invoice_no' => $serviceAllocation->invoice_no,
            'delivery_issue_to' => date('Y-m-d', strtotime($request->delivery_issue_to)),
            'delivery_date' => date('Y-m-d', strtotime($request->delivery_date)),
            'service_payment_type' => $request->service_payment_type,
            'service_amount' => $request->service_amount,
            'delivery_condition' => $request->delivery_condition,
            'created_by' => $this->userId,
        ]);


        return redirect(route('serviceDelivery.index'))->with('msg', 'Service Delivery Product Successfully');
    }

    public function edit($id) {
        $title = 'Edit Service Delivey Product';

        $formLink = 'serviceDelivery.update';
        $buttonName = 'Update';

        $serviceDelivery = ServiceDelivery::with('serviceAllocation', 'serviceAllocation.product', 'serviceAllocation.serviceProduct.dealer', 'spareProducts', 'spareProducts.product', 'serviceAllocation.staff')
                ->where('id', $id)
                ->first();

        return view('admin.serviceDelivery.edit')->with(compact('title', 'formLink', 'buttonName', 'serviceDelivery'));
    }

    public function update(Request $request) {
        $serviceDelivery = ServiceDelivery::find($request->id);

        $serviceDelivery->update([
            'delivery_issue_to' => date('Y-m-d', strtotime($request->delivery_issue_to)),
            'delivery_date' => date('Y-m-d', strtotime($request->delivery_date)),
            'service_payment_type' => $request->service_payment_type,
            'service_amount' => $request->service_amount,
            'delivery_condition' => $request->delivery_condition,
        ]);

        return redirect(route('serviceDelivery.index'))->with('msg', 'Service Delivery Product Updated Successfully');
    }

    public function delete(Request $request) {
        ServiceDelivery::where('id', $request->deliveryId)->delete();
    }

    public function getInfo(Request $request) {
        $info = ServiceAllocation::with('serviceProduct', 'serviceProduct.dealer', 'product', 'spareProducts', 'spareProducts.product', 'staff')
                ->where('id', $request->id)
                ->first();
        
        $spare_products = [];
        if (!empty($info->spareProducts) && count($info->spareProducts) > 0) {
            foreach ($info->spareProducts as $s_product) {
                $product = \App\Product::find($s_product->product_id);
                $spare_product = [
                    'product_name' => $product->name,
                    'model_no' => $s_product->model_no,
                    'serial_no' => $s_product->serial_no,
                    'sale_price' => $s_product->sale_price,
                ];
                $spare_products[] = $spare_product;
            }
        }


        if ($request->ajax()) {
            return response()->json([
                        'info' => $info,
                        'service_product' => $info->serviceProduct,
                        'dealer' => $info->serviceProduct->dealer,
                        'product' => $info->product,
                        'spare_products' => $spare_products,
                        'staff' => $info->staff,
            ]);
        }
    }

    public function invoice($id) {

        $title = 'Service Invoice';

        $serviceDelivery = ServiceDelivery::with('serviceAllocation', 'serviceAllocation.product', 'serviceAllocation.serviceProduct.dealer', 'spareProducts', 'spareProducts.product', 'serviceAllocation.staff')
                ->where('id', $id)
                ->first();

        $pdf = PDF::loadView('admin.serviceDelivery.invoice', compact('title', 'serviceDelivery'));

        return $pdf->stream('service_invoice.pdf');
    }

    public function chalan($id) {

        $title = 'Service Chalan';

        $serviceDelivery = ServiceDelivery::with('serviceAllocation', 'serviceAllocation.product', 'serviceAllocation.serviceProduct.dealer', 'spareProducts', 'spareProducts.product', 'serviceAllocation.staff')
                ->where('id', $id)
                ->first();

        $pdf = PDF::loadView('admin.serviceDelivery.chalan', compact('title', 'serviceDelivery'));

        return $pdf->stream('service_chalan.pdf');
    }

}
