<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transfer;
use App\TransferProduct;
use App\Product;
use App\VendorSetup;
use App\Lifting;
use App\LiftingProduct;
use App\ProductIssueList;
use DB;
use PDF;
use MPDF;

class TransferProductController extends Controller
{

    public function index()
    {
        $title = "Product Transfer";

        $transfers = Transfer::where('company_id', $this->company)
            //                ->where('showroom_id', $this->showroomId)
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.transferProduct.index')->with(compact('title', 'transfers'));
    }

    public function add()
    {
        $title = "Add Product Transfer";
        $formLink = "transferProduct.save";
        $buttonName = "Save";

        $storeAndShowrooms = DB::table('view_store_and_showroom')
            //                ->where('type', 'showroom')
            ->where('showroomId', $this->showroomId)
            ->where('type', 'store')
            ->orderBy('type', 'asc')
            ->orderBy('name', 'asc')
            ->get();


        $vendors = VendorSetup::orderBy('name', 'asc')
            ->where('company_id', $this->company)
            ->get();

        $products = Product::orderBy('name', 'asc')
            ->where('company_id', $this->company)
            ->get();

        $companyProductTypes = json_decode($this->companyInfo->product_types);

        $productTypes = collect([
            'warranty_product' => 'Warranty Product',
            'consumer_product' => 'Consumer Product',
            'spare_parts' => 'Spare Parts',
            'raw_product' => 'Raw Product'
        ]);

        $productTypes = $productTypes->filter(function ($value, $key) use ($companyProductTypes) {
            return in_array($key, $companyProductTypes);
        });

        return view('admin.transferProduct.add')->with(compact('title', 'formLink', 'buttonName', 'storeAndShowrooms', 'vendors', 'products', 'productTypes'));
    }

    public function save(Request $request)
    {
        $host = explode(',', $request->host);
        $hostId = $host[0];
        $hostType = $host[1];

        $destination = explode(',', $request->destination);
        $destinationId = $destination[0];
        $destinationType = $destination[1];

        $date = date('Y-m-d', strtotime($request->transferDate));

        $pType = $request->productType;

        if ($pType == 'warranty_product' || $pType == 'spare_parts') {
            if ($request->totalQty == 0) {
                return redirect(route('transferProduct.add'))->with('fail_msg', 'Please Transfer Some Products');
            }

            $transfer = Transfer::create([
                'company_id' => $this->company,
                'showroom_id' => $this->showroomId,
                'vendor_id' => $request->supplier,
                'product_id' => $request->product,
                'product_type' => $pType,
                'transfer_no' => $request->transferNo,
                'date' => $date,
                'host_type' => $hostType,
                'host_id' => $hostId,
                'destination_type' => $destinationType,
                'destination_id' => $destinationId,
                'total_qty' => $request->totalQty,
                'created_by' => $this->userId
            ]);

            if ($request->liftingProductId) {
                $countProduct = count($request->liftingProductId);
                $postData = [];
                for ($i = 0; $i < $countProduct; $i++) {
                    $liftingPro = LiftingProduct::with('product')
                        ->where('id', $request->liftingProductId[$i])
                        ->first();
                    $postData[] = [
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'transfer_id' => $transfer->id,
                        'vendor_id' => $request->supplier,
                        'lifting_product_id' => $request->liftingProductId[$i],
                        'product_id' => $liftingPro->product_id,
                        'category_id' => @$liftingPro->product->category_id,
                        'name' => $liftingPro->product_name,
                        'model_no' => $liftingPro->model_no,
                        'serial_no' => $liftingPro->serial_no,
                        'qty' => 1,
                        'created_by' => $this->userId
                    ];
                }
                TransferProduct::insert($postData);
            }
        } elseif ($pType == 'consumer_product' || $pType == 'raw_product') {

            if ($request->stotalQty == 0) {
                return redirect(route('transferProduct.add'))->with('fail_msg', 'Please Transfer Some Products');
            }

            $transfer = Transfer::create([
                'company_id' => $this->company,
                'showroom_id' => $this->showroomId,
                'vendor_id' => $request->supplier,
                'product_id' => $request->product,
                'product_type' => $pType,
                'transfer_no' => $request->transferNo,
                'date' => $date,
                'host_type' => $hostType,
                'host_id' => $hostId,
                'destination_type' => $destinationType,
                'destination_id' => $destinationId,
                'total_qty' => $request->stotalQty,
                'created_by' => $this->userId
            ]);

            if ($request->productId) {
                $countProduct = count($request->productId);
                $postData = [];
                for ($i = 0; $i < $countProduct; $i++) {
                    $product = Product::find($request->productId[$i]);
                    $postData[] = [
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'transfer_id' => $transfer->id,
                        'product_id' => $product->id,
                        'category_id' => $product->category_id,
                        'name' => $product->name,
                        'model_no' => $product->model_no,
                        'qty' => $request->qty[$i],
                        'created_by' => $this->userId
                    ];
                }
                TransferProduct::insert($postData);
            }
        }


        return redirect(route('transferProduct.index'))->with('msg', 'Vendor Added Successfully');
    }

