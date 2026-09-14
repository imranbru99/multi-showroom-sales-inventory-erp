<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lifting;
use App\LiftingProduct;
use App\LiftingReturn;
use App\LiftingReturnProduct;
use App\VendorSetup;
use App\Product;
use DB;
use PDF;
use MPDF;

class LiftingReturnController extends Controller
{

    public function index()
    {
        $title = "Lifting Return";

        if ($this->userRole == 1) {
            $liftingReturns = LiftingReturn::select('tbl_lifting_returns.*', 'tbl_vendors.name as vendorName')
                ->leftJoin('tbl_vendors', 'tbl_vendors.id', '=', 'tbl_lifting_returns.vendor_id')
                //                    ->where('tbl_lifting_returns.showroom_id', $this->showroomId)
                ->orderBy('tbl_lifting_returns.id', 'dsc')
                // ->orderBy('tbl_lifting_returns.purchase_by','asc')
                // ->orderBy('tbl_vendors.name','asc')
                ->get();
        } else {
            $liftingReturns = LiftingReturn::select('tbl_lifting_returns.*', 'tbl_vendors.name as vendorName')
                ->leftJoin('tbl_vendors', 'tbl_vendors.id', '=', 'tbl_lifting_returns.vendor_id')
                ->where('tbl_lifting_returns.company_id', $this->company)
                ->where('tbl_lifting_returns.showroom_id', $this->showroomId)
                ->orderBy('tbl_lifting_returns.id', 'dsc')
                // ->orderBy('tbl_lifting_returns.purchase_by','asc')
                // ->orderBy('tbl_vendors.name','asc')
                ->get();
        }

        return view('admin.liftingReturn.index')->with(compact('title', 'liftingReturns'));
    }

    public function add()
    {
        $title = "Add Lifting Returns";
        $formLink = "liftingReturn.save";
        $buttonName = "Save";

        $storeAndShowrooms = DB::table('view_store_and_showroom')
            //                ->where('type', 'showroom')
            ->where('companyId', $this->company)
            //                ->where('showroomId', $this->showroomId)
            //                ->where('id', $this->showroomId)
            //                ->orWhere('type', 'store')
            //                ->orderBy('type', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $vendors = VendorSetup::where('company_id', $this->company)
            //                ->where('showroom_id', $this->showroomId)
            ->where('status', '1')
            ->orderBy('name', 'asc')
            ->get();

        // $products = Product::where('status', '1')
        //     ->where('company_id', $this->company)
        //     //                ->where('tbl_products.showroom_id', $this->showroomId)
        //     ->orderBy('name', 'asc')
        //     ->get();

        $products = [];

        $companyProductTypes = json_decode($this->companyInfo->product_types);

        $productTypes = collect([
            'warranty_product' => 'Warranty Product',
            'consumer_product' => 'Consumer Product',
            'spare_parts' => 'Spare Parts',
            'raw_product' => 'Raw Product'
        ]);

        $productTypes = $productTypes->filter(function($value, $key) use ($companyProductTypes){
            return in_array($key, $companyProductTypes);
        });

        return view('admin.liftingReturn.add')->with(compact('title', 'formLink', 'buttonName', 'storeAndShowrooms', 'vendors', 'products', 'productTypes'));
    }

