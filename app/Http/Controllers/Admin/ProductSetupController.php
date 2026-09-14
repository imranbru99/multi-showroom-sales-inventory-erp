<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CategorySetup;
use App\Product;
use App\ProductAdvance;
use App\ProductImage;
use DB;

class ProductSetupController extends Controller
{

    public function index()
    {
        $title = "Product Setup";

        if ($this->userRole == 1) {
            $products = Product::select('tbl_products.*', 'tbl_categories.name as catName', 'tbl_company.name as companyName')
                ->leftJoin('tbl_categories', 'tbl_categories.id', '=', 'tbl_products.category_id')
                ->leftJoin('tbl_company', 'tbl_company.id', '=', 'tbl_products.company_id')
                ->orderBy('tbl_products.id', 'dsc')
                //                    ->where('tbl_products.company_id', $this->company)
                ->orderBy('tbl_products.name', 'asc')
                ->get();
        } else {
            $products = Product::select('tbl_products.*', 'tbl_categories.name as catName', 'tbl_company.name as companyName')
                ->leftJoin('tbl_categories', 'tbl_categories.id', '=', 'tbl_products.category_id')
                ->leftJoin('tbl_company', 'tbl_company.id', '=', 'tbl_products.company_id')
                ->orderBy('tbl_products.id', 'dsc')
                ->where('tbl_products.company_id', $this->company)
                ->orderBy('tbl_products.name', 'asc')
                ->get();
        }

        return view('admin.productSetup.index')->with(compact('title', 'products'));
    }

    public function addProduct()
    {
        $title = "Add product";

        $formLink = "productSetupBasicInfo.save";
        $buttonName = "Save";

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

        $categories = DB::table('tbl_categories as tab1')
            ->select('tab1.*')
            ->leftJoin('tbl_categories as tab2', 'tab2.parent', '=', 'tab1.id')
            ->whereNull('tab2.parent')
            ->orderBy('name', 'asc')
            ->where('tab1.company_id', $this->company)
            ->get();


        return view('admin.productSetup.add')->with(compact('title', 'categories', 'formLink', 'buttonName', 'productTypes'));
    }

    public function saveProductBasicInfo(Request $request)
    {
        $this->validate(request(), [
            'productType' => 'required',
            'category' => 'required',
            'productName' => 'required',
            'productModelNo' => 'required',
            'price' => 'required',
            'mrpPrice' => 'required',
            'hairePrice' => 'required',
        ]);

        $product = Product::create([
            'product_type' => $request->productType,
            'category_id' => $request->category,
            'name' => $request->productName,
            'code' => $request->productCode,
            'productSize' => $request->productSize,
            'model_no' => $request->productModelNo,
            'color' => $request->productColor,
            'uom' => $request->productUom,
            'price' => $request->price,
            'mrp_price' => $request->mrpPrice,
            'haire_price' => $request->hairePrice,
            'warranty' => $request->warranty,
            'transport_point' => $request->transportPoint,
            'status' => $request->status,
            // 'pdtType_status' => $request->pdtType_status ,
            'showroom_id' => $this->showroomId,
            'company_id' => $this->company
        ]);


        return redirect(route('productSetup.index'))->with('msg', 'Product Added Successfully');
    }

    public function editProduct($id)
    {
        $title = "Edit product";

        $formLink = "productSetupBasicInfo.update";
        $buttonName = "Update";

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

        $categories = DB::table('tbl_categories as tab1')
            ->select('tab1.*')
            ->leftJoin('tbl_categories as tab2', 'tab2.parent', '=', 'tab1.id')
            ->whereNull('tab2.parent')
            ->orderBy('name', 'asc')
            ->where('tab1.company_id', $this->company)
            ->get();

        $product = Product::where('id', $id)->first();

        return view('admin.productSetup.edit')->with(compact('title', 'formLink', 'buttonName', 'categories', 'product', 'productTypes'));
    }

    public function updateProductBasicInfo(Request $request)
    {
        // dd($request);
        $this->validate(request(), [
            'productType' => 'required',
            'category' => 'required',
            'productName' => 'required',
            'productModelNo' => 'required',
            'price' => 'required',
            'mrpPrice' => 'required',
            'hairePrice' => 'required',
        ]);
        $productId = $request->productId;
        $product = Product::find($productId);

        $product->update([
            'product_type' => $request->productType,
            'category_id' => $request->category,
            'name' => $request->productName,
            'code' => $request->productCode,
            'productSize' => $request->productSize,
            'model_no' => $request->productModelNo,
            'color' => $request->productColor,
            'uom' => $request->productUom,
            'price' => $request->price,
            'mrp_price' => $request->mrpPrice,
            'haire_price' => $request->hairePrice,
            'warranty' => $request->warranty,
            'transport_point' => $request->transportPoint,
            'status' => $request->status,
        ]);

        $productId = $product->id;

        return redirect(route('productSetup.index'))->with('msg', 'Product Updated Successfully');
    }

    public function deleteProduct(Request $request)
    {
        Product::where('id', $request->productId)->delete();
    }

    public function changeProductStatus(Request $request)
    {
        $productId = $request->productId;

        $product = Product::find($productId);

        if ($product->status == 0) {
            $product->update([
                'status' => 1,
            ]);
        } else {
            $product->update([
                'status' => 0,
            ]);
        }
    }

    public function productType(Request $request)
    {
        $type = $request->type;

        $products = Product::where('company_id', $this->company)
            ->where('status', 1)
            ->where('product_type', $type)
            ->get();

        return $products;
    }
}
