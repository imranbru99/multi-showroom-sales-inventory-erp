<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use MPDF;
use App\CoaSetup;
use App\StaffSetup;
use App\DealerSetup;
use App\ProductIssue;
use App\DealerCollection;
use App\ProductIssueList;
use App\AdvanceCollection;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DealerCollectionController extends Controller {

    public function index() {
        $title = "Dealer Collection";

        if ($this->userRole == 1) {
            $dealerCollections = DealerCollection::select('tbl_dealer_collections.*', 'tbl_dealers.name as dealerName', 'tbl_dealers.mobile as dealerMobile')
                    ->with('issueList', 'user')
                    ->leftJoin('tbl_dealers', 'tbl_dealers.id', '=', 'tbl_dealer_collections.dealer_id')
                    ->where('tbl_dealer_collections.adjustment', '!=', 1)
                    ->orderBy('tbl_dealer_collections.id', 'dsc')
                    ->get();

            $advanceCollections = AdvanceCollection::all();
        } else {
            $dealerCollections = DealerCollection::select('tbl_dealer_collections.*', 'tbl_dealers.name as dealerName', 'tbl_dealers.mobile as dealerMobile')
                    ->with('issueList', 'user')
                    ->leftJoin('tbl_dealers', 'tbl_dealers.id', '=', 'tbl_dealer_collections.dealer_id')
                    ->where('tbl_dealer_collections.company_id', $this->company)
//                    ->where('tbl_dealer_collections.showroom_id', $this->showroomId)
                    ->where('tbl_dealer_collections.adjustment', '!=', 1)
                    ->orderBy('tbl_dealer_collections.id', 'dsc')
                    ->get();

            $advanceCollections = AdvanceCollection::where('company_id', $this->company)
//                    ->where('showroom_id', $this->showroomId)
                    ->get();
        }

        return view('admin.dealerCollection.index')->with(compact('title', 'dealerCollections', 'advanceCollections'));
    }

    public function add() {
        $title = "Add Dealer Collection";
        $formLink = "dealerCollection.save";
        $buttonName = "Save";

        $dealers = DealerSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        $banks = CoaSetup::where('head_code', 'like', '10202%')
                ->where('company_id', $this->company)
                ->where('transaction', 1)
                ->get();

        $staffs = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get();

        return view('admin.dealerCollection.add')->with(compact('title', 'formLink', 'buttonName', 'dealers', 'banks', 'staffs'));
    }

    public function save(Request $request) {
        if ($request->payment_type != 'advance') {
            if (!$request->id) {
                return back()->with('fail_msg', 'Please select Product');
            }
        }

        $lastDealerCollectionId = DealerCollection::max('id');
        if ($lastDealerCollectionId) {
            $paymentNo = 100000000 + $lastDealerCollectionId + 1;
        } else {
            $paymentNo = 100000000 + 1;
        }

        $paymentDate = date('Y-m-d', strtotime($request->paymentDate));

        // advance payment
        if ($request->payment_type == 'advance') {
            AdvanceCollection::create([
                'company_id' => $this->company,
                'showroom_id' => $this->showroomId,
                'date' => $paymentDate,
                'dealer_id' => $request->dealer,
                'sale_by' => $request->sale_by,
                'payment_no' => $paymentNo,
                'advance_amount' => $request->payment_amount,
                'added_by' => Auth::user()->id,
                'remarks' => $request->remarks,
                'money_receipt' => $request->moneyReceiptNo,
                'type' => $request->moneyReceiptType,
            ]);

            return redirect(route('dealerCollection.index'))->with('msg', 'Dealer Collection Added Successfully');
        }


        // advance payment
        if ($request->payment_type == 'adjust') {

            $totalCollection = 0;

            foreach ($request->id as $i) {
                $totalCollection += $request->collection[$i];
                $collection = $request->collection[$i];

                $issue = productIssueList::findOrFail($i);
                $prevCollection = $issue->collection;
                $newCollection = $prevCollection + $collection;

                $issue->collection = (string) $newCollection;
                $issue->save();
            }

            $totalCollection = (string) $totalCollection;

            AdvanceCollection::create([
                'company_id' => $this->company,
                'showroom_id' => $this->showroomId,
                'date' => $paymentDate,
                'dealer_id' => $request->dealer,
                'sale_by' => $request->sale_by,
                'payment_no' => $request->paymentNo,
                'adjust_amount' => $totalCollection,
                'added_by' => Auth::user()->id,
                'remarks' => $request->remarks,
                'money_receipt' => $request->moneyReceiptNo,
                'type' => $request->moneyReceiptType,
            ]);


            $dCollection = DealerCollection::create([
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'product_issue_id' => $request->productIssue,
                        'dealer_id' => $request->dealer,
                        'sale_by' => $request->sale_by,
                        'payment_no' => $request->paymentNo,
                        'payment_date' => $paymentDate,
                        'money_receipt_no' => $request->moneyReceiptNo,
                        'money_receipt_type' => $request->moneyReceiptType,
                        'payment_amount' => $totalCollection,
                        'note' => $request->note,
                        'bank' => $request->bank,
                        'remarks' => $request->remarks,
                        'adjustment' => 1,
                        'created_by' => $this->userId,
            ]);

            foreach ($request->id as $i) {
                $issue = productIssueList::findOrFail($i);
                $issue->collection_id = $dCollection->id;
                $issue->save();
            }

            return redirect(route('dealerCollection.index'))->with('msg', 'Dealer Collection Added Successfully');
        }


        if ($request->payment_type == 'collection') {

            $totalCollection = 0;

            foreach ($request->id as $i) {
                $collection = $request->collection[$i];

                $totalCollection += $request->collection[$i];

                $issue = productIssueList::findOrFail($i);
                $prevCollection = $issue->collection;
                $newCollection = $prevCollection + $collection;

                $issue->collection = (string) $newCollection;
                $issue->save();
            }

            // dd($totalCollection);


            $dealerCollection = DealerCollection::create([
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'product_issue_id' => $request->productIssue,
                        'dealer_id' => $request->dealer,
                        'sale_by' => $request->sale_by,
                        'payment_no' => $request->paymentNo,
                        'payment_date' => $paymentDate,
                        'money_receipt_no' => $request->moneyReceiptNo,
                        'money_receipt_type' => $request->moneyReceiptType,
                        'payment_amount' => (string) $totalCollection,
                        'note' => $request->note,
                        'bank' => $request->bank,
                        'remarks' => $request->remarks,
                        'created_by' => $this->userId
            ]);

            foreach ($request->id as $i) {
                $issue = productIssueList::findOrFail($i);
                $issue->collection_id = $dealerCollection->id;
                $issue->save();
            }

            $advanceAmount = (float) $request->payment_amount - (float) $totalCollection;

            if ($totalCollection < $request->payment_amount) {
                $advanceCollection = AdvanceCollection::create([
                            'company_id' => $this->company,
                            'showroom_id' => $this->showroomId,
                            'date' => $paymentDate,
                            'dealer_id' => $request->dealer,
                            'sale_by' => $request->sale_by,
                            'payment_no' => $request->paymentNo,
                            'advance_amount' => (string) $advanceAmount,
                            'added_by' => Auth::user()->id,
                            'remarks' => "Advance From Collection",
                            'money_receipt' => $request->moneyReceiptNo,
                            'type' => $request->moneyReceiptType,
                ]);

                $dealerCollection->advance_id = $advanceCollection->id;
                $dealerCollection->remarks = "Advance From Collection";
                $dealerCollection->save();
            }

            return redirect(route('dealerCollection.index'))->with('msg', 'Dealer Collection Added Successfully');
        }
    }

    public function edit($dealerCollectionId) {
        $title = "Edit Dealer Collection";
        $formLink = "dealerCollection.update";
        $buttonName = "Update";

        $dealers = DealerSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        $banks = CoaSetup::where('head_code', 'like', '10202%')
                ->where('company_id', $this->company)
                ->where('transaction', 1)
                ->get();

        $staffs = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get();

        $dealerCollection = DealerCollection::with('issueList.product')->where('id', $dealerCollectionId)->first();
        // $allIssuedByDealer = DealerSetup::where('id', $dealerCollection->dealer_id)
        //     ->with(['issue.products.product'])
        //     ->first();

        $allIssuedByDealer = [];


        $dealerSales = ProductIssueList::with('issue')
                ->whereHas('issue', function ($q) use ($dealerCollection) {
                    $q->where('dealer_id', $dealerCollection->dealer_id);
                })
                ->where('isReturn', '==', 1)
                ->get();


        $allProducts = [];
        foreach ($dealerSales as $product) {
            $productInfo = Product::where('id', $product->product_id)->first();
            $allProducts[] = [
                'issue_no' => $product->issue->issue_no,
                'productName' => $productInfo->name,
                'productModel' => $productInfo->model_no,
                'saleAmount' => $product->amount,
                'saleListId' => $product->id,
                'collection' => $product->collection,
                'collection_id' => $product->collection_id,
            ];
        }


        return view('admin.dealerCollection.edit')->with(compact('title', 'formLink', 'buttonName', 'dealers', 'banks', 'dealerCollection', 'allIssuedByDealer', 'staffs', 'allProducts'));
    }

    public function update(Request $request) {
        // dd($request->all());
        // clean up start
        $collection = DealerCollection::findOrFail($request->collection_id);
        $collectionProducts = ProductIssueList::where('collection_id', $collection->id)->get();

        foreach ($collectionProducts as $cp) {
            $cp->update([
                'collection' => 0,
                'collection_id' => 0,
            ]);
        }
        // clean up end

        $paymentDate = date('Y-m-d', strtotime($request->paymentDate));

        // collection start
        if ($request->payment_type == 'collection') {

            $totalCollection = 0;

            foreach ($request->id as $i) {
                $pcollection = $request->collection[$i];

                $totalCollection += $request->collection[$i];

                $issue = productIssueList::findOrFail($i);
                $newCollection = $pcollection;

                $issue->collection = (string) $newCollection;
                $issue->save();
            }

            // dd($totalCollection);

            $collection->update([
//                'company_id' => $this->company,
//                'showroom_id' => $this->showroomId,
                'product_issue_id' => $request->productIssue,
                'sale_by' => $request->sale_by,
                'dealer_id' => $request->dealer,
                'payment_no' => $request->paymentNo,
                'payment_date' => $paymentDate,
                'money_receipt_no' => $request->moneyReceiptNo,
                'money_receipt_type' => $request->moneyReceiptType,
                'payment_amount' => (string) $totalCollection,
                'note' => $request->note,
                'bank' => $request->bank,
                'remarks' => $request->remarks,
                'created_by' => $this->userId
            ]);

            foreach ($request->id as $i) {
                $issue = productIssueList::findOrFail($i);
                $issue->collection_id = $collection->id;
                $issue->save();
            }

            $advanceAmount = (float) $request->payment_amount - (float) $totalCollection;

            if ($totalCollection < $request->payment_amount) {
                $advanceCollection = AdvanceCollection::create([
                            'company_id' => $this->company,
                            'showroom_id' => $this->showroomId,
                            'date' => $paymentDate,
                            'dealer_id' => $request->dealer,
                            'sale_by' => $request->sale_by,
                            'payment_no' => $request->paymentNo,
                            'advance_amount' => (string) $advanceAmount,
                            'added_by' => Auth::user()->id,
                            'remarks' => "Advance From Collection",
                            'money_receipt' => $request->moneyReceiptNo,
                            'type' => $request->moneyReceiptType,
                ]);

                $collection->advance_id = $advanceCollection->id;
                $collection->remarks = "Advance From Collection";
                $collection->save();
            }
        }
        // collection end

        return redirect(route('dealerCollection.index'))->with('msg', 'Dealer Collection Updated Successfully');
    }

    public function printMoneyReceipt($dealerCollectionId) {
        $title = "Money Receipt";

        $dealerCollection = DealerCollection::where('id', $dealerCollectionId)->with(['dealer'])->first();
        $dealer = $dealerCollection->dealer;

        $dealerTotalSale = ProductIssue::where('dealer_id', $dealer->id)->sum('total_amount');
        $dealerTotalCollection = DealerCollection::where('dealer_id', $dealer->id)->sum('payment_amount');
        $dealerTotalAdvance = AdvanceCollection::where('dealer_id', $dealer->id)->sum('advance_amount');
        // dd($dealerTotalAdvance);
        $totalCollection = $dealerTotalCollection + $dealerTotalAdvance;
        $due = $dealerTotalSale - $totalCollection;

        // $dealerCollection = DealerCollection::select('tbl_dealer_collections.*', 'tbl_dealers.name as dealerName', 'tbl_product_issue.issue_no as productIssueNo')
        //     ->leftJoin('tbl_dealers', 'tbl_dealers.id', '=', 'tbl_dealer_collections.dealer_id')
        //     ->leftJoin('tbl_product_issue', 'tbl_product_issue.id', '=', 'tbl_dealer_collections.product_issue_id')
        //     ->where('tbl_dealer_collections.id', $dealerCollectionId)
        //     ->where('tbl_dealer_collections.status', '1')
        //     ->first();
        // $productIssueList = ProductIssue::where('id', $dealerCollection->product_issue_id)->first();
        // $previousDealerCollection = DealerCollection::select(DB::raw('SUM(payment_amount) as previousCollection'))
        //     ->where('showroom_id', $this->showroomId)
        //     ->where('product_issue_id', $dealerCollection->product_issue_id)
        //     ->first();

        $pdf = PDF::loadView('admin.dealerCollection.print', ['title' => $title, 'dealerCollection' => $dealerCollection, 'due' => $due]);

        return $pdf->stream('dealer_collection_money_receipt.pdf');
    }

    public function getProductIssueInfo(Request $request) {
        $productIssues = ProductIssue::where('company_id', $this->company)
//                        ->where('showroom_id', $this->showroomId)
                        ->where('dealer_id', $request->dealerId)
                        ->where('status', '1')->get();

        $output = "";

        if ($productIssues) {
            $output .= '<select class="form-control chosen-select" id="productIssue" name="productIssue">';
            $output .= '<option value="">Select Product Issue No.</option>';
            foreach ($productIssues as $productIssue) {
                $output .= '<option value="' . $productIssue->id . '">' . $productIssue->issue_no . '</option>';
            }
            $output .= '</select>';
        } else {
            $output .= '<select class="form-control chosen-select" id="productIssue" name="productIssue">';
            $output .= '<option value="">Select Product Issue No.</option>';
            $output .= '</select>';
        }

        echo $output;
    }

    public function getDealerInfo(Request $request) {
        $productIssueList = ProductIssue::where('id', $request->productIssueId)->first();
        $dealerCollection = DealerCollection::select(DB::raw('SUM(payment_amount) as previousCollection'))
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('product_issue_id', $request->productIssueId)
                ->first();

        if ($request->ajax()) {
            return response()->json([
                        'productIssueList' => $productIssueList,
                        'dealerCollection' => $dealerCollection
            ]);
        }
    }

    public function getDealerStaffs(Request $request) {
        $dealerId = $request->dealerId;

        $staff_ids = ProductIssue::where('dealer_id', $dealerId)->select('sales_by')->groupBy('sales_by')->get()->pluck('sales_by');

        $staffs = StaffSetup::whereIn('id', $staff_ids)->get();

        return $staffs;
    }

    public function delete(Request $request) {
        $dealerCollection = DealerCollection::with('issueList')->where('id', $request->dealerCollectionId)->first();

        foreach ($dealerCollection->issueList as $issueList) {
            $issueList->update([
                'collection' => 0,
                'collection_id' => 0,
            ]);
        }

        if ($dealerCollection->advance_id > 0) {
            $collectionAdvance = AdvanceCollection::find($dealerCollection->advance_id);
            if ($collectionAdvance) {
                $collectionAdvance->delete();
            }
        }

        $dealerCollection->delete();
    }

    public function AdvanceDelete($id) {
        $advance = AdvanceCollection::findOrFail($id);
        $advance->delete();

        return back();
    }

    public function getAllProductInfo(Request $request) {
        $dealerSales = ProductIssueList::with('issue')
                ->whereHas('issue', function ($q) use ($request) {
                    $q->where('dealer_id', $request->dealerId);
                })
                ->where('isReturn', '!=', 1)
                ->get();


        $allProducts = [];
        foreach ($dealerSales as $product) {
            $productInfo = Product::where('id', $product->product_id)->first();
            $allProducts[] = [
                'issue_no' => $product->issue->issue_no,
                'productName' => $productInfo->name,
                'productModel' => $productInfo->model_no,
                'saleAmount' => $product->amount,
                'saleListId' => $product->id,
                'collection' => $product->collection,
                'collection_id' => $product->collection_id,
            ];
        }

        return $allProducts;
    }

    public function getAdjustAmount(Request $request) {
        $dealer_data = AdvanceCollection::where('dealer_id', $request->dealer_id)->get();

        $advance = $dealer_data->sum('advance_amount');
        $adjust = $dealer_data->sum('adjust_amount');

        $total = $advance - $adjust;

        return $total;
    }

}
