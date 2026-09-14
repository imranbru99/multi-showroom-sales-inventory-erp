<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\StaffSetup;
use App\Installment;
use App\InstallmentSchedule;
use App\AdvanceCollection;
use App\GroupSetup;
use App\EmployeeAllowance;
use DB;
use PDF;

class SalaryAchivementController extends Controller {

    public function index(Request $request) {
        $title = "Salary Eligibility";
        $searchFormLink = "salaryAchivement.index";
        $printFormLink = "salaryAchivement.print";
        $groupParam = $request->group;

        $reqMonth = $request->month;
        $reqYear = $request->year;

        $fromDate = date('Y-m-d', strtotime('01-' . $reqMonth . '-' . $reqYear));
        $toDate = date('Y-m-t', strtotime('01-' . $reqMonth . '-' . $reqYear));

        // dd($fromDate, $toDate);

        $print = $request->print;
        $groupList = GroupSetup::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'ASC')
                ->get();

        $groups = DB::table('tbl_groups_sales_target')
                ->select('tbl_groups_sales_target.group_id', 'tbl_groups.team_member as teamMembers')
                ->leftJoin('tbl_groups', 'tbl_groups.id', '=', 'tbl_groups_sales_target.group_id')
                ->where(function ($query) use ($groupParam) {
                    if ($groupParam) {
                        $query->whereIn('group_id', $groupParam);
                    }
                })
                ->whereBetween('date', [$fromDate, $toDate])
                ->where('tbl_groups_sales_target.company_id', $this->company)
                ->where('tbl_groups_sales_target.showroom_id', $this->showroomId)
                ->groupBy('tbl_groups_sales_target.group_id')
                ->get();

        // dd($groupSalesTarget);
        $targets = [];
        foreach ($groups as $group) {

            $groupMemberList = DB::table('view_group_and_staff')
                    ->select('view_group_and_staff.groupId', 'view_group_and_staff.staffId', 'tbl_staffs.name as staffName')
                    ->join('tbl_staffs', 'tbl_staffs.id', '=', 'view_group_and_staff.staffId')
                    ->where('view_group_and_staff.groupId', $group->group_id)
                    ->get();

            $groupSalesTarget = DB::table('tbl_groups_sales_target')
                    ->select('tbl_groups_sales_target.*', 'tbl_groups.*')
                    ->leftJoin('tbl_groups', 'tbl_groups.id', '=', 'tbl_groups_sales_target.group_id')
                    ->whereBetween('date', [$fromDate, $toDate])
                    ->where('tbl_groups_sales_target.group_id', $group->group_id)
                    ->get();

            $members = [];
            foreach ($groupMemberList as $list) {
                $members[] = $list->staffName;
            }
            $month = [];
            foreach ($groupSalesTarget as $groupSales) {
                $month[] = date('M Y', strtotime($groupSales->date));
            }

            $collectionAmount = DB::table('tbl_dealer_collections')
                    ->where('payment_date', '>=', $fromDate)
                    ->where('payment_date', '<=', $toDate)
                    ->where('remarks', '!=', 'adjust')
                    ->whereIn('sale_by', [$group->teamMembers])
                    ->sum('payment_amount');

            $advanceCollection = AdvanceCollection::with('dealer')
                    ->whereIn('sale_by', [$group->teamMembers])
                    ->where('date', '>=', $fromDate)
                    ->where('date', '<=', $toDate)
                    ->where('advance_amount', '>', 0)
                    ->sum('advance_amount');

            $totalCollectionAmount = $collectionAmount + $advanceCollection;

            $target = $groupSalesTarget->sum('total_target_amt');
            $difference = $target - $totalCollectionAmount;

            $average = ($totalCollectionAmount * 100) / $target;


            //Salary Achieve
            $salary = EmployeeAllowance::whereIn('staff_id', [$group->teamMembers])->sum('total_amount');

            $salaryAchieve = ($salary * $average) / 100;

            $targets[] = [
                'group' => $groupSalesTarget[0]->name,
                'members' => $members,
                'month' => $month,
                'target' => number_format($target, 2, '.', ''),
                'collection' => number_format($totalCollectionAmount, 2, '.', ''),
                'difference' => number_format($difference, 2, '.', ''),
                'average' => number_format($average, 2, '.', ''),
                'salaryAchieve' => number_format($salaryAchieve, 2, '.', ''),
            ];
        }

