<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Admin;
use Hash;

class AdminLoginController extends Controller {

    public function __construct() {
        $this->middleware('guest:admin')->except('adminLogout');
    }

    public function showLoginForm() {
        $title = "Small Business ERP";
        return view('auth.admin-login')->with(compact('title'));
    }

    public function login(Request $request) {
        //validate data
        $this->validate($request, [
            'username' => 'required|exists:admins,username',
            'password' => 'required|min:6',
        ]);

        $users = Admin::where('username', $request->username)->first();
        //$usersPassword = Hash::make($request->password);

        if ($users->status == 0) {
            $message = "<span class='help-block' style='color:#a94442;'><strong>You are not active for login.</strong></span>";
            return redirect(route('admin.login'))->with('msg', $message)->withInput();
        }

        /* if (@$users->password != $usersPassword) {
          $message = "<span class='help-block' style='color:#a94442;'><strong>password not matched.</strong></span>";
          return redirect(route('admin.login'))->with('passwordMessage',$message)->withInput();
          } */


        //attemt to log the admin in
        if (Auth::guard('admin')->attempt(['username' => $request->username, 'password' => $request->password], $request->remember)) {
            //if successful, then redirect to their intended location
            // return redirect(route('admin.showroom'));
            return redirect()->intended(route('admin.showroom'));
        } else {
            $message = "<span class='help-block' style='color:#a94442;'><strong>password not matched.</strong></span>";
            return redirect(route('admin.login'))->with('passwordMessage', $message)->withInput();
        }

        //if unsuccessful, then redirect back to the login with the form data
        return redirect()->back()->withInput($request->only('username', 'remember'));
    }

    public function adminLogout(Request $request) {
        $this->guard()->logout();

        return redirect(route('admin.login'));
    }

}
