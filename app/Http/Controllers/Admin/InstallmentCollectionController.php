<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Installment;
use App\CustomerProduct;
use App\RetailSales;
use App\InstallmentSchedule;
use App\InstallmentCollection;
use App\InstallmentCollectionList;
use App\Product;

class InstallmentCollectionController extends Controller {

    public function index() {
        $title = "Installment Collection List";
        $installmentCollection = InstallmentCollection::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('id', 'ASC')
                ->get();

        return view('admin.installmentCollection.index')->with(compact('title', 'installmentCollection'));
    }

    public function add() {
        $title = "Collect Installment ";
        $formLink = "installmentCollection.save";
        $buttonName = "Save";
        // $customerProducts = CustomerProduct::where('purchase_type','Installment')->get();
        $customerProducts = Installment::where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->get();

        return view('admin.installmentCollection.add')->with(compact('title', 'formLink', 'buttonName', 'customerProducts'));
    }

    public function save(Request $request) {
        $getCustomerProduct = RetailSales::where('showroom_id', $this->showroomId)->where('id', $request->customerProductId)->first();
        $installment = Installment::where('showroom_id', $this->showroomId)->where('customer_product_id', $getCustomerProduct->id)->first();
        $exist_installment_collection = InstallmentCollection::where('showroom_id', $this->showroomId)->where('installment_id', $installment->id)->first();
        // dd($exist_installment_collection);

        if (!@$exist_installment_collection) {
            $installment_collection = InstallmentCollection::create([
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'installment_id' => $installment->id,
                        'customer_product_id' => $request->customerProductId,
                        'customer_id' => $getCustomerProduct->customer_id,
                        'product_id' => $getCustomerProduct->product_id,
                        'invoice_no' => $request->invoiceNo,
                        'customer_name' => $request->customerName,
                        'installment_price' => $request->productAmount,
                        'booking_amount' => $request->bookingAmount,
                        'installment_qty' => $request->installmentQty,
                        'installment_amount' => $request->installmentAmount,
                        'created_by' => $this->userId
            ]);

            $installmentCollectionId = $installment_collection->id;
        } else {
            $installmentCollectionId = $exist_installment_collection->id;
        }

        $countInstallmentCollection = count($request->installmentScheduleId);
        $postData = [];

        for ($i = 0; $i < $countInstallmentCollection; $i++) {
            $installmentScheduleDate = date('Y-m-d', strtotime($request->installmentScheduleDate[$i]));
            $installmentCollectionDate = date('Y-m-d');

            $installment_collection_list = InstallmentCollectionList::create([
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'installment_id' => $installment->id,
                        'installment_schedule_id' => $request->installmentScheduleId[$i],
                        'installment_collection_id' => $installmentCollectionId,
                        'invoice_no' => $request->invoiceNo,
                        'installment_schedule_date' => $installmentScheduleDate,
                        'installment_collection_date' => $installmentCollectionDate,
                        'installment_schedule_amount' => $request->installmentScheduleAmount[$i],
                        'created_by' => $this->userId
            ]);

            $installmentSchedule = InstallmentSchedule::where('showroom_id', $this->showroomId)->where('id', @$request->installmentScheduleId[$i])->first();

            $installmentSchedule->update([
                'status' => 0
            ]);
        }

        $installmentSchedule = InstallmentSchedule::where('showroom_id', $this->showroomId)->where('installment_id', $installment->id)
                ->where('status', '0')
                ->count('installment_id');

        // echo ($installment->installment_qty + 1)."----";
        // echo $installment->id."----";
        // echo $installmentSchedule; exit();

        if (($installment->installment_qty + 1) == $installmentSchedule) {
            $installment->update([
                'status' => '0'
            ]);
        }

        return redirect(route('installmentCollection.index'))->with('msg', 'Installment Collected Successfully');
    }

