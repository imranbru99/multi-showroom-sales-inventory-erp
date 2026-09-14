<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\HolidaysSetup;

class HoliController extends Controller
{
    public function index()
    {
    	$title = "Holiday Setup";

    	$holidays = HolidaysSetup::where('showroom_id',$this->showroomId)
    		->orderBy('name','asc')
    		->get();

    	return view('admin.holidaysSetup.index')->with(compact('title','holidays'));
    }

    public function add()
    {
    	$title = "Add Holiday";
    	$formLink = "holidaysSetup.save";
    	$buttonName = "Save";

    	$holidayTypes = array('Holiday'=>'Holiday','Weekend'=>'Weekend');
    

    	return view('admin.holidaysSetup.add')->with(compact('title','formLink','buttonName','holidayTypes'));
    }

    public function save(Request $request)
    {
        if ($request->fromDate == "")
        {
            $fromDate = $request->fromDate;
        }
        else
        {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
        }

        if ($request->toDate == "")
        {
            $toDate = $request->toDate;
        }
        else
        {
            $toDate = date('Y-m-d', strtotime($request->toDate));
        }

        HolidaysSetup::create([
            'showroom_id' => $this->showroomId,
            'from_date' => $fromDate,
            'to_date' => $toDate,
        	'name' => $request->holidayName,
        	'type' => $request->holidayType,
            'created_by' => $this->userId
        ]);

        return redirect(route('holidaysSetup.index'))->with('msg','Holiday Added Successfully');
    }

    public function edit($holidayId)
    {
        $title = "Edit Holiday";
        $formLink = "holidaysSetup.update";
        $buttonName = "Update";

        $holidayTypes = array('Holiday'=>'Holiday','Weekend'=>'Weekend');
       

        $holiday = HolidaysSetup::where('id',$holidayId)->first();

        return view('admin.holidaysSetup.edit')->with(compact('title','formLink','buttonName','holidayTypes','holiday'));
    }

    public function update(Request $request)
    {
        // dd($request->all());
        if ($request->fromDate == "")
        {
            $fromDate = $request->fromDate;
        }
        else
        {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
        }

        if ($request->toDate == "")
        {
            $toDate = $request->toDate;
        }
        else
        {
            $toDate = date('Y-m-d', strtotime($request->toDate));
        }

        $holiday = HolidaysSetup::find($request->holidayId);

        $holiday->update([
            'showroom_id' => $this->showroomId,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'name' => $request->holidayName,
            'type' => $request->holidayType,
            'created_by' => $this->userId
        ]);

        return redirect(route('holidaysSetup.index'))->with('msg','Holiday Updated Successfully');
    }

    public function delete(Request $request)
    {    	
        HolidaysSetup::where('id',$request->holidayId)->delete();
    }

    public function status(Request $request)
    {
        $holiday = HolidaysSetup::find($request->holidayId);

        if ($holiday->status == 1)
        {
            $holiday->update([               
                'status' => 0                
            ]);
        }
        else
        {
            $holiday->update([               
                'status' => 1                
            ]);
        }
    }
}
