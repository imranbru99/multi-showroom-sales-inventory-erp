<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CustomerRegistrationSetup;
use App\ShowroomProjectSetup;
use App\StaffSetup;
use App\InstallmentCollectionList;
use Illuminate\Support\Carbon;
use PDF;

class UnpaidCustomerController extends Controller
{

    public function index(Request $request)
    {
        $title = "Collection Duration";
        $searchFormLink = "unpaidcustomer.index";
        $printFormLink = "unpaidcustomer.print";

        $print = $request->print;

        $sales_by = $request->sales_by;

        $showroom_id = $this->showroomId;
        $project_id = $request->project;

        $projects = ShowroomProjectSetup::where('status', 1)->where('showroom_id', @$this->showroomId)->get();

        $fromDate = !empty($request->fromDate) ? date('Y-m-d', strtotime($request->fromDate)) : date('Y-m-01');
        $toDate = !empty($request->toDate) ? date('Y-m-d', strtotime($request->toDate)) : date('Y-m-d');

        $staffs = StaffSetup::where('status', 1)->where('showroom_id', @$this->showroomId)->get();

        $customers = [];

        if ($request->fromDate) {
            if ($sales_by) {
                $collections = InstallmentCollectionList::with('collection')
                    ->whereBetween('installment_collection_date', [$fromDate, $toDate])
                    ->whereHas('collection', function ($query) use ($sales_by) {
                        $query->where('reference_id', $sales_by);
                    })
                    ->where('showroom_id', @$showroom_id)
                    ->where('project_id', @$project_id)
                    ->get()
                    ->pluck('collection.customer_id')
                    ->toArray();
            } else {
                $collections = InstallmentCollectionList::with('collection')
                    ->whereBetween('installment_collection_date', [$fromDate, $toDate])
                    ->where('showroom_id', @$showroom_id)
                    ->where('project_id', @$project_id)
                    ->get()
                    ->pluck('collection.customer_id')
                    ->toArray();
            }

            $customerIds = array_unique($collections);

            $customers = [];
            foreach ($customerIds as $key => $value) {
                if ($sales_by) {
                    $customer = InstallmentCollectionList::select(
                        'tbl_installment_collection_list.*',
                        'tbl_installment_collection.customer_id',
                        'tbl_customers.*',
                        'tbl_staffs.name as collector_by'
                    )
                        ->whereBetween('installment_collection_date', [$fromDate, $toDate])
                        ->leftjoin('tbl_installment_collection', 'tbl_installment_collection.id', '=', 'tbl_installment_collection_list.installment_collection_id')
                        ->leftjoin('tbl_customers', 'tbl_customers.id', '=', 'tbl_installment_collection.customer_id')
                        ->leftjoin('tbl_staffs', 'tbl_staffs.id', '=', 'tbl_installment_collection.reference_id')
                        ->where('tbl_installment_collection.reference_id', $sales_by)
                        ->where('tbl_installment_collection.customer_id', $value)
                        ->latest('installment_collection_date')
                        ->first();
                } else {
                    $customer = InstallmentCollectionList::select(
                        'tbl_installment_collection_list.*',
                        'tbl_installment_collection.customer_id',
                        'tbl_customers.*',
                        'tbl_staffs.name as collector_by'
                    )
                        ->whereBetween('installment_collection_date', [$fromDate, $toDate])
                        ->leftjoin('tbl_installment_collection', 'tbl_installment_collection.id', '=', 'tbl_installment_collection_list.installment_collection_id')
                        ->leftjoin('tbl_customers', 'tbl_customers.id', '=', 'tbl_installment_collection.customer_id')
                        ->leftjoin('tbl_staffs', 'tbl_staffs.id', '=', 'tbl_installment_collection.reference_id')
                        ->where('tbl_installment_collection.customer_id', $value)
                        ->latest('installment_collection_date')
                        ->first();
                }



                $startDate = Carbon::parse($customer->installment_collection_date);
                $endDate = Carbon::parse($toDate);

                $duration = $endDate->diffInDays($startDate);

                $customer['duration'] = $duration;

                $customers[] = $customer;
            }

            $sortting  = array_column($customers, 'duration');
            array_multisort($sortting, SORT_DESC, $customers);
        }

        return view('admin.unpaidCustomer.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'fromDate', 'toDate', 'customers', 'staffs', 'sales_by', 'projects', 'project_id'));
    }

    public function print(Request $request)
    {
        $title = "Collection Duration";

        $sales_by = $request->sales_by;

        $staff = StaffSetup::find(@$sales_by);

        $showroom_id = $this->showroomId;
        $project_id = $request->project;

        $fromDate = !empty($request->fromDate) ? date('Y-m-d', strtotime($request->fromDate)) : date('Y-m-01');
        $toDate = !empty($request->toDate) ? date('Y-m-d', strtotime($request->toDate)) : date('Y-m-d');

        $staffs = StaffSetup::where('status', 1)->get();

        $customers = [];

        if ($request->fromDate) {
            if ($sales_by) {
                $collections = InstallmentCollectionList::with('collection')
                    ->whereBetween('installment_collection_date', [$fromDate, $toDate])
                    ->whereHas('collection', function ($query) use ($sales_by) {
                        $query->where('reference_id', $sales_by);
                    })
                    ->where('showroom_id', @$showroom_id)
                    ->where('project_id', @$project_id)
                    ->get()
                    ->pluck('collection.customer_id')
                    ->toArray();
            } else {
                $collections = InstallmentCollectionList::with('collection')
                    ->whereBetween('installment_collection_date', [$fromDate, $toDate])
                    ->where('showroom_id', @$showroom_id)
                    ->where('project_id', @$project_id)
                    ->get()
                    ->pluck('collection.customer_id')
                    ->toArray();
            }

            $customerIds = array_unique($collections);

            $customers = [];
            foreach ($customerIds as $key => $value) {
                if ($sales_by) {
                    $customer = InstallmentCollectionList::select(
                        'tbl_installment_collection_list.*',
                        'tbl_installment_collection.customer_id',
                        'tbl_customers.*',
                        'tbl_staffs.name as collector_by'
                    )
                        ->whereBetween('installment_collection_date', [$fromDate, $toDate])
                        ->leftjoin('tbl_installment_collection', 'tbl_installment_collection.id', '=', 'tbl_installment_collection_list.installment_collection_id')
                        ->leftjoin('tbl_customers', 'tbl_customers.id', '=', 'tbl_installment_collection.customer_id')
                        ->leftjoin('tbl_staffs', 'tbl_staffs.id', '=', 'tbl_installment_collection.reference_id')
                        ->where('tbl_installment_collection.reference_id', $sales_by)
                        ->where('tbl_installment_collection.customer_id', $value)
                        ->latest('installment_collection_date')
                        ->first();
                } else {
                    $customer = InstallmentCollectionList::select(
                        'tbl_installment_collection_list.*',
                        'tbl_installment_collection.customer_id',
                        'tbl_customers.*',
                        'tbl_staffs.name as collector_by'
                    )
                        ->whereBetween('installment_collection_date', [$fromDate, $toDate])
                        ->leftjoin('tbl_installment_collection', 'tbl_installment_collection.id', '=', 'tbl_installment_collection_list.installment_collection_id')
                        ->leftjoin('tbl_customers', 'tbl_customers.id', '=', 'tbl_installment_collection.customer_id')
                        ->leftjoin('tbl_staffs', 'tbl_staffs.id', '=', 'tbl_installment_collection.reference_id')
                        ->where('tbl_installment_collection.customer_id', $value)
                        ->latest('installment_collection_date')
                        ->first();
                }



                $startDate = Carbon::parse($customer->installment_collection_date);
                $endDate = Carbon::parse($toDate);

                $duration = $endDate->diffInDays($startDate);

                $customer['duration'] = $duration;

                $customers[] = $customer;
            }

            $sortting  = array_column($customers, 'duration');
            array_multisort($sortting, SORT_DESC, $customers);
        }

        $pdf = PDF::loadView('admin.unpaidCustomer.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'customers' => $customers, 'sales_by' => $sales_by, 'staff' => $staff]);

        return $pdf->stream('collection_duration_' . $fromDate . '_to_' . $toDate . '.pdf');
    }
}
