<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DealerRequisition;
use App\DealerRequisitionProduct;
use DB;
use PDF;
use MPDF;

class DealerRequisitionApprovalController extends Controller {

    public function index() {
        $title = "All Pending Requisitions";
        $formLink = "dealerRequisitionApproval.update";
        $buttonName = "Save";

        $dealerRequisitions = DealerRequisition::with(['dealer'])
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->where('suggested', '>', '1')
                ->whereNull('approved_by2')
                ->orderBy('date', 'dsc')
                ->get();


        $approveDealerRequisitions = DealerRequisition::with(['dealer'])
                ->where('status', '1')
                ->where('suggested', '>', '1')
                ->whereNotNull('approved_by2')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('date', 'dsc')
                ->get();

        return view('admin.dealerRequisitionApproval.index')->with(compact('title', 'formLink', 'buttonName', 'dealerRequisitions', 'approveDealerRequisitions'));
    }

    public function suggestionIndex() {
        $title = "All Requisition Suggestion";
        $formLink = "dealerRequisitionSuggestion.update";
        $buttonName = "Save";

        $dealerRequisitions = DealerRequisition::with(['dealer'])
                ->where('status', '1')
                ->where('suggested', '<', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('date', 'dsc')
                ->get();


        $approveDealerRequisitions = DealerRequisition::with(['dealer'])
                ->where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('suggested', '>', '0')
                ->orderBy('date', 'dsc')
                ->get();

        // dd($approveDealerRequisitions);

        return view('admin.dealerRequisitionApproval.suggestion_index')->with(compact('title', 'formLink', 'buttonName', 'dealerRequisitions', 'approveDealerRequisitions'));
    }

    public function update(Request $request) {

        $dealerRequisition = DealerRequisition::find($request->dealerRequisitionId);


        $dealerRequisition->update([
            'approved_by2' => $request->approveBy,
            'total_qty' => $request->totalApproveQty,
            'total_amount' => $request->totalApproveAmount,
            'status' => 1,
            'updated_by' => $this->userId
        ]);


        $countDealerRequisitionProduct = count($request->dealerRequisitionProductId);
        if ($request->dealerRequisitionProductId) {
            for ($i = 0; $i < $countDealerRequisitionProduct; $i++) {
                $dealerRequisitionProduct = DealerRequisitionProduct::where('id', $request->dealerRequisitionProductId[$i])->first();

                $dealerRequisitionProduct->update([
                    'approved_qty' => $request->approveQty[$i],
                    'approved_amount' => $request->approveAmount[$i],
                    'status' => 1,
                    'updated_by' => $this->userId
                ]);
            }
        }

        return redirect(route('dealerRequisitionApproval.index'))->with('msg', 'Dealer Requisition Approved Successfully');
    }

    public function suggestionUpdate(Request $request) {

        $dealerRequisition = DealerRequisition::find($request->dealerRequisitionId);

        $dealerRequisition->update([
            'approved_by1' => $request->approveBy,
            'suggested' => $request->totalSuggestionQty,
            'updated_by' => $this->userId
        ]);


        $countDealerRequisitionProduct = count($request->dealerRequisitionProductId);
        if ($request->dealerRequisitionProductId) {
            for ($i = 0; $i < $countDealerRequisitionProduct; $i++) {
                $dealerRequisitionProduct = DealerRequisitionProduct::where('id', $request->dealerRequisitionProductId[$i])->first();

                $dealerRequisitionProduct->update([
                    'suggested_qty' => $request->SuggestionQty[$i],
                    'updated_by' => $this->userId
                ]);
            }
        }

        return redirect(route('dealerRequisitionSuggestion.index'))->with('msg', 'Dealer Requisition Approved Successfully');
    }

    public function dealerRequisitionInfo(Request $request) {
        $dealerRequisition = DealerRequisition::with(['requisitions', 'dealer'])
                ->where('id', $request->dealerRequisitionId)
                ->first();

        $dealerRequisitionProducts = DealerRequisitionProduct::with(['product'])
                ->where('requisition_id', $request->dealerRequisitionId)
                ->get();


        if ($request->ajax()) {
            return response()->json([
                        'dealerRequisition' => $dealerRequisition,
                        'dealerRequisitionProducts' => $dealerRequisitionProducts
            ]);
        }
    }

}
