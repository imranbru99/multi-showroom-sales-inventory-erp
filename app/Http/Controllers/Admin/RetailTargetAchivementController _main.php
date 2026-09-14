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
        foreach ($CHCollections as $CHCollection) {
            if ($CHCollection->collection->customerSale) {
                if ($CHCollection->collection->customerSale->sale_type == 'cash') {
                    $cashCollection +=  floatval($CHCollection->installment_schedule_amount);
                } else {
                    $higherCollection +=  floatval($CHCollection->installment_schedule_amount);
                }
            } else {
                $higherCollection +=  floatval($CHCollection->installment_schedule_amount);
            }
        }
        foreach ($mspCHCollections as $mspCHCollection) {
            if ($mspCHCollection->collection->customerSale) {
                if ($mspCHCollection->collection->customerSale->sale_type == 'cash') {
                    $mspCCollection +=  floatval($mspCHCollection->installment_schedule_amount);
                } else {
                    $mspHCollection +=  floatval($mspCHCollection->installment_schedule_amount);
                }
            } else {
                $mspHCollection +=  floatval($mspCHCollection->installment_schedule_amount);
            }
        }

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
            $higherAmount = $higherCollection * ($commissionPercentage->hire / 100);
        }
        if ($mspHCollection > 0) {
            $mspHAmount = $mspHCollection * ($commissionPercentage->msp_hire / 100);
        }


        $totalCollection = $cashCollection + $higherCollection + $mspCCollection + $mspHCollection;


        $average = 0;
        if ($totalCollection > 0) {
            $average = ($totalCollection * 100) / $target;
        }
        $totalCommission = 0;
        if ($average >= 100) {
            $totalCommission = $higherAmount + $mspHAmount;
        }


        $salary = EmployeeAllowance::where('staff_id', $memberId)->first();
        $totalSalary = 0;
        if ($salary) {
            $totalSalary = $salary->total_amount;
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
            'cash_com' => $this->cashCommission($memberId, $fromDate, $toDate),
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
            } else {
                $collectionPercent = 100;
            }
        }

        return number_format($collectionPercent, 2, '.', '');
    }



    public function mspCollection($fromDate, $toDate)
    {
        $collection = InstallmentCollectionList::with('collection', 'collection.customer')
            ->whereHas('collection.customer', function ($q) {
                $q->where('code', 'LIKE', "%msp%");
            })
            ->where('installment_collection_date', '>=', $fromDate)
            ->where('installment_collection_date', '<=', $toDate)
            ->sum('installment_schedule_amount');

        $commissionPercentage =  CommissionPercentage::first();
        $commissionAmount = 0;
        if ($collection > 0) {
            $commissionAmount = $collection * ($commissionPercentage->msp_hire / 100);
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
                    if ($commissionAmount > 0) {
                        $amount = $commissionAmount * ($camPercent->commission / 100);
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


        if ($targetInfo) {

            $mspCHCollections = InstallmentCollectionList::with('collection', 'collection.customerSale', 'collection.customer')
                ->whereHas('collection.customer', function ($q) {
                    $q->where('code', 'like', "%msp%");
                })
                ->where('installment_collection_date', '>=', $fromDate)
                ->where('installment_collection_date', '<=', $toDate)
                ->get();

            $mspCCollection = 0;
            $mspHCollection = 0;
            foreach ($mspCHCollections as $mspCHCollection) {
                if ($mspCHCollection->collection->customerSale) {
                    if ($mspCHCollection->collection->customerSale->sale_type == 'cash') {
                        $mspCCollection +=  floatval($mspCHCollection->installment_schedule_amount);
                    } else {
                        $mspHCollection +=  floatval($mspCHCollection->installment_schedule_amount);
                    }
                } else {
                    $mspHCollection +=  floatval($mspCHCollection->installment_schedule_amount);
                }
            }

            $total = $mspCCollection + $mspHCollection;

            $commissionPercentage =  CommissionPercentage::first();
            $commission = 0;
            if ($total > 0 && $total >= floatval($targetInfo->total_target_amt)) {
                $commission = $total * ($commissionPercentage->msp_hire / 100);
            }

            $data = [
                'group' => $targetInfo->group->name,
                'month' => date('M Y', strtotime($targetInfo->date)),
                'cash' => number_format($mspCCollection, 2, '.', ''),
                'haire' => number_format($mspHCollection, 2, '.', ''),
                'total' => number_format($total, 2, '.', ''),
                'commission' => number_format($commission, 2, '.', '')
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
                    ->where('sale_type', 'cash');
            })
            ->get();

        $totalCommission = 0;
        foreach ($retailSales as $sale) {
            $product = Product::find($sale->product_id);
            $profit = 0;
            if ($product->price > 0) {
                $profit = ($product->price * 16.8) / 100;
            }

            $diff = $product->price - $sale->sales_price;

            $commissionPercentage = CommissionPercentage::first();

            $comDif = $profit - $diff;
            $cashCom = 0;
            if ($comDif > 0) {
                $cashCom = $comDif * ($commissionPercentage->cash / 100);
                $totalCommission += $cashCom;
            }
        }

        return number_format($totalCommission, 2, '.', '');
    }
}
