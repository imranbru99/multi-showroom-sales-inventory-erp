<?php

namespace App\Http\Controllers\Admin;

use App\StaffSetup;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\InstallmentCollectionList;
use App\InstallmentCollection;
use App\CustomerRegistrationSetup;
use App\CustomerRegistration;
use App\CustomerAgreement;
use App\ShowroomProjectSetup;
use App\Http\Controllers\Controller;
use App\Services\Installment\Collection\CustomerInfo;
use App\Services\Installment\Collection\RawCollection;

class RetailCollectionController extends Controller {

    public function index(Request $request) {
        $title = "Multiple Collection";

        $project_id = $request->project;

        $projects = ShowroomProjectSetup::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get();

        if ($request->ajax()) {

            $collections = [];

            if (!empty($project_id)) {
                $collections = InstallmentCollectionList::with('collection.customer', 'user', 'collection.collector')
                        ->where('company_id', $this->company)
//                        ->where('showroom_id', $this->showroomId)
                        ->where('project_id', $project_id)
                        ->orderBy('id', 'desc');
            }


            return DataTables::of($collections)
                            ->addIndexColumn()
                            ->addColumn('action', function ($collection) {
                                $actionBtn = \App\Link::action($collection->id);
                                return $actionBtn;
                            })
                            ->addColumn('customer_code', function ($collection) {
                                return @$collection->collection->customer->code;
                            })
                            ->addColumn('customer_name', function ($collection) {
                                return @$collection->collection->customer->name;
                            })
                            ->addColumn('collection_date', function ($collection) {
                                return date('d-m-Y', strtotime($collection->installment_collection_date));
                            })
                            ->addColumn('collection_amount', function ($collection) {
                                return $collection->installment_schedule_amount;
                            })
                            ->addColumn('collector', function ($collection) {
                                return @$collection->collection->collector->name;
                            })
                            ->addColumn('user', function ($collection) {
                                return @$collection->user->name;
                            })
                            ->setRowClass(function ($retailSale) {
                                return '.row_' . $retailSale->id;
                            })
                            ->escapeColumns([])
                            ->toJson();
        }


        return view('admin.retailSalesCollection.index', compact('title', 'projects', 'project_id'));
    }

    public function add(Request $request) {
        $title = "Multiple Collection";
        $formLink = "retailCollection.save";
        $buttonName = "Save";

        $collectorID = $request->collector;

        $project_id = $request->project;

        $projects = ShowroomProjectSetup::where('company_id', $this->company)
//                        ->where('showroom_id', $this->showroomId)
                ->where('status', 1)
                ->get();

        $staffs = StaffSetup::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get();

        $data = [];

        $customers = CustomerRegistrationSetup::where('project_id', $project_id)->get();

        if (!empty($collectorID)) {
            $data = CustomerInfo::getAllCustomerByCollector($collectorID);
        }
        return view('admin.retailSalesCollection.add', compact('title', 'project_id', 'projects', 'formLink', 'buttonName', 'data', 'staffs', 'collectorID', 'customers'));
    }

    //    public function addByCollector(Request $request)
    //    {
    //        return CustomerInfo::getAllCustomerByCollector($request->collectorID);
    //    }

    public function addAccountInfo(Request $request) {
        $customer = CustomerRegistrationSetup::where('project_id', $request->project)
                ->where('code', $request->account_no)
                ->first();
        return CustomerInfo::getAddCustomerById($customer->id);
    }

    public function addCashAccount(Request $request) {
        $customer = CustomerRegistrationSetup::where('id', $request->id)->first();
        return CustomerInfo::getAddCustomerById($customer->id);
    }

    public function save(Request $request) {
        $collectedCustomers = RawCollection::formatCollectedCustomerSaveData($request);

        $company = $this->company;
        $showroom = $this->showroomId;


        RawCollection::saveMultiple($collectedCustomers, $request, $company, $showroom);

        return back()->with('msg', 'Collections Completed Successfully');
    }

    public function edit($id) {

        $title = "Collection Edit";
        $formLink = "retailCollection.update";
        $buttonName = "Update";

        $staffs = StaffSetup::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get();
        $collection = InstallmentCollectionList::with('collection', 'collection.customer')->where('id', $id)->first();


        $customers = CustomerRegistration::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('project_id', $collection->project_id)
                ->cursor();


        return view('admin.retailSalesCollection.edit', compact('title', 'formLink', 'buttonName', 'staffs', 'collection', 'customers'));
    }

    public function update(Request $request) {
        // return $request;

        $account = CustomerRegistrationSetup::where('id', $request->customer_id)->first();

        $installmentList = InstallmentCollectionList::where('id', $request->collectionID)->first();



        // $installment = InstallmentCollection::where('id', $installmentList->installment_collection_id)->first();

        $installmentId = InstallmentCollection::where('customer_id', $account->id)->first();


        // return $installment;

        if (!$installmentId) {
            $installmentId = InstallmentCollection::create([
                        'company_id' => $this->company,
                        'reference_id' => $request->collector,
                        'showroom_id' => $this->showroomId,
                        'project_id' => $account->project_id,
                        'installment_id' => 0,
                        'customer_id' => $account->id,
                        'invoice_no' => 0,
                        'installment_price' => 0,
                        'booking_amount' => 0,
                        'installment_qty' => 0,
                        'installment_amount' => 0,
                        'created_by' => auth()->user()->id,
            ]);
        }

        $installmentId->update([
            'reference_id' => @$request->collector,
        ]);

        $installmentList->update([
            'installment_collection_date' => date('Y-m-d', strtotime($request->collection_date)),
            'installment_schedule_amount' => $request->collection_amount,
            'invoice_no' => @$request->mr_no,
            'installment_collection_id' => @$installmentId->id,
        ]);





        $agreeCustomer = CustomerAgreement::where('customer_id', $account->id)->first();

        if ($agreeCustomer) {
            $agreeCustomer->update([
                'employee_id' => $request->collector,
            ]);
        } else {
            $refer = StaffSetup::where('id', $request->collector)->first();

            CustomerAgreement::create([
                'date' => date('Y-m-d', strtotime($account->created_at)),
                'customer_name' => $account->name,
                'account_no' => $account->code,
                'customer_id' => $account->id,
                'nid' => $account->nid,
                'mobile_no' => $account->phone_no,
                'address' => $account->present_address,
                'employee_id' => $request->collector,
                'employee_name' => @$refer->name,
            ]);
        }

        return redirect(route('retailCollection.index', ['project' => $installmentList->project_id]));
    }

    public function delete(Request $request) {
        $collectionId = $request->colectionId;

        InstallmentCollectionList::where('id', $collectionId)->delete();

        return $collectionId;
    }

}
