<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\StaffSetup;
use App\ProductionIssue;
use App\LiftingProduct;
use App\ProductionIsuuList;
use App\ProductionRequisition;
use App\ProductionRequisitonInfo;
use Illuminate\Support\Facades\Auth;
use DB;
use PDF;
use MPDF;

class ProSalesController extends Controller {

    public function index() {
        $title = "Production Sales";
        if ($this->userRole == 1) {
            $issuedProducts = ProductionIssue::select('tbl_production_issue.*', 'tbl_staffs.name as staffName')
                    ->leftJoin('tbl_staffs', 'tbl_staffs.id', '=', 'tbl_production_issue.requisitions_name_id')
                    ->orderBy('tbl_production_issue.id', 'dsc')
                    ->get();
        } else {
            $issuedProducts = ProductionIssue::select('tbl_production_issue.*', 'tbl_staffs.name as staffName')
                    ->leftJoin('tbl_staffs', 'tbl_staffs.id', '=', 'tbl_production_issue.requisitions_name_id')
                    ->where('tbl_production_issue.company_id', $this->company)
//                    ->where('tbl_production_issue.showroom_id', $this->showroomId)
                    ->orderBy('tbl_production_issue.id', 'dsc')
                    ->get();
        }

        return view('admin.proSales.index')->with(compact('title', 'issuedProducts'));
    }

    public function add() {
        $title = "Add Production";
        $formLink = "proSales.save";
        $buttonName = "Save";

        $staffs = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        $productionRequisitions = ProductionRequisition::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->get();

        $products = Product::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        $requisitionRequired = False;

        $usedRequisitionIds = ProductionIssue::select('requisition_id')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->whereNotNull('requisition_id')
                ->distinct()
                ->get()
                ->toArray();

        $requisitons = ProductionRequisition::with(['staff'])
                ->whereNotIn('id', $usedRequisitionIds)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->where('approved_by2', '!=', '0')
                ->orderBy('date', 'dsc')
                ->get();

        return view('admin.proSales.add')->with(compact('title', 'formLink', 'buttonName', 'requisitionRequired', 'requisitons', 'staffs', 'productionRequisitions', 'products'));
    }

    public function save(Request $request) {
        $submissionDate = date('Y-m-d', strtotime($request->submissionDate));


        $lifting = ProductionIssue::create([
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'serial_no' => $request->serialNo,
                    'requisition_id' => $request->dealerRequisitionId,
                    'requisitions_name_id' => $request->vendorId,
                    'date' => $submissionDate,
                    'total_qty' => $request->totalQty,
                    'total_price' => $request->totalPrice,
                    'created_by' => $this->userId
        ]);

        $countProduct = count($request->productId);
        $serialNo = $this->maxProductSerial();
        if ($request->productId) {
            $postData = [];
            for ($i = 0; $i < $countProduct; $i++) {
                $postData[] = [
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'issue_id' => $lifting->id,
                    'product_id' => $request->productId[$i],
                    'product_name' => $request->productName[$i],
                    'model_no' => $request->productModel[$i],
                    'serial_no' => $request->productSerialNo[$i],
                    'color' => $request->productColor[$i],
                    'qty' => $request->productQty[$i],
                    'price' => $request->productPrice[$i],
                    'created_by' => $this->userId
                ];
                $serialNo++;
            }
            ProductionIsuuList::insert($postData);
        }
        return redirect(route('proSales.index'))->with('msg', 'Production Added Successfully');
    }

    public function edit($liftingId) {
        $title = "Edit  Production";
        $formLink = "proSales.update";
        $buttonName = "Update";

        $vendors = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();


        $products = Product::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();



        $lifting = ProductionIssue::select('tbl_production_issue.*', 'tbl_production_issue_lists.product_id')
                        ->leftJoin('tbl_production_issue_lists', 'tbl_production_issue_lists.issue_id', '=', 'tbl_production_issue.id')
                        ->where('tbl_production_issue.id', $liftingId)->first();

        $liftingProducts = ProductionIsuuList::select('tbl_production_issue_lists.*', 'tbl_products.name as productName')
                ->join('tbl_products', 'tbl_products.id', '=', 'tbl_production_issue_lists.product_id')
                ->where('.tbl_production_issue_lists.issue_id', $liftingId)
                ->get();

        return view('admin.proSales.edit')->with(compact('title', 'formLink', 'buttonName', 'vendors', 'products', 'lifting', 'liftingProducts'));
    }

    public function update(Request $request) {
        // dd($request->all()); die();
        // $this->validation($request);

        $submissionDate = date('Y-m-d', strtotime($request->submissionDate));



        $liftingId = $request->liftingId;

        $lifting = ProductionIssue::find($liftingId);

        $lifting->update([
            'serial_no' => $request->serialNo,
            'requisitions_name_id' => $request->vendorId,
            'date' => $submissionDate,
            'total_qty' => $request->totalQty,
            'total_price' => $request->totalPrice,
            'updated_by' => $this->userId
        ]);

        ProductionIsuuList::where('issue_id', $liftingId)->delete();

        $countProduct = count($request->productId);
        if ($request->productId) {
            $postData = [];
            for ($i = 0; $i < $countProduct; $i++) {
                $postData[] = [
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'issue_id' => $lifting->id,
                    'product_id' => $request->productId[$i],
                    'product_name' => $request->productName[$i],
                    'model_no' => $request->productModel[$i],
                    'serial_no' => $request->productSerialNo[$i],
                    'color' => $request->productColor[$i],
                    'qty' => $request->productQty[$i],
                    'price' => $request->productPrice[$i],
                    'updated_by' => $this->userId
                ];
            }
            ProductionIsuuList::insert($postData);
        }

        return redirect(route('proSales.index'))->with('msg', 'Product Update Successfully');
    }

    public function liftingProductInfo(Request $request) {
        // echo $request->productId; die();
        $product = Product::where('id', $request->productId)->first();
        // dd($product); die();

        if ($request->ajax()) {
            return response()->json([
                        'product' => $product
            ]);
        }
    }

    public function maxProductSerial() {
        $maxLiftingProduct = ProductionIsuuList::max('serial_no');

        if (@$maxLiftingProduct) {
            $productSerialNo = $maxLiftingProduct + 1;
        } else {
            $productSerialNo = 1000000 + 1;
        }
        return $productSerialNo;
    }

    public function getRequisitionProduct(Request $request) {

        $requisionNo = $request->requisitionNo;

        // fetch products
        $productsIds = ProductionRequisitonInfo::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('requisition_id', $requisionNo)
                ->select('product_id')
                ->get()
                ->pluck('product_id');

        $products = Product::whereIn('id', $productsIds)->get();

        $requisition = ProductionRequisition::with(['requisitions'])->where('id', $requisionNo)->first();

        // fetch Dealer
        $dealer = StaffSetup::find($requisition->requisitions_name_id);

        return [
            'products' => $products,
            'dealer' => $dealer,
            'requisition' => $requisition,
        ];
    }

    public function delete(Request $request) {
        // echo $lifting = $request->liftingId; die();
        $liftingId = $request->liftingId;
        ProductionIssue::where('id', $liftingId)->delete();
        ProductionIsuuList::where('issue_id', $liftingId)->delete();
    }

}
