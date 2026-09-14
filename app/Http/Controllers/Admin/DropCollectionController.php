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
use DB;
use PDF;
use MPDF;

class DropCollectionController extends Controller {

    public function index(Request $request) {
        $title = "Drop Collection List";
        $searchFormLink = "dropCollection.index";
        $printFormLink = "dropCollection.print";

        $tillCollectionDate = Date('Y-m-d', strtotime($request->tillCollectionDate));
        $collectorParameter = $request->collector;
        $print = $request->print;

        $collectorList = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        $dropCollectionList = DB::table('tbl_installment_schedule')
                ->select('tbl_installment_schedule.installment_id as installmentId', 'tbl_installment_schedule.invoice_no as invoiceNo', 'tbl_installment.customer_id as customerId', 'tbl_installment.product_id as installmentProductId', 'tbl_installment.installment_collector_id as installmentCollectorId', 'tbl_installment_schedule.installment_schedule_date as scheduleDate', 'tbl_customers.name as customerName', 'tbl_customers.phone_no as phoneNo', DB::raw('count(tbl_installment_schedule.installment_id) AS totalDueInstallment'), DB::raw('sum(tbl_installment_schedule.installment_schedule_amount) AS totalInstallmentAmount'), 'tbl_products.id as productId', 'tbl_products.name as productName')
                ->join('tbl_installment', 'tbl_installment.id', '=', 'tbl_installment_schedule.installment_id')
                ->join('tbl_products', 'tbl_products.id', '=', 'tbl_installment.product_id')
                ->join('tbl_customers', 'tbl_customers.id', '=', 'tbl_installment.customer_id')
                ->where('tbl_installment_schedule.status', '1')
                ->where('tbl_installment_schedule.installment_schedule_date', '<', $tillCollectionDate)
                ->where(function($query) use($collectorParameter) {
                    if (@$collectorParameter) {
                        $query->whereIn('tbl_installment.installment_collector_id', $collectorParameter);
                    }
                })
                ->where('tbl_installment_schedule.company_id', $this->company)
                ->where('tbl_installment_schedule.showroom_id', $this->showroomId)
                ->groupBy('tbl_installment_schedule.installment_id')
                ->get();

        return view('admin.dropCollection.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'collectorList', 'dropCollectionList', 'tillCollectionDate', 'collectorParameter'));
    }

    public function print(Request $request) {
        $title = "Print Drop Collection List";

        $tillCollectionDate = $request->tillCollectionDate;
        $collectorParameter = $request->collector;
        $collectorList = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc')
                ->get();

        $dropCollectionList = DB::table('tbl_installment_schedule')
                ->select('tbl_installment_schedule.installment_id as installmentId', 'tbl_installment_schedule.invoice_no as invoiceNo', 'tbl_installment.customer_id as customerId', 'tbl_installment.product_id as installmentProductId', 'tbl_installment.installment_collector_id as installmentCollectorId', 'tbl_installment_schedule.installment_schedule_date as scheduleDate', 'tbl_customers.name as customerName', 'tbl_customers.phone_no as phoneNo', DB::raw('count(tbl_installment_schedule.installment_id) AS totalDueInstallment'), DB::raw('sum(tbl_installment_schedule.installment_schedule_amount) AS totalInstallmentAmount'), 'tbl_products.id as productId', 'tbl_products.name as productName')
                ->join('tbl_installment', 'tbl_installment.id', '=', 'tbl_installment_schedule.installment_id')
                ->join('tbl_products', 'tbl_products.id', '=', 'tbl_installment.product_id')
                ->join('tbl_customers', 'tbl_customers.id', '=', 'tbl_installment.customer_id')
                ->where('tbl_installment_schedule.status', '1')
                ->where('tbl_installment_schedule.installment_schedule_date', '<', $tillCollectionDate)
                ->where(function($query) use($collectorParameter) {
                    if (@$collectorParameter) {
                        $query->whereIn('tbl_installment.installment_collector_id', $collectorParameter);
                    }
                })
                ->where('tbl_installment_schedule.company_id', $this->company)
                ->where('tbl_installment_schedule.showroom_id', $this->showroomId)
                ->groupBy('tbl_installment_schedule.installment_id')
                ->get();

        $pdf = PDF::loadView('admin.dropCollection.print', ['title' => $title, 'tillCollectionDate' => $tillCollectionDate, 'dropCollectionList' => $dropCollectionList]);

        return $pdf->stream('drop_collection_list');
    }

}
