<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\BankSetup;

class BankSetupController extends Controller
{
    public function index()
    {
    	$title = "Bank/Brunch Setup";

    	$allBank = BankSetup::orderBy('name','asc')->get();

    	return view('admin.bankSetup.index')->with(compact('title','allBank'));
    }

    public function add()
    {
    	$title = "Add Bank";
    	$formLink = "bankSetup.save";
    	$buttonName = "Save";

    	return view('admin.bankSetup.add')->with(compact('title','formLink','buttonName'));
    }

    public function save(Request $request)
    {
        $this->validation($request);

        BankSetup::create( [
        	'code' => $request->code,
            'name' => $request->bankName,
            'phone' => $request->phone,
            'address' => $request->address,           
        ]);

        return redirect(route('bankSetup.index'))->with('msg','Bank Added Successfully');
    }

    public function edit($bankId)
    {
    	$title = "Edit Bank";
    	$formLink = "bankSetup.update";
    	$buttonName = "Update";

    	$bank = BankSetup::where('id',$bankId)->first();

    	return view('admin.bankSetup.edit')->with(compact('title','formLink','buttonName','bank'));
    }

    public function update(Request $request)
    {
        $this->validation($request);

        $bankId = $request->bankId;

        $bank = BankSetup::find($bankId);

        $bank->update( [
        	'code' => $request->code,
            'name' => $request->bankName,
            'phone' => $request->phone,
            'address' => $request->address,          
        ]);

        return redirect(route('bankSetup.index'))->with('msg','Bank Updated Successfully');
    }

    public function delete(Request $request)
    {    	
        BankSetup::where('id',$request->bankId)->delete();
    }

    public function changeStatus(Request $request)
    {
        $bankId = $request->bankId;

        $bank = BankSetup::find($bankId);

        if ($bank->status == 1)
        {
            $bank->update( [               
                'status' => 0                
            ]);
        }
        else
        {
            $bank->update( [               
                'status' => 1                
            ]);
        }
    }

    public function validation(Request $request)
    {
        $this->validate(request(), [
            'code' => 'required',
            'bankName' => 'required',
        ]);
    }
}
