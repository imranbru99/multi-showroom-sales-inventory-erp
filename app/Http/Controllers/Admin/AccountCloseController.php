<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\RetailSale;
use App\StaffSetup;
use App\Installment;
use App\InstallmentCollection;
use App\InstallmentCollectionList;
use App\CustomerRegistrationSetup;
use App\CloseAccount;
use App\RetailSalesReturn;
use App\RetailSalesReturnProducts;
use App\ShowroomProjectSetup;
use Yajra\DataTables\DataTables;
use PDF;

class AccountCloseController extends Controller
{

    public function index(Request $request)
    {
        $title = "Close Accounts";

        $project_id = $request->project;

        $showroomId = $this->showroomId;
        $projects = ShowroomProjectSetup::where('status', 1)->where('showroom_id', @$this->showroomId)->get();

        // $customers = CloseAccount::with('customer', 'sale.seller', 'user', 'agreement')->orderBy('created_at', 'desc')->get();
        if ($request->ajax()) {

            $customers = [];
            if (!empty($request->project)) {
                $customers = CloseAccount::with('customer', 'sale.seller', 'user')
                    ->where('showroom_id', $showroomId)
                    ->where('project_id', $request->project)
                    ->orderBy('created_at', 'desc');
            }

            return DataTables::of($customers)
                ->addIndexColumn()
                ->addColumn('action', function ($customer) {
                    $actionBtn = \App\Link::action($customer->id);
                    return $actionBtn;
                })
                ->addColumn('customer_code', function ($customer) {
                    return @$customer->customer_code;
                })
                ->addColumn('name', function ($customer) {
                    return @$customer->customer->name;
                })
                ->addColumn('invoice_amount', function ($customer) {
                    return @$customer->invoice_amount;
                })
                ->addColumn('interest_receive', function ($customer) {
                    return @$customer->interest_receive;
                })
                ->addColumn('collection', function ($customer) {
                    return @$customer->collection_amount;
                })
                ->addColumn('discount_amount', function ($customer) {
                    return @$customer->discount_amount;
                })
                ->addColumn('missing_amount', function ($customer) {
                    return @$customer->missing_amount;
                })
                ->addColumn('balance', function ($customer) {
                    if ($customer->is_settle == 0) {
                        $balance = $customer->invoice_amount + $customer->late_fee - ($customer->collection_amount + $customer->discount_amount + $customer->missing_amount);
                    } else {
                        $balance = $customer->invoice_amount + $customer->interest_receive - ($customer->collection_amount + $customer->discount_amount + $customer->missing_amount);
                    }
                    return @$balance;
                })
                ->addColumn('agreement_amount', function ($customer) {
                    return @$customer->agreement->agreement_amount;
                })
                ->addColumn('reference', function ($customer) {
                    return @$customer->sale->seller->name;
                })
                ->addColumn('user', function ($customer) {
                    return @$customer->user->name;
                })
                ->setRowClass(function ($customer) {
                    return '.row_' . $customer->id;
                })
                ->escapeColumns([])
                ->toJson();
        }
        return view('admin.closeAccount.index')->with(compact('title', 'showroomId', 'projects', 'project_id'));
    }

    public function add(Request $request)
    {
        $title = 'Close Account';
        $formLink = 'closeAccount.save';
        $buttonName = 'Close Account';

        $project_id = $request->project;
        $search = $request->q;

        $closeAccounts = CloseAccount::where('showroom_id', $this->showroomId)
            ->where('project_id', $project_id)
            ->get()
            ->pluck('customer_id')
            ->toArray();

        $cashSales = RetailSale::where('sale_type', 'Cash')
            ->with('products', 'products.product', 'seller', 'customer')
            ->where('showroom_id', $this->showroomId)
            ->where('dealer_id', $project_id)
            ->whereNotIn('customer_id', $closeAccounts)
            ->where('invoice_no', 'like', '%' . $search . '%')
            ->paginate(10);

        return view('admin.closeAccount.add')->with(compact('title', 'formLink', 'buttonName', 'project_id', 'cashSales', 'search'));
    }

