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
use App\StoreSetup;
use DB;
use PDF;
use MPDF;

class ReceiveProductController extends Controller {

    public function index() {
        $title = "Receive Products";

        $stores = StoreSetup::where('showroom_id', $this->showroomId)
                ->get()
                ->pluck('id')
                ->toArray();

        $receives = Transfer::whereIn('destination_id', $stores)
                ->orderBy('date', 'desc')
                ->get();

        return view('admin.receiveProduct.index')->with(compact('title', 'receives'));
    }

    public function approve($id) {
        $title = "Approve Transfer Products";
        $formLink = "receiveProduct.save";
        $buttonName = "Receive";

        $transfer_info = Transfer::select(
                        'tbl_transfers.*',
                        'host.name as host_name',
                        'destination.name as destination_name',
                        'tbl_vendors.name as vendor'
                )
                ->leftjoin('tbl_stores as host', 'host.id', '=', 'tbl_transfers.host_id')
                ->leftjoin('tbl_stores as destination', 'destination.id', '=', 'tbl_transfers.destination_id')
                ->leftjoin('tbl_vendors', 'tbl_vendors.id', '=', 'tbl_transfers.vendor_id')
                // ->where('showroom_id', $this->showroomId)
                ->where('tbl_transfers.id', $id)
                ->first();

        $products = TransferProduct::select(
                        'tbl_transfer_products.*',
                        'tbl_products.name as productName',
                        'tbl_products.model_no'
                )
                ->where('transfer_id', $transfer_info->id)
                ->leftjoin('tbl_products', 'tbl_products.id', '=', 'tbl_transfer_products.product_id')
                ->whereNull('approve_by')
                ->get();


        $rec_products = TransferProduct::select(
                        'tbl_transfer_products.*',
                        'tbl_products.name as productName',
                        'tbl_products.model_no'
                )
                ->where('transfer_id', $transfer_info->id)
                ->leftjoin('tbl_products', 'tbl_products.id', '=', 'tbl_transfer_products.product_id')
                ->whereNotNull('approve_by')
                ->get();



        return view('admin.receiveProduct.add')->with(compact('title', 'formLink', 'buttonName', 'transfer_info', 'products', 'rec_products'));
    }

    public function save(Request $request) {
        $transfer_id = $request->transfer_id;

        $transfer = Transfer::where('id', $transfer_id)->first();


        $receive_pro_id = $request->liftingProductId;


        $date = date('Y-m-d', strtotime($request->transferDate));

        if ($request->totalQty == 0) {
            return redirect(route('receiveProduct.add'))->with('fail_msg', 'Please Receive Some Products');
        }

        // $transfer = Transfer::create([]);

        $countProduct = count($request->productId);

        // dd(auth()->id());

        $received = TransferProduct::where('transfer_id', $transfer_id)
                ->whereNotNull('approve_by')
                ->count();

        $all_products = TransferProduct::where('transfer_id', $transfer_id)
                ->count();


        if ($all_products > ($received + $countProduct)) {

            if ($request->productId) {
                TransferProduct::whereIn('id', $receive_pro_id)
                        ->where('transfer_id', $transfer_id)
                        ->update([
                            'approve_by' => auth()->id(),
                ]);
            }
        } elseif ($all_products = ($received + $countProduct)) {
            if ($request->productId) {
                TransferProduct::whereIn('id', $receive_pro_id)
                        ->where('transfer_id', $transfer_id)
                        ->update([
                            'approve_by' => auth()->id(),
                ]);


                $transfer->update([
                    'approve_by' => auth()->id()
                ]);
            }
            return redirect(route('receiveProduct.index'))->with('msg', 'Received Successfully');
        } else {
            return redirect(route('receiveProduct.index'))->with('msg', 'Received Failed');
        }

        return redirect(route('receiveProduct.index'))->with('msg', 'Received Successfully');
    }

