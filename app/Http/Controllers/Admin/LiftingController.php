<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VendorSetup;
use App\StoreSetup;
use App\Product;
use App\Lifting;
use App\LiftingProduct;
use App\ShowroomSetup;
use DB;
use PDF;
use MPDF;

class LiftingController extends Controller {

    public function index() {
        $title = "Product Lifting";

        if ($this->userRole == 1) {
            $liftings = Lifting::select('tbl_liftings.*', 'tbl_vendors.name as vendorName')
                    ->leftJoin('tbl_vendors', 'tbl_vendors.id', '=', 'tbl_liftings.vendor_id')
//                ->where('tbl_liftings.showroom_id', $this->showroomId)
                    ->orderBy('tbl_liftings.id', 'desc')
                    ->get();
        } else {
            $liftings = Lifting::select('tbl_liftings.*', 'tbl_vendors.name as vendorName')
                    ->leftJoin('tbl_vendors', 'tbl_vendors.id', '=', 'tbl_liftings.vendor_id')
                    ->where('tbl_liftings.company_id', $this->company)
//                   ->where('tbl_liftings.showroom_id', $this->showroomId)
                    ->orderBy('tbl_liftings.id', 'desc')
                    ->get();
        }


        // $liftings = Lifting::with(['vendor'])->where('showroom_id', $this->showroomId)->orderBy('id', 'dsc')->get();


        return view('admin.lifting.index')->with(compact('title', 'liftings'));
    }

    public function add() {
        $title = "Add Product Lifting";
        $formLink = "lifting.save";
        $barcodeformLink = "barcode.print";
        $buttonName = "Save";

        $showroom = $this->showroomId;

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

        $vendors = VendorSetup::where('company_id', $this->company)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        $storesAndShowrooms = DB::table('view_store_and_showroom')
//                ->where('type', 'showroom')
                ->where('companyId', $this->company)
//                ->where('showroomId', $this->showroomId)
//                ->where('id', $this->showroomId)
//                ->orWhere('type', 'store')
//                ->orderBy('type', 'asc')
                ->orderBy('name', 'asc')
                ->get();

        $products = Product::where('status', '1')
                ->where('tbl_products.company_id', $this->company)
//                ->where('tbl_products.showroom_id', $this->showroomId)
                ->where('product_type', 'warranty_product')
                ->orderBy('name', 'asc')
                ->get();

        return view('admin.lifting.add')->with(compact('title', 'formLink', 'buttonName', 'vendors', 'storesAndShowrooms', 'products', 'barcodeformLink', 'showroom', 'productTypes'));
    }