    public function edit($id) {
        $title = "Edit Installment Collection";
        $formLink = "installmentCollection.update";
        $buttonName = "Update";

        $installmentCollection = InstallmentCollection::where('showroom_id', $this->showroomId)->where('id', $id)->first();

        $installmentScheduleList = InstallmentSchedule::where('showroom_id', $this->showroomId)
                ->where('status', 1)
                ->where('installment_id', $installmentCollection->installment_id)
                ->orderBy('installment_schedule_date', 'ASC')
                ->get();

        $installmentCollectionList = InstallmentCollectionList::where('showroom_id', $this->showroomId)
                ->where('installment_id', $installmentCollection->installment_id)
                ->orderBy('installment_schedule_date', 'ASC')
                ->get();

        $product = Product::where('id', @$installmentCollection->product_id)->first();

        return view('admin.installmentCollection.edit')->with(compact('title', 'formLink', 'buttonName', 'installmentCollection', 'installmentScheduleList', 'installmentCollectionList', 'product'));
    }

    public function update(Request $request) {
        $installmentId = $request->installmentId;
        $installmentCollectionId = $request->installmentCollectionId;
        DB::table('tbl_installment_schedule')
                ->where('showroom_id', $this->showroomId)
                ->where('installment_id', $installmentId)
                ->update([
                    'status' => 1
        ]);

        $countInstallmentCollection = count($request->installmentScheduleId);

        DB::table('tbl_installment_collection_list')
                ->where('showroom_id', $this->showroomId)
                ->where('installment_collection_id', $installmentCollectionId)
                ->where('installment_id', $installmentId)
                ->delete();

        for ($i = 0; $i < $countInstallmentCollection; $i++) {
            $installmentScheduleDate = date('Y-m-d', strtotime($request->installmentScheduleDate[$i]));
            $installmentCollectionDate = date('Y-m-d');

            $installment_collection_list = InstallmentCollectionList::create([
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'installment_id' => $installmentId,
                        'installment_schedule_id' => $request->installmentScheduleId[$i],
                        'installment_collection_id' => $installmentCollectionId,
                        'invoice_no' => $request->invoiceNo,
                        'installment_schedule_date' => $installmentScheduleDate,
                        'installment_collection_date' => $installmentCollectionDate,
                        'installment_schedule_amount' => $request->installmentScheduleAmount[$i],
                        'updated_by' => $this->userId
            ]);

            $installmentSchedule = InstallmentSchedule::where('id', @$request->installmentScheduleId[$i])->first();

            if (@$installmentSchedule) {
                $installmentSchedule->update([
                    'status' => 0
                ]);
            }
        }

        return redirect(route('installmentCollection.index'))->with('msg', 'Installment Collected Successfully');
    }

    public function delete(Request $request) {
        $installmentCollection = InstallmentCollection::find($request->installmentCollectionId);

        $installmentSchedule = InstallmentSchedule::where('installment_id', $installmentCollection->installment_id);

        if (@$installmentSchedule) {
            $installmentSchedule->update([
                'status' => '1'
            ]);
        }

        $installment = Installment::find($installmentCollection->installment_id);

        if (@$installment) {
            $installment->update([
                'status' => '1'
            ]);
        }

        InstallmentCollection::where('id', $request->installmentCollectionId)->delete();
        InstallmentCollectionList::where('installment_collection_id', $request->installmentCollectionId)->delete();
    }

    public function getInstallmentInfo(Request $request) {
        $customerProductId = $request->customerProductId;

        $installment = Installment::where('showroom_id', $this->showroomId)
                ->where('customer_product_id', $customerProductId)
                ->first();

        $installmentScheduleList = InstallmentSchedule::where('showroom_id', $this->showroomId)
                ->where('status', 1)
                ->where('installment_id', $installment->id)
                ->orderBy('installment_schedule_date', 'ASC')
                ->get();

        $installmentCollectionList = InstallmentCollectionList::where('showroom_id', $this->showroomId)
                ->where('installment_id', $installment->id)
                ->orderBy('installment_schedule_date', 'ASC')
                ->get();

        if ($request->ajax()) {
            return response()
                            ->json([
                                'installment' => $installment,
                                'installmentScheduleList' => $installmentScheduleList,
                                'installmentCollectionList' => $installmentCollectionList,
            ]);
        }
    }

}
