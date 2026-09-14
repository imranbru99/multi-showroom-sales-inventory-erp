<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DealerCommission;
use App\DealerCommissionList;
use App\CategorySetup;
use App\DealerSetup;

class DealerCommissionController extends Controller {

    public function index() {
        $title = "Dealer Commission";

        $categories = CategorySetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->whereNull('parent')
                ->orderBy('name', 'asc')
                ->get();

        $commissions = DealerCommission::with('dealer')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get();

        return view('admin.dealerCommission.index')->with(compact('title', 'categories', 'commissions'));
    }

    public function add() {
        $title = "Add Dealer Commission";
        $formLink = "dealerCommission.save";
        $buttonName = "Save";

        $dealers = DealerSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        $categories = CategorySetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                // ->whereNull('parent')
                ->orderBy('name', 'asc')
                ->get();

        return view('admin.dealerCommission.add')->with(compact('title', 'formLink', 'buttonName', 'dealers', 'categories'));
    }

    public function save(Request $request) {
        $this->validate(request(), [
            'dealer' => 'required|unique:tbl_dealer_commission,dealer_id',
        ]);

        $commission = DealerCommission::create([
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'dealer_id' => $request->dealer,
                    'created_by' => $this->userId,
                    'status' => 1,
        ]);

        $countCategory = count($request->categoryId);
        if ($request->categoryId) {
            $postData = [];
            for ($i = 0; $i < $countCategory; $i++) {
                $postData[] = [
                    'dealer_commission_id' => $commission->id,
                    'category_id' => $request->categoryId[$i],
                    'category_name' => $request->categoryName[$i],
                    'commission' => $request->commission[$i],
                ];
            }
            DealerCommissionList::insert($postData);
        }


        return redirect(route('dealerCommission.index'))->with('msg', 'Dealer Commission Added Successfully');
    }

    public function edit($id) {
        $title = "Edit Dealer Commission";
        $formLink = "dealerCommission.update";
        $buttonName = "Update";


        $commission = DealerCommission::find($id);
        $commissionList = DealerCommissionList::where('dealer_commission_id', $id)->get();

        $commissionCat = DealerCommissionList::where('dealer_commission_id', $id)
                ->get()
                ->pluck('category_id')
                ->toArray();

        $dealers = DealerSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        $categories = CategorySetup::where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->whereNull('parent')
                ->whereNotIn('id', $commissionCat)
                ->orderBy('name', 'asc')
                ->get();


        return view('admin.dealerCommission.edit')->with(compact('title', 'formLink', 'buttonName', 'categories', 'commission', 'dealers', 'commissionList'));
    }

    public function update(Request $request) {
        $commission = DealerCommission::find($request->id);

        $commission->update([
            'dealer_id' => $request->dealer,
            'created_by' => $this->userId,
            'status' => 1,
        ]);


        DealerCommissionList::where('dealer_commission_id', $commission->id)->delete();

        $countCategory = count($request->categoryId);
        if ($request->categoryId) {
            $postData = [];
            for ($i = 0; $i < $countCategory; $i++) {
                $postData[] = [
                    'dealer_commission_id' => $commission->id,
                    'category_id' => $request->categoryId[$i],
                    'category_name' => $request->categoryName[$i],
                    'commission' => $request->commission[$i],
                ];
            }
            DealerCommissionList::insert($postData);
        }

        return redirect(route('dealerCommission.index'))->with('msg', 'Dealer Commission Updated Successfully');
    }

    public function status(Request $request) {
        $commission = DealerCommission::find($request->id);

        if ($commission->status == 1) {
            $commission->update([
                'status' => 0
            ]);
        } else {
            $commission->update([
                'status' => 1
            ]);
        }
    }

    public function delete(Request $request) {
        DealerCommissionList::where('dealer_commission_id', $request->id)->delete();
        DealerCommission::where('id', $request->id)->delete();
    }

}
