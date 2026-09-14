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
use App\ProductExchange;
use App\RetailSalesReturnProducts;
use App\ShowroomProjectSetup;

class ProductExchangeController extends Controller
{
    public function index(Request $request)
    {
        $title = "Product Exchange";

        $showroomId = $this->showroomId;
        $project_id = $request->project;

        $projects = ShowroomProjectSetup::where('status', 1)->where('showroom_id', @$this->showroomId)->get();


        $exchanges = [];

        if ($request->ajax()) {

            $exchanges = [];

            if (!empty($project_id)) {
                $exchanges = ProductExchange::with(['customer'])
                    ->where('showroom_id', @$showroomId)
                    ->where('project_id', $project_id)
                    ->orderBy('id', 'desc');
            }

            return DataTables::of($exchanges)
                ->addIndexColumn()
                ->addColumn('action', function ($exchange) {
                    $actionBtn = \App\Link::action($exchange->id);
                    return $actionBtn;
                })
                ->addColumn('customer_code', function ($exchange) {
                    return @$exchange->customer->code;
                })
                ->addColumn('date', function ($exchange) {
                    return @date('d-m-Y', strtotime($exchange->date));
                })
                ->addColumn('customer_name', function ($exchange) {
                    return @$exchange->customer->name;
                })
                ->addColumn('invoice_no', function ($exchange) {
                    return @$exchange->sale->invoice_no;
                })
                ->setRowId(function ($exchange) {
                    return 'row_' . $exchange->id;
                })
                ->escapeColumns([])
                ->toJson();
        }


        return view('admin.productExchange.index')->with(compact('title', 'exchanges', 'showroomId', 'projects', 'project_id'));
    }

    public function add(Request $request)
    {
        $title = "Exchange Product";
        $formLink = "productExchange.save";
        $buttonName = "Save";

        $project_id = $request->project;

        $products = Product::where('status', 1)->orderBy('name', 'asc')->get();

        $retailSaleCustomerIds = RetailSale::select(['customer_id', 'status'])->where('status', 1)->groupBy('customer_id')->get()->pluck('customer_id');
        $customers = CustomerRegistration::whereIn('id', $retailSaleCustomerIds)->where('code', '!=', 'cash')->where('project_id', $project_id)->get();
        $projects = ShowroomProjectSetup::where('status', 1)->where('showroom_id', @$this->showroomId)->get();

        return view('admin.productExchange.add')->with(compact('project_id', 'projects', 'title', 'formLink', 'buttonName', 'customers', 'products'));
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
        

        $sale = RetailSale::find($request->saleId);


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
        $returns = [];
        foreach ($request->ids as $id) {

            $sales = RetailSales::with('sale')->where('id', $id)->first();

            $return = RetailSalesReturnProducts::create([
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

            $returns[] = $return->id;
        }

        

        ProductExchange::create([
            'showroom_id' => $this->showroomId,
            'project_id' => $request->project_id,
            'customer_id' => $request->customerId,
            'date' => date('Y-m-d', strtotime($request->date)),
            'sale_id' => $sale->id,
            'return_id' => $retailSalesReturn->id,
        ]);


        foreach ($request->productId as $key => $pid) {

            $product = Product::where('id', $pid)->first();

            $salePrice = 0;
            $indMrp = 0;
            $indHigh = 0;
            $cashPrice = $request->cashPrice[$key];
            $mrpPrice = $cashPrice + ($cashPrice * 8) / 100;
            $hirePrice = $mrpPrice + ($mrpPrice * 12) / 100;

            if ($request->saleType == 'Cash') {
                $salePrice = $cashPrice;
            } elseif ($request->saleType == 'Short Installment') {
                $salePrice = $mrpPrice;
                $indMrp = $mrpPrice;
            } elseif ($request->saleType == 'Long Installment') {
                $salePrice = $hirePrice;
                $indHigh = $hirePrice;
            } else {
                $salePrice = 0;
            }

            $retailSales = RetailSales::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'product_model' => $product->model_no,
                'cash_price' => $cashPrice,
                'sales_price' => $salePrice,
                'mrp_price' => $indMrp,
                'hire_price' => $indHigh,
                'discount' => $request->discount[$key],
                'gift_voucher' => $request->giftVoucher[$key],
                'exchange_crt' => $request->exchangeCrt[$key],
                'created_by' => $this->userId,
            ]);
        }


        return redirect(route('productExchange.index', ['project' => $request->project_id]));
    }

    public function edit($id)
    {
        $title = "Edit Retails Sales Return";
        $formLink = "retailSales.return.update";
        $buttonName = "Update";

        $productExchange = ProductExchange::find($id);


        $retailSalesReturn = RetailSalesReturn::where('id', $productExchange->return_id)->with(['products.product'])->first();

        
        // dd($retailSalesReturn);

        $exchangeProducts = RetailSale::with('products')->where('id', $productExchange->sale_id)->first();

        $retailSaleCustomerIds = RetailSale::select(['customer_id', 'status'])
            ->where('dealer_id', $retailSalesReturn->project_id)
            ->where('status', 1)
            ->groupBy('customer_id')
            ->get()
            ->pluck('customer_id');
        $customers = CustomerRegistration::whereIn('id', $retailSaleCustomerIds)->get();
        $projects = ShowroomProjectSetup::where('status', 1)->where('showroom_id', @$this->showroomId)->get();
        $products = Product::where('status', 1)->orderBy('name', 'asc')->get();

        return view('admin.productExchange.edit')->with(compact('projects', 'title', 'formLink', 'buttonName', 'customers', 'retailSalesReturn', 'productExchange', 'exchangeProducts', 'products'));
    }


    public function update(Request $request)
    {

        if (!$request->has('ids')) {
            return back()->with('fail_msg', 'Please select minimum one products');
        }

        $sale = RetailSale::find($request->saleId);
        RetailSales::where('sale_id', $sale->id)->delete();


        foreach ($request->productId as $key => $pid) {

            $product = Product::where('id', $pid)->first();

            $salePrice = 0;
            $indMrp = 0;
            $indHigh = 0;
            $cashPrice = $request->cashPrice[$key];
            $mrpPrice = $cashPrice + ($cashPrice * 8) / 100;
            $hirePrice = $mrpPrice + ($mrpPrice * 12) / 100;

            if ($request->saleType == 'Cash') {
                $salePrice = $cashPrice;
            } elseif ($request->saleType == 'Short Installment') {
                $salePrice = $mrpPrice;
                $indMrp = $mrpPrice;
            } elseif ($request->saleType == 'Long Installment') {
                $salePrice = $hirePrice;
                $indHigh = $hirePrice;
            } else {
                $salePrice = 0;
            }

            $retailSale = RetailSales::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'product_model' => $product->model_no,
                'cash_price' => $cashPrice,
                'sales_price' => $salePrice,
                'mrp_price' => $indMrp,
                'hire_price' => $indHigh,
                'discount' => $request->discount[$key],
                'gift_voucher' => $request->giftVoucher[$key],
                'exchange_crt' => $request->exchangeCrt[$key],
                'created_by' => $this->userId,
            ]);
        }

        return redirect(route('productExchange.index', ['project' => $request->project_id]));
    }


    public function getProductInfo(Request $request)
    {
        $product = Product::find($request->id);

        if ($request->ajax()) {
            return response()->json([
                'product' => $product,
            ]);
        }
    }
}
