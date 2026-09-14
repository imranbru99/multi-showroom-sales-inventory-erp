<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\StaffSetup;
use App\CampaignCommission;

class CampaignCommissionSetupController extends Controller
{

	public function index()
	{
		$title = "Commission Setup";

		$commissions = CampaignCommission::get();

		return view('admin.campaignCommissionSetup.index')->with(compact('title', 'commissions'));
	}

	public function add()
	{
		$title = "Add Commission";
		$formLink = "campaignCommissionSetup.save";
		$buttonName = "Save";

		$existIds = CampaignCommission::get()->pluck('employee_id')->toArray();

		$staffs = StaffSetup::whereNotIn('id', $existIds)->get();

		return view('admin.campaignCommissionSetup.add')->with(compact('title', 'formLink', 'buttonName', 'staffs'));
	}

	public function save(Request $request)
	{

		CampaignCommission::create([
			'showroom_id' => $this->showroomId,
			'employee_id' => $request->employee,
			'commission' => $request->commission > 0 ? $request->commission : 0,
		]);

		return redirect(route('campaignCommissionSetup.index'))->with('msg', 'Commission Added Successfully');
	}

	public function edit($id)
	{
		$title = "Edit Commission";
		$formLink = "campaignCommissionSetup.update";
		$buttonName = "Update";

		$campaign = CampaignCommission::find($id);

		$staffs = StaffSetup::get();

		return view('admin.campaignCommissionSetup.edit')->with(compact('title', 'formLink', 'buttonName', 'staffs', 'campaign'));
	}

	public function update(Request $request)
	{
		$campaign = CampaignCommission::find($request->id);

		$campaign->update([
			'employee_id' => $request->employee,
			'commission' => $request->commission > 0 ? $request->commission : 0,
		]);

		return redirect(route('campaignCommissionSetup.index'))->with('msg', 'Commission Updated Successfully');
	}

	public function delete(Request $request)
	{
		CampaignCommission::find($request->id)->delete();
	}

}
