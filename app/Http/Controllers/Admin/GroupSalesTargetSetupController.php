<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GroupSetup;
use App\GroupSalesTarget;
use App\GroupSalesTargetCategory;
use App\CategorySetup;

class GroupSalesTargetSetupController extends Controller {

    public function index() {
        $title = "Group Sales Target";

        if ($this->userRole == 1) {
            $groupSalesTergets = GroupSalesTarget::select('tbl_groups_sales_target.*', 'tbl_groups.name as groupName')
                    ->join('tbl_groups', 'tbl_groups.id', '=', 'tbl_groups_sales_target.group_id')
//                    ->where('tbl_groups_sales_target.showroom_id', $this->showroomId)
//                    ->where('tbl_groups_sales_target.company_id', $this->company)
                    ->orderBy('tbl_groups_sales_target.year', 'asc')
                    ->orderBy('tbl_groups_sales_target.month', 'asc')
                    ->orderBy('tbl_groups.name', 'asc')
                    ->get();
        } else {
            $groupSalesTergets = GroupSalesTarget::select('tbl_groups_sales_target.*', 'tbl_groups.name as groupName')
                    ->join('tbl_groups', 'tbl_groups.id', '=', 'tbl_groups_sales_target.group_id')
//                    ->where('tbl_groups_sales_target.showroom_id', $this->showroomId)
                    ->where('tbl_groups_sales_target.company_id', $this->company)
                    ->orderBy('tbl_groups_sales_target.year', 'asc')
                    ->orderBy('tbl_groups_sales_target.month', 'asc')
                    ->orderBy('tbl_groups.name', 'asc')
                    ->get();
        }


        return view('admin.groupSalesTargetSetup.index')->with(compact('title', 'groupSalesTergets'));
    }

    public function add() {
        $title = "Add Group Sales Target";
        $formLink = "groupSalesTargetSetup.save";
        $buttonName = "Save";

        $groups = GroupSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        $categories = CategorySetup::where('status', '1')
                ->where('company_id', $this->company)
                ->whereNull('parent')
                ->orderBy('name', 'asc')
                ->get();

        return view('admin.groupSalesTargetSetup.add')->with(compact('title', 'formLink', 'buttonName', 'groups', 'categories'));
    }

    public function save(Request $request) {

        if ($request->targetType == 0) {
            $groupSalesTarget = GroupSalesTarget::create([
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'group_id' => $request->group,
                        'year' => $request->year,
                        'month' => $request->month,
                        'date' => $request->year . '-' . $request->month . '-01',
                        'total_target' => $request->totalTarget,
                        'total_target_amt' => $request->totalTargetAmt,
                        'target_type' => $request->targetType,
                        'created_by' => $this->userId
            ]);

            $countCategory = count($request->categoryId);
            if ($request->categoryId) {
                $postData = [];
                for ($i = 0; $i < $countCategory; $i++) {
                    $postData[] = [
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'group_sales_target_id' => $groupSalesTarget->id,
                        'category_id' => $request->categoryId[$i],
                        'target' => $request->targets[$i],
                        'target_amt' => $request->targets_amt[$i],
                        'created_by' => $this->userId
                    ];
                }
                GroupSalesTargetCategory::insert($postData);
            }
        } elseif ($request->targetType == 1) {
            $groupSalesTarget = GroupSalesTarget::create([
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'group_id' => $request->group,
                        'year' => $request->year,
                        'month' => $request->month,
                        'date' => $request->year . '-' . $request->month . '-01',
                        'total_target' => 0,
                        'total_target_amt' => $request->totalTargetAmont,
                        'target_type' => $request->targetType,
                        'created_by' => $this->userId
            ]);


            $countCategory = count($request->category_id);
            if ($request->category_id) {
                $postData = [];
                for ($i = 0; $i < $countCategory; $i++) {
                    $postData[] = [
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'group_sales_target_id' => $groupSalesTarget->id,
                        'category_id' => $request->category_id[$i],
                        // 'target' => $request->targets[$i],
                        'target_amt' => $request->targets_amont[$i],
                        'created_by' => $this->userId
                    ];
                }
                GroupSalesTargetCategory::insert($postData);
            }

        }

        return redirect(route('groupSalesTargetSetup.index'))->with('msg', 'Group Sales Target Added Successfully');
    }

    public function edit($groupSalesTargetId) {
        $title = "Edit Group Slaes Target";
        $formLink = "groupSalesTargetSetup.update";
        $buttonName = "Update";

        $groups = GroupSetup::where('status', '1')->orderBy('name', 'asc')->get();
        $categories = CategorySetup::where('status', '1')
                ->where('company_id', $this->company)
                ->whereNull('parent')
                ->orderBy('name', 'asc')
                ->get();

        $groupSalesTarget = GroupSalesTarget::where('id', $groupSalesTargetId)->first();
        $groupSalesTargetCategories = GroupSalesTargetCategory::where('group_sales_target_id', $groupSalesTargetId)->get();

        // dd($groupSalesTarget);

        return view('admin.groupSalesTargetSetup.edit')->with(compact('title', 'formLink', 'buttonName', 'groups', 'categories', 'groupSalesTarget', 'groupSalesTargetCategories'));
    }

    public function update(Request $request) {
        $groupSalesTargetId = $request->groupSalesTargetId;

        $groupSalesTarget = GroupSalesTarget::find($groupSalesTargetId);
        if ($request->targetType == 0) {
            $groupSalesTarget->update([
                'company_id' => $this->company,
                'showroom_id' => $this->showroomId,
                'group_id' => $request->group,
                'year' => $request->year,
                'month' => $request->month,
                'date' => $request->year . '-' . $request->month . '-01',
                'total_target' => $request->totalTarget,
                'total_target_amt' => $request->totalTargetAmt,
                'target_type' => $request->targetType,
                'updated_by' => $this->userId
            ]);

            GroupSalesTargetCategory::where('group_sales_target_id', $groupSalesTargetId)->delete();

            $countCategory = count($request->categoryId);
            if ($request->categoryId) {
                $postData = [];
                for ($i = 0; $i < $countCategory; $i++) {
                    $postData[] = [
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'group_sales_target_id' => $groupSalesTarget->id,
                        'category_id' => $request->categoryId[$i],
                        'target' => $request->targets[$i],
                        'target_amt' => $request->targets_amt[$i],
                        'updated_by' => $this->userId
                    ];
                }
                GroupSalesTargetCategory::insert($postData);
            }
        } elseif ($request->targetType == 1) {
            GroupSalesTargetCategory::where('group_sales_target_id', $groupSalesTargetId)->delete();

            $groupSalesTarget->update([
                'company_id' => $this->company,
                'showroom_id' => $this->showroomId,
                'group_id' => $request->group,
                'year' => $request->year,
                'month' => $request->month,
                'date' => $request->year . '-' . $request->month . '-01',
                'total_target' => 0,
                'total_target_amt' => $request->totalTargetAmount,
                'target_type' => $request->targetType,
                'updated_by' => $this->userId
            ]);
        }

        return redirect(route('groupSalesTargetSetup.index'))->with('msg', 'Group Sales Target Updated Successfully');
    }

    public function delete(Request $request) {
        $groupSalesTargetId = $request->groupSalesTargetId;
        GroupSalesTarget::where('id', $groupSalesTargetId)->delete();
        GroupSalesTargetCategory::where('group_sales_target_id', $groupSalesTargetId)->delete();
    }

}
