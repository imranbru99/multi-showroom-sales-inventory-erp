<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CategorySetup;
use App\ManufactureSetup;
use App\ManufactureSetupList;
use App\Product;

class ManufactureController extends Controller {

    public function index() {
        $title = "Manufacture Setup";

        if ($this->userRole == 1) {
            $manufactures = ManufactureSetup::select('tbl_manufacture.*', 'tbl_categories.name', 'tbl_products.name as p_name')
                    ->leftJoin('tbl_categories', 'tbl_categories.id', '=', 'tbl_manufacture.catgorie_id')
                    ->leftJoin('tbl_products', 'tbl_products.id', '=', 'tbl_manufacture.product_id')
                    ->orderBy('tbl_manufacture.id', 'dsc')
                    ->get();
        } else {
            $manufactures = ManufactureSetup::select('tbl_manufacture.*', 'tbl_categories.name', 'tbl_products.name as p_name')
                    ->leftJoin('tbl_categories', 'tbl_categories.id', '=', 'tbl_manufacture.catgorie_id')
                    ->leftJoin('tbl_products', 'tbl_products.id', '=', 'tbl_manufacture.product_id')
                    ->where('tbl_manufacture.company_id', $this->company)
//                    ->where('tbl_manufacture.showroom_id', $this->showroomId)
                    ->orderBy('tbl_manufacture.id', 'dsc')
                    ->get();
        }

        return view('admin.manufacture.index')->with(compact('title', 'manufactures'));
    }

    public function add() {
        $title = "Add Manufacture";
        $formLink = "manufacture.save";
        $buttonName = "Save";

        $catgoires = CategorySetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('id', 'asc')
                ->get();
        $products = Product::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '0')
                ->orderBy('id', 'asc')
                ->get();

        return view('admin.manufacture.add')->with(compact('title', 'formLink', 'buttonName', 'catgoires', 'products'));
    }

    public function save(Request $request) {

        $manufacture = ManufactureSetup::create([
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'product_id' => $request->productId,
                    'catgorie_id' => $request->catagoryId,
                    'total_amount' => $request->totalAdditionAmount,
                    'product_unit' => $request->prouductionUnit,
                    'created_by' => $this->userId
        ]);


        $countProduct = count($request->partsAmount);
        if ($request->partsAmount) {

            $postdata = [];
            for ($i = 0; $i < $countProduct; $i++) {

                $postdata[] = [
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'manufacture_id' => $manufacture->id,
                    'parts_id' => $request->partstId[$i],
                    'parts_amount' => $request->partsAmount[$i],
                    'required_unit' => $request->requiredUnit[$i],
                    'created_by' => $this->userId
                ];
            }
            ManufactureSetupList::insert($postdata);
        }


        return redirect(route('manufacture.index'))->with('msg', 'Manufacture Added Successfully');
    }

    public function edit($manufactureId) {
        $title = "Edit Manufacture";
        $formLink = "manufacture.update";
        $buttonName = "Update";

        $catgoires = CategorySetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('id', 'asc')
                ->get();



        $manulist = ManufactureSetupList::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', '1')
                ->orderBy('id', 'asc')
                ->get();

        $products = Product::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('pdtType_status', '0')
                ->orderBy('id', 'asc')
                ->get();

        $manufactureList = ManufactureSetupList::select('tbl_manufacture_lists.*',
                        'tbl_products.name')
                ->leftJoin('tbl_products', 'tbl_products.id', '=', 'tbl_manufacture_lists.parts_id')
                ->where('tbl_manufacture_lists.manufacture_id', $manufactureId)
                ->first();

        $manulistget = ManufactureSetup::select('tbl_manufacture.*',
                                'tbl_manufacture_lists.*', 'tbl_products.*')
                        ->leftJoin('tbl_manufacture_lists', 'tbl_manufacture_lists.manufacture_id', '=', 'tbl_manufacture.id')
                        ->leftJoin('tbl_products', 'tbl_products.id', '=', 'tbl_manufacture.product_id')
                        ->where('tbl_manufacture.id', $manufactureId)->get();



        $manufacturesetup = ManufactureSetup::select('tbl_manufacture.*', 'tbl_products.name', 'tbl_categories.name', 'tbl_manufacture_lists.required_unit')
                ->leftJoin('tbl_products', 'tbl_products.id', '=', 'tbl_manufacture.product_id')
                ->leftJoin('tbl_manufacture_lists', 'tbl_manufacture_lists.manufacture_id', '=', 'tbl_manufacture.id')
                ->leftJoin('tbl_categories', 'tbl_categories.id', '=', 'tbl_manufacture.catgorie_id')
                ->where('tbl_manufacture.id', $manufactureId)
                ->first();

        // dd($manufacturesetup);


        return view('admin.manufacture.edit')->with(compact('title', 'formLink', 'buttonName', 'catgoires', 'manufacturesetup', 'manulist', 'manufactureList', 'products', 'manulistget'));
    }

    public function update(Request $request) {
        // dd($request->all());


        $manufacture = ManufactureSetup::where('id', $request->manufactureId)->first();


        $manufacture->update([
            'product_id' => $request->productId,
            'catgorie_id' => $request->catagoryId,
            'total_amount' => $request->totalAdditionAmount,
            'product_unit' => $request->prouductionUnit,
            'created_by' => $this->userId
        ]);

        $manufactureList = ManufactureSetupList::where('manufacture_id', $request->manufactureId)->delete();

        $countProduct = count($request->partsAmount);
        if ($request->partsAmount) {

            $postdata = [];
            for ($i = 0; $i < $countProduct; $i++) {

                $postdata[] = [
                    'company_id' => $this->company,
                    'showroom_id' => $this->showroomId,
                    'manufacture_id' => $manufacture->id,
                    'parts_id' => $request->partstId[$i],
                    'parts_amount' => $request->partsAmount[$i],
                    'required_unit' => $request->requiredUnit[$i],
                    'created_by' => $this->userId
                ];
            }
            ManufactureSetupList::insert($postdata);
        }


        return redirect(route('manufacture.index'))->with('msg', 'Employee Allowance Updated Successfully');
    }

    public function delete(Request $request) {
        ManufactureSetup::where('id', $request->allowanceId)->delete();
    }

    public function status(Request $request) {
        $employeeAllowance = ManufactureSetup::find($request->allowanceId);

        if ($employeeAllowance->status == 1) {
            $employeeAllowance->update([
                'status' => 0
            ]);
        } else {
            $employeeAllowance->update([
                'status' => 1
            ]);
        }
    }

}
