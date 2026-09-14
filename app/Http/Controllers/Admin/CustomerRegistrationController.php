<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use Auth;
use MPDF;
use Session;
use App\HelperClass;
use App\ShowroomSetup;
use Illuminate\Http\Request;
use App\CustomerRegistration;
use App\CustomerAgreement;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\StaffSetup;
use App\ShowroomProjectSetup;

class CustomerRegistrationController extends Controller {

    public function index(Request $request) {
        $title = "Customer Registration";

        $customers = [];
        $project_id = $request->project;

        $projects = ShowroomProjectSetup::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('showroom_id', @$this->showroomId)
                ->get();



        if ($request->ajax()) {

            $customers = [];

            if (!empty($request->project)) {
                $customers = CustomerRegistration::where('company_id', $this->company)
//                        ->where('showroom_id', @$this->showroomId)
                        ->where('project_id', $project_id);
            }

            return DataTables::of($customers)
                            ->addIndexColumn()
                            ->editColumn('status', function ($customer) {
                                $statusBtn = \App\Link::status($customer->id, $customer->status);
                                return $statusBtn;
                            })
                            ->addColumn('action', function ($customer) {
                                $actionBtn = \App\Link::action($customer->id);
                                return $actionBtn;
                            })
                            ->addColumn('code', function ($customer) {
                                return $customer->code;
                            })
                            ->addColumn('name', function ($customer) {
                                return $customer->name;
                            })
                            ->addColumn('nid', function ($customer) {
                                return $customer->nid;
                            })
                            ->addColumn('phone_no', function ($customer) {
                                return $customer->phone_no;
                            })
                            ->addColumn('present_address', function ($customer) {
                                return $customer->present_address;
                            })
                            ->addColumn('reference', function ($customer) {
                                $agreement = CustomerAgreement::with('staff')
                                        ->where('customer_id', $customer->id)->first();
                                $reference = '';
                                if (!empty($agreement)) {
                                    $reference = @$agreement->staff->name;
                                }
                                return $reference;
                            })
                            ->setRowClass(function ($customer) {
                                return 'row_' . $customer->id;
                            })
                            ->escapeColumns([])
                            ->toJson();
        }

        return view('admin.customerRegistration.index')->with(compact('title', 'project_id', 'projects', 'customers'));
    }

    public function printAll(Request $request) {
        $title = "Customer Registration";

        ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");

        $customers = [];

        if (!empty($request->project)) {
            $customers = CustomerRegistration::orderBy('code', 'asc')
                    ->select('code', 'name', 'phone_no', 'present_address')
                    ->where('company_id', $this->company)
//                    ->where('showroom_id', @$this->showroomId)
                    ->where('project_id', $request->project)
                    ->get();
        }

        $pdf = PDF::loadView('admin.customerRegistration.listPrint', ['title' => $title, 'customers' => $customers]);

        return $pdf->stream('customer_list.pdf');
    }

    public function add(Request $request) {
        $title = "New Customer Registration";
        $formLink = "customerRegistration.save";
        $buttonName = "Save";

        $project_id = $request->project;

        $apllicantsCode = HelperClass::customerCode();

        $refers = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', @$this->showroomId)
                ->where('status', 1)
                ->orderBy('name', 'asc')
                ->get();

        $showroomProjects = ShowroomProjectSetup::where('company_id', $this->company)
//                ->where('showroom_id', @$this->showroomId)
                ->where('status', 1)
                ->get();

        return view('admin.customerRegistration.add')->with(compact('title', 'project_id', 'showroomProjects', 'formLink', 'buttonName', 'apllicantsCode', 'refers'));
    }

    public function save(Request $request) {

        // dd($request->all());

        $request->validate([
            'name' => 'required|string',
            'code' => 'required',
        ]);
        // return $request->agree_amnt;



        if (isset($request->image)) {
            $image = \App\HelperClass::UploadImage($request->image, 'tbl_customers', 'public/uploads/customers/');
        } else {
            $image = "";
        }

        $customerRegistraion = CustomerRegistration::create([
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'project_id' => $request->project_id,
                    'name' => $request->name,
                    'code' => $request->code,
                    'nick_name' => $request->nickName,
                    'nid' => $request->nid,
                    'age' => $request->age,
                    'phone_no' => $request->phoneNo,
                    'marital_status' => $request->maritalStatus,
                    'spouse_name' => $request->spouseName,
                    'fathers_name' => $request->fathersName,
                    'mothers_name' => $request->mothersName,
                    'gender' => $request->gender,
                    'current_residence' => $request->currentResidence,
                    'residence_duration' => $request->residenceDuration,
                    'total_family_member' => $request->totalFamilyMember,
                    'present_address' => $request->presentAddress,
                    'permanent_address' => $request->permanentAddress,
                    'profession_name' => $request->professionName,
                    'profession_duration' => $request->professionDuration,
                    'total_earning_member' => $request->totalEarningMember,
                    'designation' => $request->designation,
                    'monthly_income' => $request->monthlyIncome,
                    'work_place_address' => $request->workPlaceAddress,
                    'created_by' => $this->userId,
                    'image' => $image,
        ]);

        // if ($request->employee_id) {

        $refer = StaffSetup::where('id', $request->reference)->first();

        CustomerAgreement::create([
            'date' => date('Y-m-d', strtotime($request->agree_date)),
            'customer_name' => $customerRegistraion->name,
            'account_no' => $customerRegistraion->code,
            'customer_id' => $customerRegistraion->id,
            'nid' => $customerRegistraion->nid,
            'mobile_no' => $customerRegistraion->phone_no,
            'address' => $customerRegistraion->present_address,
            'money_r_no' => $request->mr_no,
            'agreement_amount' => $request->agree_amnt,
            'employee_id' => $request->reference,
            'employee_name' => @$refer->name,
        ]);
        // }


        return back()->with('msg', 'Customer Registration Successfuly Completed');
    }

