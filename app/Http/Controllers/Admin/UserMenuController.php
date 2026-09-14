<?php

namespace App\Http\Controllers\Admin;

use App\UserMenu;
use App\UserMenuActions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use URL;
use Session;
use DB;

class UserMenuController extends Controller {

    public function index() {
        $title = "User Menu";

        $menus = UserMenu::orderBy('id', 'asc')->get();
        return view('admin.usermenus.index')->with(compact('title', 'menus'));
    }

    public function add() {
        $title = "Add User Menu";
        $formLink = "usermenu.save";
        $buttonName = "Save";
        $menus = UserMenu::with('parent')->where('menuStatus', 1)->get();
        return view('admin.usermenus.add')->with(compact('title', 'formLink', 'buttonName', 'menus'));
    }

    public function save(Request $request) {
        $this->validate(request(), [
//            'menuName' => 'required|unique:user_menus',
            'menuLink' => 'required',
            'orderBy' => 'required',
            'menuStatus' => 'required',
        ]);

        $check = UserMenu::where('menuName', $request->menuName)
                ->where('parentMenu', $request->parentMenu)
                ->first();

        if ($check) {
            return redirect(route('usermenu.add'))->with('msg', 'Menu has already added');
        }

        $menu = UserMenu::create([
                    'parentMenu' => $request->parentMenu,
                    'menuName' => $request->menuName,
                    'menuLink' => $request->menuLink,
                    'menuIcon' => $request->menuIcon,
                    'orderBy' => $request->orderBy,
                    'menuStatus' => $request->menuStatus,
        ]);

        return redirect(route('usermenu.add'))->with('msg', 'User Menu Added Successfully');
    }

    public function edit($id) {
        $title = "Edit User Menu";
        $formLink = "usermenu.update";
        $buttonName = "Update";
        $menuItem = UserMenu::where('id', $id)->first();
        $menus = UserMenu::with('parent')->where('menuStatus', 1)->get();
        return view('admin.usermenus.edit')->with(compact('title', 'formLink', 'buttonName', 'menus', 'menuItem'));
    }

    public function update(Request $request) {
        $usermenuId = $request->usermenuId;
        $menu = UserMenu::find($usermenuId);

        $check = UserMenu::where('menuName', $request->menuName)
                ->where('parentMenu', $request->parentMenu)
                ->first();

//        if ($check) {
//            return redirect(route('usermenu.edit', $usermenuId))->with('msg', 'Menu has already added');
//        }
        
        $menu->update([
            'parentMenu' => $request->parentMenu,
            'menuName' => $request->menuName,
            'menuLink' => $request->menuLink,
            'menuIcon' => $request->menuIcon,
            'orderBy' => $request->orderBy,
            'menuStatus' => $request->menuStatus,
        ]);

        return redirect(route('usermenu.index'))->with('msg', 'Menu Updated Successfully');
    }

    public function status(Request $request) {
        if ($request->ajax()) {
            $data = UserMenu::find($request->menu_id);
            $data->menuStatus = $data->menuStatus ^ 1;
            $data->update();
            print_r(1);
            return;
        }
    }

    public function destroy(Request $request) {
        UserMenu::where('id', $request->usermenuId)->delete();
        UserMenuActions::where('parentmenuId', $request->usermenuId)->delete();
    }

    /* public function deleteMenu($id){
      Menu::where('id',$id)->delete();
      return redirect(route('menu.index'))->with('msg','Menu Deleted Successfully');
      } */

    public function usermenuLink($menuId) {
        $title = "User Menu Action Link";
        $menus = UserMenuActions::where('parentmenuId', $menuId)->orderBy('id', 'ASC')->get();
        return view('admin.usermenus.usermenuLink')->with(compact('title', 'menus', 'menuId'));
    }

    public function usermenuLinkAdd($menuId) {
        $title = "Add User Menu Action";
        $menus = UserMenu::where('menuStatus', 1)->get();
        return view('admin.usermenus.usermenuLinkAdd')->with(compact('title', 'menus', 'menuId'));
    }

    public function usermenuLinkSave(Request $request, $parentMenuId) {
        $this->validate(request(), [
            'menuType' => 'required',
            'actionName' => 'required',
            'actionLink' => 'required',
            'orderBy' => 'required',
        ]);
        $menu = UserMenuActions::create([
                    'parentmenuId' => $parentMenuId,
                    'menuType' => $request->menuType,
                    'actionName' => $request->actionName,
                    'actionLink' => $request->actionLink,
                    'orderBy' => $request->orderBy,
                    'actionStatus' => $request->actionStatus,
        ]);

        //return redirect(route('usermenuLink.index',$parentMenuId))->with('msg','User Menu Action Added Successfully');     
        return redirect(route('userMenu.ActionLinkAdd', $parentMenuId))->with('msg', 'User Menu Action Added Successfully');
    }

    public function usermenuLinkEdit($parentMenuId, $id) {
        $title = "Edit User Menu Action Link";
        $menuItem = UserMenuActions::where('id', $id)->first();
        return view('admin.usermenus.usermenuLinkEdit')->with(compact('title', 'menuItem', 'parentMenuId'));
    }

    public function usermenuLinkUpdate(Request $request, $parentMenuId) {
        $actionId = $request->actionId;
        $menu = UserMenuActions::find($actionId);
        $this->validate(request(), [
            'menuType' => 'required',
            'actionName' => 'required',
            'actionLink' => 'required',
            'orderBy' => 'required',
        ]);
        $menu->update([
            'parentmenuId' => $parentMenuId,
            'menuType' => $request->menuType,
            'actionName' => $request->actionName,
            'actionLink' => $request->actionLink,
            'orderBy' => $request->orderBy,
            'actionStatus' => $request->actionStatus,
        ]);

        return redirect(route('usermenuLink.index', $parentMenuId))->with('msg', 'User Menu Action Updated Successfully');
    }

    public function actionStatus(Request $request) {
        if ($request->ajax()) {
            $data = UserMenuActions::find($request->menu_id);
            $data->actionStatus = $data->actionStatus ^ 1;
            $data->update();
            print_r(1);
            return;
        }
    }

    public function actionDestroy(Request $request) {
        UserMenuActions::where('id', $request->usermenuId)->delete();
    }

    /**
     * Internal function for validation.
     *
     * @param  $request
     * @return \validation
     */
}
