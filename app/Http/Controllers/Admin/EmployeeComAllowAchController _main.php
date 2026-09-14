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

        $fromDate = date('Y-m-d', strtotime($request->month . '-01'));
        $toDate = date('Y-m-t', strtotime($fromDate));

        $showrooms = ShowroomSetup::get();

        $staffs = StaffSetup::where('status', 1)->get();

        $targets = EmployeeCommissionAllocationList::with('allocation')
            // ->whereHas('allocation', function ($query) use ($request) {
            //     $query->where('month', $request->month);
            // })
            ->where(function ($query) use ($employee) {
                if ($employee) {
                    $query->whereIn('staff_id', $employee);
                }
            })
            ->where('showroom_id', $this->showroomId)
            ->get();


        $data = [];
        if ($request->has('print')) {
            $data = $this->getData($fromDate, $toDate, $targets, $showroomId);
        }

        return view('admin.employeeComAch.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'staffs', 'data', 'employee', 'fromDate', 'toDate', 'showrooms', 'showroomId', 'month'));
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
            // ->whereHas('allocation', function ($query) use ($request) {
            //     $query->where('month', $request->month);
            // })
            ->where(function ($query) use ($employee) {
                if ($employee) {
                    $query->whereIn('staff_id', $employee);
                }
            })
            ->where('showroom_id', $this->showroomId)
            ->get();


        $data = [];
        if ($request->has('print')) {
            $data = $this->getData($fromDate, $toDate, $targets, $showroomId);
        }


        $pdf = PDF::loadView(
            'admin.employeeComAch.print',
            [
                'title' => $title,
                'month' => $month,
                'data' => $data,
            ],
            [],
            ['orientation' => 'L']
        );

        return $pdf->stream('employee_commission_allowance.pdf');
    }

    private function getData($fromDate, $toDate, $targets)
    {


        $retDTragets = EmployeeCommissionAllocation::first();

        $collections = [
            'dealer' => $this->dealerCollection($fromDate, $toDate),
            'retailC' => $this->retailCashCollection($fromDate, $toDate),
            'retailH' => $this->retailHireCollection($fromDate, $toDate),
            // 'reC' => $this->recoveryCashCollection($fromDate, $toDate),
            // 'reH' => $this->recoveryHireCollection($fromDate, $toDate),
            'mspC' => $this->mspCashCollection($fromDate, $toDate),
            'mspH' => $this->mspHireCollection($fromDate, $toDate),
            'dealerTarget' =>  $retDTragets->dealer_target,
            'retailTarget' =>  $retDTragets->retail_target,
        ];

        $dealer = 0;
        $retailC = 0;
        $retailH = 0;
        // $reC = 0;
        // $reH = 0;
        $mspC = 0;
        $mspH = 0;


        $commissionPercentage = CommissionPercentage::first();

        if ($collections['dealer'] > 0 && $collections['dealer'] >= $retDTragets->dealer_target) {
            $dealer = $collections['dealer'] * ($commissionPercentage->dealer / 100);
        }

        $totalRC = $collections['retailC'] + $collections['retailH'];

        if ($totalRC >= $retDTragets->retail_target) {
            if ($collections['retailC'] > 0) {
                $retailC = $collections['retailC'] * ($commissionPercentage->retail_cash / 100);
            }
            if ($collections['retailH'] > 0) {
                $retailH = $collections['retailH'] * ($commissionPercentage->retail_hire / 100);
            }
        }
        // if ($collections['reC'] > 0) {
        //     $reC = $collections['reC'] * (0.5 / 100);
        // }
        // if ($collections['reC'] > 0) {
        //     $reC = $collections['reC'] * (0.5 / 100);
        // }
        if ($collections['mspC'] > 0) {
            // $mspC = $collections['mspC'] * ($commissionPercentage->msp_cash / 100);
            $mspC = $this->mspCashCommission($fromDate, $toDate);
        }
        if ($collections['mspH'] > 0) {
            $mspH = $collections['mspH'] * ($commissionPercentage->msp_hire / 100);
        }

        $commissions = [
            'dealer' => $dealer,
            'retailC' => $retailC,
            'retailH' => $retailH,
            // 'reC' => $reC,
            // 'reH' => $reH,
            'mspC' => $mspC,
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
        $retailC = 0;
        $retailH = 0;
        $reC = 0;
        $reH = 0;
        $mspC = 0;
        $mspH = 0;


        $mspCommission = $this->mspCashCommission($fromDate, $toDate);

        if ($data['dealer'] > 0 && $t->dealer_collection > 0) {
            $dealer = $data['dealer'] * ($t->dealer_collection / 100);
        }

        if ($data['retailC'] > 0 && $t->retail_cash_collection > 0) {
            $retailC = $data['retailC'] * ($t->retail_cash_collection / 100);
        }

        if ($data['retailH'] > 0 && $t->retail_hire_collection > 0) {
            $retailH = $data['retailH'] * ($t->retail_hire_collection / 100);
        }

        // if ($data['reC'] > 0 && $t->recovery_cash_collection > 0) {
        //     $reC = $data['reC'] * ($t->recovery_cash_collection / 100);
        // }

        // if ($data['reH'] > 0 && $t->recovery_hire_collection > 0) {
        //     $reH = $data['reH'] * ($t->recovery_hire_collection / 100);
        // }

        if ($mspCommission > 0 && $t->msp > 0) {
            $mspC = $mspCommission;
        }
        if ($data['mspH'] > 0 && $t->msp > 0) {
            $mspH = $data['mspH'] * ($t->msp / 100);
        }



        $collections = [
            'name' => $t->staff->name,
            'dealer' => number_format($dealer, 2, '.', ''),
            'retailC' => number_format($retailC, 2, '.', ''),
            'retailH' => number_format($retailH, 2, '.', ''),
            // 'reC' => number_format($reC, 2, '.', ''),
            // 'reH' => number_format($reH, 2, '.', ''),
            'mspC' => number_format($mspC, 2, '.', ''),
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
        $collection = InstallmentCollectionList::with('collection', 'collection.customer')
            ->whereHas('collection.customer', function ($q) {
                $q->where('code', 'NOT LIKE', "%msp%");
            })
            ->whereHas('collection', function ($q) use ($showroom) {
                if ($showroom == 2) {
                    $q->where('reference_id', 28);
                } else {
                    $q->where('reference_id', 113);
                }
            })
            ->where('showroom_id', $this->showroomId)
            ->where('installment_collection_date', '>=', $fromDate)
            ->where('installment_collection_date', '<=', $toDate)
            ->sum('installment_schedule_amount');

        return number_format($collection, 2, '.', '');
    }
    private function retailHireCollection($fromDate, $toDate)
    {
        $collection = 0;
        $showroom = $this->showroomId;
        $collection = InstallmentCollectionList::with('collection', 'collection.customer')
            ->whereHas('collection.customer', function ($q) {
                $q->where('code', 'NOT LIKE', "%msp%");
            })
            ->whereHas('collection', function ($q) use ($showroom) {
                if ($showroom == 2) {
                    $q->where('reference_id', 115);
                } else {
                    $q->where('reference_id', 114);
                }
            })
            ->where('showroom_id', $this->showroomId)
            ->where('installment_collection_date', '>=', $fromDate)
            ->where('installment_collection_date', '<=', $toDate)
            ->sum('installment_schedule_amount');

        // $collections = InstallmentCollectionList::with('collection', 'collection.customer')
        //     ->whereHas('collection.customer', function ($q) {
        //         $q->where('code', 'NOT LIKE', "%msp%");
        //     })
        //     ->whereHas('collection', function ($q) {
        //         $q->where('reference_id', 114);
        //     })
        //     ->where('installment_collection_date', '>=', $fromDate)
        //     ->where('installment_collection_date', '<=', $toDate)
        //     ->get();

        // foreach ($collections as $c) {
        //     if ($c->collection->customerSale) {
        //         if ($c->collection->customerSale->sale_type == 'cash') {
        //             $collection +=  floatval($c->installment_schedule_amount);
        //         }
        //     }
        // }

        return number_format($collection, 2, '.', '');
    }
    private function recoveryCashCollection($fromDate, $toDate)
    {

        $collection = 0;
        $collections = InstallmentCollectionList::with('collection', 'collection.customerSale', 'collection.customer')
            ->whereHas('collection', function ($q) {
                $q->whereNotIn('reference_id', [113, 114]);
                // $q->where('reference_id', $t->staff_id);
            })
            ->whereHas('collection.customer', function ($q) {
                $q->where('code', 'not like', "%msp%");
            })
            ->where('showroom_id', $this->showroomId)
            ->where('installment_collection_date', '>=', $fromDate)
            ->where('installment_collection_date', '<=', $toDate)
            ->get();

        foreach ($collections as $c) {
            if ($c->collection->customerSale) {
                if ($c->collection->customerSale->sale_type == 'cash') {
                    $collection +=  floatval($c->installment_schedule_amount);
                }
            }
        }

        return number_format($collection, 2, '.', '');
    }
    private function recoveryHireCollection($fromDate, $toDate)
    {
        $collection = 0;
        $collections = InstallmentCollectionList::with('collection', 'collection.customerSale', 'collection.customer')
            ->whereHas('collection', function ($q) {
                $q->whereNotIn('reference_id', [113, 114]);
                // $q->where('reference_id', $t->staff_id);
            })
            ->whereHas('collection.customer', function ($q) {
                $q->where('code', 'not like', "%msp%");
            })
            ->where('showroom_id', $this->showroomId)
            ->where('installment_collection_date', '>=', $fromDate)
            ->where('installment_collection_date', '<=', $toDate)
            ->get();

        foreach ($collections as $c) {
            if ($c->collection->customerSale) {
                if ($c->collection->customerSale->sale_type != 'cash') {
                    $collection +=  floatval($c->installment_schedule_amount);
                }
            }
        }

        return number_format($collection, 2, '.', '');
    }


    private function mspCashCollection($fromDate, $toDate)
    {
        $collection = InstallmentCollectionList::with('collection', 'collection.customer', 'collection.collector')
            ->whereHas('collection.customer', function ($q) {
                $q->where('code', 'LIKE', "%msp%");
            })
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
            ->whereHas('sale.customer', function ($q) {
                $q->where('code', 'LIKE', "%msp%");
            })
            ->whereHas('sale.seller', function ($q) {
                $q->where('name', 'MSP-Retail');
            })
            ->get();


        $totalCommission = 0;
        $commissionPercentage = CommissionPercentage::first();
        foreach ($retailSales as $sale) {
            $product = Product::find($sale->product_id);

            if ($product->price == $sale->sales_price) {
                $totalCommission += $product->price * ($commissionPercentage->cash / 100);
            } else {
                $profit = 0;

                if ($product->price > 0) {
                    $profit = ($product->price * 16.8) / 100;
                }

                $diff = $product->price - $sale->sales_price;

                $comDif = $profit - $diff;
                $cashCom = 0;
                if ($comDif > 0) {
                    $cashCom = $comDif * ($commissionPercentage->cash / 100);
                    $totalCommission += $cashCom;
                }
            }
        }

        return number_format($totalCommission, 2, '.', '');
    }

    private function mspHireCollection($fromDate, $toDate)
    {
        $collection = InstallmentCollectionList::with('collection', 'collection.customer', 'collection.collector')
            ->whereHas('collection.customer', function ($q) {
                $q->where('code', 'LIKE', "%msp%");
            })
            ->whereHas('collection.collector', function ($q) {
                $q->where('name', 'MSP-Hire');
            })
            ->where('showroom_id', $this->showroomId)
            ->where('installment_collection_date', '>=', $fromDate)
            ->where('installment_collection_date', '<=', $toDate)
            ->sum('installment_schedule_amount');


        return number_format($collection, 2, '.', '');
    }
}
