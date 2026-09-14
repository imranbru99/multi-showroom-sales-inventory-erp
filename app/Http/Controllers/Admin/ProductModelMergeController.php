<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\RetailSales;
use App\ProductIssueList;
use App\RetailSalesReturnProducts;
use App\SalesReturn;
use App\LiftingProduct;
use App\LiftingReturnProduct;
use App\TransferProduct;

class ProductModelMergeController extends Controller
{
    public function index(Request $request)
    {
        $title = "Product Model Merge";
        $searchFormLink = "productModelMerge.index";
        $printFormLink = "productModelMerge.save";

        $productModel = $request->productModel;

        $data = [];

        if ($request->searched) {
            $data = Product::where('model_no', 'like', '%' . $productModel . '%')->get();
        }

        return view('admin.productModelMerge.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'data', 'productModel'));
    }


    public function save(Request $request)
    {
        // dd($request->all());
        // validate

        $request->validate([
            'selected_models.*' => 'required'
        ]);

        $allModels = $request->selected_models;


        // get main product 
        $mainmodelId = $allModels[0];

        $allModelWithoutMain = $request->selected_models;

        unset($allModelWithoutMain[0]);


        // change retail sales
        RetailSales::whereIn('product_id', $allModelWithoutMain)->update([
            'product_id' => $mainmodelId,
        ]);

        // change dealer sales
        ProductIssueList::whereIn('product_id', $allModelWithoutMain)->update([
            'product_id' => $mainmodelId,
        ]);

        // change retail return
        RetailSalesReturnProducts::whereIn('product_id', $allModelWithoutMain)->update([
            'product_id' => $mainmodelId,
        ]);

        // change dealer return
        SalesReturn::whereIn('product_id', $allModelWithoutMain)->update([
            'product_id' => $mainmodelId,
        ]);

        // change Lifting
        LiftingProduct::whereIn('product_id', $allModelWithoutMain)->update([
            'product_id' => $mainmodelId,
        ]);

        // change lifting return
        LiftingReturnProduct::whereIn('product_id', $allModelWithoutMain)->update([
            'product_id' => $mainmodelId,
        ]);

        // change transfer return
        TransferProduct::whereIn('product_id', $allModelWithoutMain)->update([
            'product_id' => $mainmodelId,
        ]);

        // delete product
        Product::whereIn('id', $allModelWithoutMain)->delete();

        return redirect(route('productModelMerge.index'))->with('msg', 'Product Model Merged Successfully');
    }
}