    public function edit($id) {
        $title = "Edit Approve Transfer Products";
        $formLink = "receiveProduct.update";
        $buttonName = "Update";

        $transfer_info = Transfer::select(
                        'tbl_transfers.*',
                        'host.name as host_name',
                        'destination.name as destination_name',
                        'tbl_vendors.name as vendor'
                )
                ->leftjoin('tbl_stores as host', 'host.id', '=', 'tbl_transfers.host_id')
                ->leftjoin('tbl_stores as destination', 'destination.id', '=', 'tbl_transfers.destination_id')
                ->leftjoin('tbl_vendors', 'tbl_vendors.id', '=', 'tbl_transfers.vendor_id')
                // ->where('showroom_id', $this->showroomId)
                ->where('tbl_transfers.id', $id)
                ->first();

        $products = TransferProduct::select(
                        'tbl_transfer_products.*',
                        'tbl_products.name as productName',
                        'tbl_products.model_no'
                )
                ->where('transfer_id', $transfer_info->id)
                ->leftjoin('tbl_products', 'tbl_products.id', '=', 'tbl_transfer_products.product_id')
                ->whereNotNull('approve_by')
                ->get();

        return view('admin.receiveProduct.edit')->with(compact('title', 'formLink', 'buttonName', 'hosts', 'destinations', 'vendors', 'products', 'transfer', 'transferProducts'));
    }

    public function update(Request $request) {
        $host = explode(',', $request->host);
        $hostId = $host[0];
        $hostType = $host[1];

        $destination = explode(',', $request->destination);
        $destinationId = $destination[0];
        $destinationType = $destination[1];

        $date = date('Y-m-d', strtotime($request->transferDate));

        $transfer = Transfer::find($request->transferId);


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

        TransferProduct::where('transfer_id', $request->transferId)->delete();

        $countProduct = count($request->productId);

        if ($request->productId) {
            $postData = [];
            for ($i = 0; $i < $countProduct; $i++) {
                $postData[] = [
                    'showroom_id' => $this->showroomId,
                    'transfer_id' => $transfer->id,
                    'vendor_id' => $request->supplier,
                    'lifting_product_id' => $request->liftingProductId[$i],
                    'product_id' => $request->productId[$i],
                    'name' => $request->productName[$i],
                    'model_no' => $request->productModelNo[$i],
                    'serial_no' => $request->productSerialNo[$i],
                    'color' => $request->productColor[$i],
                    'qty' => $request->productQty[$i],
                    'updated_by' => $this->userId
                ];
            }
            TransferProduct::insert($postData);
        }

        return redirect(route('transferProduct.index'))->with('msg', 'Vendor Updated Successfully');
    }

    public function storeAndShowroomInfo(Request $request) {
        // echo $request->hostType." AND ".$request->hostId; exit();
        $output = '';

        $destinations = DB::table('view_store_and_showroom')
                ->where('type', 'showroom')
                ->where('id', $this->showroomId)
                ->orWhere('type', 'store')
                ->orderBy('type', 'asc')
                ->orderBy('name', 'asc')
                ->get();

        // dd($destinations);

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

    public function liftingProductInfo(Request $request) {
        $liftingProducts = LiftingProduct::select('tbl_lifting_products.*', 'tbl_products.name as productName')
                ->join('tbl_products', 'tbl_products.id', '=', 'tbl_lifting_products.product_id')
                ->where('tbl_lifting_products.showroom_id', $this->showroomId)
                ->where('tbl_lifting_products.product_id', $request->productId)
                ->where('tbl_lifting_products.vendor_id', $request->vendorId)
                ->where('tbl_lifting_products.store_or_showroom_type', $request->hostType)
                ->where('tbl_lifting_products.store_or_showroom_id', $request->hostId)
                ->get();

        if ($request->ajax()) {
            return response()->json([
                        'liftingProducts' => $liftingProducts,
            ]);
        }
    }

    public function print($transferId) {
        $title = "Product Receive Chalan";

        $transfer = Transfer::where('tbl_transfers.id', $transferId)->first();

        $host = DB::table('tbl_stores')
                ->select('name as hostName')
                ->where('id', $transfer->host_id)
                ->first();

        $destination = DB::table('tbl_stores')
                ->select('name as destinationName')
                ->where('id', $transfer->destination_id)
                ->first();

        $transferProducts = TransferProduct::with('product', 'product.category')
                ->where('tbl_transfer_products.transfer_id', $transferId)
                ->get();

        $pdf = PDF::loadView('admin.receiveProduct.print', ['title' => $title, 'transfer' => $transfer, 'host' => $host, 'destination' => $destination, 'transferProducts' => $transferProducts,]);

        return $pdf->stream('product_receive_chalan.pdf');
    }

    public function delete(Request $request) {
        Transfer::where('id', $request->transferId)->delete();
        TransferProduct::where('transfer_id', $request->transferId)->delete();
    }

}
