<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use MPDF;
use Carbon\Carbon;
use App\DealerSetup;
use App\SalesReturn;
use App\ProductIssue;
use Carbon\CarbonPeriod;
use App\DealerCollection;
use App\AdvanceCollection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DealerStatementController extends Controller {

    public function getDealerPreviousBalance($lastDate, $dealer) {
        $issues = ProductIssue::where('date', '<=', $lastDate)
                ->where('dealer_id', $dealer)
                ->get();

        $dcollections = DealerCollection::where('payment_date', '<=', $lastDate)
                ->where('dealer_id', $dealer)
                ->where('remarks', '!=', 'adjust')
                ->get();

        $advances = AdvanceCollection::where('date', '<=', $lastDate)
                ->where('dealer_id', $dealer)
                ->get();

        $discounts = AdvanceCollection::where('date', '<=', $lastDate)
                ->where('discount_amount', '>', 0)
                ->where('dealer_id', $dealer)
                ->get();

        $returns = SalesReturn::where('return_date', '<=', $lastDate)
                ->where('dealer_id', $dealer)
                ->sum('amount');

        $sale = $issues->sum('total_amount');
        $dealerCollection = $dcollections->sum('payment_amount');
        $totaladvance = $advances->sum('advance_amount');
        // $totalAdjust = $advances->sum('adjust_amount');
        $totalDiscount = $discounts->sum('discount_amount');


        $total = $sale - ($dealerCollection + $totaladvance + $totalDiscount + $returns);

        return $total;

        // dd( $dealerCollection);
    }

    public function index(Request $request) {
        $title = "Dealer Statement";
        $searchFormLink = "dealerStatement.index";
        $printFormLink = "dealerStatement.print";

        $dealer = $request->dealer;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;

        $fromDatee = $request->fromDate;
        $toDatee = $request->toDate;

        // dd($fromDate);

        $lastDate = Date('Y-m-d', strtotime("-1 day", strtotime($fromDate)));
        $dealers = DealerSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        $dates = CarbonPeriod::create($fromDate, $toDate)->toArray();

        $data = [];

        $totalsale = 0.00;
        $totalcollection = 0.00;
        $totaldiscount = 0.00;
        $balance = 0.00;

        $previousBalance = number_format($this->getDealerPreviousBalance($lastDate, $dealer), 2, '.', '');

        foreach ($dates as $date) {

            $lineData = [
                'date' => $date->format('Y-m-d'),
            ];
            $advances = AdvanceCollection::where('date', $date->format('Y-m-d'))->where('advance_amount', '>', 0)->where('dealer_id', $dealer)->get();

            $advanceCollection = 0.00;

            foreach ($advances as $advance) {

                if ($advance->remarks == "Advance From Collection") {
                    continue;
                }


                if ($advance->advance_amount > 0) {

                    $totalcollection += $advance->advance_amount;
                    $advanceCollection += $advance->advance_amount;
                    $balance -= $advance->advance_amount;

                    $ld = $lineData;
                    $ld['invoice_no'] = $advance->payment_no;
                    $ld['sale'] = 0.00;
                    $ld['advance'] = number_format($advance->advance_amount, 2, '.', '');
                    $ld['collection'] = 0.00;
                    $ld['discount'] = 0.00;
                    $ld['balance'] = number_format($balance, 2, '.', '');
                    $ld['return'] = 0.00;
                    $ld['remark'] = "Advance Deposit";
                    array_push($data, $ld);
                }
            }

            $discounts = AdvanceCollection::where('date', $date->format('Y-m-d'))->where('discount_amount', '>', 0)->where('dealer_id', $dealer)->get();

            foreach ($discounts as $discount) {

                if ($discount->discount_amount > 0) {

                    $totalcollection += 0;
                    $advanceCollection += 0;
                    $totaldiscount += $discount->discount_amount;
                    $balance -= $discount->discount_amount;

                    $ld = $lineData;
                    $ld['invoice_no'] = $discount->payment_no;
                    $ld['sale'] = 0.00;
                    $ld['advance'] = 0.00;
                    $ld['collection'] = 0.00;
                    $ld['discount'] = number_format($discount->discount_amount, 2, '.', '');
                    $ld['balance'] = number_format($balance, 2, '.', '');
                    $ld['return'] = 0.00;
                    $ld['remark'] = "Discount";
                    array_push($data, $ld);
                }
            }


            $issues = ProductIssue::where('date', $date->format('Y-m-d'))->where('dealer_id', $dealer)->get();

            foreach ($issues as $issue) {
                $totalsale += $issue->total_amount;
                $balance += $issue->total_amount;

                $ld = $lineData;
                $ld['invoice_no'] = $issue->issue_no;
                $ld['sale'] = number_format($issue->total_amount, 2, '.', '');
                $ld['advance'] = 0.00;
                $ld['collection'] = 0.00;
                $ld['discount'] = 0.00;
                $ld['balance'] = number_format($balance, 2, '.', '');
                $ld['return'] = 0.00;
                $ld['remark'] = 'Product Sale';

                array_push($data, $ld);
            }


            $returns = SalesReturn::where('return_date', $date->format('Y-m-d'))
                    ->where('dealer_id', $dealer)
                    ->sum('amount');


            $return = SalesReturn::where('return_date', $date->format('Y-m-d'))
                    ->where('dealer_id', $dealer)
                    ->first();

            if ($returns > 0) {

                $balance -= $returns;

                $ld = $lineData;
                $ld['invoice_no'] = $return->issue_no;
                $ld['sale'] = 0.00;
                $ld['advance'] = 0.00;
                $ld['collection'] = 0.00;
                $ld['discount'] = 0.00;
                $ld['balance'] = number_format($balance, 2, '.', '');
                $ld['return'] = number_format($returns, 2, '.', '');
                $ld['remark'] = "Return Amount";
                array_push($data, $ld);
            }

            // adjustment
            $adjustments = AdvanceCollection::where('date', $date->format('Y-m-d'))->where('adjust_amount', '>', 0)->where('dealer_id', $dealer)->get();

            foreach ($adjustments as $adjustment) {

                //                $totalcollection += $adjustment->adjust_amount;
                //                $advance = $adjustment->adjust_amount - $advanceCollection;

                $ld = $lineData;
                $ld['invoice_no'] = $adjustment->payment_no;
                $ld['sale'] = 0.00;
                $ld['advance'] = 0.00;
                $ld['collection'] = number_format($adjustment->adjust_amount, 2, '.', '');
                $ld['discount'] = 0.00;
                $ld['balance'] = number_format($balance, 2, '.', '');
                $ld['return'] = 0.00;
                $ld['remark'] = "Collection against advance";

                array_push($data, $ld);
            }

            $dcollections = DealerCollection::where('payment_date', $date->format('Y-m-d'))->where('adjustment', '!=', 1)->where('dealer_id', $dealer)->get();

            foreach ($dcollections as $collection) {

                $advance = 0.00;
                $remark = "Collection";

                if ($collection->remarks == "Advance From Collection") {
                    $advance = AdvanceCollection::find($collection->advance_id);
                    if ($advance) {
                        $advance = $advance->advance_amount;
                        $totalDeposit = $advance + $collection->payment_amount;
                        $remark = "Collection (" . $totalDeposit . " Taka)";
                    } else {
                        $advance = 0;
                        $totalDeposit = $advance + $collection->payment_amount;
                        $remark = "Collection (" . $totalDeposit . " Taka)";
                    }
                }

                $totalcollection += $collection->payment_amount;
                $balance -= $collection->payment_amount + $advance;

                $ld = $lineData;
                $ld['invoice_no'] = $collection->payment_no;
                $ld['sale'] = 0.00;
                $ld['advance'] = number_format($advance, 2, '.', '');
                $ld['collection'] = number_format($collection->payment_amount, 2, '.', '');
                $ld['discount'] = 0.00;
                $ld['balance'] = number_format($balance, 2, '.', '');
                $ld['return'] = 0.00;
                $ld['remark'] = $remark;


                array_push($data, $ld);
            }
        }


        return view('admin.dealerStatement.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'dealers', 'dealer', 'fromDate', 'toDate', 'fromDatee', 'toDatee', 'data', 'previousBalance'));
    }

    public function print(Request $request) {
        $title = "Print Dealer Statement";
        $dealer = $request->dealer;
        $delearName = DB::table('tbl_dealers')->where('id', $dealer)->first();
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $lastDate = Date('Y-m-d', strtotime("-1 day", strtotime($fromDate)));
        $dates = CarbonPeriod::create($fromDate, $toDate)->toArray();

        $data = [];

        $totalsale = 0.00;
        $totalcollection = 0.00;
        $totaldiscount = 0.00;
        $balance = 0.00;

        $previousBalance = number_format($this->getDealerPreviousBalance($lastDate, $dealer), 2, '.', '');

        foreach ($dates as $date) {

            $lineData = [
                'date' => $date->format('Y-m-d'),
            ];
            $advances = AdvanceCollection::where('date', $date->format('Y-m-d'))->where('advance_amount', '>', 0)->where('dealer_id', $dealer)->get();

            $advanceCollection = 0.00;

            foreach ($advances as $advance) {

                if ($advance->remarks == "Advance From Collection") {
                    continue;
                }


                if ($advance->advance_amount > 0) {

                    $totalcollection += $advance->advance_amount;
                    $advanceCollection += $advance->advance_amount;
                    $balance -= $advance->advance_amount;

                    $ld = $lineData;
                    $ld['invoice_no'] = $advance->payment_no;
                    $ld['sale'] = 0.00;
                    $ld['advance'] = number_format($advance->advance_amount, 2, '.', '');
                    $ld['collection'] = 0.00;
                    $ld['discount'] = 0.00;
                    $ld['balance'] = number_format($balance, 2, '.', '');
                    $ld['return'] = 0.00;
                    $ld['remark'] = "Advance Deposit";
                    array_push($data, $ld);
                }
            }

            $discounts = AdvanceCollection::where('date', $date->format('Y-m-d'))->where('discount_amount', '>', 0)->where('dealer_id', $dealer)->get();

            foreach ($discounts as $discount) {

                if ($discount->discount_amount > 0) {

                    $totalcollection += 0;
                    $advanceCollection += 0;
                    $totaldiscount += $discount->discount_amount;
                    $balance -= $discount->discount_amount;

                    $ld = $lineData;
                    $ld['invoice_no'] = $discount->payment_no;
                    $ld['sale'] = 0.00;
                    $ld['advance'] = 0.00;
                    $ld['collection'] = 0.00;
                    $ld['discount'] = number_format($discount->discount_amount, 2, '.', '');
                    $ld['balance'] = number_format($balance, 2, '.', '');
                    $ld['return'] = 0.00;
                    $ld['remark'] = "Discount";
                    array_push($data, $ld);
                }
            }


            $issues = ProductIssue::where('date', $date->format('Y-m-d'))->where('dealer_id', $dealer)->get();

            foreach ($issues as $issue) {
                $totalsale += $issue->total_amount;
                $balance += $issue->total_amount;

                $ld = $lineData;
                $ld['invoice_no'] = $issue->issue_no;
                $ld['sale'] = number_format($issue->total_amount, 2, '.', '');
                $ld['advance'] = 0.00;
                $ld['collection'] = 0.00;
                $ld['discount'] = 0.00;
                $ld['balance'] = number_format($balance, 2, '.', '');
                $ld['return'] = 0.00;
                $ld['remark'] = 'Product Sale';

                array_push($data, $ld);
            }


            $returns = SalesReturn::where('return_date', $date->format('Y-m-d'))
                    ->where('dealer_id', $dealer)
                    ->sum('amount');


            $return = SalesReturn::where('return_date', $date->format('Y-m-d'))
                    ->where('dealer_id', $dealer)
                    ->first();

            if ($returns > 0) {

                $balance -= $returns;

                $ld = $lineData;
                $ld['invoice_no'] = $return->issue_no;
                $ld['sale'] = 0.00;
                $ld['advance'] = 0.00;
                $ld['collection'] = 0.00;
                $ld['discount'] = 0.00;
                $ld['balance'] = number_format($balance, 2, '.', '');
                $ld['return'] = number_format($returns, 2, '.', '');
                $ld['remark'] = "Return Amount";
                array_push($data, $ld);
            }

            // adjustment
            $adjustments = AdvanceCollection::where('date', $date->format('Y-m-d'))->where('adjust_amount', '>', 0)->where('dealer_id', $dealer)->get();

            foreach ($adjustments as $adjustment) {

                //                $totalcollection += $adjustment->adjust_amount;
                //                $advance = $adjustment->adjust_amount - $advanceCollection;

                $ld = $lineData;
                $ld['invoice_no'] = $adjustment->payment_no;
                $ld['sale'] = 0.00;
                $ld['advance'] = 0.00;
                $ld['collection'] = number_format($adjustment->adjust_amount, 2, '.', '');
                $ld['discount'] = 0.00;
                $ld['balance'] = number_format($balance, 2, '.', '');
                $ld['return'] = 0.00;
                $ld['remark'] = "Collection against advance";

                array_push($data, $ld);
            }

            $dcollections = DealerCollection::where('payment_date', $date->format('Y-m-d'))->where('adjustment', '!=', 1)->where('dealer_id', $dealer)->get();

            foreach ($dcollections as $collection) {

                $advance = 0.00;
                $remark = "Collection";

                if ($collection->remarks == "Advance From Collection") {
                    $advance = AdvanceCollection::find($collection->advance_id);
                    if ($advance) {
                        $advance = $advance->advance_amount;
                        $totalDeposit = $advance + $collection->payment_amount;
                        $remark = "Collection (" . $totalDeposit . " Taka)";
                    } else {
                        $advance = 0;
                        $totalDeposit = $advance + $collection->payment_amount;
                        $remark = "Collection (" . $totalDeposit . " Taka)";
                    }
                }

                $totalcollection += $collection->payment_amount;
                $balance -= $collection->payment_amount + $advance;

                $ld = $lineData;
                $ld['invoice_no'] = $collection->payment_no;
                $ld['sale'] = 0.00;
                $ld['advance'] = number_format($advance, 2, '.', '');
                $ld['collection'] = number_format($collection->payment_amount, 2, '.', '');
                $ld['discount'] = 0.00;
                $ld['balance'] = number_format($balance, 2, '.', '');
                $ld['return'] = 0.00;
                $ld['remark'] = $remark;


                array_push($data, $ld);
            }
        }

        $pdf = PDF::loadView('admin.dealerStatement.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'previousBalance' => $previousBalance, 'data' => $data, 'delearName' => $delearName]);

        return $pdf->stream('dealer_statement_' . $fromDate . '_to_' . $toDate . '.pdf');
    }

}
