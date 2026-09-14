<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use MPDF;
use App\Product;
use App\DealerSetup;
use App\SalesReturn;
use App\ProductIssue;
use App\CategorySetup;
use App\DealerCollection;
use App\ProductIssueList;
use App\AdvanceCollection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SalesReturnController extends Controller {

    public function generateInvoice() {
        $requisitionNo = "";

        $maxProductIssueId = SalesReturn::max('issue_no');

        if (@$maxProductIssueId) {
            $productIssueNo = $maxProductIssueId + 1;
        } else {
            $productIssueNo = 100000000 + 1;
        }

        return $productIssueNo;
    }

    public function index(Request $request) {
        $title = "Dealer Sales Return";
        $showroomId = $this->showroomId;

        $fdealer = $request->dealer;
        $fproduct = $request->product;
        $type = $request->productType;

        $dealers = DealerSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        $products = [];
        if ($type) {
            $products = Product::where('status', 1)
                    ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                    ->where('product_type', $type)
                    ->get();
        }


        $categories = CategorySetup::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
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


        $allSales = [];

        if ($request->searched) {

            if ($request->dealer) {
                $dealerIssueIds = ProductIssue::where('dealer_id', $request->dealer)
                        ->select(['id'])
                        ->get()
                        ->pluck('id');

                $productIssueListids = ProductIssueList::whereIn('issue_id', $dealerIssueIds)
                        ->select(['id'])
                        ->whereNull('collection_id')
                        ->get()
                        ->pluck('id');
            }

            $allSales = ProductIssueList::with('product', 'issue.dealer')
                    ->where('company_id', $this->company);
//                    ->where('showroom_id', $this->showroomId);

            if ($productIssueListids) {
                $allSales = $allSales->whereIn('id', $productIssueListids);
            }

            if ($request->product) {
                $allSales = $allSales->whereIn('product_id', $request->product);
            }

            $allSales = $allSales->orderBy('serial_no', 'asc')->get();
        }

        return view('admin.salesReturn.index')->with(compact('fdealer', 'fproduct', 'title', 'allSales', 'showroomId', 'dealers', 'products', 'categories', 'productTypes', 'type'));
    }

    public function salesReturnView($id) {
        $title = "Sales Return View";
        $showroomId = $this->showroomId;
        $returnNo = $this->generateInvoice();
        $sale = DB::table('tbl_product_issue_lists')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('id', $id)
                ->first();

        return view('admin.salesReturn.view')->with(compact('title', 'sale', 'showroomId', 'returnNo'));
    }

    public function salesReturn($id) {

        $title = "Sales Return";
        $formLink = "salesReturn.save";
        $buttonName = "Save";
        $showroomId = $this->showroomId;
        $sale = DB::table('tbl_product_issue_lists')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('id', $id)
                ->first();

        $returnNo = $this->generateInvoice();

        return view('admin.salesReturn.return')->with(compact('title', 'sale', 'showroomId', 'formLink', 'buttonName', 'returnNo'));
    }

    public function salesReturnMultiple(Request $request) {
        $returnNo = $this->generateInvoice();

        $type = $request->productType;
        $ids = $request->ids;

        if (!$ids) {
            return back()->with('fail_msg', "please Select at least one product");
        }

        $title = "Sales Return";
        $formLink = "salesReturn.save.multiple";
        $buttonName = "Save";
        $showroomId = $this->showroomId;


        $sales = ProductIssueList::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->with(['issue'])
                ->whereIn('id', $ids)
                ->get();


        return view('admin.salesReturn.returnMultiple')->with(compact('title', 'sales', 'showroomId', 'formLink', 'buttonName', 'returnNo', 'type'));
    }

    public function save(Request $request) {

        $salesReturn = SalesReturn::create([
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'requisition_id' => $request->requisition_id,
                    'dealer_id' => $request->dealer_id,
                    'sales_by' => $request->sales_by,
                    'issue_type' => $request->issueType,
                    'issue_no' => $request->productIssueNo,
                    'product_id' => $request->product_id,
                    'model_no' => $request->productModel,
                    'serial_no' => $request->serial_no,
                    'commission_rate' => $request->commission,
                    'offer' => $request->offer,
                    'price' => $request->productPrice,
                    'qty' => 1,
                    'amount' => $request->amount,
                    'return_date' => date('Y-m-d', strtotime($request->return_date)),
                    'reason' => $request->reason
        ]);

        ProductIssueList::where('id', $request->id)->delete();

        $ProductIssue = DB::table('tbl_product_issue')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('id', $request->issue_id)
                ->first();

        if ($ProductIssue->total_qty > 1) {

            $update_qty = $ProductIssue->total_qty - 1;
            $update_amount = $ProductIssue->total_amount - $request->amount;
            $productIssue = ProductIssue::find($request->issue_id);

            $productIssue->update([
                'total_qty' => $update_qty,
                'total_amount' => $update_amount
            ]);
        } else {
            ProductIssue::where('id', $request->issue_id)->delete();
        }

        return redirect(route('salesReturn.index'))->with('msg', 'Product Returned Successfully');
    }

    public function saveMultiple(Request $request) {

        $totalReturnAmount = 0;

        foreach ($request->ids as $id) {

            $issueList = ProductIssueList::findOrFail($id);
            $issue = ProductIssue::findOrFail($issueList->issue_id);
            if (!empty($issueList->collection) && $issueList->collection > 0) {
                $totalReturnAmount += $issueList->collection;
            }
            $dealer_id = $issue->dealer_id;

            if ($issue->product_type == 'warranty_product') {
                $salesReturn = SalesReturn::create([
                            'company_id' => $this->company,
                            'showroom_id' => $this->showroomId,
                            'requisition_id' => $request->requisition_id,
                            'dealer_id' => $issue->dealer_id,
                            'sales_by' => $issue->sales_by,
                            'product_type' => $issue->product_type,
                            'issue_type' => $request->issueType,
                            'issue_no' => $request->productIssueNo,
                            'issue_id' => $issue->id,
                            'product_id' => $issueList->product_id,
                            'model_no' => $issueList->model_no,
                            'serial_no' => $issueList->serial_no,
                            'commission_rate' => $issueList->commission_rate,
                            'offer' => $issueList->offer,
                            'price' => $issueList->price,
                            'qty' => 1,
                            'amount' => $issueList->amount,
                            'return_date' => date('Y-m-d', strtotime($request->return_date)),
                            'reason' => $request->reason
                ]);
            } else {
                $salesReturn = SalesReturn::create([
                            'company_id' => $this->company,
                            'showroom_id' => $this->showroomId,
                            'requisition_id' => $request->requisition_id,
                            'dealer_id' => $issue->dealer_id,
                            'sales_by' => $issue->sales_by,
                            'product_type' => $issue->product_type,
                            'issue_type' => $request->issueType,
                            'issue_no' => $request->productIssueNo,
                            'issue_id' => $issue->id,
                            'product_id' => $issueList->product_id,
                            'model_no' => $issueList->model_no,
                            'commission_rate' => $issueList->commission_rate,
                            'price' => $issueList->price,
                            'qty' => $issueList->qty,
                            'amount' => $issueList->amount,
                            'return_date' => date('Y-m-d', strtotime($request->return_date)),
                            'reason' => $request->reason
                ]);
            }





            $productIssueList = ProductIssueList::find($id);
            $productIssueList->update([
                'isReturn' => 1
            ]);

            $ProductIssue = DB::table('tbl_product_issue')
                    ->where('company_id', $this->company)
//                    ->where('showroom_id', $this->showroomId)
                    ->where('id', $issue->id)
                    ->first();
            $productIssue = ProductIssue::find($ProductIssue->id);

            $productIssue->update([
                'isReturn' => 1
            ]);
        }
        $lastDealerCollectionId = DealerCollection::max('id');
        if (@$lastDealerCollectionId) {
            $paymentNo = 100000000 + $lastDealerCollectionId + 1;
        } else {
            $paymentNo = 100000000 + 1;
        }

        if ($totalReturnAmount > 0) {
            AdvanceCollection::create([
                'company_id' => $this->company,
                'showroom_id' => $this->showroomId,
                'date' => date('Y-m-d', strtotime($request->return_date)),
                'dealer_id' => $dealer_id,
                'payment_no' => $paymentNo,
                'return_amount' => $totalReturnAmount,
                'added_by' => Auth::user()->id,
                'remarks' => "Advance From Return",
                'money_receipt' => "from return",
                'type' => "advance",
            ]);
        }

        return redirect(route('salesReturn.index'))->with('msg', 'Product Returned Successfully');
    }

    public function edit($id) {
        $title = "Sales Return Edit";
        $formLink = "salesReturn.update";
        $buttonName = "Update";
        $showroomId = $this->showroomId;
        $allSalesReturn = DB::table('tbl_sales_return')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('issue_no', $id)
                ->get();
        $singleSalesReturn = DB::table('tbl_sales_return')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('issue_no', $id)
                ->first();

        $dealer = DB::table('tbl_dealers')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('id', $singleSalesReturn->dealer_id)
                ->first();


        return view('admin.salesReturn.edit')->with(compact('title', 'formLink', 'showroomId', 'buttonName', 'allSalesReturn', 'singleSalesReturn', 'dealer'));
    }

    public function update(Request $request) {
        $salesReturn = DB::table('tbl_sales_return')
                ->where('issue_no', $request->issue_no)
                ->get();

        foreach ($salesReturn as $data) {
            $salesReturn = SalesReturn::find($data->id);
            $salesReturn->update([
                'return_date' => date('Y-m-d', strtotime($request->return_date)),
                'reason' => $request->reason
            ]);
        }

        return redirect(route('salesReturnHistory.index'))->with('msg', 'Sales Return Updated Successfully');
    }

    public function salesExchange($id) {
        $title = "Sales Exchange";
        $showroomId = $this->showroomId;
        $sale = DB::table('tbl_product_issue_lists')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('id', $id)
                ->first();
        return view('admin.salesReturn.exchange')->with(compact('title', 'sale', 'showroomId'));
    }

    public function delete(Request $request) {
        $returnNo = $request->dealerCollectionId;

        $returns = SalesReturn::where('issue_no', $returnNo)->get();

        foreach ($returns as $return) {

            $productissue = ProductIssue::where('id', $return->issue_id)->first();

            $issueId = $productissue->id;

            $productissue->update([
                'total_qty' => $productissue->total_qty + $return->qty,
            ]);

            ProductIssueList::create([
                'company_id' => $this->company,
                'showroom_id' => $return->showroom_id,
                'issue_id' => $issueId,
                'product_id' => $return->product_id,
                'model_no' => $return->model_no,
                'serial_no' => $return->serial_no,
                'commission_rate' => $return->commission_rate,
                'offer' => $return->offer,
                'price' => $return->price,
                'qty' => $return->qty,
                'amount' => $return->amount,
                'created_by' => $this->userId
            ]);

            $return->delete();
        }

        // return $returns;
    }

}
