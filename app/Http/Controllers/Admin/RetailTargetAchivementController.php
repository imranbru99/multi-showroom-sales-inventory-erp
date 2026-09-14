<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GroupSetup;
use App\GroupSalesTarget;
use App\GroupSalesTargetAmount;
use App\Product;
use App\StaffSetup;
use App\Installment;
use App\AdvanceCollection;
use App\CampaignCommission;
use App\CommissionPercentage;
use App\CustomerAgreement;
use App\CustomerRegistrationSetup;
use App\EmployeeAllowance;
use App\InstallmentCollection;
use App\InstallmentCollectionList;
use App\InstallmentSchedule;
use App\RetailCommission;
use App\RetailCommissionCampaign;
use App\RetailCommissionCampaignList;
use App\RetailCommissionList;
use App\RetailSales;
use App\ShowroomProjectSetup;
use App\ShowroomSetup;
use DB;
use PDF;

class RetailTargetAchivementController extends Controller
{

    public function index(Request $request)
    {
        $title = "Retail Commission";
        $searchFormLink = "retailTargetAchivement.index";
        $printFormLink = "retailTargetAchivement.print";
        $groupParam = $request->group;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $print = $request->print;
        $showroomId = $request->showroom;
        $type = $request->type;
        $projectId = $request->project;


        // dd($request->all());

        // $groupList = [];
        // if ($groupParam) {
        $groupList = GroupSetup::where('status', 1)
            ->orderBy('name', 'ASC')
            ->get();
        // }


        $projects = ShowroomProjectSetup::where('status', 1)->get();
        $showrooms = ShowroomSetup::where('status', 1)->get();

        $groups = GroupSalesTarget::with('group', 'employees', 'employees.staff')
            ->where(function ($query) use ($groupParam, $showroomId, $type, $projectId) {
                if ($groupParam) {
                    $query->whereIn('group_id', $groupParam);
                }
                if ($showroomId) {
                    $query->where('showroom_id', $showroomId);
                }
                if ($projectId) {
                    $query->whereIn('project_id', $projectId);
                }
                if ($type) {
                    $query->where('type', $type);
                }
            })
            ->where('target_on', 'normal')
            ->whereBetween('date', [$fromDate, $toDate])
            ->get();

        $data = [];
        foreach ($groups as $group) {
            $data[] = $this->getData($group, $fromDate, $toDate);
        }

        $msp = [];
        $mspCollection = [];
        if ($request->print) {
            $msp = $this->mspCollection($fromDate, $toDate);
            $mspCollection = $this->mspIndvCollec($fromDate, $toDate);
        }

        return view('admin.retailTargetAchivement.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'groupList', 'data', 'groupParam', 'fromDate', 'toDate', 'showrooms', 'projects', 'showroomId', 'type', 'projectId', 'msp', 'mspCollection'));
    }

    public function print(Request $request)
    {
        $title = "Retail Commission Report";
        $groupParam = $request->group;
        $print = $request->print;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $showroomId = $request->showroom;
        $type = $request->type;
        $projectId = $request->project;

        $groups = GroupSalesTarget::with('group', 'employees', 'employees.staff')
            ->where(function ($query) use ($groupParam, $showroomId, $type, $projectId) {
                if ($groupParam) {
                    $query->whereIn('group_id', $groupParam);
                }
                if ($showroomId) {
                    $query->where('showroom_id', $showroomId);
                }
                if ($projectId) {
                    $query->whereIn('project_id', $projectId);
                }
            })
            ->where('target_on', 'normal')
            ->whereBetween('date', [$fromDate, $toDate])
            ->get();

        $data = [];
        foreach ($groups as $group) {
            $data[] = $this->getData($group, $fromDate, $toDate);
        }
        $msp = [];
        $mspCollection = [];
        if ($request->print) {
            $msp = $this->mspCollection($fromDate, $toDate);
            $mspCollection = $this->mspIndvCollec($fromDate, $toDate);
        }


        $pdf = PDF::loadView(
            'admin.retailTargetAchivement.print',
            [
                'title' => $title,
                'groupParamgroupParam' => $groupParam,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'data' => $data,
                'msp' => $msp,
                'mspCollection' => $mspCollection,
            ],
            [],
            ['orientation' => 'L']
        );

        return $pdf->stream('Retail Commission.pdf');
    }

    private function getData($group, $fromDate, $toDate)
    {
        $month = date('M Y', strtotime($group->date));

        $teamData = [
            'group' => @$group->group->name,
            'month' => $month,
            'leader_info' => $this->individualData($group, $group->group->team_leader, $fromDate, $toDate),
            'cus_col_per' => $this->getCustomerWise($group->group->team_leader, $fromDate, $toDate)
        ];


        return $teamData;
    }

    private function individualData($group, $memberId, $fromDate, $toDate)
    {
        $staff = StaffSetup::find($memberId);

        $CHCollections = InstallmentCollectionList::with('collection', 'collection.customerSale', 'collection.customer')
            ->whereHas('collection', function ($q) use ($memberId) {
                $q->where('reference_id', $memberId);
            })
            ->whereHas('collection.customer', function ($q) {
                $q->where('code', 'not like', "%msp%");
            })
            ->where('installment_collection_date', '>=', $fromDate)
            ->where('installment_collection_date', '<=', $toDate)
            ->get();

        $mspCHCollections = InstallmentCollectionList::with('collection', 'collection.customerSale', 'collection.customer')
            ->whereHas('collection', function ($q) use ($memberId) {
                $q->where('reference_id', $memberId);
            })
            ->whereHas('collection.customer', function ($q) {
                $q->where('code', 'like', "%msp%");
            })
            ->where('installment_collection_date', '>=', $fromDate)
            ->where('installment_collection_date', '<=', $toDate)
            ->get();


        $cashCollection = 0;
        $higherCollection = 0;
        $mspCCollection = 0;
        $mspHCollection = 0;
        $cashHireCollectionData = $this->memberCashHireCollection($fromDate, $toDate, $memberId);

        $cashCollection = $cashHireCollectionData['cash'];
        $higherCollection = $cashHireCollectionData['higher'];
        $mspCCollection = $cashHireCollectionData['mspCash'];
        $mspHCollection = $cashHireCollectionData['mspHire'];


        $agreement = CustomerAgreement::where('date', '>=', $fromDate)
            ->where('date', '<=', $toDate)
            ->where('employee_id', $memberId)
            ->sum('agreement_amount');

        $commissionPercentage =  CommissionPercentage::first();
        $agrCom = 0;
        if ($agreement > 0) {
            $agrCom = $agreement * ($commissionPercentage->agreement / 100);
        }

        $targetInfo = GroupSalesTargetAmount::select('target_amount')
            ->where('group_sales_target_id', $group->id)
            ->where('staff_id', $memberId)
            ->first();

        $target = $targetInfo->target_amount;


        $higherAmount = 0;
        $mspHAmount = 0;

        if ($higherCollection > 0) {
            $higherAmount = $higherCollection * ($commissionPercentage->r_hire / 100);
        }
        if ($mspHCollection > 0) {
            $mspHAmount = $mspHCollection * ($commissionPercentage->r_msp_hire / 100);
        }


        $totalCollection = $cashCollection + $higherCollection + $mspCCollection + $mspHCollection;


        $average = 0;
        if ($totalCollection > 0) {
            $average = ($totalCollection * 100) / $target;
        }


        $salary = EmployeeAllowance::where('staff_id', $memberId)->first();
        // $totalSalary = 0;
        $totalSalary = 12000;
        if ($salary) {
            $totalSalary = $salary->total_amount;
        }


        $cashCommissionData = $this->cashCommission($memberId, $fromDate, $toDate);

        $cash_com = 0;
        if ($average >= 100) {
            $cash_com = $cashCommissionData['cashCommission'] + $cashCommissionData['cashDiscountCommission'];
        }

        $totalCommission = 0;
        if ($average >= 100) {
            $totalCommission = $higherAmount + $mspHAmount + $cash_com;
        }

        $data = [
            'member' => $staff->name,
            'target' => number_format($target, 2, '.', ''),
            'cashCollection' => number_format($cashCollection, 2, '.', ''),
            'higherCollection' => number_format($higherCollection, 2, '.', ''),
            'mspCCollection' => number_format($mspCCollection, 2, '.', ''),
            'mspHCollection' => number_format($mspHCollection, 2, '.', ''),
            'totalCollection' => number_format($totalCollection, 2, '.', ''),
            'average' => number_format($average, 2, '.', ''),
            'totalCommission' => number_format($totalCommission, 2, '.', ''),
            'salary' => number_format($totalSalary, 2, '.', ''),
            'cash_com' => number_format($cash_com, 2, '.', ''),
            'agreement' => number_format($agreement, 2, '.', ''),
            'agrCom' => number_format($agrCom, 2, '.', ''),
        ];

        return $data;
    }


    public function getCustomerWise($memberId, $fromDate, $toDate)
    {
        $refCUstomers = DB::table('tbl_customers')
            ->select('tbl_customers.id')
            ->join('customer_agreement', 'customer_agreement.customer_id', '=', 'tbl_customers.id')
            ->where('customer_agreement.employee_id', $memberId)
            ->get()
            ->count();

        $collectCustomers = DB::table('tbl_installment_collection')
            ->select('tbl_installment_collection.id')
            ->join('tbl_installment_collection_list', 'tbl_installment_collection_list.installment_collection_id', '=', 'tbl_installment_collection.id')
            ->where('tbl_installment_collection_list.installment_collection_date', '>=', $fromDate)
            ->where('tbl_installment_collection_list.installment_collection_date', '<=', $toDate)
            ->where('tbl_installment_collection.reference_id', $memberId)
            ->groupBy('tbl_installment_collection.customer_id')
            ->get()
            ->count();

        $collectionPercent = 0;
        if ($collectCustomers > 0) {
            if ($refCUstomers > 0) {
                $collectionPercent = ($collectCustomers * 100) / $refCUstomers;

                if ($collectionPercent > 100) {
                    $collectionPercent = 100;
                }
            } else {
                $collectionPercent = 100;
            }
        }

        return number_format($collectionPercent, 2, '.', '');
    }

    private function memberCashHireCollection($fromDate, $toDate, $memberId)
    {

        $retailSales = RetailSales::with('sale', 'sale.customer')
            ->whereHas('sale', function ($q) use ($memberId, $fromDate, $toDate) {
                $q->where('sale_date', '>=', $fromDate)
                    ->where('sale_date', '<=', $toDate)
                    ->where('reference_id', $memberId)
                    ->where('sale_type', 'cash');
            })
            ->whereHas('sale.customer', function ($q) {
                $q->where('code', 'NOT LIKE', "%msp%");
            })
            ->sum('sales_price');


        $mspCash = RetailSales::with('sale', 'sale.customer')
            ->whereHas('sale', function ($q) use ($memberId, $fromDate, $toDate) {
                $q->where('sale_date', '>=', $fromDate)
                    ->where('sale_date', '<=', $toDate)
                    ->where('reference_id', $memberId)
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


        $cash = 0;
        $cashCollectionHistory = InstallmentCollectionList::with(['collection.customer', 'collection.product', 'collection.collector'])
            ->where('installment_schedule_amount', '!=', 0)
            ->where('installment_collection_date', '>=', $fromDate)
            ->where('installment_collection_date', '<=', $toDate)
            ->whereHas('collection.collector', function ($q) use ($memberId) {
                $q->where('id', $memberId);
            })
            ->whereHas('collection.customer', function ($q) {
                $q->where('code', 'NOT LIKE', "%msp%");
            })
            ->sum('installment_schedule_amount');

        $mspCollectionHistory = InstallmentCollectionList::with(['collection.customer', 'collection.product', 'collection.collector'])
            ->where('installment_schedule_amount', '!=', 0)
            ->where('installment_collection_date', '>=', $fromDate)
            ->where('installment_collection_date', '<=', $toDate)
            ->whereHas('collection.collector', function ($q) use ($memberId) {
                $q->where('id', $memberId);
            })
            ->whereHas('collection.customer', function ($q) {
                $q->where('code', 'LIKE', "%msp%");
            })
            ->sum('installment_schedule_amount');

        $cash = $retailSales;
        $higher1 = $cashCollectionHistory - $cash;
        $mspHire = $mspCollectionHistory - $mspCashSale;
        // $higher = $higher1 - $mspCashSale;
        $higher = $higher1;
        // $cashCommission = $cash * ($commissionPercentage->retail_cash / 100);


        $data = [
            'cash' => $cash,
            'higher' => $higher,
            'mspCash' => $mspCashSale,
            'mspHire' => $mspHire,
        ];
        return $data;
    }

    public function mspCollection($fromDate, $toDate)
    {

        $commissionData = $this->mspIndvCollec($fromDate, $toDate);

        $collection = 0;
        if ($commissionData) {
            $collection = $commissionData['commission'];
        }


        $targetInfo = GroupSalesTarget::with('group', 'employees.staff')
            ->whereBetween('date', [$fromDate, $toDate])
            ->where('target_on', 'campaign')
            ->first();

        $data = [];
        $sl = 1;
        if ($targetInfo) {
            foreach ($targetInfo->employees as $staff) {

                if ($sl == 1 || $sl == 2) {
                    $type = 'Leader';
                } else {
                    $type = 'Member';
                }

                $camPercent = CampaignCommission::where('employee_id', $staff->staff->id)->first();

                $amount = 0;
                if ($camPercent && $camPercent->commission > 0) {
                    if ($collection > 0) {
                        $amount = $collection * ($camPercent->commission / 100);
                    }
                }

                $data[] = [
                    'group' => $targetInfo->group->name,
                    'month' => date('M Y', strtotime($targetInfo->date)),
                    'name' => $staff->staff->name . '  -- (' . $type . ')',
                    'type' => $type,
                    'amount' => $amount
                ];

                $sl++;
            }
        }

        return $data;
    }

    public function mspIndvCollec($fromDate, $toDate)
    {

        $data = [];

        $targetInfo = GroupSalesTarget::with('group', 'employees.staff')
            ->whereBetween('date', [$fromDate, $toDate])
            ->where('target_on', 'campaign')
            ->first();

        $data = [];

        if ($targetInfo) {
            $targetAmount = $targetInfo->total_target_amt;

            $mspHCollection =  InstallmentCollectionList::with('collection', 'collection.customer', 'collection.collector')
                ->whereHas('collection.customer', function ($q) {
                    $q->where('code', 'LIKE', "%msp%");
                })
                ->whereHas('collection.collector', function ($q) {
                    $q->where('name', 'MSP-Hire');
                })
                //short by showroom_id
                ->where('showroom_id', $this->showroomId)
                ->where('installment_collection_date', '>=', $fromDate)
                ->where('installment_collection_date', '<=', $toDate)
                ->sum('installment_schedule_amount');




            $commissionPercentage =  CommissionPercentage::first();
            $CashC = 0;
            $CashCC = 0;
            $CashDC = 0;
            $CashDCC = 0;
            $cashData = $this->mspCashCommission($fromDate, $toDate);

            $CashC = $cashData['cash'];
            $CashDC = $cashData['cashDiscount'];
            $mspCCollection = $CashC + $CashDC;

            $total = $CashC + $CashDC + $mspHCollection;

            if ($total > 0 && $total >= floatval($targetInfo->total_target_amt)) {
                $CashCC = $cashData['cashCommission'];
                $CashDCC = $cashData['cashDiscountCommission'];
            }
            $hireC = 0;
            if ($mspHCollection > 0 && $total >= floatval($targetInfo->total_target_amt)) {
                $hireC = $mspHCollection * ($commissionPercentage->r_msp_hire / 100);
            }

            $totalCommission = $CashCC + $CashDCC + $hireC;

            $data = [
                'group' => $targetInfo->group->name,
                'month' => date('M Y', strtotime($targetInfo->date)),
                'cash' => number_format($mspCCollection, 2, '.', ''),
                'haire' => number_format($mspHCollection, 2, '.', ''),
                'total' => number_format($total, 2, '.', ''),
                'target' => number_format($targetAmount, 2, '.', ''),
                'hireC' => number_format($hireC, 2, '.', ''),
                'CashC' => number_format($CashC, 2, '.', ''),
                'CashCC' => number_format($CashCC, 2, '.', ''),
                'CashDC' => number_format($CashDC, 2, '.', ''),
                'CashDCC' => number_format($CashDCC, 2, '.', ''),
                'commission' => number_format($totalCommission, 2, '.', '')
            ];
        }

        return $data;
    }

    public function cashCommission($memberId, $fromDate, $toDate)
    {
        $retailSales = RetailSales::with('sale')
            ->whereHas('sale', function ($q) use ($memberId, $fromDate, $toDate) {
                $q->where('sale_date', '>=', $fromDate)
                    ->where('sale_date', '<=', $toDate)
                    ->where('reference_id', $memberId)
                    //short by showroom_id
                    ->where('showroom_id', $this->showroomId)
                    ->where('sale_type', 'cash');
            })
            ->get();

        $commissionPercentage = CommissionPercentage::first();

        $cash = 0;
        $cashCommission = 0;
        $cashDiscount = 0;
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

                $cashDiscount += $actual_sales;

                $price = $product->price;
                // $price = $sale->sales_price;
                if ($product->price == 0) {
                    // if ($sale->sales_price == 0) {
                    $price = 1;
                }

                $profitLoss = 100 - (($actual_sales * 100) / $price);
                $commissionPercent = 16.8 - $profitLoss;
                // $commissionAmount = ($actual_sales * $commissionPercent) / 100;
                $commissionAmount = $actual_sales;
                // $cashDiscountCommission += $commissionAmount * ($commissionPercentage->cash / 100);
                // $cashDiscountCommission += ($price * ($commissionPercentage->cash / 100)) / (16.80 * $commissionPercent);
                $cashDiscountCommission += ($price * ($commissionPercentage->r_cash / 100) *  $commissionPercent) / 16.80;
            } else {
                $cash += $actual_sales;

                $profitLoss = 100;
                $commissionPercent = 100;
                $cashCommission += $actual_sales * ($commissionPercentage->r_cash / 100);
            }
        }


        $data = [
            'cash' => number_format($cash, 2, '.', ''),
            'cashCommission' => number_format($cashCommission, 2, '.', ''),
            'cashDiscount' => number_format($cashDiscount, 2, '.', ''),
            'cashDiscountCommission' => number_format($cashDiscountCommission, 2, '.', ''),
        ];

        return $data;
    }


    public function mspCashCommission($fromDate, $toDate)
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
                // $cashDiscountCommission += $commissionAmount * ($commissionPercentage->cash / 100);
                // $cashDiscountCommission += ($price * ($commissionPercentage->cash / 100)) / (16.80 * $commissionPercent);
                $cashDiscountCommission += ($price * ($commissionPercentage->r_msp_cash_with_discount_commission / 100) *  $commissionPercent) / 16.80;
            }
        }


        $cash = 0;
        $cashCommission = 0;
        $cashCollectionHistory = InstallmentCollectionList::with(['collection.customer', 'collection.product', 'collection.collector'])
            ->where('installment_schedule_amount', '!=', 0)
            ->where('installment_collection_date', '>=', $fromDate)
            ->where('installment_collection_date', '<=', $toDate)
            ->whereHas('collection.collector', function ($q) {
                $q->where('name', 'MSP-Retail');
            })
            ->where('showroom_id', $showroom)
            ->sum('installment_schedule_amount');


        $cash = $cashCollectionHistory - $cashDiscount;
        $cashCommission = $cash * ($commissionPercentage->r_msp_cash_without_discount / 100);


        $data = [
            'cash' => number_format($cash, 2, '.', ''),
            'cashCommission' => number_format($cashCommission, 2, '.', ''),
            'cashDiscount' => number_format($cashDiscount, 2, '.', ''),
            'cashDiscountCommission' => number_format($cashDiscountCommission, 2, '.', ''),
        ];

        return $data;
    }



    public function list()
    {

        $title = 'Retail Commission List';

        $retails = RetailCommission::orderby('id', 'desc')->get();

        return view('admin.retailTargetAchivement.list.index')->with(compact('title', 'retails'));
    }
    public function view($id)
    {

        $title = 'Retail Commission';

        $retail = RetailCommission::with('list', 'campaign', 'campaign.list')->where('id', $id)->first();

        $pdf = PDF::loadView(
            'admin.retailTargetAchivement.list.print',
            [
                'title' => $title,
                'retail' => $retail,
            ],
            [],
            ['orientation' => 'L']
        );

        return $pdf->stream('Retail Commission.pdf');
    }

    public function save(Request $request)
    {
        $ex = RetailCommission::where('month', $request->month_retail)
            ->where('showroom_id', $request->showroom)
            ->first();
        if ($ex) {
            return back()->with('error', 'Already Exist');
        }

        $retail = RetailCommission::create([
            'showroom_id' => $request->showroom,
            'month' => $request->month_retail,
        ]);


        $campaign = RetailCommissionCampaign::create([
            'foreign_id' => $retail->id,
            'month_year' => $request->month_year,
            'cash' => $request->cash_com_com,
            'hire' => $request->hire_com,
            'total' => $request->total,
            'target' => $request->target_com,
            'cash_without_dis_col' => $request->cash_without_dis_col,
            'cash_without_dis_com' => $request->cash_without_dis_com,
            'cash_with_dis_col' => $request->cash_with_dis_col,
            'cash_with_dis_com' => $request->cash_with_dis_com,
            'hire_com' => $request->hire_com_com,
            'total_com' => $request->total_com_com,
        ]);




        $countRetail = count($request->group_name);
        for ($i = 0; $i < $countRetail; $i++) {
            RetailCommissionList::create([
                'foreign_id' => $retail->id,
                'group_name' => $request->group_name[$i],
                'month' => $request->month[$i],
                'employee' => $request->employee[$i],
                'salary' => $request->salary[$i],
                'customer' => $request->customer[$i],
                'c_salary' => $request->c_salary[$i],
                'target' => $request->target[$i],
                'cash' => $request->cash[$i],
                'hire' => $request->hire[$i],
                'msp_c' => $request->msp_c[$i],
                'msp_h' => $request->msp_h[$i],
                'total_ach' => $request->total_ach[$i],
                'achieve' => $request->achieve[$i],
                'cash_com' => $request->cash_com[$i],
                'total_com' => $request->total_com[$i],
                'com_salary' => $request->com_salary[$i],
                'agreement' => $request->agreement[$i],
                'agreement_com' => $request->agreement_com[$i],
                'net_salary' => $request->net_salary[$i],
            ]);
        }




        $countCampaign = count($request->group_name_com);
        for ($j = 0; $j < $countCampaign; $j++) {
            RetailCommissionCampaignList::create([
                'foreign_id' => $campaign->id,
                'group_name' => $request->group_name_com[$j],
                'month_year' => $request->month_year_com[$j],
                'employee' => $request->employee_com[$j],
                'commission' => $request->commission[$j],
            ]);
        }



        return redirect()->route('retailTargetAchivement.list');
    }


    public function delete(Request $request)
    {
        $retail = RetailCommission::find($request->id);
        $campaign = RetailCommissionCampaign::where('foreign_id', $request->id)->first();

        RetailCommissionList::where('foreign_id', $retail->id)->delete();
        RetailCommissionCampaignList::where('foreign_id', $campaign->id)->delete();

        $retail->delete();
        $campaign->delete();

        print_r(1);
        return;
    }
}