    public function save(Request $request)
    {
        
        $sale = RetailSale::with('products')->where('id', '=', $request->sale_id)->first();
        $returns = RetailSalesReturnProducts::with('return')
            ->whereHas('return', function ($q) use ($sale) {
                $q->where('customer_id', $sale->customer_id);
            })
            ->sum('sales_price');

        //Sales
        $total_sales = $sale->products->sum('sales_price');
        $total_discount = $sale->products->sum('discount');
        $total_voucher = $sale->products->sum('gift_voucher');
        $total_excrt = $sale->products->sum('exchange_crt');
        $actual_sales = $total_sales - ($total_discount + $total_voucher + $total_excrt);
        $actual_sales = $actual_sales - $returns;

        CloseAccount::create([
            'showroom_id' => $this->showroomId,
            'project_id' => $request->project_id,
            'customer_id' => $request->cus_id,
            'customer_code' => $request->customer_code,
            'sale_id' => $request->sale_id,
            'invoice_no' => $request->invoice_no,
            'invoice_amount' => $actual_sales,
            'collection_amount' => $request->collection,
            'close_date' => date('Y-m-d', strtotime($request->date)),
            'cam_due' => !empty($request->cam_due) ? $request->cam_due : 0,
            'interest_receive' => !empty($request->interest_receive) ? $request->interest_receive : 0,
            'discount_amount' => !empty($request->discount_amount) ? $request->discount_amount : 0,
            'is_schedule' => $request->schedule_value,
            'is_settle' => $request->settle_value,
            'missing_amount' => $request->missing_amount,
            'late_days' => $request->late_days,
            'late_fee' => $request->late_fee,
            'employee_id' => $request->employee,
            'remarks' => $request->remarks,
            'created_by' => auth()->user()->id,
        ]);

        $customer = CustomerRegistrationSetup::where('id', $request->cus_id)->first();

        $customer->update([
            'is_close' => 1,
        ]);

        return redirect(route('closeAccount.index', ['project' => $request->project_id]))->with('msg', 'Account Closed Successfully');
    }

    public function cashSave(Request $request)
    {
        $sale = RetailSale::with('products')->where('id', $request->id)->first();
        $customer = CustomerRegistrationSetup::where('id', $sale->customer_id)->first();

        $returns = RetailSalesReturnProducts::with('return')
            ->whereHas('return', function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
            })
            ->sum('sales_price');

        $total_sales = $sale->products->sum('sales_price');
        $total_discount = $sale->products->sum('discount');
        $total_voucher = $sale->products->sum('gift_voucher');
        $total_excrt = $sale->products->sum('exchange_crt');
        $actual_sales = $total_sales - ($total_discount + $total_voucher + $total_excrt);
        $actual_sales = $actual_sales - $returns;

        //Collection
        $collectionIds = InstallmentCollection::where('customer_id', $customer->id)->select('id')->get()->pluck('id');
        $collections = 0;

        $collectionList = [];
        if ($collectionIds) {
            $collectionList = InstallmentCollectionList::whereIn('installment_collection_id', $collectionIds)
                ->orderBy('installment_collection_date', 'asc')
                ->get();

            $collections = 0;
            foreach ($collectionList as $collection) {
                $collections += floatval($collection->installment_schedule_amount);
            }
        }

        CloseAccount::create([
            'showroom_id' => $this->showroomId,
            'project_id' => $sale->dealer_id,
            'customer_id' => $customer->id,
            'customer_code' => $customer->code,
            'sale_id' => $sale->id,
            'invoice_no' => $sale->invoice_no,
            'invoice_amount' => $actual_sales,
            'collection_amount' => $collections,
            'close_date' => date('Y-m-d', strtotime($sale->sale_date)),
            'cam_due' => 0,
            'interest_receive' => 0,
            'discount_amount' => 0,
            'is_schedule' => 0,
            'is_settle' => 0,
            'missing_amount' => 0,
            'late_days' => 0,
            'late_fee' => 0,
            'remarks' => 'Cash Account',
            'created_by' => auth()->user()->id,
        ]);


