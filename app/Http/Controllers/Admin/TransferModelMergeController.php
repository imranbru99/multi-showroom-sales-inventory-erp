<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\ExcelTransfer;
use App\RetailSales;
use App\TransferProduct;

class TransferModelMergeController extends Controller
{
    public function index(Request $request)
    {
        $title = "Transfer Model Merge";
        $searchFormLink = "transferModelMerge.save";

        $dones = ExcelTransfer::select('product_id')->get()->pluck('product_id');

        $products = Product::whereNotIn('id', $dones)->orderBy('model_no', 'ASC')->get();

        $salesModels = RetailSales::orderBy('product_model', 'ASC')->groupBy('product_model')->get();

        return view('admin.transferModelMerge.index')->with(compact('title', 'searchFormLink', 'products', 'salesModels'));
    }


    public function save(Request $request)
    {
        $id = $request->id;
        $product = Product::find($id);

        ExcelTransfer::create([
            'product_id' => $product->id
        ]);

        
        if (!empty($request->models) && count($request->models) > 0) {
            $retails = RetailSales::whereIn('product_model', $request->models)->get();
            foreach ($retails as $ret) {
                $ret->update([
                    'product_id' => $product->id,
                    'product_model' => $product->model_no,
                ]);
            }
        }


        return redirect(route('transferModelMerge.index'))->with('msg', 'Transfer Model Merged Successfully');
    }
}
