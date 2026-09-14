<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DealerSetup;
use App\ProductIssueList;
use App\SalesReturn;
use App\DealerCollection;
use App\ProductIssue;
use App\AdvanceCollection;
use App\StaffSetup;
use App\RegionSetup;
use App\AreaSetup;
use App\TerritorySetup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Session;
use Carbon\Carbon;

// use DB;

class DealerOutstandingRatioController extends Controller {

    public function index(Request $request) {

        $title = "Dealer Outstanding Ratio";
        $searchFormLink = "DealerOutstandingRatio.index";
        $printFormLink = "DealerOutstandingRatio.print";
        $distributions = ['region' => 'Region', 'area' => 'Area', 'territory' => 'Territory'];
        $staff = $request->staff;

        $print = $request->print;

        $staffs = StaffSetup::where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', 1)
                ->get();
        $locations = [];
        if ($request->distribution == 'region') {
            $locations = RegionSetup::where('company_id', $this->company)->get();
        } elseif ($request->distribution == 'area') {
            $locations = AreaSetup::where('company_id', $this->company)->get();
        } elseif ($request->distribution == 'territory') {
            $locations = TerritorySetup::where('company_id', $this->company)->get();
        }


        $distribution = $request->distribution;
        $location = $request->loaction;



        $all_dealers = DealerSetup::with('issue')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', 1);

        if ($distribution) {
            if ($distribution == 'region') {
                if ($location) {
                    $all_dealers = $all_dealers->where('region_id', $location);
                }
            } elseif ($distribution == 'area') {
                if ($location) {
                    $all_dealers = $all_dealers->where('area_id', $location);
                }
            } elseif ($distribution == 'territory') {
                if ($location) {
                    $all_dealers = $all_dealers->where('territory_id', $location);
                }
            }
        }

        if ($staff) {
            $all_dealers = $all_dealers->whereHas('issue', function ($q) use ($staff) {
                $q->where('sales_by', $staff);
            });
        }
        $all_dealers = $all_dealers->get();


        $outstanding = [];

        if ($print) {
            foreach ($all_dealers as $dealer) {

                $sales = ProductIssueList::with('issue.dealer', 'product')
                        ->whereHas('issue.dealer', function ($q) use ($dealer) {
                            $q->where('dealer_id', $dealer->id);
                        })
                        ->where('company_id', $this->company)
//                        ->where('showroom_id', $this->showroomId)
                        ->get();

                $latestSale = ProductIssue::with('SalesBy')->where('dealer_id', $dealer->id)->orderBy('date', 'desc')->first();

                $productIssue = ProductIssue::select('date')
                        ->where('dealer_id', $dealer->id)
                        ->orderBy('date', 'desc')
                        ->first();


                $totalSales = ProductIssueList::with('issue.dealer', 'product')
                        ->whereHas('issue.dealer', function ($q) use ($dealer) {
                            $q->where('id', $dealer->id);
                        })
                        ->sum('amount');

                $totalReturn = SalesReturn::with('dealer')
                        ->whereHas('dealer', function ($q) use ($dealer) {
                            $q->where('id', $dealer->id);
                        })
                        ->sum('amount');

                $collections = DealerCollection::with('dealer')
                        ->whereHas('dealer', function ($q) use ($dealer) {
                            $q->where('id', $dealer->id);
                        })
                        // ->where('remarks', '!=', 'adjust')
                        ->orderBy('payment_date', 'desc')
                        ->get();


                $dcollection = $collections->sum('payment_amount');

                $advanceCollection = AdvanceCollection::with('dealer')
                        ->whereHas('dealer', function ($q) use ($dealer) {
                            $q->where('id', $dealer->id);
                        })
                        ->where('advance_amount', '>', 0)
                        ->sum('advance_amount');

                $collection = $dcollection + $advanceCollection;
                // $collection = $dcollection;

                $issueReturn = SalesReturn::with('dealer')
                        ->whereHas('dealer', function ($q) use ($dealer) {
                            $q->where('id', $dealer->id);
                        })
                        ->get();

                if ($sales->sum('amount') == 0 && $collection == 0 && $issueReturn->sum('amount') == 0) {
                    continue;
                }


                // Last Collection/Sale Date
                $collectionDate = '';
                $collectionDuration = 0;
                $salesDate = '';
                $salesDuration = 0;

                if (!empty($collections) && count($collections) > 0) {
                    $collectionDate = $collections[0]->payment_date;
                    $collectionDate = date('d-m-Y', strtotime($collectionDate));
                    $collectionDuration = now()->diffInDays($collectionDate);
                }
                if (!empty($productIssue)) {
                    $salesDate = Carbon::parse($productIssue->date);
                    $salesDate = date('d-m-Y', strtotime($salesDate));
                    $salesDuration = now()->diffInDays($salesDate);
                }



                $grandTotal = $totalSales - $totalReturn;
                $totalCollection = $collection;

                $outStanding = $grandTotal - $totalCollection;

                $averagePercent = 0;
                if ($collection != 0 && $sales->sum('amount') != 0) {
                    $percent = ($collection / ($sales->sum('amount') - $issueReturn->sum('amount'))) * 100;
                    $percentage = 100 - $percent;
                    $averagePercent = number_format($percentage, 2, '.', '');
                } else if ($collection == 0) {
                    $averagePercent = 100;
                } else {
                    $averagePercent = 0;
                }

                $ld = [
                    'dealer_name' => $dealer->name,
                    'contact_no' => $dealer->mobile,
                    'short_name' => $dealer->short_name,
                    'purchase' => $sales->sum('amount') - $issueReturn->sum('amount'),
                    'collection' => round($collection),
//                    'return' => $issueReturn->sum('amount'),
                    'outstanding' => round($outStanding),
                    'averagePercent' => $averagePercent,
                    'last_collection' => $collectionDate,
                    'collection_duration' => $collectionDuration,
                    'last_sales' => $salesDate,
                    'sales_duration' => $salesDuration,
                    'sale_by' => @$latestSale->SalesBy->name,
                ];


                $outstanding[] = $ld;
            }

            $total_sales_info = 0;
            $total_collection_info = 0;
            $total_outstanding_info = 0;
            foreach ($outstanding as $allDealer) {
                $total_sales_info += $allDealer['purchase'];
                $total_collection_info += $allDealer['collection'];
                $total_outstanding_info += $allDealer['outstanding'];
            }

            $sortting = array_column($outstanding, 'averagePercent');
            array_multisort($sortting, SORT_DESC, $outstanding);
        }


//         dd($distribution);


        return view('admin.dealerOutstandingRatio.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'outstanding', 'staffs', 'staff', 'locations', 'distribution', 'distributions', 'location'));
    }

    function print(Request $request) {
        $title = "Dealer Outstanding Ratio";

        $staff = $request->staff;
        
        $staffName = StaffSetup::find($staff);

        $distribution = $request->distribution;
        $location = $request->location;

        $distributionName = '';
        if ($request->distribution == 'region') {
            $rgn = RegionSetup::find($location);
            $distributionName = $rgn->name . ' (Region)';
        } elseif ($request->distribution == 'area') {
            $are = AreaSetup::find($location);
            $distributionName = $are->name . ' (Area)';
        } elseif ($request->distribution == 'territory') {
            $tritry = TerritorySetup::find($location);
            $distributionName = $tritry->name . ' (Territory)';
        }

        $all_dealers = DealerSetup::with('issue')
                ->where('company_id', $this->company)
//                ->where('showroom_id', $this->showroomId)
                ->where('status', 1);

        if ($distribution) {
            if ($distribution == 'region') {
                if ($location) {
                    $all_dealers = $all_dealers->where('region_id', $location);
                }
            } elseif ($distribution == 'area') {
                if ($location) {
                    $all_dealers = $all_dealers->where('area_id', $location);
                }
            } elseif ($distribution == 'territory') {
                if ($location) {
                    $all_dealers = $all_dealers->where('territory_id', $location);
                }
            }
        }


        if ($staff) {
            $all_dealers = $all_dealers->whereHas('issue', function ($q) use ($staff) {
                $q->where('sales_by', $staff);
            });
        }
        $all_dealers = $all_dealers->get();


        $outstanding = [];

        foreach ($all_dealers as $dealer) {

            $sales = ProductIssueList::with('issue.dealer', 'product')
                    ->whereHas('issue.dealer', function ($q) use ($dealer) {
                        $q->where('dealer_id', $dealer->id);
                    })
                    ->get();

            $latestSale = ProductIssue::with('SalesBy')->where('dealer_id', $dealer->id)->orderBy('date', 'desc')->first();


            $productIssue = ProductIssue::select('date')
                    ->where('dealer_id', $dealer->id)
                    ->orderBy('date', 'desc')
                    ->first();


            $totalSales = ProductIssueList::with('issue.dealer', 'product')
                    ->whereHas('issue.dealer', function ($q) use ($dealer) {
                        $q->where('id', $dealer->id);
                    })
                    ->sum('amount');

            $totalReturn = SalesReturn::with('dealer')
                    ->whereHas('dealer', function ($q) use ($dealer) {
                        $q->where('id', $dealer->id);
                    })
                    ->sum('amount');

            $collections = DealerCollection::with('dealer')
                    ->whereHas('dealer', function ($q) use ($dealer) {
                        $q->where('id', $dealer->id);
                    })
                    ->where('remarks', '!=', 'adjust')
                    ->orderBy('payment_date', 'desc')
                    ->get();


            $dcollection = $collections->sum('payment_amount');

            $advanceCollection = AdvanceCollection::with('dealer')
                    ->whereHas('dealer', function ($q) use ($dealer) {
                        $q->where('id', $dealer->id);
                    })
                    ->where('advance_amount', '>', 0)
                    ->sum('advance_amount');

            $collection = $dcollection + $advanceCollection;
            // $collection = $dcollection;

            $issueReturn = SalesReturn::with('dealer')
                    ->whereHas('dealer', function ($q) use ($dealer) {
                        $q->where('id', $dealer->id);
                    })
                    ->get();

            if ($sales->sum('amount') == 0 && $collection == 0 && $issueReturn->sum('amount') == 0) {
                continue;
            }


            // Last Collection/Sale Date
            $collectionDate = '';
            $collectionDuration = 0;
            $salesDate = '';
            $salesDuration = 0;

            if (!empty($collections) && count($collections) > 0) {
                $collectionDate = $collections[0]->payment_date;
                $collectionDate = date('d-m-Y', strtotime($collectionDate));
                $collectionDuration = now()->diffInDays($collectionDate);
            }
            if (!empty($productIssue)) {
                $salesDate = Carbon::parse($productIssue->date);
                $salesDate = date('d-m-Y', strtotime($salesDate));
                $salesDuration = now()->diffInDays($salesDate);
            }



            $grandTotal = $totalSales - $totalReturn;
            $totalCollection = $collection;

            $outStanding = $grandTotal - $totalCollection;

            $averagePercent = 0;
            if ($collection != 0 && $sales->sum('amount') != 0) {
                $percent = ($collection / ($sales->sum('amount') - $issueReturn->sum('amount'))) * 100;
                $percentage = 100 - $percent;
                $averagePercent = number_format($percentage, 2, '.', '');
            } else if ($collection == 0) {
                $averagePercent = 100;
            } else {
                $averagePercent = 0;
            }

            $ld = [
                'dealer_name' => $dealer->name,
                'contact_no' => $dealer->mobile,
                'short_name' => $dealer->short_name,
                'purchase' => $sales->sum('amount') - $issueReturn->sum('amount'),
                'collection' => round($collection),
//                'return' => $issueReturn->sum('amount'),
                'outstanding' => round($outStanding),
                'averagePercent' => $averagePercent,
                'last_collection' => $collectionDate,
                'collection_duration' => $collectionDuration,
                'last_sales' => $salesDate,
                'sales_duration' => $salesDuration,
                'sale_by' => @$latestSale->SalesBy->name,
            ];


            $outstanding[] = $ld;
        }

        $total_sales_info = 0;
        $total_collection_info = 0;
        $total_outstanding_info = 0;
        foreach ($outstanding as $allDealer) {
            $total_sales_info += $allDealer['purchase'];
            $total_collection_info += $allDealer['collection'];
            $total_outstanding_info += $allDealer['outstanding'];
        }

        $sortting = array_column($outstanding, 'averagePercent');
        array_multisort($sortting, SORT_DESC, $outstanding);


        $pdf = PDF::loadView('admin.dealerOutstandingRatio.print', ['title' => $title, 'staffName' => $staffName, 'outstanding' => $outstanding, 'distributionName' => $distributionName], [], ['orientation' => 'L']);

        return $pdf->stream('dealer_outstanding_ratio.pdf');
    }

}
