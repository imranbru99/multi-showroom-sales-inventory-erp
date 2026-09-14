<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Offer;
use DB;
use PDF;
use MPDF;
use Auth;
use Session;

class OfferController extends Controller
{

    public function index()
    {
        
        $title = "Offer Management";

        $offers = DB::table('tbl_offer')
            ->where('status', 1)
            ->orderBy('id','desc')
            ->get();

        return view('admin.OfferSetup.index')->with(compact('title','offers'));
    }

    public function add()
    {
        $title = "Add Offer";
        $formLink = "offer.save";
        $buttonName = "Save";
        $products = Product::select('tbl_products.*')
            ->orderBy('id','asc')
            ->get();

        return view('admin.OfferSetup.add')->with(compact('title','formLink','buttonName','products'));
    }

    public function save(Request $request)
    {
        // dd($request->all());


        $offer = Offer::create([     
            'offerName' => $request->offerName,
            'start_date' => $request->start_date, 
            'end_date' => $request->end_date,
            'offer_type' => $request->offer_type,
            'amount' => $request->offerAmount,            
            'perAmount' => $request->percAmount,            
            'product_id' => $request->product_id,            
            'status' => 1         
        ]);

        return redirect(route('offer.index'))->with('msg','Offer Added Successfully');
    }

    public function edit($id)
    {
        $title = "Edit Offer";
        $formLink = "offer.update";
        $buttonName = "Update";
        $offer = Offer::where('id',$id)->first();
        $products = Product::select('tbl_products.*')
            ->orderBy('id','asc')
            ->get();

        return view('admin.OfferSetup.edit')->with(compact('title','formLink','buttonName','offer','products'));
    }

    public function update(Request $request)
    {
        
        $offer = Offer::find($request->offerId);

        $offer->update( [     
            'offerName' => $request->offerName,
            'start_date' => $request->start_date, 
            'end_date' => $request->end_date,
            'offer_type' => $request->offer_type,
            'amount' => $request->offerAmount,            
            'perAmount' => $request->percAmount,            
            'product_id' => $request->product_id           
        ]);

        return redirect(route('offer.index'))->with('msg','Offer Updated Successfully');
    }

    public function delete(Request $request)
    {       
        Offer::where('id',$request->offerId)->delete();
    }

    public function changeStatus(Request $request)
    {
        $offerId = $request->offerId;

        $offer = Offer::find($offerId);

        if ($offer->status == 0)
        {
            $offer->update( [               
                'status' => 1,                
            ]);
        }
        else
        {
            $offer->update( [               
                'status' => 0,                
            ]);
        }
    }

    public function validation(Request $request)
    {
        $this->validate(request(), [
            'categoryName' => 'required|string'            
        ]);
    }
}