    public function save(Request $request) {

        ini_set('max_execution_time', '500');
        ini_set("pcre.backtrack_limit", "5000000");

        $pType = $request->productType;

        if ($pType == 'warranty_product' || $pType == 'spare_parts') {
            $Lifting = Lifting::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                    ->where('serial_no', $request->serialNo)
                    ->get();

            if (count($Lifting) == 0) {
                $title = "Product Barcode";

                $submissionDate = date('Y-m-d', strtotime($request->submissionDate));
                $voucherDate = $submissionDate;


                $storesOrShowrooms = explode(',', $request->storeOrShowroom);
                $storeOrShowroomId = $storesOrShowrooms[0];
                $storeOrShowroomType = $storesOrShowrooms[1];

                $lifting = Lifting::create([
                            'company_id' => $this->company,
                            'showroom_id' => $this->showroomId,
                            'product_type' => $pType,
                            'serial_no' => $request->serialNo,
                            'vaouchar_no' => $request->voucharNo,
                            'vendor_id' => $request->vendorId,
                            'store_or_showroom_type' => $storeOrShowroomType,
                            'store_or_showroom_id' => $storeOrShowroomId,
                            'purchase_by' => $request->purchaseBy,
                            'submission_date' => $submissionDate,
                            'vouchar_date' => $voucherDate,
                            'total_qty' => $request->totalQty,
                            'total_price' => $request->totalPrice,
                            'total_mrp_price' => $request->totalMrpPrice,
                            'total_haire_price' => $request->totalHairePrice,
                            'remarks' => $request->remarks,
                            'created_by' => $this->userId
                ]);

                $countProduct = count($request->productId);
                $serialNo = $this->maxProductSerial();
                if ($request->productId) {
                    $postData = [];
                    for ($i = 0; $i < $countProduct; $i++) {
                        $postData[] = [
                            'company_id' => $this->company,
                            'showroom_id' => $this->showroomId,
                            'lifting_id' => $lifting->id,
                            'vendor_id' => $request->vendorId,
                            'product_id' => $request->productId[$i],
                            'product_name' => $request->productName[$i],
                            'store_or_showroom_type' => $storeOrShowroomType,
                            'store_or_showroom_id' => $storeOrShowroomId,
                            'model_no' => $request->productModel[$i],
                            'serial_no' => $request->productSerialNo[$i],
                            'color' => $request->productColor[$i],
                            'qty' => $request->productQty[$i],
                            'price' => $request->productPrice[$i],
                            'amount' => $request->productPrice[$i],
                            'mrp_price' => $request->productMrpPrice[$i],
                            'haire_price' => $request->productHairePrice[$i],
                            'created_by' => $this->userId
                        ];
                        $serialNo++;
                    }
                    LiftingProduct::insert($postData);
                }
                $showroom = $showrooms = ShowroomSetup::find($this->showroomId);
                $customPaper = array();
                $pdf = PDF::loadView('admin.lifting.barcodepdf', compact('title', 'postData', 'showroom'));
                return $pdf->stream('product_barcode.pdf');
            } else {
                return redirect(route('lifting.index'))->with('fail_msg', 'These Products Already Lifted');
            }
        } elseif ($pType == 'consumer_product' || $pType == 'raw_product') {
            $submissionDate = date('Y-m-d', strtotime($request->submissionDate));

            $storesOrShowrooms = explode(',', $request->storeOrShowroom);
            $storeOrShowroomId = $storesOrShowrooms[0];
            $storeOrShowroomType = $storesOrShowrooms[1];

            $lifting = Lifting::create([
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'product_type' => $pType,
                        'serial_no' => $request->serialNo,
                        'vaouchar_no' => $request->voucharNo,
                        'vendor_id' => $request->vendorId,
                        'store_or_showroom_type' => $storeOrShowroomType,
                        'store_or_showroom_id' => $storeOrShowroomId,
                        'purchase_by' => $request->purchaseBy,
                        'submission_date' => $submissionDate,
                        'vouchar_date' => $submissionDate,
                        'total_qty' => $request->totalQty,
                        'total_price' => $request->totalPrice,
                        'total_mrp_price' => $request->totalMrpPrice,
                        'total_haire_price' => $request->totalHairePrice,
                        'remarks' => $request->remarks,
                        'created_by' => $this->userId
            ]);

            if ($request->productId) {
                $countProduct = count($request->productId);
                $postData = [];
                for ($i = 0; $i < $countProduct; $i++) {
                    $postData[] = [
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'lifting_id' => $lifting->id,
                        'vendor_id' => $request->vendorId,
                        'product_id' => $request->productId[$i],
                        'product_name' => $request->productName[$i],
                        'store_or_showroom_type' => $storeOrShowroomType,
                        'store_or_showroom_id' => $storeOrShowroomId,
                        'model_no' => $request->productModel[$i],
                        'color' => $request->productColor[$i],
                        'qty' => $request->productQty[$i],
                        'price' => $request->productPrice[$i],
                        'amount' => $request->productAmount[$i],
                        'mrp_price' => $request->productMrpPrice[$i],
                        'haire_price' => $request->productHairePrice[$i],
                        'created_by' => $this->userId
                    ];
                }
                LiftingProduct::insert($postData);
            }

            return redirect(route('lifting.index'))->with('msg', 'Product Lifting Successfully');
        } else {
            return redirect(route('lifting.index'))->with('fail_msg', 'Select Product Type');
        }
    }

    public function printBarCode($id) {
        ini_set('max_execution_time', '500');
        ini_set("pcre.backtrack_limit", "5000000");

        $postData = LiftingProduct::where('lifting_id', $id)->get()->toArray();
        $title = "Product Barcode";
        $showroom = $showrooms = ShowroomSetup::find($this->showroomId);

        $pdf = PDF::loadView('admin.lifting.barcodepdf', compact('title', 'postData', 'showroom'));
        return $pdf->stream('product_barcode.pdf');
    }