    public function save(Request $request)
    {

        $storeOrShowrooms = explode(',', $request->storeOrShowroom);
        $storeOrShowroomId = $storeOrShowrooms[0];
        $storeOrShowroomType = $storeOrShowrooms[1];

        $date = date('Y-m-d', strtotime($request->liftingReturnDate));

        $type = $request->productType;

        //dd($request->all());

        if ($type == 'warranty_product' || $type == 'spare_parts') {
            if ($request->totalQty == 0) {
                $message = "Please return some lifting Products";
                return redirect(route('liftingReturn.add'))->with('fail_msg', $message)->withInput();
            }

            $liftingReturn = LiftingReturn::create([
                'company_id' => $this->company,
                'product_type' => $type,
                'vendor_id' => $request->supplier,
                'showroom_id' => $this->showroomId,
                'store_or_showroom_type' => $storeOrShowroomType,
                'store_or_showroom_id' => $storeOrShowroomId,
                'product_id' => $request->product,
                'serial_no' => $request->serialNo,
                'date' => $date,
                'total_qty' => $request->totalQty,
                'total_price' => $request->totalPrice,
                'total_mrp_price' => $request->totalMrpPrice,
                'total_haire_price' => $request->productHigherPrice,
                'remarks' => $request->remarks,
                'created_by' => $this->userId
            ]);

            $countProduct = count($request->productId);

            if ($request->productId) {
                $postData = [];
                for ($i = 0; $i < $countProduct; $i++) {

                    $product = Lifting::where('id', $request->liftingId[$i])->first();

                    $postData[] = [
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'lifting_return_id' => $liftingReturn->id,
                        'lifting_id' => $request->liftingId[$i],
                        'lifting_product_id' => $request->liftingProductId[$i],
                        'vendor_id' => $request->supplier,
                        'store_or_showroom_type' => $storeOrShowroomType,
                        'store_or_showroom_id' => $storeOrShowroomId,
                        'product_id' => $request->productId[$i],
                        'product_name' => $request->productName[$i],
                        'model_no' => $request->productModelNo[$i],
                        'serial_no' => $request->productSerialNo[$i],
                        //                        'color' => $request->productColor[$i],
                        'qty' => $request->productQty[$i],
                        'price' => $request->productPrice[$i],
                        'mrp_price' => $request->productMrpPrice[$i]
                        // 'haire_price' => $request->productHigherPrice[$i],
                    ];
                }
                LiftingReturnProduct::insert($postData);
            }
        } else {

            if ($request->stotalQty == 0) {
                $message = "Please return some lifting Products";
                return redirect(route('liftingReturn.add'))->with('fail_msg', $message)->withInput();
            }

            $liftingReturn = LiftingReturn::create([
                'company_id' => $this->company,
                'product_type' => $type,
                'vendor_id' => $request->supplier,
                'showroom_id' => $this->showroomId,
                'store_or_showroom_type' => $storeOrShowroomType,
                'store_or_showroom_id' => $storeOrShowroomId,
                'product_id' => $request->product,
                'serial_no' => $request->serialNo,
                'date' => $date,
                'total_qty' => $request->stotalQty,
                'total_price' => $request->stotalPrice,
                'total_mrp_price' => $request->stotalPrice,
                'total_haire_price' => $request->stotalPrice,
                'remarks' => $request->remarks,
                'created_by' => $this->userId
            ]);

            $countProduct = count($request->productId);

            if ($request->productId) {
                $postData = [];
                for ($i = 0; $i < $countProduct; $i++) {
                    $product = LiftingProduct::with('product')->where('id', $request->liftingProductId[$i])->first();
                    $postData[] = [
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'lifting_return_id' => $liftingReturn->id,
                        'lifting_id' => $request->liftingId[$i],
                        'lifting_product_id' => $request->liftingProductId[$i],
                        'vendor_id' => $request->supplier,
                        'store_or_showroom_type' => $storeOrShowroomType,
                        'store_or_showroom_id' => $storeOrShowroomId,
                        'product_id' => $request->productId[$i],
                        'product_name' => $request->productName[$i],
                        'model_no' => $request->productModelNo[$i],
                        //                        'serial_no' => $request->productSerialNo[$i],
                        //                        'color' => $request->productColor[$i],
                        // 'haire_price' => $request->productHigherPrice[$i],
                        'qty' => $request->productQty[$i],
                        'price' => $product->price,
                        'amount' => $product->price * $request->productQty[$i],
                        'mrp_price' => $product->price * $request->productQty[$i]
                    ];
                }
                LiftingReturnProduct::insert($postData);
            }
        }

        return redirect(route('liftingReturn.index'))->with('msg', 'Lifitng Return Added Successfully');
    }