    public function edit($transferId)
    {
        $title = "Edit Product Transfer";
        $formLink = "transferProduct.update";
        $buttonName = "Update";

        $transfer = Transfer::where('id', $transferId)->first();
        $transferProducts = TransferProduct::with('product')->where('transfer_id', $transferId)->get();

        $hosts = DB::table('view_store_and_showroom')->get();
        $destinations = DB::table('view_store_and_showroom')
            ->where('companyId', $this->company)
            ->where('id', '!=', $transfer->host_id)
            ->get();

        $vendors = VendorSetup::where('company_id', $this->company)
            ->orderBy('name', 'asc')
            ->get();

        $products = Product::where('company_id', $this->company)
            ->where('product_type', $transfer->product_type)
            ->orderBy('name', 'asc')
            ->get();


        $productTypes = [
            'warranty_product' => 'Warranty Product',
            'consumer_product' => 'Consumer Product',
            'spare_parts' => 'Spare Parts',
            'raw_product' => 'Raw Product'
        ];


        return view('admin.transferProduct.edit')->with(compact('title', 'formLink', 'buttonName', 'hosts', 'destinations', 'vendors', 'products', 'transfer', 'transferProducts', 'productTypes'));
    }

    public function update(Request $request)
    {
        $host = explode(',', $request->host);
        $hostId = $host[0];
        $hostType = $host[1];

        $destination = explode(',', $request->destination);
        $destinationId = $destination[0];
        $destinationType = $destination[1];

        $date = date('Y-m-d', strtotime($request->transferDate));

        $transfer = Transfer::find($request->transferId);

        $pType = $transfer->product_type;

        if ($pType == 'warranty_product' || $pType == 'spare_parts') {
            if ($request->totalQty == 0) {
                return redirect(route('transferProduct.index'))->with('fail_msg', 'Please Transfer Some Products');
            }

            $transfer->update([
                'showroom_id' => $this->showroomId,
                'vendor_id' => $request->supplier,
                'product_id' => $request->product,
                'transfer_no' => $request->transferNo,
                'date' => $date,
                'host_type' => $hostType,
                'host_id' => $hostId,
                'destination_type' => $destinationType,
                'destination_id' => $destinationId,
                'total_qty' => $request->totalQty,
                'updated_by' => $this->userId
            ]);

            TransferProduct::where('transfer_id', $transfer->id)->delete();

            if ($request->liftingProductId) {
                $countProduct = count($request->liftingProductId);
                $postData = [];
                for ($i = 0; $i < $countProduct; $i++) {
                    $liftingPro = LiftingProduct::with('product')
                        ->where('id', $request->liftingProductId[$i])
                        ->first();
                    $postData[] = [
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'transfer_id' => $transfer->id,
                        'vendor_id' => $request->supplier,
                        'lifting_product_id' => $request->liftingProductId[$i],
                        'product_id' => $liftingPro->product_id,
                        'category_id' => @$liftingPro->product->category_id,
                        'name' => $liftingPro->product_name,
                        'model_no' => $liftingPro->model_no,
                        'serial_no' => $liftingPro->serial_no,
                        'qty' => 1,
                        'created_by' => $this->userId
                    ];
                }
                TransferProduct::insert($postData);
            }
        } elseif ($pType == 'consumer_product' || $pType == 'raw_product') {

            if ($request->stotalQty == 0) {
                return redirect(route('transferProduct.index'))->with('fail_msg', 'Please Transfer Some Products');
            }

            $transfer->update([
                'product_id' => $request->product,
                'transfer_no' => $request->transferNo,
                'date' => $date,
                'host_type' => $hostType,
                'host_id' => $hostId,
                'destination_type' => $destinationType,
                'destination_id' => $destinationId,
                'total_qty' => $request->stotalQty,
                'updated_by' => $this->userId
            ]);

            TransferProduct::where('transfer_id', $transfer->id)->delete();

            if ($request->productId) {
                $countProduct = count($request->productId);
                $postData = [];
                for ($i = 0; $i < $countProduct; $i++) {
                    $product = Product::find($request->productId[$i]);
                    $postData[] = [
                        'company_id' => $this->company,
                        'showroom_id' => $this->showroomId,
                        'transfer_id' => $transfer->id,
                        'product_id' => $product->id,
                        'category_id' => $product->category_id,
                        'name' => $product->name,
                        'model_no' => $product->model_no,
                        'qty' => $request->qty[$i],
                        'created_by' => $this->userId
                    ];
                }
                TransferProduct::insert($postData);
            }
        }
        return redirect(route('transferProduct.index'))->with('msg', 'Vendor Updated Successfully');
    }

