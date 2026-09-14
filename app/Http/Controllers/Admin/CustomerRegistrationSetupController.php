<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\CustomerRegistrationSetup;
use App\CustomerProduct;
use App\CustomerGuarantor;
use App\ShowroomSetup;
use App\Product;
use App\InvoiceSetup;
use App\StaffSetup;

use DB;
use PDF;
use MPDF;
use Auth;
use Session;

class CustomerRegistrationSetupController extends Controller
{
    public function index(Request $request)
    {
    	$title = "Customer Registration";
        $searchFormLink  = 'customerRegistraionSetup.index';
        $printFormLink = "customerRegistraionSetup.customerListPrint";
        $staffList = StaffSetup::where('status',1)->orderBy('name','ASC')->get();
        $referenceParam = $request->referenceParam;

        $userId = $this->userId;
        $showroomId = $this->showroomId;
        
        if($request->print && $request->referenceParam != '')
        {
            $customers = DB::table('tbl_customers')
                ->select('tbl_customers.*','tbl_customer_products.reference_id','tbl_staffs.name as referenceName')
                ->join('tbl_customer_products','tbl_customers.id','=','tbl_customer_products.customer_id')
                ->join('tbl_staffs','tbl_staffs.id','=','tbl_customer_products.reference_id')
                ->whereIn('tbl_customer_products.reference_id',$referenceParam)
                ->where('tbl_customers.showroom_id',$showroomId)
                ->where('tbl_customers.status','1')
                ->groupBy('tbl_customers.id')
                ->orderBy('tbl_customers.id','asc')
                ->get();
        }
        else
        {
            $customers = DB::table('tbl_customers')
                ->select('tbl_customers.*','tbl_customer_products.reference_id','tbl_staffs.name as referenceName')
                ->leftjoin('tbl_customer_products','tbl_customers.id','=','tbl_customer_products.customer_id')
                ->leftjoin('tbl_staffs','tbl_staffs.id','=','tbl_customer_products.reference_id')
                ->where('tbl_customers.showroom_id',$showroomId)
                ->where('tbl_customers.status','1')
                ->groupBy('tbl_customers.id')
                ->orderBy('tbl_customers.id','asc')
                ->get();

        }

    	return view('admin.customerRegistraionSetup.index')->with(compact('title','searchFormLink','printFormLink','customers','staffList','referenceParam'));
    }

    public function add()
    {
    	$title = "New Customer Registration";
        $formLink = "customerRegistraionSetup.save";
        $buttonName = "Save";
        $showroomName = $this->showroom->name;
        $products = Product::where('status',1)->orderBy('name','ASC')->get();
        $staffs = StaffSetup::where('status',1)->orderBy('name','ASC')->get();
    	return view('admin.customerRegistraionSetup.add')->with(compact('title','formLink','buttonName','products','showroomName','staffs'));
    }