    public function edit($liftingReturnId)
    {
        $title = "Add Lifting Returns";
        $formLink = "liftingReturn.update";
        $buttonName = "Update";



        $productTypes = [
            'warranty_product' => 'Warranty Product',
            'consumer_product' => 'Consumer Product',
            'spare_parts' => 'Spare Parts',
            'raw_product' => 'Raw Product'
        ];

        $liftingReturn = LiftingReturn::where('id', $liftingReturnId)->first();
        $liftingReturnProducts = LiftingReturnProduct::where('lifting_return_id', $liftingReturnId)->get();


        $storeAndShowrooms = DB::table('view_store_and_showroom')
            //                ->where('type', 'showroom')
            ->where('companyId', $this->company)
            //                ->where('showroomId', $this->showroomId)
            //                ->where('id', $this->showroomId)
            //                ->orWhere('type', 'store')
            //                ->orderBy('type', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $vendors = VendorSetup::where('company_id', $this->company)
            //                ->where('showroom_id', $this->showroomId)
            ->where('status', '1')
            ->orderBy('name', 'asc')
            ->get();

        $products = Product::where('status', '1')
            ->where('company_id', $this->company)
            //                ->where('tbl_products.showroom_id', $this->showroomId)
            ->where('product_type', $liftingReturn->product_type)
            ->orderBy('name', 'asc')
            ->get();

        $liftingNo = Lifting::where('product_type', $liftingReturn->product_type)
            ->where('vendor_id', $liftingReturn->vendor_id)
            ->where('store_or_showroom_id', $liftingReturn->store_or_showroom_id)
            ->get();

        $lproducts = LiftingProduct::with('lifting', 'product')->where('lifting_id', $liftingReturnProducts[0]->lifting_id)->get();


        return view('admin.liftingReturn.edit')->with(compact('title', 'formLink', 'buttonName', 'storeAndShowrooms', 'vendors', 'products', 'liftingReturn', 'liftingReturnProducts', 'productTypes', 'liftingNo', 'lproducts'));
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $storeOrShowrooms = explode(',', $request->storeOrShowroom);
        $storeOrShowroomId = $storeOrShowrooms[0];
        $storeOrShowroomType = $storeOrShowrooms[1];

        $type = $request->productType;

        $date = date('Y-m-d', strtotime($request->liftingReturnDate));

        $liftingReturn = LiftingReturn::find($request->liftingReturnId);

        if ($type == 'warranty_product' || $type == 'spare_parts') {
            if ($request->totalQty == 0) {
                $message = "Please return some lifting Products";
                return redirect(route('liftingReturn.index'))->with('fail_msg', $message)->withInput();
            }

            $liftingReturn->update([
                'company_id' => $this->company,
                'vendor_id' => $request->supplier,
                'showroom_id' => $this->showroomId,
                'store_or_showroom_type' => $storeOrShowroomType,
                'store_or_showroom_id' => $storeOrShowroomId,
                'product_id' => $request->product,
                'serial_no' => $request->serialNo,
                'date' => $date,
                'total_qty' => $request->totalQty,
                'total_price' => $request->totalPrice,
                'total_mrp_price' => $request->totalMrpPrice,
                'total_haire_price' => $request->totalHigherPrice,
                'remarks' => $request->remarks,
                'updated_by' => $this->userId
            ]);

            LiftingReturnProduct::where('lifting_return_id', $request->liftingReturnId)->delete();

            $countProduct = count($request->productId);

            if ($request->productId) {
                $postData = [];
                for ($i = 0; $i < $countProduct; $i++) {
                    $postData[] = [
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'lifting_return_id' => $liftingReturn->id,
                        'lifting_id' => $request->liftingId[$i],
                        'lifting_product_id' => $request->liftingProductId[$i],
                        'vendor_id' => $request->supplier,
                        'store_or_showroom_type' => $storeOrShowroomType,
                        'store_or_showroom_id' => $storeOrShowroomId,
                        'product_id' => $request->productId[$i],
                        'product_name' => $request->productName[$i],
                        'model_no' => $request->productModelNo[$i],
                        'serial_no' => $request->productSerialNo[$i],
                        'color' => $request->productColor[$i],
                        'qty' => $request->productQty[$i],
                        'price' => $request->productPrice[$i],
                        'mrp_price' => $request->productMrpPrice[$i],
                        // 'haire_price' => $request->productHigherPrice[$i],
                    ];
                }
                LiftingReturnProduct::insert($postData);
            }
        } else {

            if ($request->stotalQty == 0) {
                $message = "Please return some lifting Products";
                return redirect(route('liftingReturn.add'))->with('fail_msg', $message)->withInput();
            }

            $liftingReturn->update([
                'company_id' => $this->company,
                'product_type' => $type,
                'vendor_id' => $request->supplier,
                'showroom_id' => $this->showroomId,
                'store_or_showroom_type' => $storeOrShowroomType,
                'store_or_showroom_id' => $storeOrShowroomId,
                'product_id' => $request->product,
                'serial_no' => $request->serialNo,
                'date' => $date,
                'total_qty' => $request->stotalQty,
                'total_price' => $request->stotalPrice,
                'total_mrp_price' => $request->stotalPrice,
                'total_haire_price' => $request->stotalPrice,
                'remarks' => $request->remarks,
                'created_by' => $this->userId
            ]);

            LiftingReturnProduct::where('lifting_return_id', $request->liftingReturnId)->delete();
            if ($request->productId) {

                $countProduct = count($request->productId);
                $postData = [];
                for ($i = 0; $i < $countProduct; $i++) {
                    $product = LiftingProduct::with('product')->where('id', $request->liftingProductId[$i])->first();
                    $postData[] = [
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'lifting_return_id' => $liftingReturn->id,
                        'lifting_id' => $request->liftingId[$i],
                        'lifting_product_id' => $request->liftingProductId[$i],
                        'vendor_id' => $request->supplier,
                        'store_or_showroom_type' => $storeOrShowroomType,
                        'store_or_showroom_id' => $storeOrShowroomId,
                        'product_id' => $request->productId[$i],
                        'product_name' => $request->productName[$i],
                        'model_no' => $request->productModelNo[$i],
                        //                        'serial_no' => $request->productSerialNo[$i],
                        //                        'color' => $request->productColor[$i],
                        // 'haire_price' => $request->productHigherPrice[$i],
                        'qty' => $request->productQty[$i],
                        'price' => $product->price,
                        'amount' => $product->price * $request->productQty[$i],
                        'mrp_price' => $product->price * $request->productQty[$i]
                    ];
                }
                LiftingReturnProduct::insert($postData);
            }
        }

        return redirect(route('liftingReturn.index'))->with('msg', 'Lifitng Return Updated Successfully');
    }

