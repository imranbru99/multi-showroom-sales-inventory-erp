<?php

namespace App\Http\Controllers\Admin;

use App\Product;
use App\RetailSale;
use App\RetailSales;
use App\RetailSalesReturn;
use Illuminate\Http\Request;
use App\CustomerRegistration;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\RetailSalesReturnProducts;
use App\ShowroomProjectSetup;

class RetailSalesReturnController extends Controller
{
    public static function getInvoiceNo()
    {
        $maxId = RetailSalesReturn::max('id');
        $invoiceNoPrefix =  date('ymd') . "-";
        // $invoiceNoPrefix = "inv-" . date('ymd') . "-";

        if ($maxId) {
            $invoiceNo = $invoiceNoPrefix . str_pad($maxId + 1, 0, '0', STR_PAD_LEFT);
        } else {
            $invoiceNo = $invoiceNoPrefix . "1";
        }

        return $invoiceNo;
    }


    public function index(Request $request)
    {
        $title = "Retails Sales Return";

        $showroomId = $this->showroomId;
        $project_id = $request->project;

        $projects = ShowroomProjectSetup::where('status', 1)->where('showroom_id', @$this->showroomId)->get();


        $allRetailSalesReturn = [];




        if ($request->ajax()) {

            $allRetailSalesReturn = [];

            if (!empty($project_id)) {
                $allRetailSalesReturn = RetailSalesReturn::with(['customer', 'products.product'])
                    ->where('showroom_id', @$showroomId)
                    ->where('project_id', $project_id)
                    ->orderBy('id', 'desc');
            }

            return DataTables::of($allRetailSalesReturn)
                ->addIndexColumn()
                ->addColumn('action', function ($retailSale) {
                    $actionBtn = \App\Link::action($retailSale->id);
                    return $actionBtn;
                })
                ->addColumn('customer_code', function ($retailSale) {
                    return @$retailSale->customer->code;
                })
                ->addColumn('date', function ($retailSale) {
                    return @date('d-m-Y', strtotime($retailSale->date));
                })
                ->addColumn('customer_name', function ($retailSale) {
                    return @$retailSale->customer->name;
                })
                ->addColumn('sale_price', function ($retailSale) {
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
                ->setRowId(function ($retailSale) {
                    return 'row_' . $retailSale->id;
                })
                ->escapeColumns([])
                ->toJson();
        }


        return view('admin.retailSalesReturn.index')->with(compact('title', 'allRetailSalesReturn', 'showroomId', 'projects', 'project_id'));
    }

    public function add(Request $request)
    {
        $title = "Add Retails Sales Return";
        $formLink = "retailSales.return.save";
        $buttonName = "Save";

        $project_id = $request->project;

        $retailSaleCustomerIds = RetailSale::select(['customer_id', 'status'])->where('status', 1)->groupBy('customer_id')->get()->pluck('customer_id');
        $customers = CustomerRegistration::whereIn('id', $retailSaleCustomerIds)->where('code', '!=', 'cash')->get();
        $invoiceNo = self::getInvoiceNo();
        $projects = ShowroomProjectSetup::where('status', 1)->where('showroom_id', @$this->showroomId)->get();

        return view('admin.retailSalesReturn.add')->with(compact('invoiceNo', 'project_id', 'projects', 'title', 'formLink', 'buttonName', 'customers'));
    }

    public function getCustomerProducts(Request $request)
    {
        $customerId = $request->customerId;

        // fetch this customer returned products
        $returns = RetailSalesReturn::where('customer_id', $customerId)->select(['id'])->get()->pluck('id');
        $returnProducts = RetailSalesReturnProducts::whereIn('return_id', $returns)->select(['sales_id'])->get()->pluck('sales_id')->toArray();

        // // fetch this customer product which is not returned.

        $retailSaleIds = RetailSale::where('customer_id', $customerId)->select(['id'])->get()->pluck('id')->toArray();

        // dd($retailSaleIds);
        // // dd($returnProducts);

        $retailSales = RetailSales::whereIn('sale_id', $retailSaleIds)->whereNotIn('id', $returnProducts)->with(['sale', 'product'])->get();

        return $retailSales;
    }

    public function save(Request $request)
    {
        if (!$request->has('ids')) {
            return back()->with('fail_msg', 'Please select minimum one products');
        }

        // add to sale return table 

        $retailSalesReturn = RetailSalesReturn::create([
            'date' => date('Y-m-d', strtotime($request->date)),
            'customer_id' => $request->customerId,
            'showroom_id' => $this->showroomId,
            'project_id' => $request->project_id,
            'invoice_no' => $request->invoice_no,
        ]);

        // add to sale return product table


        // dd($request->ids);

        foreach ($request->ids as $id) {

            $sales = RetailSales::with('sale')->where('id', $id)->first();

            RetailSalesReturnProducts::create([
                'store_id' => $sales->store_id,
                'return_id' => $retailSalesReturn->id,
                'sales_id' => $sales->id,
                'product_id' => $sales->product_id,
                'product_serial' => $sales->product_serial,
                'cash_price' => $sales->cash_price,
                'sales_price' => $sales->sales_price,
                'mrp_price' => $sales->mrp_price,
                'hire_price' => $sales->hire_price,
                'discount' => $sales->discount,
                'gift_voucher' => $sales->gift_voucher,
                'exchange_crt' => $sales->exchange_crt,
                'mobile_gift' => $sales->mobile_gift,
                'gift_name' => $sales->gift_name,
                'sales_by' => $sales->sale->reference_id,
            ]);
        }


        return redirect(route('retailSales.return.index', ['project' => $request->project_id]));
    }

    public function edit($returnId)
    {
        $title = "Edit Retails Sales Return";
        $formLink = "retailSales.return.update";
        $buttonName = "Update";

        $retailSalesReturn = RetailSalesReturn::where('id', $returnId)->with(['products.product'])->first();

        // dd($retailSalesReturns);

        $retailSaleCustomerIds = RetailSale::select(['customer_id', 'status'])
            ->where('dealer_id', $retailSalesReturn->project_id)
            ->where('status', 1)
            ->groupBy('customer_id')
            ->get()
            ->pluck('customer_id');
        $customers = CustomerRegistration::whereIn('id', $retailSaleCustomerIds)->get();
        $invoiceNo = self::getInvoiceNo();
        $projects = ShowroomProjectSetup::where('status', 1)->where('showroom_id', @$this->showroomId)->get();

        return view('admin.retailSalesReturn.edit')->with(compact('invoiceNo', 'projects', 'title', 'formLink', 'buttonName', 'customers', 'retailSalesReturn'));
    }


    public function update(Request $request)
    {

        if (!$request->has('ids')) {
            return back()->with('fail_msg', 'Please select minimum one products');
        }

        // update sale return table 

        $retailSalesReturn = RetailSalesReturn::findOrFail($request->return_id);

        $retailSalesReturn->update([
            'date' => date('Y-m-d', strtotime($request->date)),
            'customer_id' => $request->customerId,
            'showroom_id' => $this->showroomId,
            'project_id' => $request->project_id,
            'invoice_no' => $request->invoice_no,
        ]);


        // delete Returned products from

        RetailSalesReturnProducts::where('return_id', $retailSalesReturn->id)->delete();

        // update sale return product table

        foreach ($request->ids as $id) {

            $sales = RetailSales::with('sale')->where('id', $id)->first();

            RetailSalesReturnProducts::create([
                'return_id' => $retailSalesReturn->id,
                'sales_id' => $sales->id,
                'product_id' => $sales->product_id,
                'product_serial' => $sales->product_serial,
                'cash_price' => $sales->cash_price,
                'sales_price' => $sales->sales_price,
                'mrp_price' => $sales->mrp_price,
                'hire_price' => $sales->hire_price,
                'discount' => $sales->discount,
                'gift_voucher' => $sales->gift_voucher,
                'exchange_crt' => $sales->exchange_crt,
                'mobile_gift' => $sales->mobile_gift,
                'gift_name' => $sales->gift_name,
                'sales_by' => $sales->sale->reference_id,
            ]);
        }

        return redirect(route('retailSales.return.index', ['project' => $request->project_id]));
    }


    public function delete(Request $request)
    {
        RetailSalesReturn::where('id', $request->retailSalesReturnId)->delete();

        RetailSalesReturnProducts::where('return_id', $request->retailSalesReturnId)->delete();
    }
}
