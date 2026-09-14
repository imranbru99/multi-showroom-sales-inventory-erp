<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductionRequisition;
use App\ProductionRequisitonInfo;
use DB;
use PDF;
use MPDF;

class ProductionRequitionApprovalContrller extends Controller {

    public function index() {
        $title = "All Pending Requisitions";
        $formLink = "productionRequisitionApproval.update";
        $buttonName = "Save";

        $productionRequisition = ProductionRequisition::with(['staff'])
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->where('suggested', '>', '1')
                ->whereNull('approved_by2')
                ->orderBy('date', 'dsc')
                ->get();


        $approveproductionRequisition = ProductionRequisition::with(['staff'])
                ->where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('suggested', '>', '1')
                ->whereNotNull('approved_by2')
                ->orderBy('date', 'dsc')
                ->get();

        return view('admin.productionRequisitionApproval.index')->with(compact('title', 'formLink', 'buttonName', 'productionRequisition', 'approveproductionRequisition'));
    }

    public function suggestionIndex() {
        $title = "All Requisition Suggestion";
        $formLink = "productionRequisitionSuggestion.update";
        $buttonName = "Save";

        $productionRequisition = ProductionRequisition::with(['staff'])
                ->where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('suggested', '<', '1')
                ->orderBy('date', 'dsc')
                ->get();


        $approveproductionRequisition = ProductionRequisition::with(['staff'])
                ->where('status', '1')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('suggested', '>', '0')
                ->orderBy('date', 'dsc')
                ->get();

        // dd($approveDealerRequisitions);

        return view('admin.productionRequisitionApproval.suggestion_index')->with(compact('title', 'formLink', 'buttonName', 'productionRequisition', 'approveproductionRequisition'));
    }

    public function suggestionUpdate(Request $request) {

        $productionRequisition = ProductionRequisition::find($request->productionRequisitionId);

        $productionRequisition->update([
            'approved_by1' => $request->approveBy,
            'suggested' => $request->totalSuggestionQty,
            'updated_by' => $this->userId
        ]);


        $countProductionRequisition = count($request->productionRequisitionProductId);
        if ($request->productionRequisitionProductId) {
            for ($i = 0; $i < $countProductionRequisition; $i++) {
                $productionRequisitionInfo = ProductionRequisitonInfo::where('id', $request->productionRequisitionProductId[$i])->first();

                $productionRequisitionInfo->update([
                    'suggested_qty' => $request->SuggestionQty[$i],
                    'updated_by' => $this->userId
                ]);
            }
        }

        return redirect(route('productionRequisitionSuggestion.index'))->with('msg', 'Production Requisition Approved Successfully');
    }

    public function update(Request $request) {

        $productionRequisition = ProductionRequisition::find($request->productionRequisitionId);


        $productionRequisition->update([
            'approved_by2' => $request->approveBy,
            'total_qty' => $request->totalApproveQty,
            'status' => 1,
            'updated_by' => $this->userId
        ]);


        $countProductionRequisition = count($request->productionRequisitionProductId);
        if ($request->productionRequisitionProductId) {
            for ($i = 0; $i < $countProductionRequisition; $i++) {
                $productionRequisitionInfo = ProductionRequisitonInfo::where('id', $request->productionRequisitionProductId[$i])->first();

                $productionRequisitionInfo->update([
                    'approved_qty' => $request->approveQty[$i],
                    'status' => 1,
                    'updated_by' => $this->userId
                ]);
            }
        }

        return redirect(route('productionRequisitionApproval.index'))->with('msg', 'Production Requisition Approved Successfully');
    }

    public function productionRequisitionInfo(Request $request) {
        $productionRequisition = ProductionRequisition::with(['requisitions', 'staff'])
                ->where('id', $request->productionRequisitionId)
                ->first();

        $productionRequisitionInfo = ProductionRequisitonInfo::with(['product'])
                ->where('requisition_id', $request->productionRequisitionId)
                ->get();


        if ($request->ajax()) {
            return response()->json([
                        'productionRequisition' => $productionRequisition,
                        'productionRequisitionInfo' => $productionRequisitionInfo
            ]);
        }
    }

}