    public function liftingProductInfo(Request $request)
    {
        $liftingProducts = LiftingProduct::select('tbl_lifting_products.*', 'tbl_products.name as productName', 'tbl_liftings.product_type')
            ->join('tbl_products', 'tbl_products.id', '=', 'tbl_lifting_products.product_id')
            ->leftJoin('tbl_lifting_return_products', 'tbl_lifting_return_products.serial_no', '=', 'tbl_lifting_products.serial_no')
            ->leftjoin('tbl_liftings', 'tbl_liftings.id', '=', 'tbl_lifting_products.lifting_id')
            ->whereNull('tbl_lifting_return_products.serial_no')
            ->where('tbl_lifting_products.product_id', $request->productId)
            ->where('tbl_lifting_products.vendor_id', $request->vendorId)
            ->where('tbl_lifting_products.store_or_showroom_type', $request->storeOrShowroomType)
            ->where('tbl_lifting_products.store_or_showroom_id', $request->storeOrShowroomId)
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'liftingProducts' => $liftingProducts,
            ]);
        }
    }

    public function print($liftingReturnId)
    {
        $title = "Product Lifting Return Chalan";

        $liftingReturn = LiftingReturn::select('tbl_lifting_returns.*', 'tbl_vendors.name as vendorName')
            ->join('tbl_vendors', 'tbl_vendors.id', '=', 'tbl_lifting_returns.vendor_id')
            ->where('tbl_lifting_returns.id', $liftingReturnId)
            ->first();
        $liftingReturnProducts = LiftingReturnProduct::select('tbl_lifting_return_products.*', 'tbl_products.code as productCode')
            ->join('tbl_products', 'tbl_products.id', '=', 'tbl_lifting_return_products.product_id')
            ->where('tbl_lifting_return_products.lifting_return_id', $liftingReturnId)
            ->get();

        $pdf = PDF::loadView('admin.liftingReturn.print', ['title' => $title, 'liftingReturn' => $liftingReturn, 'liftingReturnProducts' => $liftingReturnProducts]);

        return $pdf->stream('product_lifting_return_chalan.pdf');
    }

    public function delete(Request $request)
    {
        LiftingReturn::where('id', $request->liftingReturnId)->delete();
        LiftingReturnProduct::where('lifting_return_id', $request->liftingReturnId)->delete();
    }

    public function variant(Request $request)
    {
        $type = $request->type;
        $supplier = $request->supplier;
        $store = $request->store;

        $liftings = [];
        if ($type == 'consumer_product' || $type == 'raw_product') {
            $liftings = Lifting::where('product_type', $type)
                ->where('vendor_id', $supplier)
                ->where('store_or_showroom_id', $store)
                ->get();
        } else {
            $liftings = Product::where('product_type', $type)
                ->where('company_id', $this->company)
                ->where('status', 1)
                ->get();
        }

        return $liftings;
    }

    public function liftingNoProducts(Request $request)
    {
        $liftingProducts = LiftingProduct::with('product')->where('lifting_id', $request->liftingNo)->get();

        $products = [];
        foreach ($liftingProducts as $liftingProduct) {
            $liftingReturns = LiftingReturnProduct::where('lifting_id', $liftingProduct->lifting_id)
                ->where('lifting_product_id', $liftingProduct->product_id)
                ->sum('qty');
            if (($liftingProduct->qty - $liftingReturns) > 0) {
                $products[] = [
                    'id' => $liftingProduct->id,
                    'lifting_id' => $liftingProduct->lifting_id,
                    'product_id' => $liftingProduct->product_id,
                    'product_name' => $liftingProduct->product->name,
                    'model_no' => $liftingProduct->model_no,
                    'qty' => $liftingProduct->qty - $liftingReturns,
                    'price' => $liftingProduct->price,
                    'amount' => ($liftingProduct->qty - $liftingReturns) * $liftingProduct->price,
                    'mrp_price' => ($liftingProduct->qty - $liftingReturns) * $liftingProduct->price,
                ];
            }
        }

        return $products;
    }
}
