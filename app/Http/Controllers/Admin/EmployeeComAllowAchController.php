<?php

namespace App\Http\Controllers\Admin;

use App\AdvanceCollection;
use App\CommissionPercentage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\StaffSetup;
use App\DealerCollection;
use App\EmployeeCommissionAllocation;
use App\EmployeeCommissionAllocationList;
use App\InstallmentCollectionList;
use App\Product;
use App\RetailSales;
use App\ShowroomSetup;
use App\GroupSalesTarget;
use DB;
use PDF;

class EmployeeComAllowAchController extends Controller
{

    public function index(Request $request)
    {
        $title = "Dealer & Retail";
        $searchFormLink = "employeeComAch.index";
        $printFormLink = "employeeComAch.print";
        $employee = $request->staff;
        $print = $request->print;
        $month = $request->month;

        $showroomId = $request->showroom_id;
        $showromid = @$this->showroomId;

        $fromDate = date('Y-m-d', strtotime($request->month . '-01'));
        $toDate = date('Y-m-t', strtotime($fromDate));

        $showrooms = ShowroomSetup::get();

        $staffs = StaffSetup::where('status', 1)->get();

        $targets = EmployeeCommissionAllocationList::with('allocation')
            ->whereHas('allocation', function ($query) use ($request) {
                $query->where('month', $request->month);
            })
            ->where(function ($query) use ($employee) {
                if ($employee) {
                    $query->whereIn('staff_id', $employee);
                }
            })
            ->where('showroom_id', $showroomId)
            ->orderBy('id', 'asc')
            ->get();


        $data = [];
        if ($request->has('print')) {
            $data = $this->getData($fromDate, $toDate, $targets, $showroomId, $month);
        }

        return view('admin.employeeComAch.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'staffs', 'data', 'employee', 'fromDate', 'toDate', 'showrooms', 'showroomId', 'month', 'showromid'));
    }

    public function print(Request $request)
    {
        $title = "Dealer & Retail Report";
        $employee = $request->employee;
        $print = $request->print;
        $month = $request->month;
        $showroomId = $request->showroom_id;
        $fromDate = date('Y-m-d', strtotime($request->month . '-01'));
        $toDate = date('Y-m-t', strtotime($fromDate));

        $targets = EmployeeCommissionAllocationList::with('allocation')
            ->whereHas('allocation', function ($query) use ($request) {
                $query->where('month', $request->month);
            })
            ->where(function ($query) use ($employee) {
                if ($employee) {
                    $query->whereIn('staff_id', $employee);
                }
            })
            ->where('showroom_id', $showroomId)
            ->orderBy('id', 'asc')
            ->get();


        $data = [];
        if ($request->has('print')) {
            $data = $this->getData($fromDate, $toDate, $targets, $showroomId, $month);
        }


        $pdf = PDF::loadView(
            'admin.employeeComAch.print',
            [
                'title' => $title,
                'month' => $month,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'data' => $data,
                'showroomId' => $showroomId,
            ],
            [],
            ['orientation' => 'L']
        );

        return $pdf->stream('employee_commission_allowance.pdf');
    }

    private function getData($fromDate, $toDate, $targets, $showroomId, $month)
    {

        $retDTragets = EmployeeCommissionAllocation::where('showroom_id', $showroomId)
            ->where('month', $month)
            ->first();

        $retailCashData = $this->retailCashCollection($fromDate, $toDate);
        $mspCashData = $this->mspCashCommission($fromDate, $toDate);

        $retailCashDataC = $retailCashData['cash'] + $retailCashData['cashDiscount'] + $retailCashData['cashWithDisCol'];
        $mspCashDataC = $mspCashData['cash'] + $mspCashData['cashDiscount'] + $mspCashData['cashWithDisCol'];

        $retailCash = $this->recoveryCashCollection($fromDate, $toDate, $showroomId);


        // no-1
        $retailTotalCash = $retailCash['cash'] + $retailCash['cashDiscount'];
        $retailTotalCashCom = $retailCash['cashCommission'] + $retailCash['cashDiscountCommission'];

        $DTarget = $retDTragets ? $retDTragets->dealer_target : 0;
        $RTarget = $retDTragets ? $retDTragets->retail_target : 0;
        $HTarget = $retDTragets ? $retDTragets->hire_target : 0;


        $collections = [
            'dealer' => $this->dealerCollection($fromDate, $toDate),
            'retailCc' => $retailCashData['cashWithDisCol'],
            'retailC' => $retailCashData['cash'],
            'retailDC' => $retailCashData['cashDiscount'],
            'retailH' => $this->retailHireCollection($fromDate, $toDate),
            'reC' => $retailTotalCash,
            'reH' => $this->recoveryHireCollection($fromDate, $toDate),
            'mspCc' => $mspCashData['cashWithDisCol'],
            'mspC' => $mspCashData['cash'],
            'mspDC' => $mspCashData['cashDiscount'],
            'mspH' => $this->mspHireCollection($fromDate, $toDate),
            'dealerTarget' =>  $DTarget,
            'retailTarget' =>  $RTarget,
            'hireTarget' =>  $HTarget,
        ];

        $dealer = 0;
        $retailC = 0;
        $retailCc = 0;
        $retailDC = 0;
        $retailH = 0;
        $reC = 0;
        $reH = 0;
        $mspCc = 0;
        $mspC = 0;
        $mspDC = 0;
        $mspH = 0;


        $commissionPercentage = CommissionPercentage::first();

        if ($collections['dealer'] > 0 && $collections['dealer'] >= $DTarget) {
            $dealer = $collections['dealer'] * ($commissionPercentage->dealer / 100);
        }

        $totalRC = $retailCashDataC + $mspCashDataC;

        if ($totalRC >= $RTarget) {
            if ($retailCashDataC > 0) {
                $retailCc = $retailCashData['cashWithDisColCom'];
                $retailC = $retailCashData['cashCommission'];
                $retailDC = $retailCashData['cashDiscountCommission'];
            }
            if ($collections['retailH'] >= $HTarget) {
                $retailH = $collections['retailH'] * ($commissionPercentage->d_retail_hire / 100);
            }
        }
        if ($retailTotalCashCom > 0) {
            $reC = $retailTotalCashCom;
        }
        if ($collections['reH'] > 0) {
            $reH = $collections['reH'] *  ($commissionPercentage->d_recovery_hire / 100);
        }
        if ($mspCashDataC > 0) {
            $mspCc = $mspCashData['cashWithDisColCom'];
            $mspC = $mspCashData['cashCommission'];
            $mspDC = $mspCashData['cashDiscountCommission'];
        }
        if ($collections['mspH'] > 0) {
            $mspH = $collections['mspH'] * ($commissionPercentage->d_msp_hire / 100);
        }

        $commissions = [
            'dealer' => $dealer,
            'retailCc' => $retailCc,
            'retailC' => $retailC,
            'retailDC' => $retailDC,
            'retailH' => $retailH,
            'reC' => $reC,
            'reH' => $reH,
            'mspCc' => $mspCc,
            'mspC' => $mspC,
            'mspDC' => $mspDC,
            'mspH' => $mspH,
        ];


        $data = [];
        foreach ($targets as $t) {
            $data[] = $this->calculatePercent($t, $commissions, $fromDate, $toDate);
        }


        $info = [
            'collections' => $collections,
            'commissions' => $commissions,
            'data' => $data
        ];

        return $info;
    }


    private function calculatePercent($t, $data, $fromDate, $toDate)
    {
        $dealer = 0;
        $retailCc = 0;
        $retailC = 0;
        $retailDC = 0;
        $retailH = 0;
        $reC = 0;
        $reH = 0;
        $mspCc = 0;
        $mspC = 0;
        $mspDC = 0;
        $mspH = 0;


        if ($data['dealer'] > 0 && $t->dealer_collection > 0) {
            $dealer = $data['dealer'] * ($t->dealer_collection / 100);
        }

        if ($data['retailC'] > 0 && $t->retail_cash_collection > 0) {
            $retailCc = $data['retailCc'] * ($t->retail_cash_collection / 100);
            $retailC = $data['retailC'] * ($t->retail_cash_collection / 100);
            $retailDC = $data['retailDC'] * ($t->retail_cash_collection / 100);
        }

        if ($data['retailH'] > 0 && $t->retail_hire_collection > 0) {
            $retailH = $data['retailH'] * ($t->retail_hire_collection / 100);
        }

        if ($data['reC'] > 0 && $t->recovery_cash_collection > 0) {
            $reC = $data['reC'] * ($t->recovery_cash_collection / 100);
        }

        if ($data['reH'] > 0 && $t->recovery_hire_collection > 0) {
            $reH = $data['reH'] * ($t->recovery_hire_collection / 100);
        }

        if ($data['mspCc'] > 0 && $t->msp > 0) {
            $mspCc = $data['mspCc'] * ($t->msp / 100);
        }
        if ($data['mspC'] > 0 && $t->msp > 0) {
            $mspC = $data['mspC'] * ($t->msp / 100);
        }
        if ($data['mspDC'] > 0 && $t->msp > 0) {
            $mspDC = $data['mspDC'] * ($t->msp / 100);
        }
        if ($data['mspH'] > 0 && $t->msp > 0) {
            $mspH = $data['mspH'] * ($t->msp / 100);
        }



        $collections = [
            'dealer_id' => $t->staff->id,
            'name' => $t->staff->name,
            'dealer' => number_format($dealer, 2, '.', ''),
            'retailCc' => number_format($retailCc, 2, '.', ''),
            'retailC' => number_format($retailC, 2, '.', ''),
            'retailDC' => number_format($retailDC, 2, '.', ''),
            'retailH' => number_format($retailH, 2, '.', ''),
            'reC' => number_format($reC, 2, '.', ''),
            'reH' => number_format($reH, 2, '.', ''),
            'mspCc' => number_format($mspCc, 2, '.', ''),
            'mspC' => number_format($mspC, 2, '.', ''),
            'mspDC' => number_format($mspDC, 2, '.', ''),
            'mspH' => number_format($mspH, 2, '.', ''),
        ];

        return $collections;
    }






    private function dealerCollection($fromDate, $toDate)
    {
        // $collection = DealerCollection::where('payment_date', '>=', $fromDate)
        //     ->where('payment_date', '<=', $toDate)
        //     ->sum('payment_amount');

        $collection = 0;
        if ($this->showroomId == 1) {

            $collectionHistories = DealerCollection::where('payment_date', '>=', $fromDate)
                ->where('payment_date', '<=', $toDate)
                ->get();


            $collection = 0;
            foreach ($collectionHistories as $cHistory) {

                if ($cHistory->adjustment == 1) {
                    continue;
                }

                $payment = $cHistory->payment_amount;
                if ($cHistory->advance_id !== null) {
                    $payment = $payment + $cHistory->advance->advance_amount;
                }

                $collection += floatval($payment);
            }


            $advanceHistory = AdvanceCollection::where('date', '>=', $fromDate)
                ->where('date', '<=', $toDate)
                ->where('advance_amount', '>', 0)
                ->where('remarks', '!=', "Advance From Collection")
                ->get();


            foreach ($advanceHistory as $a) {

                if ($a->remarks == 'Advance From Collection') {
                    continue;
                }

                $collection += floatval($a->advance_amount);
            }
        }

        return number_format($collection, 2, '.', '');
    }


    private function retailCashCollection($fromDate, $toDate)
    {
        $showroom = $this->showroomId;
        if ($showroom == 1) {
            $project_id = 102;
        } else {
            $project_id = 103;
        }
        $retailSales = RetailSales::with('sale', 'sale.customer', 'sale.seller')
            ->whereHas('sale', function ($q) use ($fromDate, $toDate, $showroom) {
                $q->where('sale_date', '>=', $fromDate)
                    ->where('sale_date', '<=', $toDate)
                    ->where('sale_type', 'cash');
            })
            // ->whereHas('sale.customer', function ($q) {
            //     $q->where('code', 'NOT LIKE', "%msp%");
            // })
            ->whereHas('sale.seller', function ($q) use ($showroom) {
                if ($showroom == 2) {
                    $q->where('reference_id', 28);
                } else {
                    $q->where('reference_id', 113);
                }
            })
            ->get();



        $commissionPercentage = CommissionPercentage::first();


        $cash = 0;
        $cashCommission = 0;
        $cashDiscount = 0;
        $cashDiscountCommission = 0;
        foreach ($retailSales as $sale) {
            $product = Product::find($sale->product_id);

            $total_sales = $sale->sales_price;
            $total_discount = $sale->discount;
            $total_voucher = $sale->gift_voucher;
            $total_excrt = $sale->exchange_crt;
            $actual_sales = $total_sales - ($total_discount + $total_voucher + $total_excrt);

            if ($product->price > $actual_sales) {
                // if ($sale->sales_price > $actual_sales) {

                $cashDiscount += $actual_sales;

                $price = $product->price;
                // $price = $sale->sales_price;
                if ($product->price == 0) {
                    // if ($sale->sales_price == 0) {
                    $price = 1;
                }

                $profitLoss = 100 - (($actual_sales * 100) / $price);
                $commissionPercent = 16.8 - $profitLoss;
                $commissionAmount = ($actual_sales * $commissionPercent) / 100;
                // $cashDiscountCommission += $price * ($commissionPercentage->retail_cash / 100);
                // $employeeCom += $actual_sales * (2 / 100);

                $cashDiscountCommission += ($price * ($commissionPercentage->d_retail_cash_with_discount / 100) *  $commissionPercent) / 16.80;
            } else {

                $price = $product->price;
                // $price = $sale->sales_price;
                $cash += $actual_sales;

                if ($product->price == 0) {
                    // if ($sale->sales_price == 0) {
                    $price = 1;
                }

                $profitLoss = 100;
                $commissionPercent = 100;
                $commissionAmount = $actual_sales;
                $cashCommission += $commissionAmount * ($commissionPercentage->d_retail_cash_without_discount / 100);
                // $cashCommission += ($price * ($commissionPercentage->retail_cash / 100) *  $commissionPercent) / 16.80;
            }
        }


        $cashCollectionHistory = InstallmentCollectionList::with(['collection.customer', 'collection.product', 'collection.collector'])
            ->where('installment_schedule_amount', '!=', 0)
            //short by project Id 
            ->where('project_id', $project_id)
            ->where('installment_collection_date', '>=', $fromDate)
            ->where('installment_collection_date', '<=', $toDate)
            ->whereHas('collection.collector', function ($q) use ($showroom) {
                if ($showroom == 2) {
                    $q->where('reference_id', 28);
                } else {
                    $q->where('reference_id', 113);
                }
            })
            ->sum('installment_schedule_amount');


        $cashWithDisCol = $cashCollectionHistory - ($cashDiscount + $cash);
        // $cashWithDisCol = $cash;
        $cashWithDisColCom = $cashWithDisCol * ($commissionPercentage->d_retail_discount / 100);


        $data = [
            'cash' => number_format($cash, 2, '.', ''),
            'cashCommission' => number_format($cashCommission, 2, '.', ''),
            'cashDiscount' => number_format($cashDiscount, 2, '.', ''),
            'cashDiscountCommission' => number_format($cashDiscountCommission, 2, '.', ''),
            'cashWithDisCol' => number_format($cashWithDisCol, 2, '.', ''),
            'cashWithDisColCom' => number_format($cashWithDisColCom, 2, '.', ''),
        ];

        return $data;
    }




    private function retailHireCollection($fromDate, $toDate)
    {
        $showroom = $this->showroomId;
        if ($showroom == 1) {
            $project_id = 102;
        } else {
            $project_id = 103;
        }
        $collection = InstallmentCollectionList::with('collection', 'collection.customer')
            // ->whereHas('collection.customer', function ($q) {
            //     $q->where('code', 'NOT LIKE', "%msp%");
            // })
            ->whereHas('collection', function ($q) use ($showroom) {
                if ($showroom == 2) {
                    $q->where('reference_id', 115);
                } else {
                    $q->where('reference_id', 114);
                }
            })
            // short by project_id 2
            ->where('project_id', $project_id)
            ->where('showroom_id', $this->showroomId)
            ->where('installment_collection_date', '>=', $fromDate)
            ->where('installment_collection_date', '<=', $toDate)
            ->sum('installment_schedule_amount');

        return number_format($collection, 2, '.', '');
    }
    private function recoveryCashCollection($fromDate, $toDate, $showroomId = null)
    {

        $groups = GroupSalesTarget::with('group', 'employees', 'employees.staff')
            ->where('target_on', 'normal')
            ->whereBetween('date', [$fromDate, $toDate])
            ->get();

        $cash = 0;
        $cashCommission = 0;
        $cashDiscount = 0;
        $cashDiscountCommission = 0;
        foreach ($groups as $group) {
            $cashData = $this->cashCommission($group->group->team_leader, $fromDate, $toDate, $showroomId);
            $cash += $cashData['cash'];
            $cashDiscount += $cashData['cashDiscount'];
            $cashCommission += $cashData['cashCommission'] + $cashData['cashDiscountCommission'];
        }

        // dd($cash, $cashDiscount);

        $data = [
            'cash' => number_format($cash, 2, '.', ''),
            'cashCommission' => number_format($cashCommission, 2, '.', ''),
            'cashDiscount' => number_format($cashDiscount, 2, '.', ''),
            'cashDiscountCommission' => number_format($cashDiscountCommission, 2, '.', ''),
        ];

        return $data;
    }

    public function cashCommission($memberId, $fromDate, $toDate, $showroomId = null)
    {

        $retailSales = RetailSales::with('sale')
            ->whereHas('sale', function ($q) use ($memberId, $fromDate, $toDate, $showroomId) {
                $q->where('sale_date', '>=', $fromDate)
                    ->where('sale_date', '<=', $toDate)
                    ->where('reference_id', $memberId)
                    ->where('sale_type', 'cash')
                    ->where('showroom_id', $showroomId);
            })

            ->get();

        $commissionPercentage = CommissionPercentage::first();

        // $cash = 0;
        $cashCommission = 0;
        // $cashDiscount = 0;
        $cashDiscountCommission = 0;
        foreach ($retailSales as $sale) {
            $product = Product::find($sale->product_id);

            $cashMainCommission = $product->price * ($commissionPercentage->r_cash / 100);

            $total_sales = $sale->sales_price;
            $total_discount = $sale->discount;
            $total_voucher = $sale->gift_voucher;
            $total_excrt = $sale->exchange_crt;
            $actual_sales = $total_sales - ($total_discount + $total_voucher + $total_excrt);

            if ($product->price > $actual_sales) {
                // if ($sale->sales_price > $actual_sales) {

                // $cashDiscount += $actual_sales;

                $price = $product->price;
                // $price = $sale->sales_price;
                if ($product->price == 0) {
                    // if ($sale->sales_price == 0) {
                    $price = 1;
                }

                $profitLoss = 100 - (($actual_sales * 100) / $price);
                $commissionPercent = 16.8 - $profitLoss;
                // $commissionAmount = ($actual_sales * $commissionPercent) / 100;
                // $commissionAmount = $actual_sales;
                // $cashDiscountCommission += $commissionAmount * ($commissionPercentage->cash / 100);
                // $cashDiscountCommission += ($price * ($commissionPercentage->cash / 100)) / (16.80 * $commissionPercent);
                $cashDiscountCommission += ($price * ($commissionPercentage->r_cash / 100) *  $commissionPercent) / 16.80;
            } else {
                // $cash += $actual_sales;

                $profitLoss = 100;
                $commissionPercent = 100;
                $cashCommission += $actual_sales * ($commissionPercentage->r_cash / 100);
            }
        }


        $retailSales = RetailSales::with('sale', 'sale.customer')
            ->whereHas('sale', function ($q) use ($memberId, $fromDate, $toDate, $showroomId) {
                $q->where('sale_date', '>=', $fromDate)
                    ->where('sale_date', '<=', $toDate)
                    ->where('reference_id', $memberId)
                    ->where('showroom_id', $showroomId)
                    ->where('sale_type', 'cash');
            })
            ->whereHas('sale.customer', function ($q) {
                $q->where('code', 'NOT LIKE', "%msp%");
            })
            ->sum('sales_price');


        $mspCash = RetailSales::with('sale', 'sale.customer')
            ->whereHas('sale', function ($q) use ($memberId, $fromDate, $toDate, $showroomId) {
                $q->where('sale_date', '>=', $fromDate)
                    ->where('sale_date', '<=', $toDate)
                    ->where('reference_id', $memberId)
                    ->where('showroom_id', $showroomId)
                    ->where('sale_type', 'cash');
            })
            ->whereHas('sale.customer', function ($q) {
                $q->where('code', 'LIKE', "%msp%");
            })
            ->get();


        $mspCashSale = 0;
        $td = 0;
        foreach ($mspCash as $mC) {
            $total_sales = $mC->sales_price;
            $total_discount = $mC->discount;
            $total_voucher = $mC->gift_voucher;
            $total_excrt = $mC->exchange_crt;
            $actual_sales = $total_sales - ($total_discount + $total_voucher + $total_excrt);

            $mspCashSale += $actual_sales;
            $td += $total_discount;
        }

        $cash = $mspCashSale + $retailSales;
        $cashDiscount = $td;

        $data = [
            'cash' => number_format($cash, 2, '.', ''),
            'cashCommission' => number_format($cashCommission, 2, '.', ''),
            'cashDiscount' => number_format($cashDiscount, 2, '.', ''),
            'cashDiscountCommission' => number_format($cashDiscountCommission, 2, '.', ''),
        ];

        return $data;
    }


    private function recoveryHireCollection($fromDate, $toDate)
    {
        $collection = 0;
        $groups = GroupSalesTarget::with('group', 'employees', 'employees.staff')
            ->where('target_on', 'normal')
            ->whereBetween('date', [$fromDate, $toDate])
            ->get();

        $cashCollectionHistoryTotal = 0;
        $retailSalesTotal = 0;
        foreach ($groups as $group) {
            // $HireCollection = InstallmentCollectionList::with('collection', 'collection.customer', 'collection.collector')
            //     // ->whereHas('collection.customer', function ($q) {
            //     //     $q->where('code', 'LIKE', "%msp%");
            //     // })
            //     ->whereHas('collection.collector', function ($q) use ($group) {
            //         $q->where('id',  $group->group->team_leader);
            //     })
            //     ->where('showroom_id', $this->showroomId)
            //     ->where('installment_collection_date', '>=', $fromDate)
            //     ->where('installment_collection_date', '<=', $toDate)
            //     ->sum('installment_schedule_amount');

            // $collection += $HireCollection;

            //find project Id

            $retailSales = RetailSales::with('sale', 'sale.customer')
                ->whereHas('sale', function ($q) use ($group, $fromDate, $toDate) {
                    $q->where('sale_date', '>=', $fromDate)
                        ->where('sale_date', '<=', $toDate)
                        ->where('reference_id', $group->group->team_leader)
                        ->where('showroom_id', $this->showroomId)
                        ->where('sale_type', 'cash');
                })
                ->whereHas('sale.customer', function ($q) {
                    $q->where('code', 'NOT LIKE', "%msp%");
                })
                ->sum('sales_price');
            $mspretailSales = RetailSales::with('sale', 'sale.customer')
                ->whereHas('sale', function ($q) use ($group, $fromDate, $toDate) {
                    $q->where('sale_date', '>=', $fromDate)
                        ->where('sale_date', '<=', $toDate)
                        ->where('reference_id', $group->group->team_leader)
                        ->where('showroom_id', $this->showroomId)
                        ->where('sale_type', 'cash');
                })
                ->whereHas('sale.customer', function ($q) {
                    $q->where('code', 'NOT LIKE', "%msp%");
                })
                ->sum('sales_price');

            $cashCollectionHistory = InstallmentCollectionList::with(['collection.customer', 'collection.product', 'collection.collector'])

                ->where('installment_schedule_amount', '!=', 0)
                ->where('installment_collection_date', '>=', $fromDate)
                ->where('installment_collection_date', '<=', $toDate)
                ->where('showroom_id', $this->showroomId)
                ->whereHas('collection.collector', function ($q) use ($group) {
                    $q->where('id', $group->group->team_leader);
                })
                ->whereHas('collection.customer', function ($q) {
                    $q->where('code', 'NOT LIKE', "%msp%");
                })
                ->sum('installment_schedule_amount');

            $cashCollectionHistoryTotal += $cashCollectionHistory;
            $retailSalesTotal += $retailSales;
        }

        // $cahCollection = $this->recoveryCashCollection($fromDate, $toDate);

        // $cash = $cahCollection['cash'] + $cahCollection['cashDiscount'];

        // $collection = $collection - $cash;

        $collection = $cashCollectionHistoryTotal - $retailSalesTotal;

        return number_format($collection, 2, '.', '');
    }


    private function mspCashCollection($fromDate, $toDate)
    {
        $collection = InstallmentCollectionList::with('collection', 'collection.customer', 'collection.collector')
            // ->whereHas('collection.customer', function ($q) {
            //     $q->where('code', 'LIKE', "%msp%");
            // })
            ->whereHas('collection.collector', function ($q) {
                $q->where('name', 'MSP-Retail');
            })
            ->where('showroom_id', $this->showroomId)
            ->where('installment_collection_date', '>=', $fromDate)
            ->where('installment_collection_date', '<=', $toDate)
            ->sum('installment_schedule_amount');




        return number_format($collection, 2, '.', '');
    }

    private function mspCashCommission($fromDate, $toDate)
    {
        $showroom = $this->showroomId;
        $retailSales = RetailSales::with('sale', 'sale.customer', 'sale.seller')
            ->whereHas('sale', function ($q) use ($fromDate, $toDate, $showroom) {
                $q->where('sale_date', '>=', $fromDate)
                    ->where('sale_date', '<=', $toDate)
                    ->where('sale_type', 'cash')
                    ->where('showroom_id', $showroom);
            })
            // ->whereHas('sale.customer', function ($q) {
            //     $q->where('code', 'LIKE', "%msp%");
            // })
            ->whereHas('sale.seller', function ($q) {
                $q->where('name', 'MSP-Retail');
            })
            ->get();

        $commissionPercentage = CommissionPercentage::first();

        $cash = 0;
        $cashCommission = 0;
        $cashDiscount = 0;
        $cashDiscountCommission = 0;
        foreach ($retailSales as $sale) {
            $product = Product::find($sale->product_id);

            $price = $product->price;
            // $price = $sale->sales_price;
            if ($product->price == 0) {
                // if ($sale->sales_price == 0) {
                $price = 1;
            }


            $total_sales = $sale->sales_price;
            $total_discount = $sale->discount;
            $total_voucher = $sale->gift_voucher;
            $total_excrt = $sale->exchange_crt;
            $actual_sales = $total_sales - ($total_discount + $total_voucher + $total_excrt);

            if ($product->price > $actual_sales) {
                // if ($sale->sales_price > $actual_sales) {

                $cashDiscount += $actual_sales;


                $profitLoss = 100 - (($actual_sales * 100) / $price);
                $commissionPercent = 16.8 - $profitLoss;
                $commissionAmount = ($actual_sales * $commissionPercent) / 100;
                // $cashDiscountCommission += $price * (0.50 / 100);
                // $cashDiscountCommission += ($price * ($commissionPercentage->msp_cash / 100)) / (16.80 * $commissionPercent);
                $cashDiscountCommission += ($price * ($commissionPercentage->d_msp_cash_with_discount / 100) *  $commissionPercent) / 16.80;
            } else {
                $cash += $actual_sales;

                $profitLoss = 100;
                $commissionPercent = 100;
                $cashCommission += $actual_sales * ($commissionPercentage->d_msp_cash_wihout_discount / 100);
            }
        }


        $cashCollectionHistory = InstallmentCollectionList::with(['collection.collector'])
            ->where('installment_schedule_amount', '!=', 0)
            ->where('installment_collection_date', '>=', $fromDate)
            ->where('installment_collection_date', '<=', $toDate)
            ->whereHas('collection.collector', function ($q) {
                $q->where('name', 'MSP-Retail');
            })
            ->where('showroom_id', $showroom)
            ->sum('installment_schedule_amount');



        $cashWithDisCol = $cashCollectionHistory - ($cashDiscount + $cash);
        $cashWithDisColCom = $cashWithDisCol * ($commissionPercentage->d_msp_cash_discount / 100);


        $data = [
            'cash' => number_format($cash, 2, '.', ''),
            'cashCommission' => number_format($cashCommission, 2, '.', ''),
            'cashDiscount' => number_format($cashDiscount, 2, '.', ''),
            'cashDiscountCommission' => number_format($cashDiscountCommission, 2, '.', ''),
            'cashWithDisCol' => number_format($cashWithDisCol, 2, '.', ''),
            'cashWithDisColCom' => number_format($cashWithDisColCom, 2, '.', ''),
        ];

        return $data;
    }

    private function mspHireCollection($fromDate, $toDate)
    {
        $collection = InstallmentCollectionList::with('collection', 'collection.customer', 'collection.collector')
            // ->whereHas('collection.customer', function ($q) {
            //     $q->where('code', 'LIKE', "%msp%");
            // })
            ->whereHas('collection.collector', function ($q) {
                $q->where('name', 'MSP-Hire');
            })
            //that was comment;
            ->where('showroom_id', $this->showroomId)
            ->where('installment_collection_date', '>=', $fromDate)
            ->where('installment_collection_date', '<=', $toDate)
            ->sum('installment_schedule_amount');

        $groups = GroupSalesTarget::with('group', 'employees', 'employees.staff')
            ->where('target_on', 'normal')
            ->whereBetween('date', [$fromDate, $toDate])
            ->get();

        foreach ($groups as $group) {
            $mspCash = RetailSales::with('sale', 'sale.customer')
                ->whereHas('sale', function ($q) use ($group, $fromDate, $toDate) {
                    $q->where('sale_date', '>=', $fromDate)
                        ->where('sale_date', '<=', $toDate)
                        ->where('reference_id', $group->group->team_leader)
                        ->where('showroom_id', $this->showroomId)
                        ->where('sale_type', 'cash');
                })
                ->whereHas('sale.customer', function ($q) {
                    $q->where('code', 'LIKE', "%msp%");
                })
                ->get();

            $mspCashSale = 0;
            foreach ($mspCash as $mC) {
                $total_sales = $mC->sales_price;
                $total_discount = $mC->discount;
                $total_voucher = $mC->gift_voucher;
                $total_excrt = $mC->exchange_crt;
                $actual_sales = $total_sales - ($total_discount + $total_voucher + $total_excrt);

                $mspCashSale += $actual_sales;
            }


            $mspCollectionHistory = InstallmentCollectionList::with(['collection.customer', 'collection.product', 'collection.collector'])
                ->where('installment_schedule_amount', '!=', 0)
                ->where('installment_collection_date', '>=', $fromDate)
                ->where('installment_collection_date', '<=', $toDate)
                ->where('showroom_id', $this->showroomId)
                ->whereHas('collection.collector', function ($q) use ($group) {
                    $q->where('id', $group->group->team_leader);
                })
                ->whereHas('collection.customer', function ($q) {
                    $q->where('code', 'LIKE', "%msp%");
                })
                ->sum('installment_schedule_amount');

            $collection += $mspCollectionHistory - $mspCashSale;
        }



        return number_format($collection, 2, '.', '');
    }
}
