<?php

namespace App\Http\Controllers;

use App\CompanySetup;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\ShowroomSetup;

use Auth;
use Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->userId = Auth::user()->id;
            $this->userRole = Auth::user()->role;
            $this->company = Auth::user()->company_id;
            $this->companyInfo = CompanySetup::find(Auth::user()->company_id);
            $this->showroomId = Session::get('showroom');
            $this->showroom = ShowroomSetup::where('id', $this->showroomId)->where('status', 1)->first();

            session(['companyInfo' => $this->companyInfo]);

            // if (Session::get('showroom') == "") {
            //     return redirect()->intended(route('admin.showroom'));
            // }
            return $next($request);
        });
    }
}
