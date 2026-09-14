<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DealerRequisition;
use App\DealerRequisitionProduct;
use App\Admin;
use DB;
use PDF;
use MPDF;

class DealerFinalApprovalController extends Controller {

    public function index() {
        $title = "All Pending Final Requisitions";
        $formLink = "dealerFinalRequisitionApproval.update";
        $buttonName = "Save";

        $dealerRequisitions = DealerRequisition::with(['dealer'])
                ->where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('final_status', '1')
                ->where('suggested', '>', '0')
                ->orderBy('date', 'dsc')
                ->get();

        $approveDealerRequisitions = DealerRequisition::with(['dealer', 'requisitions'])
                ->where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('approved_by2', '!=', '0')
                ->orderBy('date', 'dsc')
                ->get();

        return view('admin.dealerFinalRequisitionApproval.index')->with(compact('title', 'formLink', 'buttonName', 'dealerRequisitions', 'approveDealerRequisitions'));
    }

    public function update(Request $request) {

        // dd($request->all());

        $dealerRequisition = DealerRequisition::find($request->dealerRequisitionId);

        $dealerRequisition->update([
            'approved_by2' => $request->approveBy,
            'total_qty2' => $request->totalFinalApproveQty,
            'total_amount2' => $request->totalFinalApproveAmount,
            'final_status' => 0,
            'updated_by' => $this->userId
        ]);

        $countDealerRequisitionProduct = count($request->dealerRequisitionProductId);
        if ($request->dealerRequisitionProductId) {
            for ($i = 0; $i < $countDealerRequisitionProduct; $i++) {
                $dealerRequisitionProduct = DealerRequisitionProduct::where('id', $request->dealerRequisitionProductId[$i])->first();

                $dealerRequisitionProduct->update([
                    'approved_qty2' => $request->finalApproveQty[$i],
                    'approved_amount2' => $request->finalApproveAmount[$i],
                    'approved_by2' => $request->approveBy,
                    'final_status' => 0,
                    'updated_by' => $this->userId
                ]);
            }
        }

        return redirect(route('dealerFinalRequisitionApproval.index'))->with('msg', 'Dealer Requisition Approved Successfully');
    }

    public function dealerRequisitionInfo(Request $request) {
        $dealerRequisition = DealerRequisition::select('tbl_dealer_requisitions.*', 'tbl_dealers.name as dealerName')
                ->leftJoin('tbl_dealers', 'tbl_dealers.id', '=', 'tbl_dealer_requisitions.dealer_id')
                ->where('tbl_dealer_requisitions.id', $request->dealerRequisitionId)
                // ->where('tbl_dealer_requisitions.status','1')
                ->first();

        $dealerRequisitionProducts = DealerRequisitionProduct::select('tbl_dealer_requisition_products.*', 'tbl_products.name as productName', 'tbl_products.model_no as modelNo')
                ->leftJoin('tbl_products', 'tbl_products.id', '=', 'tbl_dealer_requisition_products.product_id')
                ->where('tbl_dealer_requisition_products.requisition_id', $request->dealerRequisitionId)
                ->get();
        $user = Admin::where('id', $dealerRequisition->approved_by)->first();
        if (!empty($user)) {
            $user_name = $user->name;
        } else {
            $user_name = "";
        }
        if ($request->ajax()) {
            return response()->json([
                        'dealerRequisition' => $dealerRequisition,
                        'dealerRequisitionProducts' => $dealerRequisitionProducts,
                        'userName' => $user_name,
            ]);
        }
    }

}
