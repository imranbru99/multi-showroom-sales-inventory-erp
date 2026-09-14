<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CompanySetup;
use App\UserMenu;
use App\UserMenuActions;
use App\UserRoles;
use App\Admin;
use App\ShowroomSetup;
use App\StoreSetup;
use App\CoaSetup;
use App\CompanyCoa;
use DB;

class CompanySetupController extends Controller {

    public function index() {
        $title = "Company Setup";

        if (auth()->user()->role == 1) {
            $companies = CompanySetup::orderBy('id', 'desc')->get();
        } else {
            $company = CompanySetup::where('id', $this->company)->first();

            return view('admin.companySetup.company')->with(compact('title', 'company'));
        }

        return view('admin.companySetup.index')->with(compact('title', 'companies'));
    }

    public function add() {
        $title = "Add New Company";
        $formLink = "companySetup.save";
        $buttonName = "Save";

        $menus = UserMenu::whereNull('parentMenu')->whereNotIn('id', [69, 215])->where('menuStatus', 1)->orderBy('orderBy', 'asc')->get();

        return view('admin.companySetup.add')->with(compact('title', 'formLink', 'buttonName', 'menus'));
    }

    public function save(Request $request) {
        $this->validate(request(), [
            'username' => 'required|unique:admins',
        ]);

        DB::beginTransaction();

        $fix = ['69', '215'];
        $reqModules = array_merge($fix, $request->modules);
        $menus = UserMenu::with('children')
                ->whereIn('id', $reqModules)
                ->get();

        $modules = [];
        foreach ($menus as $menu) {
            $modules[] = $menu->id;
            if (count($menu->children) > 0) {
                foreach ($menu->children as $child) {
                    $modules[] = $child->id;
                    if (count($child->children) > 0) {
                        foreach ($child->children as $subchild) {
                            $modules[] = $subchild->id;
                            if (count($subchild->children) > 0) {
                                foreach ($subchild->children as $sMenu) {
                                    $modules[] = $sMenu->id;
                                }
                            }
                        }
                    }
                }
            }
        }

        $moduleActions = UserMenuActions::whereIn('parentmenuId', $modules)
                ->where('actionStatus', 1)
                ->whereNotIn('parentmenuId', [1])
                ->pluck('id')
                ->toArray();
        $modules = array_diff($modules, [1]);
        $modules = implode(',', $modules);
        $moduleActions = implode(',', $moduleActions);
        try {
            // create company
            $company = CompanySetup::create([
                        'prefix' => $request->prefix,
                        'name' => $request->companyName,
                        'trade_license' => $request->tradeLicense,
                        'vat' => $request->vat,
                        'tin' => $request->tin,
                        'email' => $request->email,
                        'auth_username' => $request->username,
                        'website' => $request->webSite,
                        'phone' => $request->phoneNumber,
                        'fax' => $request->faxNumber,
                        'address' => $request->address,
                        'status' => '1',
            ]);

            $showRoom = ShowroomSetup::create([
                        'company_id' => $company->id,
                        'prefix' => $request->prefix,
                        'name' => 'Head Office',
                        'address' => $request->address,
                        'status' => '1',
            ]);


            StoreSetup::create([
                'showroom_id' => $showRoom->id,
                'company_id' => $company->id,
                'type' => 'Branch Store',
                'name' => $showRoom->name . ' Store',
                'address' => $request->address,
                'status' => '1',
            ]);


            // create role
            $userRole = UserRoles::create([
                        'company_id' => $company->id,
                        'name' => $request->companyName,
                        'status' => '1',
                        'permission' => @$modules,
                        'actionPermission' => @$moduleActions,
            ]);

            // create user
            Admin::create([
                'role' => $userRole->id,
                'company_id' => $company->id,
                'showroomId' => $showRoom->id,
                'name' => $request->companyName,
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt(123456),
                'status' => '1',
            ]);

            $company->update([
                'role_id' => $userRole->id,
                'modules' => implode(',', $request->modules),
            ]);



            //Account Setup
            $coas = CompanyCoa::get();
            foreach($coas as $coa){
                CoaSetup::create([
                    'company_id' =>$company->id,
                    'head_code' => $coa->head_code,
                    'head_name' => $coa->head_name,
                    'parent_head_name' => $coa->parent_head_name,
                    'head_level' => $coa->head_level,
                    'head_type' => $coa->head_type,
                    'head_type' => $coa->transaction,
                    'head_type' => $coa->general_leadger,
                ]);
            }
            DB::commit();

            return redirect(route('companySetup.index'))->with('msg', 'Company Successfuly Saved');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect(route('companySetup.index'))->with('msg', 'Something Went Wrong!!!');
        }
    }

