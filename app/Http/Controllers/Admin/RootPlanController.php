<?php

namespace App\Http\Controllers\Admin;

use App\DealerSetup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductIssue;
use App\ProductIssueList;
use App\RootPlan;
use App\RootPlanProducts;
use App\TerritorySetup;
use App\VehicleSetup;
use DB;
use PDF;

class RootPlanController extends Controller
{
    public function index(Request $request)
    {
        $title = "Root Plan";

        $rootPlans = RootPlan::where('showroom_id', $this->showroomId)->get();


        return view('admin.rootPlan.index')->with(compact('title', 'rootPlans'));
    }


    public function add()
    {

        $title = "Add Root Plan";

        $formLink = "rootPlan.save";
        $buttonName = "Save";

        $maxId = RootPlan::max('id');

        if(!$maxId){
            $planNo = 121000001;
        }else{
            $planNo = 121000001 + $maxId;
        }

        $territories = TerritorySetup::with('area', 'area.region')->where('status', 1)->get();
        $vehicles = VehicleSetup::where('status', 1)->get();

        return view('admin.rootPlan.add')->with(compact('title', 'buttonName', 'formLink', 'territories', 'vehicles', 'planNo'));
    }


    public function getInvoices(Request $request)
    {
        $id = $request->id;
        $reqId = [];
        if (!empty($id)) {
            $reqId = explode(',', $id);
        }


        $dealers = DealerSetup::whereIn('territory_id', $request->roads)->get()->pluck('id')->toArray();

        $rootPlans = RootPlan::where('showroom_id', $this->showroomId)
            ->get()
            ->pluck('issue_id')
            ->toArray();

        $issuedProducts = ProductIssue::with('dealer', 'products', 'products.product')
            ->where('showroom_id', $this->showroomId)
            // ->whereNotIn('id', $ids)
            ->whereIn('dealer_id', $dealers)
            ->orderBy('id', 'asc')
            ->get();

        return $issuedProducts;
    }


    public function getVehicle(Request $request)
    {
        return VehicleSetup::find($request->id);
    }

    public function getProductInfo(Request $request)
    {
        $issueProduct = ProductIssueList::find($request->id);

        $product = Product::find($issueProduct->product_id);

        $capacity = 0;
        if ($product->transport_point) {
            $capacity = $product->transport_point;
        }
        return $capacity;
    }


    public function save(Request $request)
    {

        // dd($request->all());

        if (empty($request->issue_list_id)) {
            return redirect(route('rootPlan.add'))->with('msg', 'Select Some Product');
        }

        $maxId = RootPlan::max('id');

        if(!$maxId){
            $planNo = 121000001;
        }else{
            $planNo = 121000001 + $maxId;
        }

        $roads = [];

        // $roads = explode(',', $request->root_plan);
        $roads = implode(',', $request->root_plan);

        // dd($roads);

        $root = RootPlan::create([
            'showroom_id' => $this->showroomId,
            'root_plan_no' => $planNo,
            'date' => date('Y-m-d', strtotime($request->date)),
            'driver_name' => $request->driver_name,
            'contact_no' => $request->contact_no,
            'vehicle_id' => $request->vehicle_id,
            'vehicle_capacity' => $request->vehicle_capacity,
            'product_capacity' => $request->product_capacity,
            'root_plan' => $roads,
            'created_by' => auth()->user()->id,
        ]);

        if (!empty($request->issue_list_id) && count($request->issue_list_id) > 0) {
            foreach ($request->issue_list_id as $issue) {
                $issueList = ProductIssueList::with('product', 'issue')->where('id', $issue)->first();
                RootPlanProducts::create([
                    'showroom_id' => $this->showroomId,
                    'root_plan_id' => $root->id,
                    'issue_list_id' => $issueList->id,
                    'dealer_id' => $issueList->issue->dealer_id,
                    'product_id' => $issueList->product_id,
                    'product_name' => $issueList->product->name,
                    'product_model' => $issueList->model_no,
                    'product_serial' => $issueList->serial_no,
                ]);
            }
        }


        return redirect(route('rootPlan.index'))->with('msg', 'Root Plan Added Successfully');
    }



    public function edit($id)
    {

        $title = "Edit Gate Pass";

        $formLink = "rootPlan.update";
        $buttonName = "Update";

        $territories = TerritorySetup::with('area', 'area.region')->get();

        $rootPlan = RootPlan::find($id);
        $vehicles = VehicleSetup::where('status', 1)->get();


        $issues = explode(',', $rootPlan->issue_id);

        $productIssues = ProductIssue::with('dealer')->whereIn('id', $issues)->get();


        return view('admin.rootPlan.edit')->with(compact('title', 'buttonName', 'formLink', 'rootPlan', 'productIssues', 'territories', 'vehicles'));
    }




    public function update(Request $request)
    {

        if (empty($request->issue_list_id)) {
            return redirect(route('rootPlan.edit', $request->id))->with('msg', 'Select Some Product');
        }

        $roads = '';

        $roads = implode(',', $request->root_plan);

        $rootPlan = RootPlan::find($request->id);

        $rootPlan->update([
            'showroom_id' => $this->showroomId,
            'date' => date('Y-m-d', strtotime($request->date)),
            'driver_name' => $request->driver_name,
            'contact_no' => $request->contact_no,
            'vehicle_id' => $request->vehicle_id,
            'vehicle_capacity' => $request->vehicle_capacity,
            'product_capacity' => $request->product_capacity,
            'root_plan' => $roads,
            'created_by' => auth()->user()->id,
        ]);

        RootPlanProducts::where('root_plan_id', $rootPlan->id)->delete();

        if (!empty($request->issue_list_id) && count($request->issue_list_id) > 0) {
            foreach ($request->issue_list_id as $issue) {
                $issueList = ProductIssueList::with('product', 'issue')->where('id', $issue)->first();
                RootPlanProducts::create([
                    'showroom_id' => $this->showroomId,
                    'root_plan_id' => $rootPlan->id,
                    'issue_list_id' => $issueList->id,
                    'dealer_id' => $issueList->issue->dealer_id,
                    'product_id' => $issueList->product_id,
                    'product_name' => $issueList->product->name,
                    'product_model' => $issueList->model_no,
                    'product_serial' => $issueList->serial_no,
                ]);
            }
        }


        return redirect(route('rootPlan.index'))->with('msg', 'Root Plan Updated Successfully');
    }


    public function delete(Request $request)
    {
        RootPlanProducts::where('root_plan_id', $request->id)->delete();
        RootPlan::where('id', $request->id)->delete();
    }





    public function print($id)
    {
        $title = "Root Plan";


        $rootPlan = RootPlan::with('vehicle')->where('id', $id)->first();
        $rootPlanDealers = RootPlanProducts::with('dealer')->where('root_plan_id', $id)->groupBy('dealer_id')->get();

        $roads = TerritorySetup::with('area', 'area.region')->whereIn('id', explode(',', $rootPlan->root_plan))->get();

        $pdf = PDF::loadView('admin.rootPlan.print', ['title' => $title, 'rootPlan' => $rootPlan, 'rootPlanDealers' => $rootPlanDealers, 'roads' => $roads]);

        return $pdf->stream('root_plan.pdf');
    }
}
