<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Session;

class CheckCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        if (Session::get('customerId')) {
            return $next($request);
        }else{
            return redirect('/customer/login');
        }
    }
}
