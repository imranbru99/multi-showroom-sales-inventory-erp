<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Offer;
use App\Product;
use App\StaffSetup;
use App\DealerSetup;
use App\ProductIssue;
use App\ProductIssueList;

use App\GatePass;
use App\GatePasses;

use DB;
use PDF;
use MPDF;

class GatePassController extends Controller
{
    public function index(Request $request)
    {
        $title = "Gate Pass";

        $gatePasses = GatePass::with('gatePasses', 'dealer')->where('showroom_id', $this->showroomId)->orderBy('id', 'desc')->get();


        return view('admin.gatePass.index')->with(compact('title', 'gatePasses'));
    }


    public function add()
    {

        $title = "Add Gate Pass";

        $formLink = "gatePass.save";
        $buttonName = "Save";

        $maxId = GatePass::max('id');

        if (@$maxId) {
            $issueNo = 21231342 + ($maxId + 1);
        } else {
            $issueNo = 21231342 + 1;
        }

        $dealers = DealerSetup::where('status', 1)->get();


        return view('admin.gatePass.add')->with(compact('title', 'buttonName', 'formLink', 'dealers', 'issueNo'));
    }


    public function getInvoices(Request $request)
    {

        $gatePasses = GatePasses::with('gatePass')
            ->where('showroom_id', $this->showroomId)
            ->whereHas('gatePass', function ($q) use ($request) {
                $q->where('dealer_id', $request->dealer_id);
            })
            ->get()
            ->pluck('issue_id')
            ->toArray();

        $invoices = ProductIssue::where('showroom_id', $this->showroomId)
            ->whereNotIn('id', $gatePasses)
            ->where('date', '>', '2022-01-01')
            ->where('dealer_id', $request->dealer_id)
            ->orderBy('id', 'asc')
            ->get();
        return $invoices;
    }


    public function save(Request $request)
    {

        $gatePass = GatePass::create([
            'showroom_id' => $this->showroomId,
            'date' => date('Y-m-d', strtotime($request->date)),
            'invoice_no' => $request->invoice_no,
            'dealer_id' => $request->dealer_id,
            'created_by' => auth()->user()->id,
        ]);

        if (!empty($request->issues) && count($request->issues)) {
            $count = count($request->issues);
            $postData = [];
            for ($i = 0; $i < $count; $i++) {
                $issue = ProductIssue::find($request->issues[$i]);
                $postData[] = [
                    'showroom_id' => $this->showroomId,
                    'pass_id' => $gatePass->id,
                    'issue_id' => $issue->id,
                    'issue_no' => $issue->issue_no,
                    'created_by' => auth()->user()->id,
                ];
            }
            GatePasses::insert($postData);
        }


        return redirect(route('gatePass.index'))->with('msg', 'Gate Pass Added Successfully');
    }



    public function edit($id)
    {

        $title = "Edit Gate Pass";

        $formLink = "gatePass.update";
        $buttonName = "Update";

        $dealers = DealerSetup::where('status', 1)->get();

        $gatePass = GatePass::with('gatePasses')->where('id', $id)->first();

        return view('admin.gatePass.edit')->with(compact('title', 'buttonName', 'formLink', 'gatePass', 'dealers'));
    }




    public function update(Request $request)
    {
        $gatePass = GatePass::find($request->id);

        $gatePass->update([
            'showroom_id' => $this->showroomId,
            'date' => date('Y-m-d', strtotime($request->date)),
            'invoice_no' => $request->invoice_no,
            'dealer_id' => $request->dealer_id,
            'created_by' => auth()->user()->id,
        ]);

        GatePasses::where('pass_id', $gatePass->id)->delete();

        if (!empty($request->issues) && count($request->issues)) {
            $count = count($request->issues);
            $postData = [];
            for ($i = 0; $i < $count; $i++) {
                $issue = ProductIssue::find($request->issues[$i]);
                $postData[] = [
                    'showroom_id' => $this->showroomId,
                    'pass_id' => $gatePass->id,
                    'issue_id' => $issue->id,
                    'issue_no' => $issue->issue_no,
                    'created_by' => auth()->user()->id,
                ];
            }
            GatePasses::insert($postData);
        }


        return redirect(route('gatePass.index'))->with('msg', 'Gate Pass Updated Successfully');
    }


    public function delete(Request $request)
    {
        GatePass::where('id', $request->id)->delete();
    }





    public function printChalan($id)
    {
        $title = "Print Challan";


        $gatePass = GatePass::find($id);


        $issueIds = explode(',', $gatePass->issue_id);

        $productIssues = ProductIssue::with('dealer', 'dealer.region', 'dealer.territory', 'dealer.area', 'SalesBy', 'products', 'products.product')
            ->whereIn('tbl_product_issue.id', $issueIds)
            ->orderBy('tbl_product_issue.id', 'asc')
            ->get();


        // dd($groupByProductIds);

        $pdf = PDF::loadView('admin.gatePass.printChalan', ['title' => $title, 'productIssues' => $productIssues, 'gatePass' => $gatePass]);

        return $pdf->stream('product_issue_' . $gatePass->invoice_no . '.pdf');
    }

    public function printInvoice($id)
    {

        $title = "Print Invoice";

        $gatePass = GatePass::find($id);
        $gatePasses = GatePasses::where('pass_id', $gatePass->id)->get()->pluck('issue_id')->toArray();


        $productIssues = ProductIssue::with('dealer', 'dealer.region', 'dealer.territory', 'dealer.area', 'SalesBy', 'products', 'products.product')
            ->whereIn('tbl_product_issue.id', $gatePasses)
            ->orderBy('tbl_product_issue.id', 'asc')
            ->get();

        // dd($productIssue);

        $pdf = PDF::loadView('admin.gatePass.printInvoice', ['title' => $title, 'productIssues' => $productIssues, 'gatePass' => $gatePass]);

        return $pdf->stream('product_issue_' . $gatePass->invoice_no . '.pdf');
    }
}
