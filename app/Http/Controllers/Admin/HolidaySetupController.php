<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\HolidaySetup;

class HolidaySetupController extends Controller {

    public function index() {
        $title = "Holiday Setup";

        $holidays = HolidaySetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        return view('admin.holidaySetup.index')->with(compact('title', 'holidays'));
    }

    public function add() {
        $title = "Add Holiday";
        $formLink = "holidaySetup.save";
        $buttonName = "Save";

        $holidayTypes = array('Special' => 'Special', 'Regular' => 'Regular', 'Others' => 'Others');
        $holidayFor = array('One Day' => 'One Day', 'Several Day' => 'Several Day');

        return view('admin.holidaySetup.add')->with(compact('title', 'formLink', 'buttonName', 'holidayTypes', 'holidayFor'));
    }

    public function save(Request $request) {
        if ($request->fromDate == "") {
            $fromDate = $request->fromDate;
        } else {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
        }

        if ($request->toDate == "") {
            $toDate = $request->toDate;
        } else {
            $toDate = date('Y-m-d', strtotime($request->toDate));
        }

        HolidaySetup::create([
            'company_id' => $this->company,
            'showroom_id' => $this->showroomId,
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'name' => $request->holidayName,
            'type' => $request->holidayType,
            'description' => $request->description,
            'days' => $request->numberOfDay,
            'holiday_for' => $request->holidayFor,
            'created_by' => $this->userId
        ]);

        return redirect(route('holidaySetup.index'))->with('msg', 'Holiday Added Successfully');
    }

    public function edit($holidayId) {
        $title = "Edit Holiday";
        $formLink = "holidaySetup.update";
        $buttonName = "Update";

        $holidayTypes = array('Special' => 'Special', 'Regular' => 'Regular', 'Others' => 'Others');
        $holidayFor = array('One Day' => 'One Day', 'Several Day' => 'Several Day');

        $holiday = HolidaySetup::where('id', $holidayId)->first();

        return view('admin.holidaySetup.edit')->with(compact('title', 'formLink', 'buttonName', 'holidayTypes', 'holidayFor', 'holiday'));
    }

    public function update(Request $request) {
        // dd($request->all());
        if ($request->fromDate == "") {
            $fromDate = $request->fromDate;
        } else {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
        }

        if ($request->toDate == "") {
            $toDate = $request->toDate;
        } else {
            $toDate = date('Y-m-d', strtotime($request->toDate));
        }

        $holiday = HolidaySetup::find($request->holidayId);

        $holiday->update([
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'name' => $request->holidayName,
            'type' => $request->holidayType,
            'description' => $request->description,
            'days' => $request->numberOfDay,
            'holiday_for' => $request->holidayFor,
            'created_by' => $this->userId
        ]);

        return redirect(route('holidaySetup.index'))->with('msg', 'Holiday Updated Successfully');
    }

    public function delete(Request $request) {
        HolidaySetup::where('id', $request->holidayId)->delete();
    }

    public function status(Request $request) {
        $holiday = HolidaySetup::find($request->holidayId);

        if ($holiday->status == 1) {
            $holiday->update([
                'status' => 0
            ]);
        } else {
            $holiday->update([
                'status' => 1
            ]);
        }
    }

}
