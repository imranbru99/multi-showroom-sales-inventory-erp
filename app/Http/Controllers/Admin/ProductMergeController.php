<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;

class ProductMergeController extends Controller
{
    public function index(Request $request)
    {
        $title = "Product Name Merge";

        $name = $request->name;

        $searchProducts = [];
        if ($name) {
            $searchProducts = Product::where('name', 'like', '%' . $name . '%')->get();
        }


        return view('admin.productMerge.index')->with(compact('title', 'name', 'searchProducts'));
    }


    public function save(Request $request)
    {

        if (!empty($request->id) && count($request->id)) {
            $products = Product::whereIn('id', $request->id)->get();

            foreach ($products as $product) {
                $product->update([
                    'name' => $products[0]->name,
                ]);
            }
        }

        return redirect(route('productMerge.index'))->with('msg', 'Product Name Merged Successfully');
    }
}
