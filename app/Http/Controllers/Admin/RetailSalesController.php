<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;
use MPDF;
use Session;
use App\Product;
use Carbon\Carbon;
use App\RetailSale;
use App\StaffSetup;
use App\DealerSetup;
use App\HelperClass;
use App\Installment;
use App\RetailSales;
use App\InvoiceSetup;
use App\ShowroomSetup;
use App\LiftingProduct;
use App\CustomerGuarantor;
use App\InstallmentSchedule;
use Illuminate\Http\Request;
use App\CustomerRegistration;
use App\ShowroomProjectSetup;
use App\InstallmentCollection;
use Yajra\DataTables\DataTables;
use App\CustomerRegistrationSetup;
use App\Services\Installment\Collection\CustomerInfo;
use App\InstallmentCollectionList;
use App\Http\Controllers\Controller;
use App\StoreSetup;
use Illuminate\Support\Facades\Auth;

class RetailSalesController extends Controller {

    public static function customerCode() {
        $maxId = CustomerRegistration::max('id');
        $codePrefix = "cust-" . date('ymd') . "-";

        if ($maxId) {
            // $maxOrderNo = substr($maxOrderNo, strlen($orderPrefix));
            $apllicantsCode = $codePrefix . str_pad($maxId + 1, 9, '0', STR_PAD_LEFT);
        } else {
            $apllicantsCode = $codePrefix . "000000001";
        }

        return $apllicantsCode;
    }

    public static function getInvoiceNo() {
        $maxId = RetailSales::max('id');
        $invoiceNoPrefix = date('ymd') . "-";
        // $invoiceNoPrefix = "inv-" . date('ymd') . "-";

        if ($maxId) {
            $invoiceNo = $invoiceNoPrefix . str_pad($maxId + 1, 0, '0', STR_PAD_LEFT);
        } else {
            $invoiceNo = $invoiceNoPrefix . "1";
        }

        return $invoiceNo;
    }

    public function index(Request $request) {
        $title = "Retails Sales";
        $showroomId = $this->showroomId;
        $projects = ShowroomProjectSetup::where('status', 1)->where('showroom_id', @$this->showroomId)->get();

        $project_id = $request->project;



        if ($request->ajax()) {

            $retailSales = [];

            if (!empty($request->project)) {
                $retailSales = RetailSale::with(['customer', 'products.product', 'seller'])
                        ->whereHas('products', function ($q) {
                            $q->where('sales_price', '>', 0);
                        })
                        ->where('company_id', $this->company)
//                        ->where('showroom_id', $this->showroomId)
                        ->where('dealer_id', $project_id)
                        ->orderBy('id', 'desc');
            }

            return DataTables::of($retailSales)
                            ->addIndexColumn()
                            ->editColumn('status', function ($retailSale) {
                                $statusBtn = \App\Link::status($retailSale->id, $retailSale->status);
                                return $statusBtn;
                            })
                            ->addColumn('action', function ($retailSale) {
                                $actionBtn = \App\Link::action($retailSale->id);
                                return $actionBtn;
                            })
                            ->addColumn('sale_date', function ($retailSale) {
                                return date('d-m-Y', strtotime($retailSale->sale_date));
                            })
                            ->addColumn('customer_code', function ($retailSale) {
                                return @$retailSale->customer->code;
                            })
                            ->addColumn('customer_name', function ($retailSale) {
                                return @$retailSale->customer->name;
                            })
                            ->addColumn('sales_by', function ($retailSale) {
                                return @$retailSale->seller->name;
                            })
                            ->addColumn('sale_price', function ($retailSale) {

                                // if ($retailSale->sale_type == 'Cash') {
                                //     $salesPrice = $retailSale->products->sum('cash_price');
                                // } elseif ($retailSale->sale_type == 'Short Installment') {
                                //     $salesPrice = $retailSale->installment_price + $retailSale->deposite;
                                // } else {
                                //     $salesPrice = $retailSale->installment_price + $retailSale->deposite;
                                // }

                                return $retailSale->products->sum('sales_price');
                            })
                            ->addColumn('discount', function ($retailSale) {
                                return $retailSale->products->sum('discount');
                            })
                            ->addColumn('gift_voucher', function ($retailSale) {
                                return $retailSale->products->sum('gift_voucher');
                            })
                            ->addColumn('exchange_crt', function ($retailSale) {
                                return $retailSale->products->sum('exchange_crt');
                            })
                            ->addColumn('net_sale', function ($retailSale) {
                                $sales = $retailSale->products->sum('sales_price');
                                $dicount = $retailSale->products->sum('discount');
                                $voucher = $retailSale->products->sum('gift_voucher');
                                $excrt = $retailSale->products->sum('exchange_crt');

                                $netSale = $sales - ($dicount + $voucher + $excrt);

                                return $netSale;
                            })
                            ->addColumn('user', function ($retailSale) {
                                return @$retailSale->user->name;
                            })
                            ->setRowClass(function ($retailSale) {
                                return '.row_' . $retailSale->id;
                            })
                            ->escapeColumns([])
                            ->toJson();
        }

        return view('admin.retailSales.index')->with(compact('title', 'showroomId', 'projects', 'project_id'));
    }

