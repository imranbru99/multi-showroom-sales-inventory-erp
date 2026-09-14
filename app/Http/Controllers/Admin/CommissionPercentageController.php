<?php

namespace App\Http\Controllers\Admin;

use App\CommissionPercentage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class CommissionPercentageController extends Controller
{
    public function index()
    {
        $title = "Edit Commission Percentage";

        $commission = CommissionPercentage::first();

        $formLink = 'commissionpercentage.update';
        $buttonName = "Update";

        return view('admin.commissionPercentage.edit')->with(compact('title', 'buttonName', 'formLink', 'commission'));
    }

    public function update(Request $request)
    {
        $commission = CommissionPercentage::first();

        $commission->update([
            'dealer' => $request->dealer ? $request->dealer : 0,
            'd_retail_discount' => $request->d_retail_discount ? $request->d_retail_discount : 0,
            'd_retail_cash_without_discount' => $request->d_retail_cash_without_discount ? $request->d_retail_cash_without_discount : 0,
            'd_retail_cash_with_discount' => $request->d_retail_cash_with_discount ? $request->d_retail_cash_with_discount : 0,
            'd_retail_hire' => $request->d_retail_hire ? $request->d_retail_hire : 0,
            'd_recovery_cash' => $request->d_recovery_cash ? $request->d_recovery_cash : 0,
            'd_recovery_hire' => $request->d_recovery_hire ? $request->d_recovery_hire : 0,
            'd_msp_cash_discount' => $request->d_msp_cash_discount ? $request->d_msp_cash_discount : 0,
            'd_msp_cash_wihout_discount' => $request->d_msp_cash_wihout_discount ? $request->d_msp_cash_wihout_discount : 0,
            'd_msp_cash_with_discount' => $request->d_msp_cash_with_discount ? $request->d_msp_cash_with_discount : 0,
            'd_msp_hire' => $request->d_msp_hire ? $request->d_msp_hire : 0,
            'r_cash' => $request->r_cash ? $request->r_cash : 0,
            'r_hire' => $request->r_hire ? $request->r_hire : 0,
            'r_msp' => $request->r_msp ? $request->r_msp : 0,
            'r_msp_hire' => $request->r_msp_hire ? $request->r_msp_hire : 0,
            'r_msp_cash_without_discount' => $request->r_msp_cash_without_discount ? $request->r_msp_cash_without_discount : 0,
            'r_msp_cash_with_discount_commission' => $request->r_msp_cash_with_discount_commission ? $request->r_msp_cash_with_discount_commission : 0,
            'agreement' => $request->agreement ? $request->agreement : 0,
        ]);

        return redirect(route('commissionpercentage.index'))->with('msg', 'Commission Percentage Updated Successfully');
    }
}
