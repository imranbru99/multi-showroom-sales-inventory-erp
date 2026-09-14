<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\UserRoles;
use App\UserMenu;
use App\UserMenuActions;
use App\CompanySetup;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Manage Users Role";
        if ($this->userRole == 1) {
            $userRoles = UserRoles::with('company')->orderBy('id', 'ASC')->get();
        } else {
            $userRoles = UserRoles::with('company')
                ->where('company_id', $this->company)
                ->orderBy('id', 'ASC')
                ->get();
        }

        return view('admin.userRole.index')->with(compact('title', 'userRoles'));
    }

    public function changeuserRoleStatus(Request $request)
    {
        if ($request->ajax()) {
            $data = UserRoles::find($request->userRole_id);
            $data->status = $data->status ^ 1;
            $data->update();
            print_r(1);
            return;
        }
        return redirect(route('user-roles.index'))->with('message', 'Wrong move!');
    }

    public function adduserRole()
    {
        $title = "Add User Roles";
        $formLink = "userRole.save";
        $buttonName = "Save";

        $companies = CompanySetup::where('status', 1)->get();

        return view('admin.userRole.adduserRole')->with(compact('title', 'formLink', 'buttonName', 'companies'));
    }

    public function saveuserRole(Request $request)
    {
        $this->validation($request);

        if ($this->userRole == 1) {
            $company = $request->company;
        } else {
            $company = $this->company;
        }

        $userRoles = UserRoles::create([
            'company_id' => $company,
            'name' => $request->name,
            'status' => '0',
        ]);

        // $product = Product::create($request->all());

        return redirect(route('user-roles.index'))->with('msg', 'User Role Added Successfully');
    }

    public function edituserRole($id)
    {
        $title = "Edit User Role";
        $formLink = "userRole.update";
        $buttonName = "Update";
        $userRoles = UserRoles::where('id', $id)->first();
        return view('admin.userRole.updateuserRole')->with(compact('title', 'formLink', 'buttonName', 'userRoles'));
    }

    public function updateuserRole(Request $request)
    {
        $this->validate(request(), [
            'name' => 'required',
        ]);

        $userroleId = $request->userroleId;

        $userRoles = UserRoles::find($userroleId);

        $userRoles->update([
            'name' => $request->name,
        ]);

        return redirect(route('user-roles.index'))->with('msg', 'User Role Updated Successfully');
    }

    public function deleteUserRole(Request $request)
    {
        UserRoles::where('id', $request->userRoleId)->delete();
    }

    public function validation(Request $request)
    {
        $this->validate(request(), [
            'name' => 'required',
        ]);
    }

    public function permission($id)
    {
        $title = "User Permission";
        $formLink = "userRole.permissionUpdate";
        $buttonName = "Update";
        // $userMenus = UserMenu::orderBy('id','ASC')->where('menuStatus',1)->get();
        $userRole = UserRoles::where('id', $id)->first();

        // $rootMenus = UserMenu::whereNull('parentMenu')
        //     ->orderBy('id','ASC')
        //     ->where('menuStatus',1)
        //     ->get();
        // $menus =   UserRoleController::menuUi($rootMenus);
        // dd($menus);
        $menus = [];

        return view('admin.userRole.userRolePermission')->with(compact('title', 'formLink', 'buttonName', 'menus', 'userRole'));
    }

    public function loadData(Request $request)
    {
        if ($this->userRole == 1) {
            $rootMenus = UserMenu::select(
                'id',
                'parentMenu',
                'menuName',
            )
                ->with('children')
                ->whereNull('parentMenu')
                ->orderBy('orderBy', 'ASC')
                ->where('menuStatus', 1)
                ->get();
        } else {
            $company = CompanySetup::where('id', $this->company)->first();
            $pMenus = explode(',', $company->modules);
            $rootMenus = UserMenu::select(
                'id',
                'parentMenu',
                'menuName',
            )
                ->with('children')
                ->whereNull('parentMenu')
                ->whereIn('id', $pMenus)
                ->orWhereIn('id', [215, 69])
                ->orderBy('orderBy', 'ASC')
                ->where('menuStatus', 1)
                ->get();
        }


        $userRole = UserRoles::where('id', $request->role)->first();
        $menus = UserRoleController::menuUi($rootMenus, $userRole);
        // $html = '<li>Main Menu<ul>' . $menus . '</ul></li>';

        $html = '';
        $html .= '<div class="accordion" id="userMenus">
                    <div class="card">
                        <div class="card-header bg-success" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false">     
                            <span class="title">
                            <input type="checkbox" class="select_all mr-1" onclick="selectAll()" name="select_all">
                            User Menus
                            </span>
                            <span class="accicon"><i class="fa fa-angle-down rotate-icon"></i></span>
                        </div>
                        <div id="collapseOne" class="collapse show" data-parent="#userMenus">
                            <div class="card-body">'
            . $menus .
            '</div>
                        </div>
                    </div>
                </div>';
        return $html;
    }

    public static function menuUi($menus, $userRole)
    {
        $html = ' ';
        foreach ($menus as $key => $element) {
            $userMenuAction = UserMenuActions::where('actionStatus', 1)->orderBy('orderBy', 'ASC')->where('parentmenuId', $element->id)->get();
            $rolePermission = explode(',', $userRole->permission);
            if (in_array($element->id, $rolePermission)) {
                $checked = "checked";
            } else {
                $checked = "";
            }
            if (count($element->children) > 0) {
                $html .= '<div class="card">
                            <div class="card-header bg-dark collapsed" id="col_' . $element->id . '" data-toggle="collapse" data-target="#' . $element->id . '" aria-expanded="false" aria-controls="startMenu">     
                                <span class="title">
                                <input class="parentMenu_' . $element->parentMenu . ' menu mr-1" id="cd_' . $element->id . '" type="checkbox" name="usermenu[]" value="' . $element->id . '" ' . $checked . '  data-id="' . $element->id . '" onclick="menu(' . $element->id . ');"> '
                    . $element->menuName .
                    '</span>
                                <span class="accicon"><i class="fa fa-angle-down rotate-icon"></i></span>
                            </div>
                            <div id="' . $element->id . '" class="collapse coll_' . $element->id . '" data-parent="#' . $element->id . '">
                                <div class="card-body">';

                $subchild = UserRoleController::subMenu($element->children, $userRole);
                if (!empty($subchild)) {
                    $html .= $subchild;
                }
                $html .= ' </div> </div> </div>';
            } else {
                $html .= '<div class="card">
                            <div class="card-header bg-dark collapsed" id="col_' . $element->id . '" data-toggle="collapse" data-target="#' . $element->id . '" aria-expanded="false" aria-controls="startMenu">     
                                <span class="title">
                                <input class="parentMenu_' . $element->parentMenu . ' menu mr-1" id="cd_' . $element->id . '" type="checkbox" name="usermenu[]" value="' . $element->id . '" ' . $checked . '  data-id="' . $element->id . '" onclick="menu(' . $element->id . ');"> '
                    . $element->menuName .
                    '</span>
                                <span class="accicon"><i class="fa fa-angle-down rotate-icon"></i></span>
                            </div>
                            <div id="' . $element->id . '" class="collapse coll_' . $element->id . '" data-parent="#' . $element->id . '">
                            </div>
                        </div>';
            }
        }
        return $html;
    }

    public static function subMenu($child, $userRole)
    {
        $html = ' ';
        if (!empty($child)) {
            foreach ($child as $key => $element) {
                $userMenuAction = UserMenuActions::where('actionStatus', 1)->orderBy('orderBy', 'ASC')->where('parentmenuId', $element->id)->get();
                $rolePermission = explode(',', $userRole->permission);
                if (in_array($element->id, $rolePermission)) {
                    $checked = "checked";
                } else {
                    $checked = "";
                }
                if (count($element->children) > 0) {
                    $html .= '<div class="card">
                            <div class="card-header bg-success collapsed" id="col_' . $element->id . '" data-toggle="collapse" data-target="#' . $element->id . '" aria-expanded="false" aria-controls="startMenu">     
                                <span class="title">
                                <input class="parentMenu_' . $element->parentMenu . ' menu mr-1" id="cd_' . $element->id . '" type="checkbox" name="usermenu[]" value="' . $element->id . '" ' . $checked . '  data-id="' . $element->id . '" onclick="menu(' . $element->id . ');"> '
                        . $element->menuName .
                        '</span>
                                <span class="accicon"><i class="fa fa-angle-down rotate-icon"></i></span>
                            </div>
                            <div id="' . $element->id . '" class="collapse coll_' . $element->id . '" data-parent="#' . $element->id . '">
                                <div class="card-body">';
                    $subchild = UserRoleController::subMenu($element->children, $userRole);
                    if (!empty($subchild)) {
                        $html .= $subchild;
                    }
                    $html .= '</div> </div> </div>';
                } else {
                    $html .= '<div class="card">
                            <div class="card-header bg-info collapsed" id="col_' . $element->id . '" data-toggle="collapse" data-target="#' . $element->id . '" aria-expanded="false" aria-controls="startMenu">     
                                <span class="title">
                                <input class="parentMenu_' . $element->parentMenu . ' menu mr-1" id="cd_' . $element->id . '" type="checkbox" name="usermenu[]" value="' . $element->id . '" ' . $checked . '  data-id="' . $element->id . '" onclick="menu(' . $element->id . ');"> '
                        . $element->menuName .
                        '</span>
                                <span class="accicon"><i class="fa fa-angle-down rotate-icon"></i></span>
                            </div>
                            <div id="' . $element->id . '" class="collapse coll_' . $element->id . '" data-parent="#' . $element->id . '">
                                <div class="card-body">
                                <div class="row">';
                    foreach ($userMenuAction as $action) {

                        $actionPermission = explode(',', $userRole->actionPermission);
                        if (in_array($action->id, $actionPermission)) {
                            $actionChecked = "checked";
                        } else {
                            $actionChecked = "";
                        }
                        // $html  .= '<div class="col-md-2">';
                        $html .= '<div class="col-md-2"><input class="childMenu_' . $action->parentmenuId . '" type="checkbox" name="usermenuAction[]" style="margin-top: 4px;" value="' . $action->id . '" style="margin-bottom: 8px;" ' . $actionChecked . '> <span>' . $action->actionName . '</span></div>';
                        // $html .= '</div>';
                    }
                    $html .= '</div> </div> </div> </div>';
                }
            }
        }
        return $html;
    }

    public function permissionUpdate(Request $request)
    {

        // dd($request->all());
        $userroleId = $request->userroleId;
        $userRoles = UserRoles::find($userroleId);

        $usermenus = implode(',', $request->usermenu);

        if (@$request->usermenuAction) {
            $usermenuAction = implode(',', @$request->usermenuAction);
        } else {
            $usermenuAction = '';
        }
        $userRoles->update([
            'permission' => @$usermenus,
            'actionPermission' => @$usermenuAction,
        ]);

        return redirect(route('user-roles.index'))->with('msg', 'User Role Permission Updated Successfully');
    }
}
