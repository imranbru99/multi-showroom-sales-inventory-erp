<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use PDF;
use DB;

use App\DealerSetup;
use App\StaffSetup;
use App\GroupSetup;
use App\CategorySetup;
use App\CommissionConfiguration;

class CommmissionConfigurationController extends Controller
{
    public function index()
    {
        $title = "Commission Configuration";
        $allCommissions = CommissionConfiguration::orderBy('id','asc')
            ->where('showroom_id',$this->showroomId)
            ->groupBy('dealer_id')
            ->groupBy('staff_id')
            ->get();

        return view('admin.commissionConfiguration.index')->with(compact('title','allCommissions'));
    }

    public function add()
    {
        $title = "Prepare New Commission";
        $formLink = "commissionConfiguration.save";
        $buttonName = "Save";
        $delaerList = DealerSetup::where('showroom_id',$this->showroomId)->where('status',1)->orderBy('name','asc')->get();
        $staffList = StaffSetup::where('showroom_id',$this->showroomId)->where('status',1)->orderBy('name','asc')->get();
        $categoryList = CategorySetup::where('status',1)
            ->orderBy('name','asc')
            ->get();

        return view('admin.commissionConfiguration.add')->with(compact('title','formLink','buttonName','delaerList','staffList','categoryList'));
    }

    public function save(Request $request)
    {
        $buttonValue = $request->buttonAddEdit;
        $countCategory = count($request->categoryId);
        
        if($request->commissionType == "Dealer Commission")
        {
            $request->staffId = NULL;
            CommissionConfiguration::where('dealer_id',$request->dealerId)->delete();
        }
        elseif($request->commissionType == "Staff Commission")
        {
            $request->dealerId = NULL;
            CommissionConfiguration::where('staff_id',$request->staffId)->delete();
        }

        for ($i=0; $i <$countCategory ; $i++)
        {   
            $commission = CommissionConfiguration::create( [
                'showroom_id' => $this->showroomId,
                'commission_type'=> $request->commissionType,
                'dealer_id'=> $request->dealerId,
                'staff_id'=> $request->staffId,
                'category_id' => $request->categoryId[$i], 
                'category_name' => $request->categoryName[$i], 
                'commission_rate' => $request->commissionRate[$i],
                'created_by' => $this->userId      
            ]);
        }

        return redirect(route('commissionConfiguration.index'))->with('msg','New Commission Prepared Successfully');
    }

    public function view($id)
    {
        $title = "View Commission";
        $categoryList = CategorySetup::where('status',1)
            ->orderBy('name','asc')
            ->get();
        $commission = CommissionConfiguration::where('id',$id)->first();
        return view('admin.commissionConfiguration.view')->with(compact('title','categoryList','commission'));
    }

    public function print($id)
    {
        $title = "Commission Details";
        $categoryList = CategorySetup::where('status',1)
            ->orderBy('name','asc')
            ->get();
        $commission = CommissionConfiguration::where('id',$id)->first();
        $pdf = PDF::loadView('admin.commissionConfiguration.print',['title'=>$title,'categoryList'=>$categoryList,'commission'=>$commission]);

        return $pdf->stream('commission_details');
    }

    public function getInfo(Request $request)
    { 
        $commissionType = $request->commissionType;
        $dealerId = $request->dealerId;
        $staffId = $request->staffId;
        if($commissionType == "Dealer Commission")
        {
           $commissions = CommissionConfiguration::where('showroom_id',$this->showroomId)->where('commission_type',$commissionType)->where('dealer_id',$dealerId)->get(); 
        }
        elseif($commissionType == "Staff Commission")
        {
            $commissions = CommissionConfiguration::where('showroom_id',$this->showroomId)->where('commission_type',$commissionType)->where('staff_id',$staffId)->get(); 
        }
        
        if($request->ajax())
        {
            return response()->json([
                'commissions'=>$commissions,
            ]);
        }
    }
}
