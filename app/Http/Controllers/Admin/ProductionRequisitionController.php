<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductionRequisition;
use App\ProductionRequisitonInfo;
use App\Product;
use App\StaffSetup;

class ProductionRequisitionController extends Controller {

    public function index() {
        $title = "Production Requisation";

        if ($this->userRole == 1) {
            $productionRequisition = ProductionRequisition::select('tbl_production_requisitions.*', 'tbl_staffs.name as staffName')
                    ->leftJoin('tbl_products', 'tbl_products.id', '=', 'tbl_production_requisitions.product_id')
                    ->leftJoin('tbl_staffs', 'tbl_staffs.id', '=', 'tbl_production_requisitions.requisitions_name_id')
                    // ->where('tbl_dealer_requisitions.status','1')
                    ->orderBy('tbl_production_requisitions.date', 'dsc')
                    ->get();
        } else {
            $productionRequisition = ProductionRequisition::select('tbl_production_requisitions.*', 'tbl_staffs.name as staffName')
                    ->leftJoin('tbl_products', 'tbl_products.id', '=', 'tbl_production_requisitions.product_id')
                    ->leftJoin('tbl_staffs', 'tbl_staffs.id', '=', 'tbl_production_requisitions.requisitions_name_id')
                    ->where('tbl_production_requisitions.company_id', $this->company)
//                ->where('tbl_production_requisitions.showroom_id', $this->showroomId)
                    // ->where('tbl_dealer_requisitions.status','1')
                    ->orderBy('tbl_production_requisitions.date', 'dsc')
                    ->get();
        }

        return view('admin.productionRequisition.index')->with(compact('title', 'productionRequisition'));
    }

    public function add() {
        $title = "Add Production Requisation";
        $formLink = "productionRequisition.save";
        $buttonName = "Save";

        $req_name = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('id', 'asc')
                ->get();

        $products = Product::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        return view('admin.productionRequisition.add')->with(compact('title', 'formLink', 'buttonName', 'products', 'req_name'));
    }

    public function save(Request $request) {
        // $this->validation($request);

        $requisitionDate = date('Y-m-d', strtotime($request->requisitionDate));

        $productionRequisition = ProductionRequisition::create([
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'requisition_no' => $request->requisitionNo,
                    'requisitions_name_id' => $request->requisitionNameId,
                    'date' => $requisitionDate,
                    'product_id' => $request->product,
                    'total_qty' => $request->totalQty,
                    'created_by' => $this->userId
        ]);

        $countProduct = count($request->productId);
        if ($request->productId) {
            $postData = [];
            for ($i = 0; $i < $countProduct; $i++) {
                $postData[] = [
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'requisition_id' => $productionRequisition->id,
                    'product_id' => $request->productId[$i],
                    'product_name' => $request->productName[$i],
                    'model_no' => $request->productModel[$i],
                    'qty' => $request->productQty[$i],
                    'created_by' => $this->userId
                ];
            }
            ProductionRequisitonInfo::insert($postData);
        }

        return redirect(route('productionRequisition.index'))->with('msg', 'Production Requisition Added Successfully');
    }

    public function edit($productionRequisitionId) {
        $title = "Edit Production Requisation";
        $formLink = "productionRequisition.update";
        $buttonName = "Update";

        $req_name = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('id', 'asc')
                ->get();

        $productionRequisition = ProductionRequisition::where('id', $productionRequisitionId)->first();
        $productionRequisitionInfo = ProductionRequisitonInfo::where('requisition_id', $productionRequisitionId)->get();
        $products = product::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        return view('admin.productionRequisition.edit')->with(compact('title', 'formLink', 'buttonName', 'products', 'productionRequisition', 'productionRequisitionInfo', 'req_name'));
    }

    public function update(Request $request) {
        $requisitionDate = date('Y-m-d', strtotime($request->requisitionDate));

        $productionRequisition = ProductionRequisition::find($request->productionRequisitionId);

        $productionRequisition->update([
            'requisition_no' => $request->requisitionNo,
            'requisitions_name_id' => $request->requisitionNameId,
            'date' => $requisitionDate,
            'product_id' => $request->product,
            'total_qty' => $request->totalQty,
            'updated_by' => $this->userId
        ]);

        ProductionRequisitonInfo::where('requisition_id', $request->productionRequisitionId)->delete();

        $countProduct = count($request->productId);
        if ($request->productId) {
            $postData = [];
            for ($i = 0; $i < $countProduct; $i++) {
                $postData[] = [
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'requisition_id' => $productionRequisition->id,
                    'product_id' => $request->productId[$i],
                    'product_name' => $request->productName[$i],
                    'model_no' => $request->productModel[$i],
                    'qty' => $request->productQty[$i],
                    'updated_by' => $this->userId
                ];
            }
            ProductionRequisitonInfo::insert($postData);
        }

        return redirect(route('productionRequisition.index'))->with('msg', 'Production Requisition Updated Successfully');
    }

    public function productInfo(Request $request) {
        $product = Product::where('id', $request->productId)->first();

        if ($request->ajax()) {
            return response()->json([
                        'product' => $product
            ]);
        }
    }

    public function requisitionProductInfo(Request $request) {


        $productionRequisition = ProductionRequisition::select('tbl_production_requisitions.*', 'tbl_staffs.name as staffName')
                ->leftJoin('tbl_staffs', 'tbl_staffs.id', '=', 'tbl_production_requisitions.requisitions_name_id')
                ->where('tbl_production_requisitions.id', $request->productionRequisitionId)
                ->first();

        $productionRequisitionInfo = ProductionRequisitonInfo::select('tbl_production_requisition_info.*', 'tbl_products.name as productName', 'tbl_products.model_no as modelNo')
                ->leftJoin('tbl_products', 'tbl_products.id', '=', 'tbl_production_requisition_info.product_id')
                ->where('tbl_production_requisition_info.requisition_id', $request->productionRequisitionId)
                ->get();

        if ($request->ajax()) {
            return response()->json([
                        'productionRequisition' => $productionRequisition,
                        'productionRequisitionInfo' => $productionRequisitionInfo
            ]);
        }
    }

    public function delete(Request $request) {
        ProductionRequisition::where('id', $request->productionRequisitionId)->delete();
        ProductionRequisitonInfo::where('requisition_id', $request->productionRequisitionId)->delete();
    }

}
