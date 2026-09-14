<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use MPDF;
use App\Offer;
use App\Product;
use App\StaffSetup;
use App\DealerSetup;
use App\ProductIssue;
use App\LiftingProduct;
use App\ProductIssueList;
use App\DealerRequisition;
use App\DealerCollection;
use Illuminate\Http\Request;
use App\LiftingReturnProduct;
use App\CommissionConfiguration;
use App\DealerRequisitionProduct;
use App\DealerCommissionList;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProductIssueController extends Controller {

    public function index() {
        $title = "Dealer Sales";

        if ($this->userRole == 1) {
            $issuedProducts = ProductIssue::with('user')
                    ->select('tbl_product_issue.*', 'tbl_dealers.name as dealerName')
                    ->leftJoin('tbl_dealers', 'tbl_dealers.id', '=', 'tbl_product_issue.dealer_id')
                    ->orderBy('tbl_product_issue.id', 'dsc')
                    ->get();
        } else {
            $issuedProducts = ProductIssue::with('user')
                    ->select('tbl_product_issue.*', 'tbl_dealers.name as dealerName')
                    ->leftJoin('tbl_dealers', 'tbl_dealers.id', '=', 'tbl_product_issue.dealer_id')
                    ->where('tbl_product_issue.company_id', $this->company)
//                    ->where('tbl_product_issue.showroom_id', $this->showroomId)
                    ->orderBy('tbl_product_issue.id', 'dsc')
                    ->get();
        }

        return view('admin.productIssue.index')->with(compact('title', 'issuedProducts'));
    }

    public function add() {
        $title = "Add Dealer Sales";
        $formLink = "productIssue.save";
        $buttonName = "Save";

        $dealers = DealerSetup::where('company_id', $this->company)
                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        $staffs = StaffSetup::where('status', '1')
                ->where('company_id', $this->company)
                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        $dealerRequisitions = DealerRequisition::where('company_id', $this->company)
                ->where('showroom_id', $this->showroomId)
                ->where('status', '0')
                ->get();

        $products = [];

//        $requisitionRequired = Auth::user()->role == 1 || Auth::user()->role == 5 ? False : True;
        $requisitionRequired = False;

        $usedRequisitionIds = ProductIssue::select('requisition_id')
                ->whereNotNull('requisition_id')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->distinct()
                ->get()
                ->toArray();

        $requisitons = DealerRequisition::with(['dealer'])
                ->whereNotIn('id', $usedRequisitionIds)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->where('approved_by2', '!=', '0')
                ->orderBy('date', 'dsc')
                ->get();

        // $productTypes = [
        //     'warranty_product' => 'Warranty Product',
        //     'consumer_product' => 'Consumer Product'
        // ];

        $companyProductTypes = json_decode($this->companyInfo->product_types);

        $productTypes = collect([
            'warranty_product' => 'Warranty Product',
            'consumer_product' => 'Consumer Product',
            'spare_parts' => 'Spare Parts',
            'raw_product' => 'Raw Product'
        ]);

        $productTypes = $productTypes->filter(function ($value, $key) use ($companyProductTypes) {
            return in_array($key, $companyProductTypes);
        });

        return view('admin.productIssue.add')->with(compact(
                                'requisitionRequired',
                                'requisitons',
                                'title',
                                'formLink',
                                'buttonName',
                                'dealers',
                                'staffs',
                                'dealerRequisitions',
                                'products',
                                'productTypes'
        ));
    }

    public function save(Request $request) {

        if ($request->salesType == "salesManual") {
            $type = 1;
        } elseif ($request->salesType == "salesBlank") {
            $type = 2;
        } else {
            $type = 0;
        }

        $productType = $request->productType;

//        dd($request->all());

        if ($productType == 'warranty_product') {
            $issueDate = date('Y-m-d', strtotime($request->issueDate));

            $productIssue = ProductIssue::create([
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'product_type' => $productType,
                        'requisition_id' => $request->dealerRequisitionId,
                        'dealer_id' => $request->dealer,
                        'sales_by' => $request->sales_by,
                        'issue_type' => $request->issueType,
                        'issue_no' => $request->productIssueNo,
                        'date' => $issueDate,
                        'total_qty' => $request->totalQty,
                        'total_amount' => $request->totalAmount,
                        'created_by' => $this->userId,
                        'type' => $type
            ]);

            $countProduct = count($request->productId);
            if ($request->productId) {
                $postData = [];
                for ($i = 0; $i < $countProduct; $i++) {
                    $postData[] = [
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'issue_id' => $productIssue->id,
                        'product_id' => $request->productId[$i],
                        'model_no' => $request->productModel[$i],
                        'serial_no' => $request->productSerial[$i],
                        'commission_rate' => $request->commission[$i],
//                    'offer' => $request->offer[$i],
                        'price' => $request->productPrice[$i],
                        'qty' => $request->productQty[$i],
                        'amount' => $request->amount[$i],
                        'created_by' => $this->userId
                    ];
                }
                ProductIssueList::insert($postData);
            }
        } else {
            $issueDate = date('Y-m-d', strtotime($request->issueDate));

            $productIssue = ProductIssue::create([
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'product_type' => $productType,
                        'requisition_id' => $request->dealerRequisitionId,
                        'dealer_id' => $request->dealer,
                        'sales_by' => $request->sales_by,
                        'issue_type' => $request->issueType,
                        'issue_no' => $request->productIssueNo,
                        'date' => $issueDate,
                        'total_qty' => $request->totalQty,
                        'total_amount' => $request->totalAmount,
                        'created_by' => $this->userId,
                        'type' => $type
            ]);

            $countProduct = count($request->productId);
            if ($request->productId) {
                $postData = [];
                for ($i = 0; $i < $countProduct; $i++) {
                    $postData[] = [
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'issue_id' => $productIssue->id,
                        'product_id' => $request->productId[$i],
                        'model_no' => $request->productModel[$i],
//                        'serial_no' => $request->productSerial[$i],
                        'commission_rate' => $request->commission[$i],
//                    'offer' => $request->offer[$i],
                        'price' => $request->productPrice[$i],
                        'qty' => $request->productQty[$i],
                        'amount' => $request->amount[$i],
                        'created_by' => $this->userId
                    ];
                }
                ProductIssueList::insert($postData);
            }
        }

        return redirect(route('productIssue.index'))->with('msg', 'Product Issue Added Successfully');
    }

    public function edit($issueId) {
        $title = "Edit Dealer Sales";
        $formLink = "productIssue.update";
        $buttonName = "Update";
        $dealers = DealerSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        $staffs = StaffSetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        $issuedProduct = ProductIssue::select('tbl_product_issue.*', 'tbl_dealer_requisitions.requisition_no as requisitionNo')
                        ->leftJoin('tbl_dealer_requisitions', 'tbl_dealer_requisitions.id', '=', 'tbl_product_issue.requisition_id')
                        ->where('tbl_product_issue.id', $issueId)->first();

        $issuedProductLists = ProductIssueList::select('tbl_product_issue_lists.*', 'tbl_products.name as productName')
                        ->leftJoin('tbl_products', 'tbl_products.id', '=', 'tbl_product_issue_lists.product_id')
                        ->where('issue_id', $issueId)->get();

        return view('admin.productIssue.edit')->with(compact('title', 'formLink', 'buttonName', 'issuedProduct', 'issuedProductLists', 'dealers', 'staffs'));
    }

    public function update(Request $request) {
        // $this->validation($request);
        // dd($request->all());

        $issueDate = date('Y-m-d', strtotime($request->issueDate));

        $productIssue = ProductIssue::find($request->issueId);

        $productIssue->update([
            'requisition_id' => $request->dealerRequisitionId,
            'dealer_id' => $request->dealerId,
            'sales_by' => $request->sales_by,
            'issue_no' => $request->issueType,
            'issue_no' => $request->productIssueNo,
            'date' => $issueDate,
            'total_qty' => $request->totalQty,
            'total_amount' => $request->totalAmount,
            'updated_by' => $this->userId
        ]);

        ProductIssueList::where('issue_id', $request->issueId)->delete();

        if ($productIssue->product_type == 'warranty_product') {
            if ($request->productId) {
                $countProduct = count($request->productId);
                $postData = [];
                for ($i = 0; $i < $countProduct; $i++) {
                    $postData[] = [
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'issue_id' => $productIssue->id,
                        'product_id' => $request->productId[$i],
                        'model_no' => $request->productModel[$i],
                        'serial_no' => $request->productSerial[$i],
                        'commission_rate' => $request->commission[$i],
//                    'offer' => $request->offer[$i],
                        'price' => $request->productPrice[$i],
                        'qty' => $request->productQty[$i],
                        'amount' => $request->amount[$i],
                        'updated_by' => $this->userId
                    ];
                }
                ProductIssueList::insert($postData);
            }
        } else {
            if ($request->productId) {
                $countProduct = count($request->productId);
                $postData = [];
                for ($i = 0; $i < $countProduct; $i++) {
                    $postData[] = [
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'issue_id' => $productIssue->id,
                        'product_id' => $request->productId[$i],
                        'model_no' => $request->productModel[$i],
//                        'serial_no' => $request->productSerial[$i],
                        'commission_rate' => $request->commission[$i],
//                    'offer' => $request->offer[$i],
                        'price' => $request->productPrice[$i],
                        'qty' => $request->productQty[$i],
                        'amount' => $request->amount[$i],
                        'updated_by' => $this->userId
                    ];
                }
                ProductIssueList::insert($postData);
            }
        }

        return redirect(route('productIssue.index'))->with('msg', 'Product Issue Updated Successfully');
    }

    public function productInfo(Request $request) {
        $product = Product::where('id', $request->productId)->first();

        //Main
        // $commission = DealerSetup::where('id', $request->dealerId)
        //     ->where('status', 1)
        //     ->first();
        $dealer = DealerSetup::where('id', $request->dealerId)
                ->where('status', 1)
                ->first();

        $commission = DealerCommissionList::with('commission')
                ->whereHas('commission', function ($q) use ($request) {
                    $q->where('dealer_id', @$request->dealerId)
                    ->where('status', 1);
                })
                ->where('category_id', $product->category_id)
                ->first();

                // dd($request->dealerId, $product->category_id);

        $dealer_sale = ProductIssue::where('dealer_id', $request->dealerId)
                ->get()
                ->sum('total_amount');

        $dealer_collection = DealerCollection::where('dealer_id', $request->dealerId)
                ->get()
                ->sum('payment_amount');

        $dealer_adv = DealerCollection::select(
                        'dealer_advance_collection.advance_amount',
                        'dealer_advance_collection.adjust_amount',
                        'dealer_advance_collection.return_amount'
                )
                ->leftjoin('dealer_advance_collection', 'dealer_advance_collection.id', '=', 'tbl_dealer_collections.advance_id')
                ->where('tbl_dealer_collections.dealer_id', $request->dealerId)
                ->get();

        $total_adv = ($dealer_adv->sum('advance_amount') + $dealer_adv->sum('return_amount')) - $dealer_adv->sum('adjust_amount');



        $credit_limit = ($dealer->credit_limit + $total_adv) - ($dealer_sale - $dealer_collection);

        $offer = Offer::where('product_id', $request->productId)->where('status', 1)->first();

        if ($offer) {
            if ($offer->offer_type == 'fixed') {
                $offerRate = $offer->amount;
                $offerAmt = $offer->amount;
            } else {

                $offerRate = $offer->perAmount;
                $offerAmt = ($offerRate / 100) * $product->mrp_price;
            }
        } else {
            $offerRate = 0;
            $offerAmt = 0;
        }

        if ($commission) {
            $commissionRate = $commission->commission;
            $calculationAmt = $product->mrp_price - $offerAmt;
            $commissionAmt = ($commissionRate / 100) * $calculationAmt;
        } else {
            $commissionRate = 0;
            $commissionAmt = 0;
        }

        $netAmount = 0;

        if ($product) {
            $netAmount = $product->mrp_price - $offerAmt - $commissionAmt;
        }


        if ($request->ajax()) {
            return response()->json([
                        'product' => $product,
                        'offerRate' => $offerRate,
                        'commissionRate' => $commissionRate,
                        'netAmount' => $netAmount,
                        'credit_limit' => $credit_limit
            ]);
        }
    }

    public function consumerProductInfo(Request $request) {
        $product = Product::where('id', $request->productId)->first();

        $dealer = DealerSetup::where('id', $request->dealerId)
                ->where('status', 1)
                ->first();

        $commission = DealerCommissionList::with('commission')
                ->whereHas('commission', function ($q) use ($request) {
                    $q->where('dealer_id', @$request->dealerId)
                    ->where('status', 1);
                })
                ->where('category_id', $product->category_id)
                ->first();

        $dealer_sale = ProductIssue::where('dealer_id', $request->dealerId)
                ->get()
                ->sum('total_amount');

        $dealer_collection = DealerCollection::where('dealer_id', $request->dealerId)
                ->get()
                ->sum('payment_amount');

        $dealer_adv = DealerCollection::select(
                        'dealer_advance_collection.advance_amount',
                        'dealer_advance_collection.adjust_amount',
                        'dealer_advance_collection.return_amount'
                )
                ->leftjoin('dealer_advance_collection', 'dealer_advance_collection.id', '=', 'tbl_dealer_collections.advance_id')
                ->where('tbl_dealer_collections.dealer_id', $request->dealerId)
                ->get();

        $total_adv = ($dealer_adv->sum('advance_amount') + $dealer_adv->sum('return_amount')) - $dealer_adv->sum('adjust_amount');



        $credit_limit = ($dealer->credit_limit + $total_adv) - ($dealer_sale - $dealer_collection);

        $offer = Offer::where('product_id', $request->productId)->where('status', 1)->first();

        if ($offer) {
            if ($offer->offer_type == 'fixed') {
                $offerRate = $offer->amount;
                $offerAmt = $offer->amount;
            } else {

                $offerRate = $offer->perAmount;
                $offerAmt = ($offerRate / 100) * $product->mrp_price;
            }
        } else {
            $offerRate = 0;
            $offerAmt = 0;
        }

        if ($commission) {
            $commissionRate = $commission->commission;
            $calculationAmt = $product->mrp_price - $offerAmt;
            $commissionAmt = ($commissionRate / 100) * $calculationAmt;
        } else {
            $commissionRate = 0;
            $commissionAmt = 0;
        }

        $netAmount = 0;

        if ($product) {
            $netAmount = $product->mrp_price - $offerAmt - $commissionAmt;
        }


        if ($request->ajax()) {
            return response()->json([
                        'product' => $product,
                        'offerRate' => $offerRate,
                        'commissionRate' => $commissionRate,
                        'netAmount' => $netAmount * $request->qty,
                        'qty' => $request->qty,
                        'credit_limit' => $credit_limit
            ]);
        }
    }

    public function productSerialInfo(Request $request) {

        $liftingReturnSerials = LiftingReturnProduct::where('product_id', $request->productId)
                ->select(['serial_no'])
                ->get()
                ->pluck('serial_no')
                ->toArray();

        $productSerials = LiftingProduct::where('product_id', $request->productId)
                ->whereNotIn('serial_no', $liftingReturnSerials)
                ->select(['serial_no'])
                ->get()
                ->pluck('serial_no')
                ->toArray();

        $soldProducts = ProductIssueList::where('product_id', $request->productId)
                ->where('isReturn', 0)
                ->select(['serial_no'])
                ->get()
                ->pluck('serial_no')
                ->toArray();


        $notSoldSerials = array_diff($productSerials, $soldProducts);

        if ($request->ajax()) {
            return response()->json([
                        'productSerials' => $notSoldSerials
            ]);
        }
    }

    public function dealerRequisitionInfo(Request $request) {
        $dealerRequisition = DealerRequisition::where('id', $request->dealerRequisitionId)->first();
        $dealerRequisitionProducts = DealerRequisitionProduct::select('tbl_dealer_requisition_products.*', 'tbl_products.name as productName', 'tbl_products.code as productCode', 'tbl_products.category_id as categoryId')
                        ->leftJoin('tbl_products', 'tbl_products.id', '=', 'tbl_dealer_requisition_products.product_id')
                        ->where('requisition_id', $request->dealerRequisitionId)->get();

        $dealer = DealerSetup::where('id', $dealerRequisition->dealer_id)->first();

        $productSerials = LiftingProduct::select('tbl_lifting_products.*')
                ->leftJoin('tbl_product_issue_lists', 'tbl_product_issue_lists.serial_no', '=', 'tbl_lifting_products.serial_no')
                ->whereNull('tbl_product_issue_lists.serial_no')
                ->where('tbl_lifting_products.company_id', $this->company)
//                ->where('tbl_lifting_products.showroom_id', $this->showroomId)
                ->where('tbl_lifting_products.status', '1')
                ->get();

        // dd($productSerials);

        $productIssueLists = ProductIssue::select('tbl_product_issue_lists.*')
                ->join('tbl_product_issue_lists', 'tbl_product_issue_lists.issue_id', '=', 'tbl_product_issue.id')
                ->where('tbl_product_issue.requisition_id', $request->dealerRequisitionId)
                ->get();

        $allCommissions = CommissionConfiguration::where('commission_type', 'Dealer Commission')
                ->where('dealer_id', $dealerRequisition->dealer_id)
                ->get();

        // dd($dealer);

        if ($request->ajax()) {
            return response()->json([
                        'dealerRequisition' => $dealerRequisition,
                        'dealer' => $dealer,
                        'dealerRequisitionProducts' => $dealerRequisitionProducts,
                        'productSerials' => $productSerials,
                        'productIssueLists' => $productIssueLists,
                        'allCommissions' => $allCommissions
            ]);
        }
    }

    public function printChalan($productIssueId) {
        
        $title = "Print Challan";


        $productIssue = ProductIssue::select(
                        'tbl_product_issue.*',
                        'tbl_dealers.name as dealerName',
                        'tbl_dealers.code as dealerCode',
                        'tbl_dealers.address as dealerAddress',
                        'tbl_dealers.mobile as dealerMobile',
                        'tbl_region.name as regionName',
                        'tbl_area.name as areaName',
                        'tbl_staffs.name as staffName',
                        'tbl_territories.name as territoryName'
                )
                ->leftJoin('tbl_dealers', 'tbl_dealers.id', '=', 'tbl_product_issue.dealer_id')
                ->leftJoin('tbl_region', 'tbl_region.id', '=', 'tbl_dealers.region_id')
                ->leftJoin('tbl_area', 'tbl_area.id', '=', 'tbl_dealers.area_id')
                ->leftJoin('tbl_territories', 'tbl_territories.id', '=', 'tbl_dealers.territory_id')
                ->leftJoin('tbl_staffs', 'tbl_staffs.id', '=', 'tbl_product_issue.sales_by')
                ->where('tbl_product_issue.id', $productIssueId)
                ->first();

        $productIssueLists = ProductIssueList::select('tbl_product_issue_lists.*', 'tbl_products.name as productName')
                ->leftJoin('tbl_products', 'tbl_products.id', '=', 'tbl_product_issue_lists.product_id')
                ->where('tbl_product_issue_lists.issue_id', $productIssueId)
                ->get();

        $groupByProductIds = $productIssueLists->groupBy('product_id');


        // dd($groupByProductIds);

        $pdf = PDF::loadView('admin.productIssue.printChalan', ['title' => $title, 'productIssue' => $productIssue, 'productIssueLists' => $productIssueLists, 'groupByProductIds' => $groupByProductIds]);

        return $pdf->stream('product_issue_invoice_' . $productIssue->issue_no . '.pdf');
    }

    public function printInvoice($productIssueId) {

        $title = "Print Invoice";

        $productIssue = ProductIssue::select(
                        'tbl_product_issue.*',
                        'tbl_dealers.name as dealerName',
                        'tbl_dealers.code as dealerCode',
                        'tbl_dealers.address as dealerAddress',
                        'tbl_dealers.mobile as dealerMobile',
                        'tbl_region.name as regionName',
                        'tbl_area.name as areaName',
                        'tbl_staffs.name as staffName',
                        'tbl_territories.name as territoryName'
                )->leftJoin('tbl_dealers', 'tbl_dealers.id', '=', 'tbl_product_issue.dealer_id')
                ->leftJoin('tbl_region', 'tbl_region.id', '=', 'tbl_dealers.region_id')
                ->leftJoin('tbl_area', 'tbl_area.id', '=', 'tbl_dealers.area_id')
                ->leftJoin('tbl_territories', 'tbl_territories.id', '=', 'tbl_dealers.territory_id')
                ->leftJoin('tbl_staffs', 'tbl_staffs.id', '=', 'tbl_product_issue.sales_by')
                ->where('tbl_product_issue.id', $productIssueId)
                ->first();

        // dd($productIssue);


        $productIssueLists = ProductIssueList::select('tbl_product_issue_lists.*', 'tbl_products.name as productName')
                ->leftJoin('tbl_products', 'tbl_products.id', '=', 'tbl_product_issue_lists.product_id')
                ->where('tbl_product_issue_lists.issue_id', $productIssueId)
                ->get();

        $groupByProductIds = $productIssueLists->groupBy('product_id');

        // dd($groupByProductIds[605]);
        // dd($productIssueLists);

        $pdf = PDF::loadView('admin.productIssue.printInvoice', ['title' => $title, 'productIssue' => $productIssue, 'productIssueLists' => $productIssueLists, 'groupByProductIds' => $groupByProductIds]);

        return $pdf->stream('product_issue_chalan_' . $productIssue->issue_no . '.pdf');
    }

    public function delete(Request $request) {

        ProductIssue::where('id', $request->productIssueId)->delete();

        ProductIssueList::where('issue_id', $request->productIssueId)->delete();
    }

    function getpdetails() {
        //        $i = 1;
        $pid = $this->input->post('productid');
        //        $oldpid = $pid;
        //$pdata = $this->db->query("select * from purchase where product_id = '$pid' order by id desc limit 1")->row();
        $data['pdata'] = $this->db->query("select * from purchase where product_id = '$pid' order by id desc limit 1")->row();
        $data['qty'] = 1;
        echo json_encode($data);
    }

    public function getProduct(Request $request) {

        $productDetail = LiftingProduct::select('tbl_lifting_products.*')
                ->where('tbl_lifting_products.serial_no', $request->serialNo)
                ->where('tbl_lifting_products.status', '1')
                ->first();

        if ($request->ajax()) {
            return response()->json([
                        'productDetail' => $productDetail
            ]);
        }
    }

    public function getRequisitionProduct(Request $request) {

        $requisionNo = $request->requisitionNo;

        // fetch products
        $productsIds = DealerRequisitionProduct::where('requisition_id', $requisionNo)->select('product_id')->get()->pluck('product_id');

        $products = Product::whereIn('id', $productsIds)->get();

        $requisition = DealerRequisition::with(['requisitions'])->where('id', $requisionNo)->first();

        // fetch Dealer
        $dealer = DealerSetup::find($requisition->dealer_id);

        return [
            'products' => $products,
            'dealer' => $dealer,
            'requisition' => $requisition,
        ];
    }

}
