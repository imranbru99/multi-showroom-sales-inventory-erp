<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\VendorSetup;
use App\CategorySetup;
use App\Product;
use App\StaffSetup;
use App\Installment;
use App\InstallmentSchedule;
use App\CustomerProduct;

use DB;
use PDF;
use MPDF;

class ProspectiveCollectionController extends Controller
{
    public function index(Request $request)
    {
    	$title = "Prospective Collection List";
    	$searchFormLink = "prospectiveCollection.index";
    	$printFormLink = "prospectiveCollection.print";

        $tillCollectionDate = Date('Y-m-d',strtotime($request->tillCollectionDate));
        $collectorParameter = $request->collector;
        $installmentProductParameter = $request->installmentProduct;
        $print = $request->print;

    	$collectorList = StaffSetup::where('showroom_id',$this->showroomId)->orderBy('name','asc')->get();
        //$vendorList = Installment::all();
        $installmentProductList = Installment::where('showroom_id',$this->showroomId)->groupBy('product_id')->get();

        $prospectiveCollectionList = DB::table('tbl_installment_schedule')
            ->select('tbl_installment_schedule.installment_id as installmentId','tbl_installment_schedule.invoice_no as invoiceNo','tbl_installment.customer_id as customerId','tbl_installment.product_id as installmentProductId','tbl_installment.installment_collector_id as installmentCollectorId','tbl_installment.installment_collector_name as installmentCollectorName','tbl_installment.customer_product_id as customerProductId','tbl_installment_schedule.installment_schedule_date as scheduleDate','tbl_customers.name as customerName','tbl_customers.phone_no as phoneNo',DB::raw('count(tbl_installment_schedule.installment_id) AS totalDueInstallment'),DB::raw('sum(tbl_installment_schedule.installment_schedule_amount) AS totalInstallmentAmount'),'tbl_products.id as productId','tbl_products.name as productName')
            ->join('tbl_installment','tbl_installment.id','=','tbl_installment_schedule.installment_id')
            ->join('tbl_products','tbl_products.id','=','tbl_installment.product_id')
            ->join('tbl_customers','tbl_customers.id','=','tbl_installment.customer_id')
            ->where('tbl_installment_schedule.status','1')
            ->where('tbl_installment_schedule.installment_schedule_date','<=',$tillCollectionDate)
            ->where(function($query) use($collectorParameter){
                if (@$collectorParameter)
                {
                    $query->whereIn('tbl_installment.installment_collector_id',$collectorParameter);
                }
            })
            ->where(function($query) use($installmentProductParameter){
                if (@$installmentProductParameter)
                {
                    $query->whereIn('tbl_installment.product_id',$installmentProductParameter);
                }
            })
            ->where('tbl_installment_schedule.showroom_id',$this->showroomId)
            ->groupBy('tbl_installment_schedule.installment_id')
            ->get();

    	return view('admin.prospectiveCollection.index')->with(compact('title','searchFormLink','printFormLink','print','collectorList','installmentProductList','prospectiveCollectionList','tillCollectionDate','collectorParameter','installmentProductParameter'));
    }

    public function print(Request $request)
    {
    	$title = "Print Prospective Collection List";

    	$tillCollectionDate = $request->tillCollectionDate;
        $collectorParameter = $request->collector;
        $installmentProductParameter = $request->installmentProduct;

        $collectorList = StaffSetup::orderBy('name','asc')->get();

        $prospectiveCollectionList = DB::table('tbl_installment_schedule')
            ->select('tbl_installment_schedule.installment_id as installmentId','tbl_installment_schedule.invoice_no as invoiceNo','tbl_installment.customer_id as customerId','tbl_installment.product_id as installmentProductId','tbl_installment.installment_collector_id as installmentCollectorId','tbl_installment.installment_collector_name as installmentCollectorName','tbl_installment.customer_product_id as customerProductId','tbl_installment_schedule.installment_schedule_date as scheduleDate','tbl_customers.name as customerName','tbl_customers.phone_no as phoneNo',DB::raw('count(tbl_installment_schedule.installment_id) AS totalDueInstallment'),DB::raw('sum(tbl_installment_schedule.installment_schedule_amount) AS totalInstallmentAmount'),'tbl_products.id as productId','tbl_products.name as productName')
            ->join('tbl_installment','tbl_installment.id','=','tbl_installment_schedule.installment_id')
            ->join('tbl_products','tbl_products.id','=','tbl_installment.product_id')
            ->join('tbl_customers','tbl_customers.id','=','tbl_installment.customer_id')
            ->where('tbl_installment_schedule.status','1')
            ->where('tbl_installment_schedule.installment_schedule_date','<=',$tillCollectionDate)
            ->where(function($query) use($collectorParameter){
                if (@$collectorParameter)
                {
                    $query->whereIn('tbl_installment.installment_collector_id',$collectorParameter);
                }
            })
            ->where(function($query) use($installmentProductParameter){
                if (@$installmentProductParameter)
                {
                    $query->whereIn('tbl_installment.product_id',$installmentProductParameter);
                }
            })
            ->where('tbl_installment_schedule.showroom_id',$this->showroomId)
            ->groupBy('tbl_installment_schedule.installment_id')
            ->get();

        $pdf = PDF::loadView('admin.prospectiveCollection.print',['title'=>$title,'tillCollectionDate'=>$tillCollectionDate,'prospectiveCollectionList'=>$prospectiveCollectionList]);

        return $pdf->stream('prospective_collection_list');
    }
}
