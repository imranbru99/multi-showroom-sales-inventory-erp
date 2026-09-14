<?php

namespace App\Http\Controllers;

use App\Lifting;
use App\Product;
use Carbon\Carbon;
use App\RetailSale;
use App\StaffSetup;
use App\Installment;
use App\RetailSales;
use App\VendorSetup;
use App\PrevPurchase;
use App\LiftingReturn;
use App\LiftingProduct;
use App\CustomerAgreement;
use App\CustomerGuarantor;
use App\RetailSalesReport;
use App\PrevPurchaseReturn;
use App\InstallmentSchedule;
use Illuminate\Http\Request;
use App\LiftingReturnProduct;
use App\InstallmentCollection;
use App\PreviousRetailCollection;
use App\CustomerRegistrationSetup;
use App\DealerSetup;
use App\InstallmentCollectionList;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class DBScriptController extends Controller
{

    public function insertVendorFromPreviousPurchase()
    {
        $vendors = PrevPurchase::groupBy('vendor_name')->select('vendor_name')->get()->pluck('vendor_name');

        foreach ($vendors as $vendor) {

            // check if already exists
            $vendorExists = VendorSetup::where('name', $vendor)->count();

            if ($vendorExists) {
                continue;
            }

            // insert vendor
            VendorSetup::create([
                'name' =>  $vendor,
            ]);
        }

        return "Done";
    }

    public function b()
    {

        $vendors = VendorSetup::all();

        foreach ($vendors as $vendor) {

            PrevPurchase::where('vendor_name', $vendor->name)->update([
                'vendor_id' => $vendor->id,
            ]);
        }

        return "Done";
    }

    public function c()
    {

        $cashCustomers = RetailSalesReport::where('account_no', 'like', 'cash')->get();

        foreach ($cashCustomers as $cashCustomer) {
            // check if already exists
            $customer = CustomerRegistrationSetup::where('name', $cashCustomer->customer_name)->count();

            if ($customer) {
                continue;
            }

            // insert customer
            CustomerRegistrationSetup::create([
                'code' => $cashCustomer->account_no,
                'showroom_id' => $cashCustomer->showroom_id,
                'name' => $cashCustomer->customer_name,
                'nick_name' => $cashCustomer->customer_name,
                'nid' => $cashCustomer->nid,
                'age' => 30,
                'phone_no' => $cashCustomer->mobile_no,
            ]);
        }

        return "Done";
    }

    public function d()
    {

        $installCustomers = RetailSalesReport::where('customer_id', '')->get();

        foreach ($installCustomers as $installCustomer) {
            // check if already exists
            $customer = CustomerRegistrationSetup::where('name', 'like', $installCustomer->account_no)->count();

            if ($customer) {
                continue;
            }

            // insert customer
            CustomerRegistrationSetup::create([
                'code' => $installCustomer->account_no,
                'showroom_id' => $installCustomer->showroom_id,
                'name' => $installCustomer->customer_name,
                'nick_name' => $installCustomer->customer_name,
                'nid' => $installCustomer->nid,
                'age' => 30,
                'phone_no' => $installCustomer->mobile_no,
            ]);
        }

        return "Done";
    }

    public function e()
    {

        // $customers = CustomerRegistrationSetup::skip(0)->take(5000)->get();

        // foreach ($customers as $customer) {

        //     RetailSalesReport::where('account_no', $customer->code)
        //         ->update([
        //             'customer_id' => $customer->id,
        //         ]);

        //     echo "customer " . $customer->name . "  code " . $customer->code . " added <br>";
        // }


        $previousCollection_list = RetailSalesReport::all();

        $i = 1;

        foreach ($previousCollection_list as $previousCollection_l) {

            $customer = CustomerRegistrationSetup::where('code', $previousCollection_l->account_no)->first();

            if (!$customer) {
                $customer = CustomerRegistrationSetup::create([

                    'code' => $previousCollection_l->account_no,
                    'showroom_id' => $previousCollection_l->showroom_id,
                    'name' => $previousCollection_l->customer_name,
                    'nick_name' => $previousCollection_l->customer_name,
                    'nid' => $previousCollection_l->nid,
                    'age' => 30,
                    'phone_no' => $previousCollection_l->mobile_no,

                ]);
            }

            $previousCollection_l->update([
                'customer_id' => $customer->id,
            ]);

            echo $i . '  ';
            $i++;
        }

        return "Done";
    }

    public function f()
    {

        $products = RetailSalesReport::where('product_name', '!=', '')->skip(0)->limit(40000)->get();

        $i = 1;
        foreach ($products as $product) {

            $p = Product::where('name', $product->product_name)->where('model_no', $product->model_no)->count();

            if ($p) {
                continue;
            }

            Product::create([
                'name' => $product->product_name,
                'model_no' => $product->model_no,
                'price' => $product->price,
            ]);

            echo $i . '  ';
            $i++;
        }

        return "Done";
    }

    public function g()
    {

        $products = Product::all();

        $i = 1;

        foreach ($products as $product) {
            // dd($product);

            // PrevPurchase::where('product_name', $product->name)
            //     ->where('model_no', $product->model_no)
            //     ->update([
            //         'product_id' => $product->id,
            //     ]);


            RetailSalesReport::where('product_name', $product->name)
                ->where('model_no', $product->model_no)
                ->update([
                    'product_id' => $product->id,
                ]);


            echo $i . '  ';
            $i++;
        }

        return "Done";
    }

    public function h()
    {

        $purchases = PrevPurchase::all();

        foreach ($purchases as $purchase) {

            PrevPurchase::findOrFail($purchase->id)
                ->update([
                    'date' => date('Y-m-d', strtotime($purchase->date))
                ]);
        }

        return "Done";
    }

    public function i()
    {

        $maxLiftingProduct = LiftingProduct::max('serial_no');

        if (@$maxLiftingProduct) {
            $productSerialNo = $maxLiftingProduct + 1;
        } else {
            $productSerialNo = 1000000 + 1;
        }

        $vendors = VendorSetup::get();

        foreach ($vendors as $vendor) {

            $vouchers = PrevPurchase::where('vendor_id', $vendor->id)->groupBy('invoice_no')->get()->pluck('invoice_no');

            foreach ($vouchers as $voucher) {

                $lifting = Lifting::where('vaouchar_no', $voucher)->count();

                if ($lifting) {
                    continue;
                }

                $voucherWiseVendorPurchase = PrevPurchase::where('vendor_id', $vendor->id)->where('invoice_no', $voucher)->get();

                $maxLifting = Lifting::max('serial_no');

                if (@$maxLifting) {
                    $serialNo = $maxLifting + 1;
                } else {
                    $serialNo = 1000000 + 1;
                }

                // insert lifting

                $lifting = Lifting::create([
                    'showroom_id' => Session::get('showroom'),
                    'serial_no' => $serialNo,
                    'vaouchar_no' => $voucher,
                    'vendor_id' => $vendor->id,
                    'store_or_showroom_type' => 'showroom',
                    'store_or_showroom_id' => $voucherWiseVendorPurchase[0]->showroom_id,
                    'purchase_by' => "Admin",
                    'submission_date' => date('Y-m-d', strtotime(now())),
                    'vouchar_date' => $voucherWiseVendorPurchase[0]->date,
                    'total_qty' => $voucherWiseVendorPurchase->sum('qty'),
                    'total_cost' => $voucherWiseVendorPurchase->sum('invoice_amount'),
                    'total_price' => $voucherWiseVendorPurchase->sum('mrp_tk'),
                    'created_by' => Auth::guard('admin')->id(),
                ]);


                // insert lifting product infos

                foreach ($voucherWiseVendorPurchase as $vwp) {

                    LiftingProduct::create([
                        'showroom_id' => Session::get('showroom'),
                        'lifting_id' => $lifting->id,
                        'vendor_id' => $vendor->id,
                        'product_id' => $vwp->product_id,
                        'product_name' => $vwp->product_name,
                        'store_or_showroom_type' => 'showroom',
                        'store_or_showroom_id' => $vwp->showroom_id,
                        'model_no' => $vwp->model_no,
                        'serial_no' => $productSerialNo,
                        'qty' => $vwp->qty,
                        'cost' => $vwp->cp_tk,
                        'price' => $vwp->mrp_tk,
                        'created_by' =>
                        Auth::guard('admin')->id(),
                    ]);

                    $productSerialNo++;
                }
            }
        }

        return "Done";
    }

    public function j()
    {
        $staffs = RetailSalesReport::groupBy('employee_name')->select('employee_name')->get()->pluck('employee_name');

        foreach ($staffs as $staff) {

            $staffExists = StaffSetup::where('name', $staff)->count();

            if ($staffExists) {
                continue;
            }

            StaffSetup::create([
                'name' => $staff,
            ]);
        }

        return "Done";
    }

    public function k()
    {
        $staffs = StaffSetup::all();

        foreach ($staffs as $staff) {

            RetailSalesReport::where('employee_name', $staff->name)->update([
                'employee_id' => $staff->id,
            ]);
        }

        return "Done";
    }

    public function l()
    {

        $cashSalesMemos = RetailSalesReport::where('account_no', 'cash')
            ->where('memo_no', '!=', '')
            // ->select(['memo_no'])
            // ->skip(0)
            // ->limit(5000)
            // ->groupBy('memo_no')
            ->get();

        // dd($cashSalesMemos);

        foreach ($cashSalesMemos as $cashSalesMemo) {

            // check if memo exists
            // $memoExists = RetailSale::where('invoice_no', $cashSalesMemo->memo_no)->count();

            // if ($memoExists) {
            //     continue;
            // }

            // $csm = RetailSalesReport::where('memo_no', $cashSalesMemo->memo_no)
            //     ->get();


            $sale = RetailSale::create([
                'invoice_no' => $cashSalesMemo->memo_no,
                'showroom_id' => $cashSalesMemo->showroom_id,
                'customer_id' => $cashSalesMemo->customer_id,
                'dealer_id' => $cashSalesMemo->dealer_id,
                'reference_id' => $cashSalesMemo->employee_id,
                'sale_date' => $cashSalesMemo->date,
                'sale_type' => "Cash",
                'installment_type' => "",
                'deposite' => $cashSalesMemo->sum('sales_amount'),
                'installment_price' => Null,
                'total_installment' => 0,
                'monthly_installment_amount' => 0,
                'product_usage_address' => $cashSalesMemo->address,
                'status' => 1,
                'created_by' => 1,
            ]);

            // $retailSaleProducts = RetailSalesReport::where('memo_no', $cashSalesMemo->memo_no)->get();

            // foreach ($retailSaleProducts as $retailSaleProduct) {

            $product = Product::where('name', 'like', '%' . $cashSalesMemo->product_name . '%')->where('model_no', 'like', '%' . $cashSalesMemo->model_no . '%')->first();

            RetailSales::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'product_serial' => Null,
                'remarks' => "",
                'product_model' => $product->model_no,
                'cash_price' => $cashSalesMemo->price,
                'sales_price' => $cashSalesMemo->collection,
                'mrp_price' => Null,
                'hire_price' => Null,
                'warranty' => Null,
                'discount' => $cashSalesMemo->discount,
                'gift_voucher' => $cashSalesMemo->gift_voucher,
                'exchange_crt' => $cashSalesMemo->exchange_crt,
                'mobile_gift' => $cashSalesMemo->mobile_gift,
                'created_by' => 1,
            ]);
            // }
        }

        return "Done";
    }

    public function m()
    {

        $installSalesMemos = RetailSalesReport::where('account_no', '!=', 'cash')
            ->where('memo_no', '!=', '')
            ->select(['memo_no'])
            // ->skip(3000)
            // ->limit(1000)
            // ->groupBy('memo_no')
            ->get();

        // dd($installSalesMemos);

        $a = 1;

        foreach ($installSalesMemos as $installSalesMemo) {

            // check if memo exists
            // $memoExists = RetailSale::where('invoice_no', $installSalesMemo->memo_no)->count();

            // dd($memoExists);

            // if ($memoExists) {
            //     continue;
            // }

            $ism = RetailSalesReport::where('memo_no', $installSalesMemo->memo_no)
                ->get();


            $sale = RetailSale::create([
                'invoice_no' => $ism[0]->memo_no,
                'showroom_id' => $ism[0]->showroom_id,
                'customer_id' => $ism[0]->customer_id,
                'dealer_id' => $ism[0]->dealer_id,
                'reference_id' => $ism[0]->employee_id,
                'sale_date' => $ism[0]->date,
                'sale_type' => "Short Installment",
                'installment_type' => "Weekly",
                'deposite' => $ism->sum('collection'),
                'installment_price' => Null,
                'total_installment' => 0,
                'monthly_installment_amount' => 0,
                'product_usage_address' => $ism[0]->address,
                'status' => 1,
                'created_by' => 1,
            ]);

            $retailSaleProducts = RetailSalesReport::where('memo_no', $ism[0]->memo_no)->get();

            // dd($retailSaleProducts);

            foreach ($retailSaleProducts as $retailSaleProduct) {

                $product = Product::where('name', 'like', '%' . $retailSaleProduct->name . '%')->where('model_no', 'like', '%' . $retailSaleProduct->model_no . '%')->first();

                $as = RetailSales::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_serial' => Null,
                    'remarks' => "",
                    'product_model' => $product->model_no,
                    'cash_price' => $retailSaleProduct->price,
                    'sales_price' => $retailSaleProduct->collection,
                    'mrp_price' => Null,
                    'hire_price' => Null,
                    'warranty' => Null,
                    'discount' => $retailSaleProduct->discount,
                    'gift_voucher' => $retailSaleProduct->gift_voucher,
                    'exchange_crt' => $retailSaleProduct->exchange_crt,
                    'mobile_gift' => $retailSaleProduct->mobile_gift,
                    'created_by' => 1,
                ]);

                // dd($as);
            }

            CustomerGuarantor::create([
                'sale_id' => $sale->id,
                'retail_invoice_no' => $ism[0]->memo_no,
                'showroom_id' => $ism[0]->showroom_id,
                'customer_id' => $ism[0]->customer_id,
                'dealer_id' => $ism[0]->dealer_id,
                'gurantor_name' => '',
                'gurantor_phone_no' => '',
                'gurantor_age' => '',
                'guarantor_marital_status' => '',
                'guarantor_spouse_name' => '',
                'guarantor_father_name' => '',
                'guarantor_present_address' => '',
                'guarantor_permanent_address' => '',
                'guarantor_profession_name' => '',
                'guarantor_designation' => '',
                'guarantor_workplace_phone_no' => '',
                'guarantor_monthly_income' => '',
                'guarantor_work_place_address' => '',
                'created_by' => Auth::guard('admin')->id(),
            ]);

            CustomerGuarantor::create([
                'sale_id' => $sale->id,
                'retail_invoice_no' => $ism[0]->memo_no,
                'showroom_id' => $ism[0]->showroom_id,
                'customer_id' => $ism[0]->customer_id,
                'dealer_id' => $ism[0]->dealer_id,
                'gurantor_name' => '',
                'gurantor_phone_no' => '',
                'gurantor_age' => '',
                'guarantor_marital_status' => '',
                'guarantor_spouse_name' => '',
                'guarantor_father_name' => '',
                'guarantor_present_address' => '',
                'guarantor_permanent_address' => '',
                'guarantor_profession_name' => '',
                'guarantor_designation' => '',
                'guarantor_workplace_phone_no' => '',
                'guarantor_monthly_income' => '',
                'guarantor_work_place_address' => '',
                'created_by' => Auth::guard('admin')->id(),
            ]);

            // add installment start
            $installment = Installment::create([
                'showroom_id' => $ism[0]->showroom_id,
                'customer_id' => $ism[0]->customer_id,
                'product_id' => $product->id,
                'invoice_no' => $ism[0]->memo_no,
                'installment_collector_id' => $ism[0]->employee_id,
                'installment_price' => $ism->sum('total_balance'),
                'booking_amount' => $ism->sum('collection'),
                'installment_qty' => 1,
                'installment_amount' => $ism->sum('total_balance'),
                'created_by' => Auth::guard('admin')->id(),
            ]);

            if ($installment) {

                $startDate = new Carbon($ism[0]->date);

                $installmentDayEscape = [
                    'Daily' => 1,
                    'Weekly' => 7,
                    'Bi-Monthly' => 15,
                    'Monthly' => 30
                ];

                $dates = [$startDate->format('Y-m-d')];

                for ($i = 1; $i < 1; $i++) {
                    $a = $startDate->addDays($installmentDayEscape['Weekly']);
                    array_push($dates, $a->format('Y-m-d'));
                }

                $depositeAdded = false;

                foreach ($dates as $date) {

                    $insAmount = $ism->sum('total_balance');
                    $status = 1;

                    if (!$depositeAdded) {
                        $insAmount = $ism->sum('collection');
                        $depositeAdded = true;
                        $status = 0;
                    }

                    InstallmentSchedule::create([
                        'showroom_id' => $ism[0]->showroom_id,
                        'installment_id' => $installment->id,
                        'invoice_no' => $sale->invoice_no,
                        'installment_schedule_date' => $date,
                        'installment_schedule_amount' => $insAmount,
                        'status' => $status,
                        'created_by' => Auth::guard('admin')->id()
                    ]);
                }
            }


            // add collection Master Table
            $collection = InstallmentCollection::create([
                'showroom_id' => $ism[0]->showroom_id,
                'installment_id' => $installment->id,
                'customer_id' => $ism[0]->customer_id,
                'product_id' => $product->id,
                'invoice_no' =>  $ism[0]->memo_no,
                'installment_price' => $ism->sum('total_balance'),
                'booking_amount' =>  $ism->sum('collection'),
                'installment_qty' => 1,
                'installment_amount' => $ism->sum('total_balance'),
                'created_by' => Auth::guard('admin')->id(),
            ]);


            // add deposite collection

            $depositeSchedule = InstallmentSchedule::where('installment_id', $installment->id)
                ->orderBy('installment_schedule_date', 'asc')
                ->first();

            $installment_collection_list = InstallmentCollectionList::create([
                'showroom_id' => $ism[0]->showroom_id,
                'installment_id' => $installment->id,
                'installment_schedule_id' => $depositeSchedule->id,
                'installment_collection_id' => $collection->id,
                'invoice_no' => $sale->invoice_no,
                'installment_schedule_date' => $depositeSchedule->installment_schedule_date,
                'installment_collection_date' => $ism[0]->date,
                'installment_schedule_amount' => $ism->sum('collection'),
                'created_by' => Auth::guard('admin')->id(),
            ]);

            echo $a . " done   ";

            $a++;
        }

        return "Done";
    }


    public function n()
    {

        $products = Product::groupBy('name')->select('name')->get();

        foreach ($products as $product) {
            echo $product->name . ',<br>';
        }
    }

    public function o()
    {

        $liftingReturns = PrevPurchaseReturn::all();

        foreach ($liftingReturns as $liftingReturn) {

            $vendor = VendorSetup::where('name', 'like', '%' . $liftingReturn->vendor_name . '%')->first();

            $liftingReturn->update([
                'vendor_id' => $vendor->id
            ]);
        }

        return "Done";
    }


    public function p()
    {

        $liftingReturns = PrevPurchaseReturn::all();

        foreach ($liftingReturns as $liftingReturn) {

            $product = Product::where('name', 'like', '%' . $liftingReturn->product_name . '%')
                ->where('model_no', 'like', '%' . $liftingReturn->model_no . '%')
                ->first();

            if (!$product) {
                continue;
            }

            $liftingReturn->update([
                'product_id' => $product->id
            ]);
        }

        return "Done";
    }

    public function q()
    {

        $liftingReturns = PrevPurchaseReturn::whereNull('product_id')->get();

        foreach ($liftingReturns as $liftingReturn) {

            Product::create([
                'name' => $liftingReturn->product_name,
                'model_no' => $liftingReturn->model_no,
                'price' => $liftingReturn->cp_tk,
            ]);
        }

        return "Done";
    }

    public function r()
    {

        $liftingReturns = PrevPurchaseReturn::get();

        $lReturn = LiftingReturn::create([

            'showroom_id' => $liftingReturns[0]->showroom_id,
            'vendor_id' => $liftingReturns[0]->vendor_id,
            'date' => date('Y-m-d', strtotime($liftingReturns[0]->date)),
            'total_qty' => $liftingReturns->sum('qty'),
            'total_price' => $liftingReturns->sum('total_amount'),
            'status' => 1,
            'created_by' => 1,

        ]);


        foreach ($liftingReturns as $liftingReturn) {

            $liftingReturnProducts = LiftingReturnProduct::create([

                'showroom_id' => $liftingReturn->showroom_id,
                'lifting_return_id' => $lReturn->id,
                'vendor_id' => $liftingReturn->vendor_id,
                'product_id' => $liftingReturn->product_id,
                'product_name' => $liftingReturn->product_name,
                'model_no' => $liftingReturn->model_no,
                'qty' => $liftingReturn->qty,
                'price' => $liftingReturn->total_amount,
                'status' => 1,
                'created_by' => 1,

            ]);
        }

        return "Done";
    }


    public function s()
    {

        $retailSales = RetailSalesReport::get();

        foreach ($retailSales as $retailSale) {

            $date = explode('-', $retailSale->date);

            // if (strlen($date[2]) == 4) {
            //     continue;
            // }

            // dd($date);


            $retailSale->update([
                //     'date' => $date[0] . '-' . $date[1] . '-20' . $date[2],
                'date' => $date[2] . '-' . $date[1] . '-' . $date[0],
            ]);

            // echo $retailSale->id . ' updated  ';
        }

        return "Done";
    }


    public function u()
    {
        $retailSales = RetailSalesReport::where('account_no', '')->groupBy('memo_no')->get();

        // dd($retailSales);

        $i = 1;


        foreach ($retailSales as $retailSale) {

            $account_no = "no_account" . $i;

            $retailSale->update([
                'account_no' => $account_no,
            ]);


            $i++;
        }
    }

    public function v()
    {

        $retailCollections = PreviousRetailCollection::groupBy('account_no')->get();

        foreach ($retailCollections as $retailCollection) {

            $customer = CustomerRegistrationSetup::where('code', $retailCollection->account_no)->count();

            if (!$customer) {

                // insert customer
                CustomerRegistrationSetup::create([

                    'code' => $retailCollection->account_no,
                    'showroom_id' => $retailCollection->showroom_id,
                    'name' => $retailCollection->customer_name,
                    'nick_name' => $retailCollection->customer_name,
                    'nid' => $retailCollection->nid,
                    'age' => 30,
                    'phone_no' => $retailCollection->mobile_no,

                ]);
            }
        }

        return "Done";
    }


    public function w()
    {

        $retailCollections = RetailSalesReport::skip(0)->limit(20000)->get();

        // $retailCollections = RetailSalesReport::where('account_no', '=', 'cash')->skip(0)->limit(100000)->get();

        // dd($retailCollections);

        $i = 1;

        foreach ($retailCollections as $retailCollection) {

            $invoice = '';

            if ($retailCollection->money_receipt_no != '') {
                $invoice = $retailCollection->money_receipt_no;
            } else {
                $invoice = $retailCollection->memo_no;
            }

            // add collection Master Table
            $collection = InstallmentCollection::create([
                'showroom_id' => $retailCollection->showroom_id,
                'installment_id' => 0,
                'customer_id' => $retailCollection->customer_id,
                'product_id' => 0,
                'invoice_no' =>  $invoice,
                'installment_price' => $retailCollection->total_balance,
                'booking_amount' =>  $retailCollection->collection,
                'installment_qty' => 1,
                'installment_amount' => $retailCollection->total_balance,
                'created_by' => Auth::guard('admin')->id(),
            ]);


            $installment_collection_list = InstallmentCollectionList::create([
                'showroom_id' => $retailCollection->showroom_id,
                'installment_id' => 0,
                'installment_schedule_id' => 0,
                'installment_collection_id' => $collection->id,
                'invoice_no' => $invoice,
                'installment_schedule_date' => 0,
                'installment_collection_date' => $retailCollection->date,
                'installment_schedule_amount' => $retailCollection->collection,
                'created_by' => Auth::guard('admin')->id(),
            ]);

            echo $i . '   ';
            $i++;
        }

        return "Done";
    }


    public function x()
    {
        DB::disableQueryLog();

        $customers = CustomerRegistrationSetup::all();
        // $customers = CustomerRegistrationSetup::where('id', 201)->get();

        $i = 1;

        foreach ($customers as $customer) {

            RetailSalesReport::where('account_no', $customer->code)->update([
                'customer_id' => $customer->id,
            ]);

            echo $i . '  ';

            $i++;
        }

        return  "Done";
    }

    public function y()
    {
        DB::disableQueryLog();

        $previousCollection_list = RetailSalesReport::where('money_receipt_no', '!=', '')->skip(0)->limit(50000)->get();

        $i = 1;

        foreach ($previousCollection_list as $previousCollection_l) {

            $installment = Installment::where('invoice_no', $previousCollection_l->money_receipt_no);

            $installment->update([
                'customer_id' => $previousCollection_l->customer_id,
            ]);

            echo $i . '  ';

            $i++;
        }

        return  "Done";
    }


    public function addProductId()
    {
        $rSales = RetailSalesReport::all();


        $i = 1;
        foreach ($rSales as $rSale) {

            $p = Product::where('name', $rSale->product_name)->where('model_no', $rSale->model_no)->first();

            if (!$p) {

                $p = Product::create([
                    'name' => $rSale->product_name,
                    'model_no' => $rSale->model_no,
                    'price' => $rSale->price,
                ]);
            }

            $rSale->update([
                'product_id' => $p->id,
            ]);

            echo $i . '  ';
            $i++;
        }

        return "Done";
    }

    public function addCustomerId()
    {

        $rSales = RetailSalesReport::skip(90000)->limit(20000)->get();

        $i = 1;

        foreach ($rSales as $rSale) {

            $customer = CustomerRegistrationSetup::where('code', $rSale->account_no)->select('id')->first();

            if (!$customer) {
                // insert customer
                // $customer = CustomerRegistrationSetup::create([

                //     'code' => $rSale->account_no,
                //     'showroom_id' => $rSale->showroom_id,
                //     'name' => $rSale->customer_name,
                //     'nick_name' => $rSale->customer_name,
                //     'nid' => $rSale->nid,
                //     'age' => 00,
                //     'phone_no' => $rSale->mobile_no,

                // ]);

                $customer['id'] = 0;
                $customer = (object) $customer;
            }

            $rSale->update([
                'customer_id' => $customer->id,
            ]);

            echo $i . '  ';
            $i++;
        }
    }

    public function fixDate()
    {
        $rSales = CustomerAgreement::all();


        $i = 1;

        foreach ($rSales as $rSale) {

            $date = explode('.', $rSale->date);

            $date = '20' . $date[2] . '-' . $date[1] . '-' . $date[0];

            // echo $date . '    ';

            $rSale->update([
                'date' => $date,
            ]);

            echo $i . '  ';
            $i++;
        }


        return 'Done';
    }


    public function getCustomerCodes()
    {
        $agreements = CustomerAgreement::all();

        $i = 1;

        foreach ($agreements as $agreement) {

            $customer = CustomerRegistrationSetup::where('name', $agreement->customer_name)->where('code',  $agreement->account_no)->first();

            if (!$customer) {

                $customer = CustomerRegistrationSetup::create([

                    'code' => $agreement->account_no,
                    'showroom_id' => 1,
                    'name' => $agreement->customer_name,
                    'nick_name' => $agreement->customer_name,
                    'nid' => $agreement->nid,
                    'age' => 00,
                    'phone_no' => $agreement->mobile_no,
                ]);
            }

            $agreement->update([
                'customer_id' => $customer->id,
            ]);


            echo $i . '  ';
            $i++;
        }


        return "Done";
    }




    public function getEmployeeIDS()
    {
        // $collections = RetailSalesReport::skip(0)->limit(50000)->get();

        $collections = InstallmentCollection::all();

        $i = 1;

        foreach ($collections as $collection) {

            $retailSale = RetailSalesReport::where('money_receipt_no', $collection->invoice_no)->first();

            if (!$retailSale) {
                continue;
            }

            $collection->update([
                'reference_id' => $retailSale->employee_id,
            ]);

            echo $i . '  ';
            $i++;
        }


        return "Done";
    }


    public function convertToLong()
    {
        $sales = RetailSale::where('sale_type', 'Short Installment')->get();

        $i = 1;

        foreach ($sales as $sale) {

            $sale->update([
                'sale_type' => 'Long Installment',
            ]);

            echo $i . '  ';
            $i++;
        }
    }

    public function moveLong()
    {
        $retailSales = RetailSale::where('sale_type', 'Long Installment')->get();

        $i = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $p) {

                $p->update([
                    'hire_price' => $p->cash_price,
                ]);

                echo $i . '  ';
                $i++;
            }
        }
    }


    // udpate collector id in installment from retail sale stuff

    public function updateInstallmentCollectorId()
    {
        $installments = Installment::all();

        $i = 1;

        foreach ($installments as $installment) {

            // get installment sale
            $retailSale = RetailSale::where('invoice_no', $installment->invoice_no)->select('reference_id')->first();


            $installment->update([
                'installment_collector_id' => $retailSale->reference_id,
            ]);

            echo $i . '  ';
            $i++;
        }

        return "Done";
    }





    public function update()
    {
        $dealers = DealerSetup::get();
        $i = 1;
        foreach ($dealers as $dealer) {
            $commission = \App\DealerCommission::create([
                'showroom_id' => 7,
                'dealer_id' => $dealer->id,
                'created_by' => 1,
                'status' => 1,
            ]);

            \App\DealerCommissionList::create([
                'dealer_commission_id' => $commission->id,
                'category_id' => 124,
                'category_name' => 'AC',
                'commission' => 18,
            ]);
            \App\DealerCommissionList::create([
                'dealer_commission_id' => $commission->id,
                'category_id' => 123,
                'category_name' => 'HOME APPLIENCE',
                'commission' => 18,
            ]);
            \App\DealerCommissionList::create([
                'dealer_commission_id' => $commission->id,
                'category_id' => 119,
                'category_name' => 'LED',
                'commission' => 18,
            ]);

            echo $i . '  ';
            $i++;
        }
    }
}