    public function edit($id) {
        $title = "Edit Company";
        $formLink = "companySetup.update";
        $buttonName = "Update";
        $company = CompanySetup::where('id', $id)->first();
        $menus = UserMenu::whereNull('parentMenu')
                ->whereNotIn('id', [69, 215])
                ->where('menuStatus', 1)
                ->orderBy('orderBy', 'asc')
                ->get();

        $pMenus = [];
        if ($company->modules) {
            $pMenus = explode(',', $company->modules);
            $pMenus = UserMenu::whereIn('id', $pMenus)->get();
        }

        return view('admin.companySetup.edit')->with(compact('title', 'formLink', 'buttonName', 'company', 'menus', 'pMenus'));
    }

    public function update(Request $request) {
        $companyId = $request->companyId;
        $company = CompanySetup::find($companyId);

        $productTypes = json_encode($request->companyProductTypes);

        if (auth()->user()->role == 1) {
            $fix = ['69', '215'];
            $reqModules = array_merge($fix, $request->modules);
            $menus = UserMenu::with('children')
                    ->whereIn('id', $reqModules)
                    ->get();

            $modules = [];
            foreach ($menus as $menu) {
                $modules[] = $menu->id;
                if (count($menu->children) > 0) {
                    foreach ($menu->children as $child) {
                        $modules[] = $child->id;
                        if (count($child->children) > 0) {
                            foreach ($child->children as $subchild) {
                                $modules[] = $subchild->id;
                                if (count($subchild->children) > 0) {
                                    foreach ($subchild->children as $sMenu) {
                                        $modules[] = $sMenu->id;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $moduleActions = UserMenuActions::whereIn('parentmenuId', $modules)
                    ->where('actionStatus', 1)
                    ->whereNotIn('parentmenuId', [1])
                    ->pluck('id')
                    ->toArray();
            $modules = array_diff($modules, [1]);
            $modules = implode(',', $modules);
            $moduleActions = implode(',', $moduleActions);

            $role = UserRoles::find($company->role_id);
            $role->update([
                'permission' => @$modules,
                'actionPermission' => @$moduleActions,
            ]);

            $company->update([
                'prefix' => $request->prefix,
                'name' => $request->companyName,
                'trade_license' => $request->tradeLicense,
                'vat' => $request->vat,
                'tin' => $request->tin,
                'email' => $request->email,
                'website' => $request->webSite,
                'product_types' => $productTypes,
                'phone' => $request->phoneNumber,
                'fax' => $request->faxNumber,
                'address' => $request->address,
                'modules' => implode(',', $request->modules),
            ]);

            $Ecoa = CoaSetup::where('company_id', $company->id)->count();
            if($Ecoa == 0){
                //Account Setup
                $coas = CompanyCoa::get();
                foreach($coas as $coa){
                    CoaSetup::create([
                        'company_id' =>$company->id,
                        'head_code' => $coa->head_code,
                        'head_name' => $coa->head_name,
                        'parent_head_name' => $coa->parent_head_name,
                        'head_level' => $coa->head_level,
                        'head_type' => $coa->head_type,
                        'head_type' => $coa->transaction,
                        'head_type' => $coa->general_leadger,
                    ]);
                }
            }
            

        } else {
            
            $company->update([
                'prefix' => $request->prefix,
                'name' => $request->companyName,
                'trade_license' => $request->tradeLicense,
                'vat' => $request->vat,
                'tin' => $request->tin,
                'email' => $request->email,
                'website' => $request->webSite,
                'product_types' => $productTypes,
                'phone' => $request->phoneNumber,
                'fax' => $request->faxNumber,
                'address' => $request->address,
            ]);

        }

        return redirect(route('companySetup.index'))->with('msg', 'Company Successfully Updated');
    }

    public function changeStatus(Request $request) {
        $company = CompanySetup::find($request->id);

        if ($company->status == 1) {
            $company->update([
                'status' => 0
            ]);
        } else {
            $company->update([
                'status' => 1
            ]);
        }
    }

}