        $customer->update([
            'is_close' => 1,
        ]);
    }

    public function edit($id)
    {
        $title = "Edit Close Accounts";
        $formLink = 'closeAccount.update';
        $buttonName = 'Update Close Account';

        $customer = CloseAccount::with('customer', 'sale.seller', 'user')->where('id', $id)->first();
        $staffs = StaffSetup::where('showroom_id', $this->showroomId)->get();

        return view('admin.closeAccount.edit')->with(compact('title', 'formLink', 'buttonName', 'customer', 'staffs'));
    }

    public function update(Request $request)
    {
        $customer = CloseAccount::where('id', $request->id)->first();

        $sale = RetailSale::with('products')->where('id', $customer->sale_id)->first();

        $returns = RetailSalesReturnProducts::with('return')
            ->whereHas('return', function ($q) use ($customer) {
                $q->where('customer_id', $customer->id);
            })
            ->sum('sales_price');

        $total_sales = $sale->products->sum('sales_price');
        $total_discount = $sale->products->sum('discount');
        $total_voucher = $sale->products->sum('gift_voucher');
        $total_excrt = $sale->products->sum('exchange_crt');
        $actual_sales = $total_sales - ($total_discount + $total_voucher + $total_excrt);
        $actual_sales = $actual_sales - $returns;

        $customer->update([
            'invoice_amount' => $actual_sales,
            'customer_code' => $request->customer_code,
            'close_date' => date('Y-m-d', strtotime($request->date)),
            'collection_amount' => $request->collection,
            'cam_due' => !empty($request->cam_due) ? $request->cam_due : 0,
            'interest_receive' => !empty($request->interest_receive) ? $request->interest_receive : 0,
            'discount_amount' => !empty($request->discount_amount) ? $request->discount_amount : 0,
            'is_schedule' => $request->schedule_value,
            'is_settle' => $request->settle_value,
            'missing_amount' => $request->missing_amount,
            'late_days' => $request->late_days,
            'late_fee' => $request->late_fee,
            'employee_id' => $request->employee,
            'remarks' => $request->remarks,
            'created_by' => auth()->user()->id,
        ]);

        return redirect(route('closeAccount.index', ['project' => $customer->project_id]))->with('msg', 'Account Closed Successfully');
    }

    public function accountInfo(Request $request)
    {

        $customer = CustomerRegistrationSetup::where('code', $request->account_no)
            ->where('showroom_id', $this->showroomId)
            ->where('project_id', $request->project)
            ->whereNull('is_close')
            ->first();


        $staffs = StaffSetup::where('showroom_id', $this->showroomId)->get();

        $data = [];
        if ($customer) {
            $invoice_details = RetailSale::where('customer_id', $customer->id)
                ->with('products', 'products.product')
                ->first();

            $returns = RetailSalesReturnProducts::with('return')
                ->whereHas('return', function ($q) use ($customer) {
                    $q->where('customer_id', $customer->id);
                })
                ->sum('sales_price');

            //Sales
            $total_sales = $invoice_details->products->sum('sales_price');
            $total_discount = $invoice_details->products->sum('discount');
            $total_voucher = $invoice_details->products->sum('gift_voucher');
            $total_excrt = $invoice_details->products->sum('exchange_crt');
            $actual_sales = $total_sales - ($total_discount + $total_voucher + $total_excrt);
            $actual_sales = $actual_sales - $returns;

            //Collection
            $collectionIds = InstallmentCollection::where('customer_id', $customer->id)->select('id')->get()->pluck('id');
            $collections = 0;
            $invoice_date = date('d-m-Y', strtotime($invoice_details->sale_date));
            $first_date = date('d-m-Y', strtotime($invoice_details->sale_date));
            $last_date = date('d-m-Y', strtotime($invoice_details->sale_date));
            $collectionList = [];
            if ($collectionIds) {
                $collectionList = InstallmentCollectionList::whereIn('installment_collection_id', $collectionIds)
                    ->orderBy('installment_collection_date', 'asc')
                    ->get();

                $collections = 0;
                foreach ($collectionList as $collection) {
                    $collections += floatval($collection->installment_schedule_amount);
                }


                //first and last collection date
                if ($collections > 0) {
                    $count = count($collectionList);
                    $countInstallment = $count - 1;
                    $first_date = date('d-m-Y', strtotime($collectionList[0]->installment_collection_date));
                    $last_date = date('d-m-Y', strtotime($collectionList[$countInstallment]->installment_collection_date));
                }
            }

            $outstanding = $actual_sales - $collections;





            $start_date = $invoice_details->sale_date;
            $last_col_date = Carbon::parse(now());

            $late_days = $last_col_date->diffInDays($start_date);

            $typeDays = 0;
            if ($invoice_details->sale_type == 'Long Installment') {
                $typeDays = 365;
            } else {
                $typeDays = 183;
            }
            $duration = $late_days - $typeDays;

            if ($duration < 0) {
                $duration = 0;
            }

            $late_fee = 0;
            if ($duration > 0) {
                $total = $actual_sales * 0.12;
                $fee = $total / 365;
                if ($invoice_details->sale_type == 'Long Installment') {
                    $late_fee = round($fee * $duration);
                } else {
                    $late_fee = round($fee * ($duration + 183));
                }
            }

            $data = [
                'customer' => $customer,
                'invoice_details' => $invoice_details,
                'sales' => $actual_sales,
                'collections' => $collections,
                'outstanding' => $outstanding,
                'late_days' => $duration,
                'late_fee' => $late_fee,
                'first_date' => $first_date,
                'last_date' => $last_date,
                'invoice_date' => $invoice_date,
                'installments' => $collectionList,
                'staffs' => $staffs,
            ];
        }


        return $data;
    }

    public function lateDays(Request $request)
    {

        $invoice_details = RetailSale::where('customer_id', $request->customer_id)->with('products', 'products.product')->first();


        if ($request->schedule == 1) {
            $intallment = Installment::with('schedule')
                ->where('invoice_no', $invoice_details->invoice_no)
                ->where('customer_id', $invoice_details->customer_id)
                ->first();
            if ($intallment) {
                $last_date = date('Y-m-d');
                $last_date = $intallment->schedule[0]->installment_schedule_date;

                $todayDate = now();

                $late_days = $todayDate->diffInDays($last_date);

                $duration = $late_days - 365;

                $data = [
                    'late_days' => $duration,
                ];
            } else {
                $start_date = $invoice_details->sale_date;
                $todayDate = now();

                $late_days = $todayDate->diffInDays($start_date);
                $duration = $late_days - 365;

                $data = [
                    'late_days' => $duration,
                ];
            }
        } elseif ($request->schedule == 0) {

            $start_date = $invoice_details->sale_date;
            $todayDate = now();

            $late_days = $todayDate->diffInDays($start_date);
            $duration = $late_days - 365;

            $data = [
                'late_days' => $duration,
            ];
        }

        return $data;
    }

    public function piclateDays(Request $request)
    {
        $invoice = RetailSale::where('customer_id', $request->customer)
            ->with('products', 'products.product')
            ->first();

        $returns = RetailSalesReturnProducts::with('return')
            ->whereHas('return', function ($q) use ($invoice) {
                $q->where('customer_id', $invoice->customer_id);
            })
            ->sum('sales_price');

        //Sales
        $total_sales = $invoice->products->sum('sales_price');
        $total_discount = $invoice->products->sum('discount');
        $total_voucher = $invoice->products->sum('gift_voucher');
        $total_excrt = $invoice->products->sum('exchange_crt');
        $actual_sales = $total_sales - ($total_discount + $total_voucher + $total_excrt);
        $actual_sales = $actual_sales - $returns;

        $last_date = $invoice->sale_date;
        $reDate = Carbon::parse(date('Y-m-d', strtotime($request->date)));

        $late_days = $reDate->diffInDays($last_date);

        $duration = $late_days - 365;
        if ($invoice->sale_type == 'Long Installment') {
            $duration = $late_days - 365;
        } else {
            $duration = $late_days - 183;
        }

        if ($duration < 0) {
            $duration = 0;
        }

        $late_fee = 0;
        if ($duration > 0) {
            $total = $actual_sales * 0.12;
            $fee = $total / 365;
            if ($invoice->sale_type == 'Long Installment') {
                $late_fee = round($fee * $duration);
            } else {
                $late_fee = round($fee * ($duration + 183));
            }
        }

        $data = [
            'duration' => $duration,
            'late_fee' => $late_fee,
        ];

        return $data;
    }

    public function print($id)
    {
        $title = "Close Account";

        $customer = CloseAccount::with('customer', 'sale.seller', 'user', 'agreement')->where('id', $id)->first();

        $pdf = PDF::loadView('admin.closeAccount.print', ['title' => $title, 'customer' => $customer]);

        return $pdf->stream('close_account.pdf');
    }
}
