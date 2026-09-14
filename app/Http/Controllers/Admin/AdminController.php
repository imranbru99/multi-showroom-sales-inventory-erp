<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Admin;
use App\UserRoles;
use App\ShowroomSetup;
use App\CompanySetup;
use DB;

class AdminController extends Controller {

    public function index() {
        $title = "Manage Users";
        if ($this->userRole == 1) {
            $users = Admin::with('company')->orderBy('name', 'asc')->get();
        } else {
            $users = Admin::with('company')->where('company_id', $this->company)->orderBy('name', 'asc')->get();
        }

        return view('admin.users.index')->with(compact('title', 'users'));
    }

    public function addUser() {
        $title = "Add New User";
        $formLink = "user.save";
        $buttonName = "Save";


        $companies = CompanySetup::where('status', 1)->get();

        if ($this->userRole == 1) {
            $userRoles = UserRoles::where('id', '!=', 1)->orderBy('name', 'ASC')->get();
            $showrooms = ShowroomSetup::orderBy('name', 'ASC')->get();
        } else {
            $userRoles = UserRoles::where('company_id', $this->company)->orderBy('name', 'ASC')->get();
            $showrooms = ShowroomSetup::where('company_id', $this->company)->orderBy('name', 'ASC')->get();
        }

        return view('admin.users.add')->with(compact('title', 'formLink', 'buttonName', 'userRoles', 'showrooms', 'companies'));
    }

    public function saveUser(Request $request) {
        $this->validation($request);

        if ($request->showrooms) {
            $showroomId = implode(',', $request->showrooms);
        }

        if (isset($request->userImage)) {
            $userImage = \App\HelperClass::UploadImage($request->userImage, 'admins', 'public/uploads/admin_images/');
        } else {
            $userImage = "";
        }

        if (auth()->user()->role == 1) {
            $users = Admin::create([
                        'role' => $request->role,
                        'company_id' => $request->company,
                        'name' => $request->name,
                        'username' => $request->username,
                        'image' => $userImage,
                        'email' => $request->email,
                        'password' => bcrypt($request->password),
                        'status' => '1',
            ]);
        } else {
            $users = Admin::create([
                        'role' => $request->role,
                        'company_id' => auth()->user()->company_id,
                        'showroomId' => @$showroomId,
                        'name' => $request->name,
                        'username' => $request->username,
                        'image' => $userImage,
                        'email' => $request->email,
                        'password' => bcrypt($request->password),
                        'status' => '1',
            ]);
        }



        // $product = Product::create($request->all());

        return redirect(route('user.index'))->with('msg', 'User Added Successfully');
    }

    public function editUser($id) {
        $title = "Edit User";
        $formLink = "user.update";
        $buttonName = "Update";

        $companies = CompanySetup::where('status', 1)->get();

        if ($this->userRole == 1) {
            $userRoles = UserRoles::where('id', '!=', 1)->orderBy('name', 'ASC')->get();
            $showrooms = ShowroomSetup::orderBy('name', 'ASC')->get();
        } else {
            $userRoles = UserRoles::where('company_id', $this->company)->orderBy('name', 'ASC')->get();
            $showrooms = ShowroomSetup::where('company_id', $this->company)->orderBy('name', 'ASC')->get();
        }

        $users = Admin::where('id', $id)->first();


        return view('admin.users.edit')->with(compact('title', 'formLink', 'buttonName', 'users', 'userRoles', 'showrooms', 'companies'));
    }

    public function updateUser(Request $request) {
        $this->validate(request(), [
            'role' => 'required',
            'name' => 'required',
            'email' => 'requiredunique:admins,email,' . $request->userId,
            'username' => 'requiredunique:admins,username,' . $request->userId,
        ]);

        $userId = $request->userId;
        $showroomId = implode(',', $request->showrooms);

        if (isset($request->userImage)) {
            $userImage = \App\HelperClass::UploadImage($request->userImage, 'admins', 'public/uploads/admin_images/');
        } else {
            $userImage = $request->previousUserImage;
        }

        $users = Admin::find($userId);

        $users->update([
            'role' => $request->role,
            'showroomId' => $showroomId,
            'name' => $request->name,
            'username' => $request->username,
            'image' => $userImage,
            'email' => $request->email,
        ]);

        // $product = Product::create($request->all());

        return redirect(route('user.index'))->with('msg', 'User Updated Successfully');
    }

    public function password($id) {
        $title = "Change Password";
        $formLink = "user.savePassword";
        $buttonName = "Update";
        $users = Admin::where('id', $id)->first();
        return view('admin.users.changePassword')->with(compact('title', 'formLink', 'buttonName', 'users'));
    }

    public function passwordChange(Request $request) {
        $this->validate(request(), [
            'password' => 'required',
        ]);
        $userId = $request->userId;

        $users = Admin::find($userId);

        $users->update([
            'password' => bcrypt($request->password),
        ]);

        // $product = Product::create($request->all());
        if (auth()->user()->role == 1) {
            return redirect(route('user.index'))->with('msg', 'Password Changed Successfully');
        } else {
            return redirect(route('admin.showroom'))->with('msg', 'Password Changed Successfully');
        }
    }

    public function changeUserStatus(Request $request) {
        $userId = $request->userId;
        $status = $request->status;

        $userInfo = Admin::where('id', $userId)->first();

        $users = Admin::find($userId);

        if ($status == 0) {
            $users->update([
                'status' => 1,
            ]);
        } else {
            $users->update([
                'status' => 0,
            ]);
        }
    }

    public function deleteUser(Request $request) {
        Admin::where('id', $request->userId)->delete();
    }

    public function userProfile(Request $request) {
        $title = 'My Profile';

        $name = "";
        $users = Admin::select('admins.*', 'user_roles.name as userRoleName')
                ->join('user_roles', 'user_roles.id', '=', 'admins.role')
                // ->join('tbl_showroom','tbl_showroom.id','=','admins.showroomId')
                ->where('admins.id', $request->userId)
                ->first();

        $showrooms = [];
        if ($users->showroomId) {
            $showrooms = explode(',', $users->showroomId);
        }
        $showroomName = ShowroomSetup::select('name')
                ->whereIn('id', $showrooms)
                ->get();

        $loop = count($showroomName);

        foreach ($showroomName as $value) {
            if ($loop == 1) {
                $name .= $value->name;
            } else {
                $name .= $value->name . ", ";
                $loop--;
            }
        }

        $userRoles = UserRoles::find($users->role);

        //        $data = ['user' => $user, 'name' => $name];
        //        return $data;

        return view('admin.users.profile')->with(compact('title', 'users', 'userRoles'));
    }

    public function validation(Request $request) {
        $this->validate(request(), [
            'role' => 'required',
            'name' => 'required',
            'email' => 'required|unique:admins',
            'username' => 'required',
            'password' => 'required',
        ]);
    }

}
