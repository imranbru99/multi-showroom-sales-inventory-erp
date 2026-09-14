<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\StaffLoan;
use App\StaffLoanCollection;
use App\StaffLoanCollectionList;
use App\StaffLoanSchedule;
use App\StaffSetup;
use Carbon\Carbon;
use PDF;
use MPDF;

class StaffLoanCollectionController extends Controller
{
    public function index()
    {
        $title = "Staff Loan Collection";

        $collections = StaffLoanCollection::where('showroom_id', $this->showroomId)->orderBy('id', 'desc')->get();

        return view('admin.staffLoanCollection.index')->with(compact('title', 'collections'));
    }

    public function add()
    {
        $title = "Add Staff Loan/Advance";
        $formLink = "staffLoanCollection.save";
        $buttonName = "Save";

        $staffs = StaffSetup::where('showroom_id', $this->showroomId)->orderBy('name', 'asc')->get();

        return view('admin.staffLoanCollection.add')->with(compact('title', 'formLink', 'buttonName', 'staffs'));
    }

    public function schedule(Request $request)
    {
        $schedules = StaffLoanSchedule::with('loan')
            ->whereHas('loan', function ($q) use ($request) {
                $q->where('staff_id', $request->staff);
            })
            ->get();

        return $schedules;
    }

    public function save(Request $request)
    {

        $collection = StaffLoanCollection::create([
            'showroom_id' => $this->showroomId,
            'staff_id' => $request->staff_id,
            'collection_date' => date('Y-m-d', strtotime($request->date)),
            'amount' => $request->amount,
            'created_by' => auth()->user()->id,
        ]);

        if ($request->collection_amount) {
            $count = count($request->collection_amount);
            for ($i = 0; $i < $count; $i++) {
                if ($request->collection_amount[$i] > 0) {
                    $list = StaffLoanCollectionList::create([
                        'showroom_id' => $this->showroomId,
                        'collection_id' => $collection->id,
                        'schedule_id' => $request->schedule_id[$i],
                        'schedule_date' => $request->schedule_date[$i],
                        'collection_amount' => $request->collection_amount[$i],
                        'created_by' => auth()->user()->id,
                    ]);

                    $schedule = StaffLoanSchedule::find($request->schedule_id[$i]);

                    $schedule->update([
                        'collection_id' => $list->id,
                        'collection_amount' => $request->collection_amount[$i]
                    ]);
                }
            }
        }

        return redirect(route('staffLoanCollection.index'))->with('msg', 'Staff Loan Added Successfully');
    }

    public function edit($id)
    {
        $title = "Edit Staff";
        $formLink = "staffLoan.update";
        $buttonName = "Update";

        $staffs = StaffSetup::where('showroom_id', @$this->showroomId)->orderBy('name', 'asc')->get();



        $collection = StaffLoanCollection::with('collections')->where('id', $id)->first();


        $colSchedules = StaffLoanSchedule::with('loan')
            ->whereHas('loan', function ($q) use ($collection) {
                $q->where('staff_id', $collection->staff_id);
            })
            ->get();

        return view('admin.staffLoanCollection.edit')->with(compact('title', 'formLink', 'buttonName', 'staffs', 'collection', 'colSchedules'));
    }

    public function update(Request $request)
    {

        $collection = StaffLoanCollection::find($request->id);

        $collection = StaffLoanCollection::create([
            'collection_date' => date('Y-m-d', strtotime($request->date)),
            'amount' => $request->amount,
            'created_by' => auth()->user()->id,
        ]);

        StaffLoanCollectionList::where('collection_id', $collection->id)->delete();

        if ($request->collection_amount) {
            $count = count($request->collection_amount);
            for ($i = 0; $i < $count; $i++) {
                if ($request->collection_amount[$i] > 0) {
                    $list = StaffLoanCollectionList::create([
                        'collection_id' => $collection->id,
                        'schedule_id' => $request->schedule_id[$i],
                        'schedule_date' => $request->schedule_date[$i],
                        'collection_amount' => $request->collection_amount[$i],
                        'created_by' => auth()->user()->id,
                    ]);

                    $schedule = StaffLoanSchedule::find($request->schedule_id[$i]);

                    $schedule->update([
                        'collection_id' => $list->id,
                        'collection_amount' => $request->collection_amount[$i]
                    ]);
                }
            }
        }


        return redirect(route('staffLoanCollection.index'))->with('msg', 'Staff Loan Updated Successfully');
    }

    public function delete(Request $request)
    {
        StaffLoanCollectionList::where('collection_id', $request->id)->delete();
        StaffLoanCollection::where('id', $request->id)->delete();
    }
}
