<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CategorySetup;
use App\Product;
use DB;
use PDF;
use MPDF;

class ProductListController extends Controller {

    public function index(Request $request) {
        $title = "Product List Report";
        $searchFormLink = "productList.index";
        $printFormLink = "productList.print";

        $productCategory = $request->productCategory;
        $product = $request->product;
        $print = $request->print;

        $categories = CategorySetup::where('company_id', $this->company)
                ->orderBy('name', 'asc')
                ->get();
        $products = Product::where('company_id', $this->company)
                ->orderBy('name', 'asc')
                ->get();

        if ($productCategory == "" && $product == "") {
            $productLists = Product::select(
                            'tbl_categories.name as categoryName',
                            'tbl_products.name as productName',
                            'tbl_products.model_no as productModel',
                            'tbl_products.price as price',
                            'tbl_products.mrp_price as mrpPrice',
                            'tbl_products.haire_price as hairePrice'
                    )
                    ->join('tbl_categories', 'tbl_categories.id', '=', 'tbl_products.category_id')
                    ->where('tbl_products.company_id', $this->company)
//                    ->where('tbl_products.showroom_id', $this->showroomId)
                    ->orderBy('categoryName')
                    ->orderBy('productName')
                    ->get();
        } else {
            $productLists = Product::select('tbl_categories.name as categoryName', 'tbl_products.name as productName', 'tbl_products.model_no as productModel', 'tbl_products.price as price', 'tbl_products.mrp_price as mrpPrice', 'tbl_products.haire_price as hairePrice')
                    ->join('tbl_categories', 'tbl_categories.id', '=', 'tbl_products.category_id')
                    ->orWhere(function($query) use($productCategory, $product) {
                        if (@$productCategory) {
                            foreach ($productCategory as $productCategoryInfo) {
                                $query->orWhereRaw('find_in_set(?,tbl_products.category_id)', [$productCategoryInfo]);
                            }
                        }

                        if ($product) {
                            $query->whereIn('tbl_products.id', $product);
                        }
                    })
                    ->where('tbl_products.company_id', $this->company)
//                    ->where('tbl_products.showroom_id', $this->showroomId)
                    ->orderBy('categoryName')
                    ->orderBy('productName')
                    ->get();
        }

        return view('admin.productList.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'productCategory', 'product', 'print', 'categories', 'products', 'productLists'));
    }

    public function print(Request $request) {
        $title = "Print Product List Report";
        $searchFormLink = "productList.index";
        $printFormLink = "productList.print";

        $productCategory = $request->productCategory;
        $product = $request->product;

        if ($productCategory == "" && $product == "") {
            $productLists = Product::select('tbl_categories.name as categoryName', 'tbl_products.name as productName', 'tbl_products.model_no as productModel', 'tbl_products.price as price', 'tbl_products.mrp_price as mrpPrice', 'tbl_products.haire_price as hairePrice')
                    ->join('tbl_categories', 'tbl_categories.id', '=', 'tbl_products.category_id')
//                    ->where('tbl_products.showroom_id', $this->showroomId)
                    ->where('tbl_products.company_id', $this->company)
                    ->orderBy('categoryName')
                    ->orderBy('productName')
                    ->get();
        } else {
            $productLists = Product::select('tbl_categories.name as categoryName', 'tbl_products.name as productName', 'tbl_products.model_no as productModel', 'tbl_products.price as price', 'tbl_products.mrp_price as mrpPrice', 'tbl_products.haire_price as hairePrice')
                    ->join('tbl_categories', 'tbl_categories.id', '=', 'tbl_products.category_id')
                    ->orWhere(function($query) use($productCategory, $product) {
                        if (@$productCategory) {
                            foreach ($productCategory as $productCategoryInfo) {
                                $query->orWhereRaw('find_in_set(?,tbl_products.category_id)', [$productCategoryInfo]);
                            }
                        }

                        if ($product) {
                            $query->whereIn('tbl_products.id', $product);
                        }
                    })
                    ->where('tbl_products.company_id', $this->company)
//                    ->where('tbl_products.showroom_id', $this->showroomId)
                    ->orderBy('categoryName')
                    ->orderBy('productName')
                    ->get();
        }

        $pdf = PDF::loadView('admin.productList.print', ['title' => $title, 'productLists' => $productLists]);

        return $pdf->stream('product_list.pdf');
    }

}