    public function edit($customerId) {
        $title = "Edit Customer Registration";
        $formLink = "customerRegistration.update";
        $buttonName = "Update";

        $refers = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', 1)
                ->orderBy('name', 'asc')
                ->get();

        $customer = CustomerRegistration::where('id', $customerId)->first();
        $customerAgree = CustomerAgreement::where('customer_id', $customerId)->first();
        $showroomProjects = ShowroomProjectSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', 1)
                ->get();

        return view('admin.customerRegistration.edit')->with(compact('title', 'showroomProjects', 'formLink', 'buttonName', 'customer', 'customerAgree', 'refers'));
    }

    public function update(Request $request) {
        // dd($request->all());

        $customerRegistraion = CustomerRegistration::find($request->customerId);
        $customerAgreement = CustomerAgreement::where('customer_id', $request->customerId)->first();



        if (isset($request->image)) {
            @unlink($customerRegistraion->image);
            $image = \App\HelperClass::UploadImage($request->image, 'tbl_customers', 'public/uploads/customers/');

            $customerRegistraion->update([
                'image' => $image,
            ]);
        }

        $customerRegistraion->update([
            'project_id' => $request->project_id,
            'name' => $request->name,
            'code' => $request->code,
            'nick_name' => $request->nickName,
            'nid' => $request->nid,
            'age' => $request->age,
            'phone_no' => $request->phoneNo,
            'marital_status' => $request->maritalStatus,
            'spouse_name' => $request->spouseName,
            'fathers_name' => $request->fathersName,
            'mothers_name' => $request->mothersName,
            'gender' => $request->gender,
            'current_residence' => $request->currentResidence,
            'residence_duration' => $request->residenceDuration,
            'total_family_member' => $request->totalFamilyMember,
            'present_address' => $request->presentAddress,
            'permanent_address' => $request->permanentAddress,
            'profession_name' => $request->professionName,
            'profession_duration' => $request->professionDuration,
            'total_earning_member' => $request->totalEarningMember,
            'designation' => $request->designation,
            'monthly_income' => $request->monthlyIncome,
            'work_place_address' => $request->workPlaceAddress,
            'created_by' => $this->userId,
        ]);

        // if ($request->agree_amnt) {

        $refer = StaffSetup::where('id', $request->reference)->first();
        if (!empty($customerAgreement)) {

            $customerAgreement->update([
                'date' => date('Y-m-d', strtotime($request->agree_date)),
                'customer_name' => $customerRegistraion->name,
                'account_no' => $customerRegistraion->code,
                'customer_id' => $customerRegistraion->id,
                'nid' => $customerRegistraion->nid,
                'mobile_no' => $customerRegistraion->phone_no,
                'address' => $customerRegistraion->present_address,
                'money_r_no' => $request->mr_no,
                'agreement_amount' => $request->agree_amnt,
                'employee_id' => $request->reference,
                'employee_name' => @$refer->name,
            ]);
        } else {

            CustomerAgreement::create([
                'company_id' => $this->company,
                'showroom_id' => $this->showroomId,
                'date' => date('Y-m-d', strtotime($request->agree_date)),
                'customer_name' => $customerRegistraion->name,
                'account_no' => $customerRegistraion->code,
                'customer_id' => $customerRegistraion->id,
                'nid' => $customerRegistraion->nid,
                'mobile_no' => $customerRegistraion->phone_no,
                'address' => $customerRegistraion->present_address,
                'money_r_no' => $request->mr_no,
                'agreement_amount' => $request->agree_amnt,
                'employee_id' => $request->reference,
                'employee_name' => @$refer->name,
            ]);
        }
        // }

        return redirect(route('customerRegistration.index', ['project' => $request->project_id]))->with('msg', 'Customer Registration Successfuly Updated');
    }

    public function delete(Request $request) {
        CustomerAgreement::where('customer_id', $request->customerId)->delete();
        CustomerRegistration::where('id', $request->customerId)->delete();
    }

    public function status(Request $request) {
        $customer = CustomerRegistration::find($request->customerId);

        if ($customer->status == 1) {
            $customer->update([
                'status' => 0
            ]);
        } else {
            $customer->update([
                'status' => 1
            ]);
        }
    }

}
