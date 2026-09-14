<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use PDF;
use App\InvoiceSetup;
use App\CashCollection;
use App\Product;
use App\CustomerRegistrationSetup;
use App\StaffSetup;

use DB;

class CashCollectionController extends Controller
{
    public function index()
    {
    	$title = "Individual Collection";

        $cashCollections = CashCollection::select('tbl_cash_collection.*','tbl_staffs.name as collectorName')
            ->leftJoin('tbl_staffs','tbl_staffs.id','=','tbl_cash_collection.collector_id')
            // ->where('tbl_cash_collection.showroom_id',$this->showroomId)
            ->get();

    	return view('admin.cashCollection.index')->with(compact('title','cashCollections'));
    }

    public function add()
    {
    	$title = "New Individual Collection";
        $formLink = "cashCollection.save";
        $buttonName = "Save";

        $invoiceList = InvoiceSetup::select('tbl_invoice.*')
            ->leftJoin('tbl_cash_collection','tbl_cash_collection.invoice_id','=','tbl_invoice.id')
            ->whereNull('tbl_cash_collection.invoice_id')
            ->where('tbl_invoice.collection_type','=','Cash')
            ->where('tbl_invoice.showroom_id',$this->showroomId)
            ->orderBy('tbl_invoice.id','asc')
            ->get();

        $collectorList = StaffSetup::where('showroom_id',$this->showroomId)->orderBy('name','asc')->get();

    	return view('admin.cashCollection.add')->with(compact('title','formLink','buttonName','invoiceList','collectorList'));
    }

    public function save(Request $request)
    {
        // dd($request->all());
    	$collectionDate = date('Y-m-d',strtotime($request->collectionDate));
        $collector = StaffSetup::where('id',$request->collectorId)->first();

    	$cashCollection = CashCollection::create( [
            'showroom_id' => $this->showroomId,
            'invoice_id' => $request->invoiceId,        
            'collector_id' => $request->collectorId,       
            'collector_name' => $collector->name,     
            'collection_no' => $request->collectionNo,       
            'invoice_amount' => $request->invoiceAmount,       
            'previous_collection' => $request->previousCollection,
            'collection_date' => $collectionDate,       
            'collection_amount' => $request->collectionAmount,       
            'current_due' => $request->currentDue, 
            'remarks' => $request->remarks,
            'created_by' => $this->userId
        ]);

        $collectionAmount = InvoiceSetup::select('tbl_invoice.customer_product_price as customerProductPrice',DB::raw('SUM(tbl_cash_collection.collection_amount) as cashCollectionAmount'))
            ->leftJoin('tbl_cash_collection','tbl_cash_collection.invoice_id','=','tbl_invoice.id')
            ->where('tbl_invoice.collection_type','!=','Installment')
            ->where('tbl_invoice.showroom_id',$this->showroomId)
            ->where('tbl_invoice.id',$request->invoiceId)
            ->groupBy('tbl_invoice.id')
            ->first();

        // if($collectionAmount->customerProductPrice == $collectionAmount->cashCollectionAmount)
        // {
        //     $invoice = InvoiceSetup::find($request->invoiceId);

        //     $invoice->update([
        //         'status' => '0'
        //     ]);
        // }

        return redirect(route('cashCollection.add'))->with('msg', 'Cash Payment Collected Successfully');
    }

    public function edit($id)
    {
        $title = "Edit Cash Collection";
        $formLink = "cashCollection.update";
        $buttonName = "Update";

        $cashCollection = CashCollection::select('tbl_cash_collection.*','tbl_staffs.name as collectorName')
            ->leftJoin('tbl_staffs','tbl_staffs.id','=','tbl_cash_collection.collector_id')
            ->where('tbl_cash_collection.id',$id)
            ->first();

        $invoice = InvoiceSetup::where('id',$cashCollection->invoice_id)->first();
        $product = Product::where('id',$invoice->product_id)->first();

        return view('admin.cashCollection.edit')->with(compact('title','formLink','buttonName','cashCollection','invoice','product'));
    }

    public function update(Request $request)
    {
    	$collectionId = $request->collectionId;
    	$cashCollection = CashCollection::find($collectionId);
    	$collectionDate = date('Y-m-d',strtotime($request->collectionDate));
    	$cashCollection->update( [
            'showroom_id' => $this->showroomId,      
            'invoice_amount' => $request->invoiceAmount,       
            'previous_collection' => $request->previousCollection,
            'collection_date' => $collectionDate,       
            'collection_amount' => $request->collectionAmount,       
            'current_due' => $request->currentDue, 
            'remarks' => $request->remarks,
            'updated_by' => $this->userId         
        ]);

        return redirect(route('cashCollection.index'))->with('msg', 'Cash Payment Collection Update Successfully');
    }

    public function print($collectionId)
    {
        $title = "Money Receipt";
        $cashCollection = CashCollection::Where('id',$collectionId)->first();
        $invoice = InvoiceSetup::Where('id',$cashCollection->invoice_id)->first();
        $customer = CustomerRegistrationSetup::Where('id',$invoice->customer_id)->first();
        $product = Product::where('id',$invoice->product_id)->first();

        $pdf = PDF::loadView('admin.cashCollection.print',['title'=>$title,'cashCollection'=>$cashCollection,'invoice'=>$invoice,'customer'=>$customer,'product'=>$product]);

        return $pdf->stream('money_receipt.pdf');
    }

    public function delete(Request $request)
    {
        $cashCollection = CashCollection::find($request->collectionId);

        CashCollection::where('id',$request->collectionId)->delete();
    }

    public function getInvoiceInformation(Request $request)
    {
        $invoiceId = $request->invoiceId;

        $invoice = InvoiceSetup::where('id',$invoiceId)->first();

        $product = Product::where('id',$invoice->product_id)->first();
        $previous_collection = CashCollection::where('showroom_id',$this->showroomId)->where('invoice_id',$invoiceId)->sum('collection_amount');

        if($request->ajax()){
        return response()
                ->json([
                    'invoice'=>$invoice,
                    'product'=>$product,
                    'previous_collection'=>$previous_collection,
                ]);
            }
    }
}
