<?php

namespace App\Http\Controllers\Admin;

use App\AdvanceCollection;
use App\CustomerAgreement;
use App\DealerCollection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\RetailSales;
use App\InstallmentCollectionList;
use App\ProductIssueList;
use App\RetailSalesReturnProducts;
use App\SalesReturn;
use App\ShowroomSetup;
use Carbon\Carbon;
use App\Product;
use App\Helper\Stock;
use App\ShowroomProjectSetup;
use DB;
use PDF;

class DailyAverageReportController extends Controller
{



    public function index(Request $request)
    {
        $title = "Business Summary Report";
        $searchFormLink = "dailyAverageReport.index";
        $printFormLink = "dailyAverageReport.print";

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $previosDate = date('Y-m-d', strtotime("-1 day", strtotime($fromDate)));

        $print = $request->print;
        $showroom = !empty($request->showroom) ? $request->showroom : [];

        $showrooms = ShowroomSetup::where('status', 1)->get();

        if (!empty($showroom) && count($showroom) > 0) {
            $searchShowrooms = ShowroomSetup::whereIn('id', $showroom)->get();
        } else {
            $searchShowrooms = ShowroomSetup::where('status', 1)->get();
        }

        $retails = [];
        $dealerDetails = [
            'previous' => 0.00,
            'sales' => 0.00,
            'returns' =>  0.00,
            'actualSales' =>  0.00,
            'collections' =>  0.00,
            'outstanding' =>  0.00,
        ];
        if ($print) {
            foreach ($searchShowrooms as $searchShowroom) {
                $projects = ShowroomProjectSetup::where('showroom_id', $searchShowroom->id)->where('status', 1)->get();
                $projectSales = [];
                foreach ($projects as $project) {
                    $rsales = RetailSales::with('sale')
                        ->whereHas('sale', function ($query) use ($fromDate, $toDate, $searchShowroom, $project) {
                            $query->where('sale_date', '>=', $fromDate)
                                ->where('sale_date', '<=', $toDate)
                                ->where('showroom_id', $searchShowroom->id)
                                ->where('dealer_id', $project->id);
                        })
                        ->sum('sales_price');


                    $rreturns = RetailSalesReturnProducts::with('return')
                        ->whereHas('return', function ($query) use ($fromDate, $toDate, $searchShowroom, $project) {
                            $query->where('date', '>=', $fromDate)
                                ->where('date', '<=', $toDate)
                                ->where('showroom_id', $searchShowroom->id)
                                ->where('project_id', $project->id);
                        })
                        ->sum('sales_price');


                    $rcollections = InstallmentCollectionList::where('installment_collection_date', '>=', $fromDate)
                        ->where('installment_collection_date', '<=', $toDate)
                        ->where('showroom_id', $searchShowroom->id)
                        ->where('project_id', $project->id)
                        ->sum('installment_schedule_amount');

                    $ractualSales =  $rsales - $rreturns;

                    $agreement = CustomerAgreement::where('project_id', $project->id)
                                ->where('date', '>=', $fromDate)
                                ->where('date', '<=', $toDate)
                                ->sum('agreement_amount');


                    $projectSales[] = [
                        'project' => $project->name,
                        'previous' => $this->previous($previosDate, $searchShowroom->id, $project->id),
                        'sales' => number_format($rsales, 2, '.', ''),
                        'returns' => number_format($rreturns, 2, '.', ''),
                        'actualSales' => number_format($ractualSales, 2, '.', ''),
                        'collections' => number_format($rcollections, 2, '.', ''),
                        'outstanding' => number_format(($ractualSales - $rcollections), 2, '.', ''),
                        'agreement' => number_format($agreement, 2, '.', ''),
                    ];
                }

                $retails[] = [
                    'showroom' => $searchShowroom->name,
                    'info' => $projectSales,
                ];
            }


            $dsales = ProductIssueList::with('issue')
                ->whereHas('issue', function ($query) use ($fromDate, $toDate) {
                    $query->where('date', '>=', $fromDate)
                        ->where('date', '<=', $toDate);
                })
                ->sum('amount');



            $dreturns = SalesReturn::where('return_date', '>=', $fromDate)
                ->where('return_date', '<=', $toDate)
                ->sum('amount');



            $dcollection = DealerCollection::where('payment_date', '>=', $fromDate)
                ->where('payment_date', '<=', $toDate)
                ->where('remarks', '!=', 'adjust')
                ->sum('payment_amount');

            $advanceCollection = AdvanceCollection::where('date', '>=', $fromDate)
                ->where('date', '<=', $toDate)
                ->where('advance_amount', '>', 0)
                ->sum('advance_amount');

            $collection = $dcollection + $advanceCollection;

            $dactualSales =  $dsales - $dreturns;


            $dealerDetails = [
                'previous' => $this->previousDealer($previosDate),
                'sales' => number_format($dsales, 2, '.', ''),
                'returns' => number_format($dreturns, 2, '.', ''),
                'actualSales' => number_format($dactualSales, 2, '.', ''),
                'collections' => number_format($collection, 2, '.', ''),
                'outstanding' => number_format(($dactualSales - $collection), 2, '.', ''),
            ];
        }





        return view('admin.dailyAverageReport.index')->with(compact(
            'title',
            'searchFormLink',
            'printFormLink',
            'print',
            'fromDate',
            'toDate',
            'showroom',
            'showrooms',
            'retails',
            'dealerDetails',
        ));
    }