    public function save(Request $request)
    {
        // dd($request->all());

        // echo $this->showroomId; exit();
        $customerRegistraion = CustomerRegistrationSetup::create( [
            'showroom_id' => $this->showroomId,
            'project_id' => $request->project,
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

        if($customerRegistraion){
            $purchaseDate = date('Y-m-d', strtotime($request->purchaseDate));
            if($request->purchaseType == 'Cash')
            {
                $request->installmentType = '';
                $deposite = '';

                $request->shortInstallmentPrice = '';
                $request->longInstallmentPrice = '';

                $totalInstallment = '';

                $installmentAmount = '';
            }

            if($request->purchaseType == 'Short Installment')
            {
                $deposite = $request->shortInstallmentDeposite;
                $request->longInstallmentPrice = '';
                $totalInstallment = $request->shortTotalInstallment;
                $installmentAmount = $request->shortInstallmentAmount;
            }

            if($request->purchaseType == 'Long Installment')
            {
                $deposite = $request->longInstallmentDeposite;
                $request->shortInstallmentPrice = '';
                $totalInstallment = $request->longTotalInstallment;
                $installmentAmount = $request->longInstallmentAmount;
            }

            $product = CustomerProduct::create( [
                'showroom_id' => $this->showroomId,
                'customer_id' => $customerRegistraion->id,       
                'product_id' => $request->productId,       
                'reference_id' => $request->referenceId,       
                'remarks' => $request->remarks,       
                'product_model' => $request->productModel,       
                'cash_price' => $request->cashPrice,
                'mrp_price' => $request->shortInstallmentPrice,
                'warranty' => $request->warranty,       
                'purchase_date' => $purchaseDate,       
                'purchase_type' => $request->purchaseType,       
                'installment_type' => $request->installmentType,       
                'deposite' => $deposite,       
                'installment_price' => $request->longInstallmentPrice,       
                'total_installment' => $totalInstallment,       
                'monthly_installment_amount' => $installmentAmount,       
                'product_usage_address' => $request->productUsageAddress,         
                'created_by' => $this->userId,         
            ]);
        }

        if(@$product)
        {
            $countCustomerGuarantor = count($request->gurantorName);
            if($request->gurantorName)
            {
                $postData = [];
                for ($i=0; $i <$countCustomerGuarantor ; $i++)
                {
                    if ($request->gurantorName[$i] != "")
                    { 
                        $postData[] = [
                            'showroom_id' => $this->showroomId, 
                            'customer_id'=> $customerRegistraion->id,
                            'product_id'=> $request->productId,
                            'gurantor_name' => $request->gurantorName[$i],
                            'gurantor_phone_no' => $request->gurantorPhoneNo[$i], 
                            'gurantor_age' => $request->gurantorAge[$i],
                            'guarantor_marital_status' => $request->guarantorMaritalStatus[$i],
                            'guarantor_spouse_name' => @$request->guarantorSpouseName[$i],
                            'guarantor_father_name' => @$request->guarantorFatherName[$i],
                            'guarantor_present_address' => $request->guarantorPresentAddress[$i],
                            'guarantor_permanent_address' => $request->guarantorPermanentAddress[$i],
                            'guarantor_profession_name' => $request->guarantorProfessionName[$i],
                            'guarantor_designation' => $request->guarantorDesignation[$i],
                            'guarantor_workplace_phone_no' => $request->guarantorWorkplacePhoneNo[$i],
                            'guarantor_monthly_income' => $request->guarantorMonthlyIncome[$i],
                            'guarantor_work_place_address' => $request->guarantorWorkPlaceAddress[$i],         
                            'created_by' => $this->userId, 
                        ];
                    }
                }
                
                CustomerGuarantor::insert($postData);
            }
        }        

        return redirect(route('customerRegistraionSetup.index'))->with('msg','Customer Registration Successfuly  Complete');
    }

    public function viewCustomerDetails($id)
    {
        $title = "Customer Details Information";
        $formLink = "customerRegistraionSetup.NewProductSave";
        $buttonName = "Save";
        $customer = CustomerRegistrationSetup::orWhere('id',$id)->first();
        $showroomName = $this->showroom->name;
        $products = Product::where('status',1)->orderBy('name','ASC')->get();
        $staffs = StaffSetup::where('status',1)->orderBy('name','ASC')->get();
        $customerProducts = CustomerProduct::where('customer_id',$customer->id)->get();
        $customerGuarantor = CustomerGuarantor::where('customer_id',$customer->id)->get();
        return view('admin.customerRegistraionSetup.view')->with(compact('title','formLink','buttonName','customer','products','showroomName','customerProducts','customerGuarantor','staffs'));
    }

    public function newProductSave(Request $request)
    {
        $customerId = $request->customerId;

        if($request->hiddenProduct == '1')
        {
            $purchaseDate = date('Y-m-d', strtotime($request->purchaseDate));
            if($request->purchaseType == 'Cash')
            {
                $request->installmentType = '';
                $deposite = '';

                $request->shortInstallmentPrice = '';
                $request->longInstallmentPrice = '';

                $totalInstallment = '';

                $installmentAmount = '';
            }

            if($request->purchaseType == 'Short Installment')
            {
                $deposite = $request->shortInstallmentDeposite;
                $request->longInstallmentPrice = '';
                $totalInstallment = $request->shortTotalInstallment;
                $installmentAmount = $request->shortInstallmentAmount;
            }

            if($request->purchaseType == 'Long Installment')
            {
                $deposite = $request->longInstallmentDeposite;
                $request->shortInstallmentPrice = '';
                $totalInstallment = $request->longTotalInstallment;
                $installmentAmount = $request->longInstallmentAmount;
            }

            $product = CustomerProduct::create( [
                'showroom_id' => $this->showroomId,
                'customer_id' => $customerId,       
                'product_id' => $request->productId,       
                'reference_id' => $request->referenceId,       
                'remarks' => $request->remarks,       
                'product_model' => $request->productModel,       
                'cash_price' => $request->cashPrice,
                'mrp_price' => $request->shortInstallmentPrice,
                'warranty' => $request->warranty,       
                'purchase_date' => $purchaseDate,       
                'purchase_type' => $request->purchaseType,       
                'installment_type' => $request->installmentType,       
                'deposite' => $deposite,       
                'installment_price' => $request->longInstallmentPrice,       
                'total_installment' => $totalInstallment,       
                'monthly_installment_amount' => $installmentAmount,       
                'product_usage_address' => $request->productUsageAddress,         
                'created_by' => $this->userId,         
            ]);
        }

        if(@$product)
        {
            $countCustomerGuarantor = count($request->gurantorName);
            if($request->gurantorName)
            {
                $postData = [];
                for ($i=0; $i <$countCustomerGuarantor ; $i++)
                {
                    if ($request->gurantorName[$i] != "")
                    {  
                        $postData[] = [
                            'showroom_id' => $this->showroomId,
                            'customer_id'=> $customerId,
                            'product_id'=> $request->productId,
                            'gurantor_name' => $request->gurantorName[$i],
                            'gurantor_phone_no' => $request->gurantorPhoneNo[$i], 
                            'gurantor_age' => $request->gurantorAge[$i],
                            'guarantor_marital_status' => $request->guarantorMaritalStatus[$i],
                            'guarantor_spouse_name' => @$request->guarantorSpouseName[$i],
                            'guarantor_father_name' => @$request->guarantorFatherName[$i],
                            'guarantor_present_address' => $request->guarantorPresentAddress[$i],
                            'guarantor_permanent_address' => $request->guarantorPermanentAddress[$i],
                            'guarantor_profession_name' => $request->guarantorProfessionName[$i],
                            'guarantor_designation' => $request->guarantorDesignation[$i],
                            'guarantor_workplace_phone_no' => $request->guarantorWorkplacePhoneNo[$i],
                            'guarantor_monthly_income' => $request->guarantorMonthlyIncome[$i],
                            'guarantor_work_place_address' => $request->guarantorWorkPlaceAddress[$i],         
                            'created_by' => $this->userId,
                        ];
                    }
                }
                
                CustomerGuarantor::insert($postData);
            }
        }
        return redirect(route('customerRegistraionSetup.index'))->with('msg','Customer New Product Successfuly Created');
    }

    public function edit($id)
    {
    	$title = "Edit Customer";
        $formLink = "customerRegistraionSetup.update";
        $buttonName = "Update";
        $customer = CustomerRegistrationSetup::where('id',$id)->first();
        return view('admin.customerRegistraionSetup.edit.editCustomer')->with(compact('title','formLink','buttonName','customer'));
    }

    public function update(Request $request)
    {
    	$customerId = $request->customerId;
    	$customer = CustomerRegistrationSetup::find($customerId);
    	$customerUpdate = $customer->update([
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
            'updated_by' => $this->userId,                 
        ]);
        return redirect(route('customerRegistraionSetup.view',['id'=>$customerId]))->with(compact('customer'))->with('msg','Customer Information Successfuly Updated');
    }


    public function editCustomerProduct(Request $request, $customerId=NULL,$customerProductId=NULL)
    {
        if($request->isMethod('post'))
        {
            if(count($request->all()) > 0){
                // dd($request->all());
                $customerId = $request->customerId;
                $customerProductId = $request->customerProductId;
                $customerExistProduct = CustomerProduct::where('customer_id',$customerId)->where('id',$customerProductId)->first();

                $guarantor = CustomerGuarantor::where('customer_id',$customerId)->where('product_id',$customerExistProduct->product_id)->first();
                if($guarantor){
                    $guarantor->update( [
                        'product_id' => $request->productId,
                    ]);
                }

                $purchaseDate = date('Y-m-d', strtotime($request->purchaseDate));
                if($request->purchaseType == 'Cash')
                {
                    // $request->installmentType = '';
                    $deposite = '';

                    $request->shortInstallmentPrice = '';
                    $request->longInstallmentPrice = '';

                    $totalInstallment = '';

                    $installmentAmount = '';
                }

                if($request->purchaseType == 'Short Installment')
                {
                    $deposite = $request->shortInstallmentDeposite;
                    $request->longInstallmentPrice = '';
                    $totalInstallment = $request->shortTotalInstallment;
                    $installmentAmount = $request->shortInstallmentAmount;
                }

                if($request->purchaseType == 'Long Installment')
                {
                    $deposite = $request->longInstallmentDeposite;
                    $request->shortInstallmentPrice = '';
                    $totalInstallment = $request->longTotalInstallment;
                    $installmentAmount = $request->longInstallmentAmount;
                }

                $updateCustomerProduct = $customerExistProduct->update([
                    'showroom_id' => $this->showroomId,
                    'customer_id' => $customerId,       
                    'product_id' => $request->productId,       
                    'reference_id' => $request->referenceId,       
                    'remarks' => $request->remarks,       
                    'product_model' => $request->productModel,       
                    'cash_price' => $request->cashPrice,
                    'mrp_price' => $request->shortInstallmentPrice,
                    'warranty' => $request->warranty,       
                    'purchase_date' => $purchaseDate,       
                    'purchase_type' => $request->purchaseType,
                    'installment_type' => $request->installmentType,       
                    'deposite' => $deposite,       
                    'installment_price' => $request->longInstallmentPrice,       
                    'total_installment' => $totalInstallment,       
                    'monthly_installment_amount' => $installmentAmount,       
                    'product_usage_address' => $request->productUsageAddress,
                    'updated_by' => $this->userId,         
                ]);

                if($updateCustomerProduct){
                    InvoiceSetup::where('showroom_id',$this->showroomId)->where('customer_product_id',$customerProductId)->delete();
                }

                $customer = CustomerRegistrationSetup::where('id',$customerId)->first();

                return redirect(route('customerRegistraionSetup.view',['id'=>$customerId]))->with(compact('customer'))->with('msg','Customer Product Successfuly Updated');
            }
        }
        else
        {
            $title = "Edit Customer Product";
            $formLink = "customerRegistraionSetup.updateCustomerProduct";
            $buttonName = "Update";
            $showroomName = $this->showroom->name;
            $products = Product::where('status',1)->orderBy('name','ASC')->get();
            $staffs = StaffSetup::where('showroom_id',$this->showroomId)->where('status',1)->orderBy('name','ASC')->get();
            $customerProduct = CustomerProduct::where('showroom_id',$this->showroomId)->where('customer_id',$customerId)->where('id',$customerProductId)->first();
            // dd($customerProduct);
            return view('admin.customerRegistraionSetup.edit.editCustomerProduct')->with(compact('title','formLink','buttonName','customerProduct','showroomName','products','staffs'));
        }
    }

    public function editGuarantor(Request $request, $customerId=NULL,$guarantorId=NULL)
    {
        if($request->isMethod('post'))
        {
            if(count($request->all()) > 0)
            {
                $customerId = $request->customerId;
                $guarantorId = $request->guarantorId;
                $customerExistGuarantor = CustomerGuarantor::where('customer_id',$customerId)->where('id',$guarantorId)->first();
                $customerExistGuarantor->update( [
                    'showroom_id' => $this->showroomId,
                    'gurantor_name' => $request->gurantorName,
                    'gurantor_phone_no' => $request->gurantorPhoneNo, 
                    'gurantor_age' => $request->gurantorAge,
                    'guarantor_marital_status' => $request->guarantorMaritalStatus,
                    'guarantor_spouse_name' => @$request->guarantorSpouseName,
                    'guarantor_father_name' => @$request->guarantorFatherName,
                    'guarantor_present_address' => $request->guarantorPresentAddress,
                    'guarantor_permanent_address' => $request->guarantorPermanentAddress,
                    'guarantor_profession_name' => $request->guarantorProfessionName,
                    'guarantor_designation' => $request->guarantorDesignation,
                    'guarantor_workplace_phone_no' => $request->guarantorWorkplacePhoneNo,
                    'guarantor_monthly_income' => $request->guarantorMonthlyIncome,
                    'guarantor_work_place_address' => $request->guarantorWorkPlaceAddress,
                    'updated_by' => $this->userId,         
                ]);

                $customer = CustomerRegistrationSetup::orWhere('id',$customerId)->first();

                return redirect(route('customerRegistraionSetup.view',['id'=>$customerId]))->with(compact('customer'))->with('msg','Customer Guarantor Successfuly Updated');

            }
        }
        else
        {
            $title = "Edit Customer Guarantor";
            $formLink = "customerRegistraionSetup.updateCustomerGuarantor";
            $buttonName = "Update";
            $guarantor = CustomerGuarantor::where('customer_id',$customerId)->where('id',$guarantorId)->first();
            return view('admin.customerRegistraionSetup.edit.editCustomerGuarantor')->with(compact('title','formLink','buttonName','guarantor'));
        }

    }

    public function delete(Request $request)
    {
        // CustomerRegistrationSetup::where('id',$request->customerId)->delete();
        // CustomerProduct::where('customer_id',$request->customerId)->delete();
        // CustomerGuarantor::where('customer_id',$request->customerId)->delete();

        $customerRegistration = CustomerRegistrationSetup::find($request->customerId);

        $customerRegistration->update([
            'status' => '0'
        ]);
    }

    public function print($customerId)
    {
        $title = "Print Customer Details";
        $customer = CustomerRegistrationSetup::orWhere('id',$customerId)->first();
        $showrooms = ShowroomSetup::orderBy('name','asc')->where('status',1)->get();
        $products = Product::where('status',1)->orderBy('name','ASC')->get();
        $customerProducts = CustomerProduct::where('customer_id',$customer->id)->get();
        $customerGuarantor = CustomerGuarantor::where('customer_id',$customer->id)->get();

        $pdf = PDF::loadView('admin.customerRegistraionSetup.print',['title'=>$title,'customer'=>$customer,'showrooms'=>$showrooms,'products'=>$products,'customerProducts'=>$customerProducts,'customerGuarantor'=>$customerGuarantor]);

        return $pdf->stream('customer_details.pdf');
    }


    public function getProductInfo(Request $request)
    {
        $productId = $request->productId;

        $product = Product::where('id',$productId)
            ->first();

        if($request->ajax()){
        return response()
                ->json([
                    'product'=>$product
                ]);
            }
   
    }

    public function getGuarantorInfo(Request $request)
    {
        $guarantorId = $request->guarantorId;

        $gurantor = CustomerGuarantor::where('id',$guarantorId)
            ->first();

        if($request->ajax()){
        return response()
                ->json([
                    'gurantor'=>$gurantor
                ]);
            }
    }

    public function customerListPrint(Request $request)
    {  
        $title = "Customer List";
        if ($request->reference != '')
        {
           $customerLists = DB::table('tbl_customers')
                ->select('tbl_customers.*','tbl_customer_products.reference_id','tbl_staffs.name as referenceName')
                ->join('tbl_customer_products','tbl_customers.id','=','tbl_customer_products.customer_id')
                ->join('tbl_staffs','tbl_staffs.id','=','tbl_customer_products.reference_id')
                ->whereIn('tbl_customer_products.reference_id',$request->reference)
                ->orderBy('id','asc')
                ->get();
        }
        else
        {
            $customerLists = DB::table('tbl_customers')
                ->select('tbl_customers.*','tbl_customer_products.reference_id','tbl_staffs.name as referenceName')
                ->leftjoin('tbl_customer_products','tbl_customers.id','=','tbl_customer_products.customer_id')
                ->leftjoin('tbl_staffs','tbl_staffs.id','=','tbl_customer_products.reference_id')
                ->orderBy('id','asc')
                ->get();
        }

        $pdf = PDF::loadView('admin.customerRegistraionSetup.customerListPrint',['title'=>$title,'customerLists'=>$customerLists]);

        return $pdf->stream('customer_lists.pdf');
    }
}