        $sortting = array_column($targets, 'average');
        array_multisort($sortting, SORT_DESC, $targets);

        return view('admin.salaryAchievement.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'groupList', 'targets', 'groupParam', 'fromDate', 'toDate', 'reqMonth', 'reqYear'));
    }

    public function print(Request $request) {
        $title = "Salary Eligibility";

        $groupParam = $request->group;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        $groups = DB::table('tbl_groups_sales_target')
                ->select('tbl_groups_sales_target.group_id', 'tbl_groups.team_member as teamMembers')
                ->leftJoin('tbl_groups', 'tbl_groups.id', '=', 'tbl_groups_sales_target.group_id')
                ->where(function ($query) use ($groupParam) {
                    if ($groupParam) {
                        $query->whereIn('group_id', $groupParam);
                    }
                })
                ->whereBetween('date', [$fromDate, $toDate])
                ->where('tbl_groups_sales_target.showroom_id', $this->showroomId)
                ->groupBy('tbl_groups_sales_target.group_id')
                ->get();

        // dd($groupSalesTarget);
        $targets = [];
        foreach ($groups as $group) {

            $groupMemberList = DB::table('view_group_and_staff')
                    ->select('view_group_and_staff.groupId', 'view_group_and_staff.staffId', 'tbl_staffs.name as staffName')
                    ->join('tbl_staffs', 'tbl_staffs.id', '=', 'view_group_and_staff.staffId')
                    ->where('view_group_and_staff.groupId', $group->group_id)
                    ->get();

            $groupSalesTarget = DB::table('tbl_groups_sales_target')
                    ->select('tbl_groups_sales_target.*', 'tbl_groups.*')
                    ->leftJoin('tbl_groups', 'tbl_groups.id', '=', 'tbl_groups_sales_target.group_id')
                    ->whereBetween('date', [$fromDate, $toDate])
                    ->where('tbl_groups_sales_target.group_id', $group->group_id)
                    ->get();

            $members = [];
            foreach ($groupMemberList as $list) {
                $members[] = $list->staffName;
            }
            $month = [];
            foreach ($groupSalesTarget as $groupSales) {
                $month[] = date('M Y', strtotime($groupSales->date));
            }

            $collectionAmount = DB::table('tbl_dealer_collections')
                    ->where('payment_date', '>=', $fromDate)
                    ->where('payment_date', '<=', $toDate)
                    ->where('remarks', '!=', 'adjust')
                    ->whereIn('sale_by', [$group->teamMembers])
                    ->sum('payment_amount');

            $advanceCollection = AdvanceCollection::with('dealer')
                    ->whereIn('sale_by', [$group->teamMembers])
                    ->where('date', '>=', $fromDate)
                    ->where('date', '<=', $toDate)
                    ->where('advance_amount', '>', 0)
                    ->sum('advance_amount');

            $totalCollectionAmount = $collectionAmount + $advanceCollection;

            $target = $groupSalesTarget->sum('total_target_amt');
            $difference = $target - $totalCollectionAmount;

            $average = ($totalCollectionAmount * 100) / $target;


            //Salary Achieve
            $salary = EmployeeAllowance::whereIn('staff_id', [$group->teamMembers])->sum('total_amount');
            if ($salary) {
                $salaryAchieve = ($salary * $average) / 100;
            } else {
                $salaryAchieve = 0;
            }


            $targets[] = [
                'group' => $groupSalesTarget[0]->name,
                'members' => $members,
                'month' => $month,
                'target' => number_format($target, 2, '.', ''),
                'collection' => number_format($totalCollectionAmount, 2, '.', ''),
                'difference' => number_format($difference, 2, '.', ''),
                'average' => number_format($average, 2, '.', ''),
                'salaryAchieve' => number_format($salaryAchieve, 2, '.', ''),
            ];
        }

        $sortting = array_column($targets, 'average');
        array_multisort($sortting, SORT_DESC, $targets);

        $pdf = PDF::loadView('admin.salaryAchievement.print', ['title' => $title, 'groupParamgroupParam' => $groupParam, 'fromDate' => $fromDate, 'toDate' => $toDate, 'targets' => $targets]);

        return $pdf->stream('salary_eligibility.pdf');
    }

}