    public function print(Request $request)
    {
        $title = "Business Summary Report";

        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));
        $previosDate = date('Y-m-d', strtotime("-1 day", strtotime($fromDate)));
        $print = $request->print;
        $showroom = !empty($request->showroom) ? explode(',', $request->showroom) : [];


        if (!empty($showroom) && count($showroom) > 0) {
            $searchShowrooms = ShowroomSetup::whereIn('id', $showroom)->get();
        } else {
            $searchShowrooms = ShowroomSetup::where('status', 1)->get();
        }


        $retails = [];
        foreach ($searchShowrooms as $searchShowroom) {
            $projects = ShowroomProjectSetup::where('showroom_id', $searchShowroom->id)->where('status', 1)->get();
            $projectSales = [];
            foreach ($projects as $project) {
                $rsales = RetailSales::with('sale')
                    ->whereHas('sale', function ($query) use ($fromDate, $toDate, $searchShowroom, $project) {
                        $query->where('sale_date', '>=', $fromDate)
                            ->where('sale_date', '<=', $toDate)
                            ->where('showroom_id', $searchShowroom->id)
                            ->where('dealer_id', $project->id);
                    })
                    ->sum('sales_price');


                $rreturns = RetailSalesReturnProducts::with('return')
                    ->whereHas('return', function ($query) use ($fromDate, $toDate, $searchShowroom, $project) {
                        $query->where('date', '>=', $fromDate)
                            ->where('date', '<=', $toDate)
                            ->where('showroom_id', $searchShowroom->id)
                            ->where('project_id', $project->id);
                    })
                    ->sum('sales_price');


                $rcollections = InstallmentCollectionList::where('installment_collection_date', '>=', $fromDate)
                    ->where('installment_collection_date', '<=', $toDate)
                    ->where('showroom_id', $searchShowroom->id)
                    ->where('project_id', $project->id)
                    ->sum('installment_schedule_amount');

                $ractualSales =  $rsales - $rreturns;

                $agreement = CustomerAgreement::where('project_id', $project->id)
                                ->where('date', '>=', $fromDate)
                                ->where('date', '<=', $toDate)
                                ->sum('agreement_amount');


                $projectSales[] = [
                    'project' => $project->name,
                    'previous' => $this->previous($previosDate, $searchShowroom->id, $project->id),
                    'sales' => number_format($rsales, 2, '.', ''),
                    'returns' => number_format($rreturns, 2, '.', ''),
                    'actualSales' => number_format($ractualSales, 2, '.', ''),
                    'collections' => number_format($rcollections, 2, '.', ''),
                    'outstanding' => number_format(($ractualSales - $rcollections), 2, '.', ''),
                    'agreement' => number_format(($agreement), 2, '.', ''),
                ];
            }

            $retails[] = [
                'showroom' => $searchShowroom->name,
                'info' => $projectSales,
            ];
        }


        $dsales = ProductIssueList::with('issue')
            ->whereHas('issue', function ($query) use ($fromDate, $toDate) {
                $query->where('date', '>=', $fromDate)
                    ->where('date', '<=', $toDate);
            })
            ->sum('amount');



        $dreturns = SalesReturn::where('return_date', '>=', $fromDate)
            ->where('return_date', '<=', $toDate)
            ->sum('amount');



        $dcollection = DealerCollection::where('payment_date', '>=', $fromDate)
            ->where('payment_date', '<=', $toDate)
            ->where('remarks', '!=', 'adjust')
            ->sum('payment_amount');

        $advanceCollection = AdvanceCollection::where('date', '>=', $fromDate)
            ->where('date', '<=', $toDate)
            ->where('advance_amount', '>', 0)
            ->sum('advance_amount');

        $collection = $dcollection + $advanceCollection;

        $dactualSales =  $dsales - $dreturns;


        $dealerDetails = [
            'previous' => $this->previousDealer($previosDate),
            'sales' => number_format($dsales, 2, '.', ''),
            'returns' => number_format($dreturns, 2, '.', ''),
            'actualSales' => number_format($dactualSales, 2, '.', ''),
            'collections' => number_format($collection, 2, '.', ''),
            'outstanding' => number_format(($dactualSales - $collection), 2, '.', ''),
        ];


        $pdf = PDF::loadView('admin.dailyAverageReport.print', [
            'title' => $title,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'retails' => $retails,
            'dealerDetails' => $dealerDetails,
            'print' => $print,
        ]);

        return $pdf->stream('business_summary_report_' . $fromDate . '_to_' . $toDate . '.pdf');
    }


    private function previous($date, $showroom, $project)
    {
        $rsales = RetailSales::with('sale')
            ->whereHas('sale', function ($query) use ($date, $showroom, $project) {
                $query->where('sale_date', '<=', $date)
                    ->where('showroom_id', $showroom)
                    ->where('dealer_id', $project);
            })
            ->sum('sales_price');


        $rreturns = RetailSalesReturnProducts::with('return')
            ->whereHas('return', function ($query) use ($date, $showroom, $project) {
                $query->where('date', '<=', $date)
                    ->where('showroom_id', $showroom)
                    ->where('project_id', $project);
            })
            ->sum('sales_price');


        $rcollections = InstallmentCollectionList::where('installment_collection_date', '<=', $date)
            ->where('showroom_id', $showroom)
            ->where('project_id', $project)
            ->sum('installment_schedule_amount');

        $ractualSales =  $rsales - $rreturns;

        $outstanding = number_format(($ractualSales - $rcollections), 2, '.', '');

        return $outstanding;
    }

    private function previousDealer($date)
    {
        $dsales = ProductIssueList::with('issue')
            ->whereHas('issue', function ($query) use ($date) {
                $query->where('date', '<=', $date);
            })
            ->sum('amount');



        $dreturns = SalesReturn::where('return_date', '<=', $date)->sum('amount');



        $dcollection = DealerCollection::where('payment_date', '<=', $date)
            ->where('remarks', '!=', 'adjust')
            ->sum('payment_amount');

        $advanceCollection = AdvanceCollection::where('date', '<=', $date)
            ->where('advance_amount', '>', 0)
            ->sum('advance_amount');

        $collection = $dcollection + $advanceCollection;

        $dactualSales =  $dsales - $dreturns;

        $outstanding = number_format(($dactualSales - $collection), 2, '.', '');

        return $outstanding;
    }
}