    public function add(Request $request) {
        $title = "Add Retails Sales";
        $formLink = "retailSales.save";
        $buttonName = "Save";

        $project_id = $request->project;

        $showroomId = $this->showroomId;

        $customers = CustomerRegistration::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('project_id', @$project_id)
                ->get();

        // $products = Product::where('status', 1)->get();
        $products = Product::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', 1)
                ->groupBy('name')
                ->orderBy('name', 'asc')
                ->get();

        $stores = StoreSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get();

        $staffs = StaffSetup::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get();
        $showroomProjects = ShowroomProjectSetup::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get();

        $invoiceNo = self::getInvoiceNo();
        $apllicantsCode = self::customerCode();

        return view('admin.retailSales.add')->with(compact('apllicantsCode', 'project_id', 'invoiceNo', 'title', 'formLink', 'buttonName', 'customers', 'products', 'staffs', 'showroomProjects', 'showroomId', 'stores'));
    }

    public function getModel(Request $request) {
        $product = Product::where('id', $request->productId)->first();


        $models = Product::select('model_no')
                ->where('status', 1)
                ->where('name', $product->name)
                ->get();

        return $models;
    }

    public function save(Request $request) {

        DB::beginTransaction();
        $purchaseDate = date('Y-m-d', strtotime($request->purchaseDate));

        if ($request->purchaseType == 'Cash') {
            $request->installmentType = '';
            $deposite = '';

            $request->shortInstallmentPrice = '';
            $request->longInstallmentPrice = '';

            $totalInstallment = '';

            $installmentAmount = '';
        }

        if ($request->purchaseType == 'Short Installment') {
            $deposite = $request->shortInstallmentDeposite;
            $dueAmount = $request->shortInstallmentDueAmount;
            $request->longInstallmentPrice = '';
            $request->salesPrice = '';
            $totalInstallment = $request->shortTotalInstallment;
            $installmentAmount = $request->shortInstallmentAmount;
        }

        if ($request->purchaseType == 'Long Installment') {
            $deposite = $request->longInstallmentDeposite;
            $dueAmount = $request->longInstallmentDueAmount;
            $request->shortInstallmentPrice = '';
            $request->salesPrice = '';
            $totalInstallment = $request->longTotalInstallment;
            $installmentAmount = $request->longInstallmentAmount;
        }

        $project_id = $request->dealerId;

        $customerId = $request->customerId;

        try {
            $cus = CustomerRegistrationSetup::find($customerId);

            $cus->update([
                'phone_no' => $request->customerPhone,
            ]);

            $sale = RetailSale::create([
                        'company_id' => $this->company,
                        'store_id' => $request->store,
                        'invoice_no' => $request->invoice_no,
                        'showroom_id' => $this->showroomId,
                        'customer_id' => $customerId,
                        'dealer_id' => $request->dealerId,
                        'reference_id' => $request->referenceId,
                        'sale_date' => $purchaseDate,
                        'sale_type' => $request->purchaseType,
                        'installment_type' => $request->installmentType,
                        'deposite' => $deposite,
                        'installment_price' => @$dueAmount,
                        'total_installment' => $totalInstallment,
                        'monthly_installment_amount' => $installmentAmount,
                        'product_usage_address' => $request->productUsageAddress,
                        'status' => 1,
                        'created_by' => Auth::user()->id,
            ]);

            //            dd($sale);


            $totalSalesAmount = 0;
            $collectionAmount = 0;
            $i = 1;
            foreach ($request->prmodel as $key => $prmodel) {

                $totalSalesAmount += empty($request->salesPrice) ? 0 : $request->salesPrice[$key];

                $product = Product::where('model_no', $prmodel)->first();
                $serial = 0;


                if (!$product->old) {
                    $serial = $request->productSerials[$key];
                }

                $salePrice = 0;
                $indMrp = 0;
                $indHigh = 0;
                $cashPrice = $request->cashPrice[$key];
                $mrpPrice = $cashPrice + ($cashPrice * 8) / 100;
                $hirePrice = $mrpPrice + ($mrpPrice * 12) / 100;

                if ($request->purchaseType == 'Cash') {
                    $salePrice = $cashPrice;
                } elseif ($request->purchaseType == 'Short Installment') {
                    $salePrice = $mrpPrice;
                    $indMrp = $mrpPrice;
                } elseif ($request->purchaseType == 'Long Installment') {
                    $salePrice = $hirePrice;
                    $indHigh = $hirePrice;
                } else {
                    $salePrice = 0;
                }

                $discount = $request->discount[$key];
                $gift = $request->giftVoucher[$key];
                $crt = $request->exchangeCrt[$key];

                $collectionAmount += $salePrice - ($discount + $gift + $crt);

                $retailSales = RetailSales::create([
                            'company_id' => $this->company,
                            'store_id' => $request->store,
                            'sale_id' => $sale->id,
                            'product_id' => $product->id,
                            'product_serial' => $serial,
                            'remarks' => $request->remarks[$key],
                            'product_model' => $product->model_no,
                            'cash_price' => $request->cashPrice[$key],
                            'sales_price' => $salePrice,
                            'mrp_price' => $indMrp,
                            'hire_price' => $indHigh,
                            'warranty' => $request->warranty[$key],
                            'discount' => $request->discount[$key],
                            'gift_voucher' => $request->giftVoucher[$key],
                            'exchange_crt' => $request->exchangeCrt[$key],
                            'mobile_gift' => $request->mobileGift[$key],
                            'gift_name' => $request->GiftName[$key],
                            'created_by' => $this->userId,
                ]);
            }


            if (@$retailSales != '' && $request->purchaseType != 'Cash') {

                foreach ($request->gurantorName as $key => $gName) {

                    CustomerGuarantor::create([
                        'sale_id' => $sale->id,
                        'retail_invoice_no' => $request->invoice_no,
                        'showroom_id' => $this->showroomId,
                        'customer_id' => $customerId,
                        'dealer_id' => $request->dealerId,
                        'gurantor_name' => $request->gurantorName[$key],
                        'gurantor_phone_no' => $request->gurantorPhoneNo[$key],
                        'gurantor_age' => $request->gurantorAge[$key],
                        'guarantor_marital_status' => $request->guarantorMaritalStatus[$key],
                        'guarantor_spouse_name' => @$request->guarantorSpouseName[$key],
                        'guarantor_father_name' => @$request->guarantorFatherName[$key],
                        'guarantor_present_address' => $request->guarantorPresentAddress[$key],
                        'guarantor_permanent_address' => $request->guarantorPermanentAddress[$key],
                        'guarantor_profession_name' => $request->guarantorProfessionName[$key],
                        'guarantor_designation' => $request->guarantorDesignation[$key],
                        'guarantor_workplace_phone_no' => $request->guarantorWorkplacePhoneNo[$key],
                        'guarantor_monthly_income' => $request->guarantorMonthlyIncome[$key],
                        'guarantor_work_place_address' => $request->guarantorWorkPlaceAddress[$key],
                        'created_by' => $this->userId
                    ]);
                }
            }

            if ($request->purchaseType != 'Cash') {

                // add installment start
                $installment = Installment::create([
                            'company_id' => $this->company,
                            'showroom_id' => $this->showroomId,
                            'project_id' => $request->dealerId,
                            'customer_id' => $customerId,
                            'invoice_no' => $sale->invoice_no,
                            'installment_collector_id' => $request->referenceId,
                            'installment_price' => @$dueAmount,
                            'booking_amount' => $deposite,
                            'installment_qty' => $totalInstallment,
                            'installment_amount' => $installmentAmount,
                            'created_by' => $this->userId
                ]);

                if ($installment) {

                    // dd($request->all());

                    $startDate = new Carbon($purchaseDate);

                    $installmentDayEscape = [
                        'Daily' => 1,
                        'Weekly' => 7,
                        'Bi-Monthly' => 15,
                        'Monthly' => 30
                    ];

                    $dates = [$startDate->format('Y-m-d')];

                    for ($i = 1; $i < $totalInstallment; $i++) {
                        $a = $startDate->addDays($installmentDayEscape[$request->installmentType]);
                        array_push($dates, $a->format('Y-m-d'));
                    }

                    // dd($dates);

                    $depositeAdded = false;

                    foreach ($dates as $date) {

                        $insAmount = $installmentAmount;
                        $status = 1;

                        if (!$depositeAdded) {
                            $insAmount = $deposite;
                            $depositeAdded = true;
                            $status = 0;
                        }

                        InstallmentSchedule::create([
                            'company_id' => $this->company,
                            'showroom_id' => $this->showroomId,
                            'installment_id' => $installment->id,
                            'invoice_no' => $sale->invoice_no,
                            'installment_schedule_date' => $date,
                            'installment_schedule_amount' => $insAmount,
                            'status' => $status,
                            'created_by' => $this->userId
                        ]);
                    }
                }

                // add installment end
                // add collection Master Table
                if ($deposite > 0) {
                    $collection = InstallmentCollection::create([
                                'company_id' => $this->company,
                                'showroom_id' => $this->showroomId,
                                'project_id' => $request->dealerId,
                                'installment_id' => $installment->id,
                                'customer_id' => $customerId,
                                'invoice_no' => $sale->invoice_no,
                                'installment_price' => @$dueAmount,
                                'booking_amount' => $deposite,
                                'installment_qty' => $totalInstallment,
                                'installment_amount' => $installmentAmount,
                                'created_by' => Auth::user()->id,
                                'reference_id' => $request->referenceId
                    ]);

                    // add deposite collection

                    $depositeSchedule = InstallmentSchedule::where('installment_id', $installment->id)
                            ->orderBy('installment_schedule_date', 'asc')
                            ->first();

                    $installment_collection_list = InstallmentCollectionList::create([
                                'company_id' => $this->company,
                                'showroom_id' => $this->showroomId,
                                'project_id' => $request->dealerId,
                                'customer_id' => $customerId,
                                'installment_id' => $installment->id,
                                'installment_schedule_id' => $depositeSchedule->id,
                                'installment_collection_id' => $collection->id,
                                'invoice_no' => $sale->invoice_no,
                                'installment_schedule_date' => $depositeSchedule->installment_schedule_date,
                                'installment_collection_date' => $purchaseDate,
                                'installment_schedule_amount' => $deposite,
                                'created_by' => Auth::user()->id,
                    ]);
                }
            } else {
                if ($deposite > 0) {
                    // add collection Master Table
                    $collection = InstallmentCollection::create([
                                'company_id' => $this->company,
                                'showroom_id' => $this->showroomId,
                                'project_id' => $request->dealerId,
                                'installment_id' => 0,
                                'customer_id' => $customerId,
                                'invoice_no' => $sale->invoice_no,
                                'installment_price' => 0,
                                'booking_amount' => $totalSalesAmount,
                                'installment_qty' => $totalInstallment,
                                'installment_amount' => $installmentAmount,
                                'created_by' => Auth::user()->id,
                                'reference_id' => $request->referenceId
                    ]);

                    InstallmentCollectionList::create([
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'project_id' => $request->dealerId,
                        'installment_id' => 0,
                        'installment_schedule_id' => 0,
                        'installment_collection_id' => $collection->id,
                        'invoice_no' => $sale->invoice_no,
                        'installment_schedule_date' => '',
                        'installment_collection_date' => $purchaseDate,
                        'installment_schedule_amount' => $totalSalesAmount,
                        'created_by' => Auth::guard('admin')->id(),
                    ]);
                }
            }

            if ($request->purchaseType == 'Cash') {
                $collection = InstallmentCollection::create([
                            'company_id' => $this->company,
                            'showroom_id' => $this->showroomId,
                            'project_id' => $request->dealerId,
                            'installment_id' => 0,
                            'customer_id' => $customerId,
                            'invoice_no' => $sale->invoice_no,
                            'installment_price' => 0,
                            'booking_amount' => $collectionAmount,
                            'installment_qty' => 1,
                            'installment_amount' => $collectionAmount,
                            'created_by' => Auth::user()->id,
                            'reference_id' => $request->referenceId
                ]);

                InstallmentCollectionList::create([
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'project_id' => $request->dealerId,
                    'installment_id' => 0,
                    'installment_schedule_id' => 0,
                    'installment_collection_id' => $collection->id,
                    'invoice_no' => $sale->invoice_no,
                    'installment_schedule_date' => $purchaseDate,
                    'installment_collection_date' => $purchaseDate,
                    'installment_schedule_amount' => $collectionAmount,
                    'created_by' => Auth::guard('admin')->id(),
                ]);
            }
            DB::commit();

            //            dd($i);

            return redirect(route('retailSales.index', ['project' => $project_id]))->with('msg', 'Retail Sales Successfuly Complete');
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    public function edit($retailSalesId, Request $request) {
        $title = "Edit Retails Sales";
        $formLink = "retailSales.update";
        $buttonName = "Update";
        $showroomId = $this->showroomId;

        $retailSales = RetailSale::with(['customer', 'products.product'])->where('id', $retailSalesId)->first();

        $products = Product::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->groupBy('name')
                ->orderBy('name', 'asc')
                ->get();

        $apllicantsCode = self::customerCode();
        $stores = StoreSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get();

        $customers = CustomerRegistration::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('project_id', $retailSales->dealer_id)
                ->get();
        $staffs = StaffSetup::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('showroom_id', $showroomId)
                ->get();

        // $products = Product::where('status', 1)->get();
        $showroomProjects = ShowroomProjectSetup::where('status', 1)
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->get();


        return view('admin.retailSales.edit')->with(compact('apllicantsCode', 'showroomId', 'title', 'formLink', 'buttonName', 'customers', 'products', 'staffs', 'showroomProjects', 'retailSales', 'stores'));
    }

    public function update(Request $request) {

        // if (HelperClass::has_dupes($request->productSerials)) {
        //     return back()->with('fail_msg', "Cannot use same serial twice");
        // }

        $purchaseDate = date('Y-m-d', strtotime($request->purchaseDate));

        if ($request->purchaseType == 'Cash') {
            $request->installmentType = '';
            $deposite = '';

            $request->shortInstallmentPrice = '';
            $request->longInstallmentPrice = '';

            $totalInstallment = '';

            $installmentAmount = '';
        }

        if ($request->purchaseType == 'Short Installment') {
            $deposite = $request->shortInstallmentDeposite;
            $request->longInstallmentPrice = '';
            $request->salesPrice = '';
            $totalInstallment = $request->shortTotalInstallment;
            $installmentAmount = $request->shortInstallmentAmount;
            $dueAmount = $request->shortInstallmentDueAmount;
        }

        if ($request->purchaseType == 'Long Installment') {
            $deposite = $request->longInstallmentDeposite;
            $request->shortInstallmentPrice = '';
            $request->salesPrice = '';
            $totalInstallment = $request->longTotalInstallment;
            $installmentAmount = $request->longInstallmentAmount;
            $dueAmount = $request->longInstallmentDueAmount;
        }

        $sale = RetailSale::find($request->retailSalesId);

        $sale->update([
            'invoice_no' => $request->invoice_no,
            'customer_id' => $request->customerId,
            'dealer_id' => $request->dealerId,
            'reference_id' => $request->referenceId,
            'sale_date' => $purchaseDate,
            'sale_type' => $request->purchaseType,
            'installment_type' => $request->installmentType,
            'deposite' => $deposite,
            'installment_price' => @$dueAmount,
            'total_installment' => $totalInstallment,
            'monthly_installment_amount' => $installmentAmount,
            'product_usage_address' => $request->productUsageAddress,
            'status' => 1,
            'created_by' => Auth::user()->id,
        ]);

        RetailSales::where('sale_id', $sale->id)->delete();

        foreach ($request->prmodel as $key => $prmodel) {

            $product = Product::where('model_no', $prmodel)->first();

            $salePrice = 0;
            $indMrp = 0;
            $indHigh = 0;
            $cashPrice = $request->cashPrice[$key];
            $mrpPrice = $cashPrice + ($cashPrice * 8) / 100;
            $hirePrice = $mrpPrice + ($mrpPrice * 12) / 100;

            if ($request->purchaseType == 'Cash') {
                $salePrice = $cashPrice;
            } elseif ($request->purchaseType == 'Short Installment') {
                $salePrice = $mrpPrice;
                $indMrp = $mrpPrice;
            } elseif ($request->purchaseType == 'Long Installment') {
                $salePrice = $hirePrice;
                $indHigh = $hirePrice;
            } else {
                $salePrice = 0;
            }

            $retailSales = RetailSales::create([
                        'company_id' => $this->company,
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'product_serial' => $request->productSerials[$key],
                        'remarks' => $request->remarks[$key],
                        'product_model' => $product->model_no,
                        'cash_price' => $request->cashPrice[$key],
                        'sales_price' => $salePrice,
                        'mrp_price' => $indMrp,
                        'hire_price' => $indHigh,
                        'warranty' => $request->warranty[$key],
                        'discount' => $request->discount[$key],
                        'gift_voucher' => $request->giftVoucher[$key],
                        'gift_name' => $request->GiftName[$key],
                        'exchange_crt' => $request->exchangeCrt[$key],
                        'mobile_gift' => $request->mobileGift[$key],
                        'created_by' => $this->userId,
            ]);
        }

        CustomerGuarantor::where('sale_id', $sale->id)->delete();

        if (@$retailSales != '' && $request->purchaseType != 'Cash') {
            if ($request->gurantorName) {
                foreach ($request->gurantorName as $key => $gName) {

                    CustomerGuarantor::create([
                        'sale_id' => $sale->id,
                        'retail_invoice_no' => $request->invoice_no,
                        'showroom_id' => $this->showroomId,
                        'customer_id' => $request->customerId,
                        'dealer_id' => $request->dealerId,
                        'gurantor_name' => $request->gurantorName[$key],
                        'gurantor_phone_no' => $request->gurantorPhoneNo[$key],
                        'gurantor_age' => $request->gurantorAge[$key],
                        'guarantor_marital_status' => $request->guarantorMaritalStatus[$key],
                        'guarantor_spouse_name' => @$request->guarantorSpouseName[$key],
                        'guarantor_father_name' => @$request->guarantorFatherName[$key],
                        'guarantor_present_address' => $request->guarantorPresentAddress[$key],
                        'guarantor_permanent_address' => $request->guarantorPermanentAddress[$key],
                        'guarantor_profession_name' => $request->guarantorProfessionName[$key],
                        'guarantor_designation' => $request->guarantorDesignation[$key],
                        'guarantor_workplace_phone_no' => $request->guarantorWorkplacePhoneNo[$key],
                        'guarantor_monthly_income' => $request->guarantorMonthlyIncome[$key],
                        'guarantor_work_place_address' => $request->guarantorWorkPlaceAddress[$key],
                        'created_by' => $this->userId
                    ]);
                }
            }
        }

        return redirect(route('retailSales.index', ['project' => $request->dealerId]))->with('msg', 'Retail Sales Successfuly Updated');
    }

    public function view($retailSalesId) {
        $title = "Retails Sales Information";

        $retailSales = RetailSale::where('id', $retailSalesId)->with(['customer', 'seller', 'guarantor', 'products.product'])->first();

        // dd($retailSales);

        return view('admin.retailSales.view')->with(compact('title', 'retailSales'));
    }

    public function getCustomerInfo(Request $request) {
        $customer = CustomerRegistration::where('id', $request->customerId)->first();

        $retailcusId = RetailSale::select('customer_id')->where('customer_id', $customer->id)->first();

        if (!empty($retailcusId)) {
            $customerSavings = [
                'totalSavings' => 0
            ];
        } else {
            $customerSavings = CustomerInfo::getCustomerRetailSaleSavings($customer->id);
        }

        //        if ($request->ajax()) {
        return response()->json([
                    'customer' => $customer,
                    'customerSavings' => $customerSavings
        ]);
        //        }
    }

    public static function getProductStock($product_id) {
        $lifting = LiftingProduct::where('product_id', $product_id)->sum('qty');
        $sale = RetailSales::where('product_id', $product_id)->sum('qty');

        $stock = $lifting - $sale;

        return $stock;
    }

    public static function getProductSerial($product_id) {
        $lifting = LiftingProduct::where('product_id', $product_id)->select('serial_no')->get()->pluck('serial_no')->toArray();
        $retailSale = RetailSales::where('product_id', $product_id)->select('product_serial')->get()->pluck('product_serial')->toArray();

        $aviliableSerials = array_diff($lifting, $retailSale);

        return $aviliableSerials;
    }

    public function getAllProduct(Request $request) {
        $product = Product::where('id', $request->productId)->first();
        $products = Product::where('name', $product->name)->get();

        if ($request->ajax()) {
            return response()->json([
                        'products' => $products,
                        // 'stock' => self::getProductStock($request->productId),
                        'serial_no' => self::getProductSerial($request->productId),
            ]);
        }
    }

    public function getProductInfo(Request $request) {
        $product = Product::where('model_no', $request->model_no)->first();

        if ($request->ajax()) {
            return response()->json([
                        'product' => $product,
                        'stock' => self::getProductStock($product->id),
                        'serial_no' => self::getProductSerial($product->id),
            ]);
        }
    }

    public function delete(Request $request) {
        $retailSale = RetailSale::findOrFail($request->retailSalesId);

        RetailSales::where('sale_id', $retailSale->id)->delete();
        CustomerGuarantor::where('sale_id', $retailSale->id)->delete();

        $retailSale->delete();
    }

    public function status(Request $request) {
        $retailSale = RetailSale::find($request->retailSalesId);

        if ($retailSale->status == 1) {
            $retailSale->update([
                'status' => 0
            ]);
        } else {
            $retailSale->update([
                'status' => 1
            ]);
        }
    }

}