    public function storeAndShowroomInfo(Request $request)
    {
        // echo $request->hostType." AND ".$request->hostId; exit();
        $output = '';

        $destinations = DB::table('view_store_and_showroom')
            //                ->where('type', 'showroom')
            ->where('companyId', $this->company)
            ->where('id', '!=', $request->hostId)
            ->where('type', 'store')
            ->orderBy('type', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        //         dd($destinations);

        if ($destinations) {
            $output .= '<select class="form-control chosen-select destination" name="destination" id="destination">';
            $output .= '<option value="">Select Destination</option>';
            foreach ($destinations as $destination) {
                if ($destination->type != $request->hostType || $destination->id != $request->hostId) {
                    $output .= '<option value="' . $destination->id . ',' . $destination->type . '">' . $destination->name . '</option>';
                }
            }
            $output .= '</select>';
        } else {
            $output .= '<select class="form-control chosen-select destination" name="destination" id="destination">';
            $output .= '<option value="">Select Destination</option>';
            $output .= '</select>';
        }

        echo $output;
    }

    public function liftingProductInfo(Request $request)
    {

        $sales = ProductIssueList::select('serial_no')
            ->where('company_id', $this->company)
            ->where('product_id', $request->productId)
            ->get()
            ->pluck('serial_no')
            ->toArray();

        $transfers = TransferProduct::with('transfer')
            ->select('serial_no')
            ->whereHas('transfer', function ($q) use ($request) {
                $q->where('host_id', $request->hostId);
            })
            ->where('company_id', $this->company)
            ->where('product_id', $request->productId)
            ->get()
            ->pluck('serial_no')
            ->toArray();

        $transferRs = TransferProduct::with('transfer')
            ->select('serial_no')
            ->whereHas('transfer', function ($q) use ($request) {
                $q->where('destination_id', $request->hostId);
            })
            ->where('company_id', $this->company)
            ->where('product_id', $request->productId)
            ->get()
            ->pluck('serial_no')
            ->toArray();

        $serials = array_merge($sales, $transfers, $transferRs);
        $serials = array_unique($serials);


        $liftingProducts = LiftingProduct::select('tbl_lifting_products.*', 'tbl_products.name as productName')
            ->join('tbl_products', 'tbl_products.id', '=', 'tbl_lifting_products.product_id')
            ->where('tbl_lifting_products.showroom_id', $this->showroomId)
            ->where('tbl_lifting_products.product_id', $request->productId)
            //                ->where('tbl_lifting_products.vendor_id', $request->vendorId)
            ->where('tbl_lifting_products.store_or_showroom_type', $request->hostType)
            ->where('tbl_lifting_products.store_or_showroom_id', $request->hostId)
            ->whereNotIn('tbl_lifting_products.serial_no', $serials)
            ->get();


        if ($request->ajax()) {
            return response()->json([
                'liftingProducts' => $liftingProducts,
            ]);
        }
    }

    public function getSerialProduct(Request $request)
    {
        $liftingProduct = LiftingProduct::with('product')
            ->where('serial_no', $request->serial_no)
            ->where('company_id', $this->company)
            ->first();


        if ($request->ajax()) {
            return response()->json([
                'liftingProduct' => $liftingProduct,
            ]);
        }
    }

    public function getConsumerProduct(Request $request)
    {

        //        $sales = ProductIssueList::with('lifting')
        //                ->whereHas('lifting', function($q) use ($request) {
        //                    $q->where('store_or_showroom_id', $request->host)
        //                    ->where('store_or_showroom_type', 'store');
        //                })
        //                ->where('product_id', $request->product)
        //                ->sum('qty');

        $sales = 0;


        $transfers = TransferProduct::with('transfer')
            ->whereHas('transfer', function ($q) use ($request) {
                $q->where('host_id', $request->hostId);
            })
            ->where('product_id', $request->product)
            ->sum('qty');

        $transferRs = TransferProduct::with('transfer')
            ->whereHas('transfer', function ($q) use ($request) {
                $q->where('destination_id', $request->hostId);
            })
            ->where('product_id', $request->product)
            ->sum('qty');

        $liftingProduct = LiftingProduct::with('lifting')
            ->whereHas('lifting', function ($q) use ($request) {
                $q->where('store_or_showroom_id', $request->host)
                    ->where('store_or_showroom_type', 'store');
            })
            ->where('product_id', $request->product)
            ->sum('qty');

        $remainQty = $liftingProduct + $transferRs - ($transfers + $sales);

        $reqQty = $request->reqQty;

        $product = Product::find($request->product);

        if ($request->ajax()) {
            return response()->json([
                'remainQty' => $remainQty,
                'reqQty' => $reqQty,
                'product' => $product
            ]);
        }
    }

    public function print($transferId)
    {
        $title = "Product Transfer Chalan";

        $transfer = Transfer::where('tbl_transfers.id', $transferId)->first();

        $host = DB::table('view_store_and_showroom')
            ->select('name as hostName')
            ->where('type', $transfer->host_type)
            ->where('id', $transfer->host_id)
            ->where('companyId', $this->company)
            ->first();


        $destination = DB::table('view_store_and_showroom')
            ->select('name as destinationName')
            ->where('type', $transfer->destination_type)
            ->where('id', $transfer->destination_id)
            ->first();

        if ($transfer->product_type == 'warranty_product' || $transfer->product_type == 'spare_parts') {
            $transferProducts = TransferProduct::with('lifting')
                ->select('tbl_transfer_products.*', 'tbl_products.code as productCode')
                ->join('tbl_products', 'tbl_products.id', '=', 'tbl_transfer_products.product_id')
                ->where('tbl_transfer_products.transfer_id', $transferId)
                ->get();
        } else {
            $transferProducts = TransferProduct::with('product')
                ->where('transfer_id', $transferId)
                ->get();
        }

        $pdf = PDF::loadView('admin.transferProduct.print', ['title' => $title, 'transfer' => $transfer, 'host' => $host, 'destination' => $destination, 'transferProducts' => $transferProducts,]);

        return $pdf->stream('product_transfer_chalan.pdf');
    }

    public function delete(Request $request)
    {
        Transfer::where('id', $request->transferId)->delete();
        TransferProduct::where('transfer_id', $request->transferId)->delete();
    }
}