    public function edit($liftingId) {
        $title = "Edit Product Lifting";
        $formLink = "lifting.update";
        $buttonName = "Update";

        $showroom = $this->showroomId;

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

        $vendors = VendorSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('name', 'asc')
                ->get();

        $storesAndShowrooms = DB::table('view_store_and_showroom')
//                ->where('type', 'showroom')
                ->where('companyId', $this->company)
//                ->where('showroomId', $this->showroomId)
//                ->where('id', $this->showroomId)
//                ->orWhere('type', 'store')
//                ->orderBy('type', 'asc')
                ->orderBy('name', 'asc')
                ->get();



        $lifting = Lifting::where('id', $liftingId)->first();

        $liftingProducts = LiftingProduct::select('tbl_lifting_products.*', 'tbl_products.name as productName')
                ->join('tbl_products', 'tbl_products.id', '=', 'tbl_lifting_products.product_id')
                ->where('tbl_lifting_products.lifting_id', $liftingId)
                ->get();

        $products = Product::where('status', '1')
                ->where('company_id', $this->company)
                ->where('product_type', $lifting->product_type)
                ->orderBy('name', 'asc')
                ->get();

        return view('admin.lifting.edit')->with(compact('title', 'formLink', 'buttonName', 'vendors', 'storesAndShowrooms', 'products', 'lifting', 'liftingProducts', 'showroom', 'productTypes'));
    }

    public function update(Request $request) {

        $pType = $request->productType;

        if ($pType == 'warranty_product' || $pType == 'spare_parts') {
            $submissionDate = date('Y-m-d', strtotime($request->submissionDate));
            $voucherDate = $submissionDate;

            $storesOrShowrooms = explode(',', $request->storeOrShowroom);
            $storeOrShowroomId = $storesOrShowrooms[0];
            $storeOrShowroomType = $storesOrShowrooms[1];

            $liftingId = $request->liftingId;

            $lifting = Lifting::find($liftingId);

            $lifting->update([
                'company_id' => $this->company,
                'showroom_id' => $this->showroomId,
                'serial_no' => $request->serialNo,
                'vaouchar_no' => $request->voucharNo,
                'vendor_id' => $request->vendorId,
                'store_or_showroom_type' => $storeOrShowroomType,
                'store_or_showroom_id' => $storeOrShowroomId,
                'purchase_by' => $request->purchaseBy,
                'submission_date' => $submissionDate,
                'vouchar_date' => $voucherDate,
                'total_qty' => $request->totalQty,
                'total_price' => $request->totalPrice,
                'total_mrp_price' => $request->totalMrpPrice,
                'total_haire_price' => $request->totalHairePrice,
                'remarks' => $request->remarks,
                'updated_by' => $this->userId
            ]);

            LiftingProduct::where('lifting_id', $liftingId)->delete();

            $countProduct = count($request->productId);
            if ($request->productId) {
                $postData = [];
                for ($i = 0; $i < $countProduct; $i++) {
                    $postData[] = [
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'lifting_id' => $lifting->id,
                        'vendor_id' => $request->vendorId,
                        'product_id' => $request->productId[$i],
                        'product_name' => $request->productName[$i],
                        'store_or_showroom_type' => $storeOrShowroomType,
                        'store_or_showroom_id' => $storeOrShowroomId,
                        'model_no' => $request->productModel[$i],
                        'serial_no' => $request->productSerialNo[$i],
                        'color' => $request->productColor[$i],
                        'qty' => $request->productQty[$i],
                        'price' => $request->productPrice[$i],
                        'amount' => $request->productPrice[$i],
                        'mrp_price' => $request->productMrpPrice[$i],
                        'haire_price' => $request->productHairePrice[$i],
                        'updated_by' => $this->userId
                    ];
                }
                LiftingProduct::insert($postData);
            }
        } elseif ($pType == 'consumer_product' || $pType == 'raw_product') {
            $submissionDate = date('Y-m-d', strtotime($request->submissionDate));
            $voucherDate = date('Y-m-d', strtotime($request->voucherDate));

            $storesOrShowrooms = explode(',', $request->storeOrShowroom);
            $storeOrShowroomId = $storesOrShowrooms[0];
            $storeOrShowroomType = $storesOrShowrooms[1];

            $liftingId = $request->liftingId;

            $lifting = Lifting::find($liftingId);

            $lifting->update([
                'product_type' => $pType,
                'serial_no' => $request->serialNo,
                'vaouchar_no' => $request->voucharNo,
                'vendor_id' => $request->vendorId,
                'store_or_showroom_type' => $storeOrShowroomType,
                'store_or_showroom_id' => $storeOrShowroomId,
                'purchase_by' => $request->purchaseBy,
                'submission_date' => $submissionDate,
                'vouchar_date' => $submissionDate,
                'total_qty' => $request->totalQty,
                'total_price' => $request->totalPrice,
                'total_mrp_price' => $request->totalMrpPrice,
                'total_haire_price' => $request->totalHairePrice,
                'remarks' => $request->remarks,
                'created_by' => $this->userId
            ]);

            LiftingProduct::where('lifting_id', $liftingId)->delete();

            if ($request->productId) {
                $countProduct = count($request->productId);
                $postData = [];
                for ($i = 0; $i < $countProduct; $i++) {
                    $postData[] = [
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'lifting_id' => $lifting->id,
                        'vendor_id' => $request->vendorId,
                        'product_id' => $request->productId[$i],
                        'product_name' => $request->productName[$i],
                        'store_or_showroom_type' => $storeOrShowroomType,
                        'store_or_showroom_id' => $storeOrShowroomId,
                        'model_no' => $request->productModel[$i],
                        'color' => $request->productColor[$i],
                        'qty' => $request->productQty[$i],
                        'price' => $request->productPrice[$i],
                        'amount' => $request->productAmount[$i],
                        'mrp_price' => $request->productMrpPrice[$i],
                        'haire_price' => $request->productHairePrice[$i],
                        'created_by' => $this->userId
                    ];
                }
                LiftingProduct::insert($postData);
            }
        }


        return redirect(route('lifting.index'))->with('msg', 'Product Lifting Updated Successfully');
    }

    public function liftingProductInfo(Request $request) {
        // echo $request->productId; die();
        $product = Product::where('id', $request->productId)->first();
        // dd($product); die();

        if ($request->ajax()) {
            return response()->json([
                        'product' => $product
            ]);
        }
    }

    public function maxProductSerial() {
        $maxLiftingProduct = LiftingProduct::max('serial_no');

        if (@$maxLiftingProduct) {
            $productSerialNo = $maxLiftingProduct + 1;
        } else {
            $productSerialNo = 1000000 + 1;
        }
        return $productSerialNo;
    }

    public function print($liftingId) {

        ini_set('max_execution_time', '500');
        ini_set("pcre.backtrack_limit", "5000000");

        $title = "Product Lifting Chalan";

        $lifting = Lifting::select('tbl_liftings.*', 'tbl_vendors.name as vendorName')
                ->leftjoin('tbl_vendors', 'tbl_vendors.id', '=', 'tbl_liftings.vendor_id')
                ->where('tbl_liftings.id', $liftingId)
                ->first();
        $liftingProducts = LiftingProduct::select('tbl_lifting_products.*', 'tbl_products.name as productName', 'tbl_products.code as productCode')
                ->leftjoin('tbl_products', 'tbl_products.id', '=', 'tbl_lifting_products.product_id')
                ->where('tbl_lifting_products.lifting_id', $liftingId)
                ->get();

        $pdf = PDF::loadView('admin.lifting.print', ['title' => $title, 'lifting' => $lifting, 'liftingProducts' => $liftingProducts]);

        return $pdf->stream('product_lifting_chalan.pdf');
    }

    public function delete(Request $request) {
        // echo $lifting = $request->liftingId; die();
        $liftingId = $request->liftingId;
        Lifting::where('id', $liftingId)->delete();
        LiftingProduct::where('lifting_id', $liftingId)->delete();
    }

    public function barCodePrint(Request $request) {

        ini_set('max_execution_time', '500');
        ini_set("pcre.backtrack_limit", "5000000");

        $title = "Print of Product Barcode";
        $print = $request->print;


        $countProduct = count($request->productId);

        if ($request->productId) {
            $postData = [];
            for ($i = 0; $i < $countProduct; $i++) {
                $postData[] = [
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'lifting_id' => $lifting->id,
                    'vendor_id' => $request->vendorId,
                    'product_id' => $request->productId[$i],
                    'product_name' => $request->productName[$i],
                    'store_or_showroom_type' => $storeOrShowroomType,
                    'store_or_showroom_id' => $storeOrShowroomId,
                    'model_no' => $request->productModel[$i],
                    'serial_no' => $request->productSerialNo[$i],
                    'color' => $request->productColor[$i],
                    'qty' => $request->productQty[$i],
                    'price' => $request->productPrice[$i],
                    'mrp_price' => $request->productMrpPrice[$i],
                    'haire_price' => $request->productHairePrice[$i],
                    'created_by' => $this->userId
                ];
            }
        }


        $pdf = PDF::loadView('admin.barcode.print', ['title' => $title, 'postData' => $postData, 'countProduct' => $countProduct]);

        return $pdf->stream('product_barcode.pdf');
    }

    public function variantProduct(Request $request) {
        $type = $request->type;
        $product = $request->product;
        if ($product) {
            $products = Product::find($product);
        } else {
            $products = Product::where('company_id', $this->company)
                    ->where('status', 1)
                    ->where('product_type', $type)
                    ->get();
        }

        return $products;
    }

}
