<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ShowroomSetup;
use App\Admin;
use App\RetailSale;
use App\InstallmentCollectionList;
use App\DealerSetup;
use App\DealerCollection;
use App\StaffSetup;
use App\SalesReturn;
use App\AdvanceCollection;
use App\ProductIssueList;
use App\ProductionIsuuList;
use App\PaymentToCompany;
use App\Lifting;
use App\LiftingProduct;
use App\LiftingReturn;
use App\LiftingReturnProduct;
use App\RegionSetup;
use App\CustomerRegistration;
use App\RetailSales;
use App\RetailSalesReturnProducts;
use App\Helper\Stock;
use DB;
use Auth;
use Session;
use Hash;
use Carbon\Carbon;

class HomeController extends Controller {

    public function __construct() {
        $this->middleware('auth:admin');
    }

    public function index() {
        $title = "Dashboard";
        $company = auth()->user()->company_id;

        $dealers = DealerSetup::where('company_id', $company)
                ->where('status', 1)
                ->count();


        // Monthly Slaes for flow
        $end = date('Y-m-d', strtotime(now()->subDays(30)));
        $now = date('Y-m-d');
        $dealerSales = DB::table('view_product_issue_history')
                ->where('companyId', $company)
                ->where('date', '>=', $end)
                ->where('date', '<=', $now)
                ->sum('issueAmount');

        $retailSales = RetailSales::with('sale')
                ->whereHas('sale', function ($q) use ($end, $now) {
                    $q->where('sale_date', '>=', $end)
                    ->where('sale_date', '<=', $now);
                })
                ->where('company_id', $company)
                ->sum('sales_price');

        $retailReturn = RetailSalesReturnProducts::with('return')
                ->whereHas('return', function ($q) use ($end, $now) {
                    $q->where('date', '>=', $end)
                    ->where('date', '<=', $now);
                })
                ->where('company_id', $company)
                ->sum('sales_price');

        $retailCollection = InstallmentCollectionList::where('company_id', $company)
                ->where('installment_collection_date', '>=', $end)
                ->where('installment_collection_date', '<=', $now)
                ->sum('installment_schedule_amount');


        $dealerCollection = DealerCollection::where('company_id', $company)
                ->where('payment_date', '>=', $end)
                ->where('payment_date', '<=', $now)
                ->sum('payment_amount');

        $montlysales = ($dealerSales + $retailSales) - $retailReturn;
        $monthlycollection = $dealerCollection + $retailCollection;

        // Dealer Chart
        $total_sales_collections = [];
        for ($i = 30; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime(now()->subDays($i)));

            $dealerAllSales = DB::table('view_product_issue_history')
                    ->select('date', 'issueAmount')
                    ->where('companyId', $company)
                    ->where('date', $day)
                    ->orderby('date', 'asc')
                    ->get();

            $total_amount = 0;
            foreach ($dealerAllSales as $sale) {
                $total_amount += $sale->issueAmount;
            }

            $allcollection = DealerCollection::where('payment_date', $day)
                    ->where('company_id', $company)
                    ->orderBy('payment_date', 'desc')
                    ->get();

            $total_collection = 0;
            foreach ($allcollection as $collection) {
                $total_collection += $collection->payment_amount;
            }


            $rSales = RetailSales::with('sale')
                    ->whereHas('sale', function ($q) use ($day) {
                        $q->where('sale_date', $day);
                    })
                    ->where('company_id', $company)
                    ->sum('sales_price');

            $rReturn = RetailSalesReturnProducts::with('return')
                    ->whereHas('return', function ($q) use ($day) {
                        $q->where('date', $day);
                    })
                    ->where('company_id', $company)
                    ->sum('sales_price');

            $retailActual = $rSales - $rReturn;

            $total_amount += $retailActual;

            $retailCollection = InstallmentCollectionList::where('company_id', $company)
                    ->where('installment_collection_date', $day)
                    ->sum('installment_schedule_amount');

            $total_collection += $retailCollection;

            $info = [
                'total_sales' => number_format($total_amount, 2, '.', ''),
                'total_collection' => number_format($total_collection, 2, '.', ''),
                'date' => date('d-m', strtotime($day)),
            ];

            $total_sales_collections[] = $info;
        }


        $showroom = auth()->user()->showroom_id;
        $Regionsales = [];

        $regions = RegionSetup::where('status', 1)
                ->where('company_id', $company)
//                ->where('showroom_id', $showroom)
                ->get();

        foreach ($regions as $region) {
            $regionSales = ProductIssueList::with('issue.dealer')
                    ->whereHas('issue.dealer', function ($q) use ($region) {
                        $q->where('region_id', $region->id);
                    })
                    ->where('company_id', $company)
                    ->sum('amount');

            $regionReturn = SalesReturn::with('dealer')
                    ->whereHas('dealer', function ($q) use ($region) {
                        $q->where('region_id', $region->id);
                    })
                    ->where('company_id', $company)
                    ->sum('amount');

            $actualregionSales = $regionSales - $regionReturn;

            $Regionsales[] = [
                'name' => $region->name,
                'amount' => number_format($actualregionSales, 2, '.', '')
            ];
        }
        if (auth()->user()->role == 1) {
            return view('admin.index')->with(compact('title', 'total_sales_collections', 'montlysales', 'monthlycollection', 'Regionsales'));
        } else {
            return view('admin.blank')->with(compact('title'));
        }
        // return view('home');
    }

    public function showroom() {
        $title = "Dashboard";
        $formLink = "admin.loginWithShowroom";
        $company = auth()->user()->company_id;
        Session::put('showroom', '');

        $userId = Auth::user()->id;

        $userInfo = Admin::where('id', $userId)->first();





        if ($userInfo->role == 1) {
            $showrooms = ShowroomSetup::all();
        } else {
            $showrooms = explode(',', $userInfo->showroomId);
            $showrooms = ShowroomSetup::all()->whereIn('id', $showrooms);
        }




        if ($showrooms->count() == 1) {
            Session::put('showroom', $showrooms->first()->id);

            $dealers = DealerSetup::where('company_id', $company)
                    ->where('status', 1)
                    ->count();


            // Monthly Slaes for flow
            $end = date('Y-m-d', strtotime(now()->subDays(30)));
            $now = date('Y-m-d');
            $dealerSales = DB::table('view_product_issue_history')
                    ->where('companyId', $company)
                    ->where('date', '>=', $end)
                    ->where('date', '<=', $now)
                    ->sum('issueAmount');

            $retailSales = RetailSales::with('sale')
                    ->whereHas('sale', function ($q) use ($end, $now) {
                        $q->where('sale_date', '>=', $end)
                        ->where('sale_date', '<=', $now);
                    })
                    ->where('company_id', $company)
                    ->sum('sales_price');

            $retailReturn = RetailSalesReturnProducts::with('return')
                    ->whereHas('return', function ($q) use ($end, $now) {
                        $q->where('date', '>=', $end)
                        ->where('date', '<=', $now);
                    })
                    ->where('company_id', $company)
                    ->sum('sales_price');

            $retailCollection = InstallmentCollectionList::where('company_id', $company)
                    ->where('installment_collection_date', '>=', $end)
                    ->where('installment_collection_date', '<=', $now)
                    ->sum('installment_schedule_amount');


            $dealerCollection = DealerCollection::where('company_id', $company)
                    ->where('payment_date', '>=', $end)
                    ->where('payment_date', '<=', $now)
                    ->sum('payment_amount');

            $montlysales = ($dealerSales + $retailSales) - $retailReturn;
            $monthlycollection = $dealerCollection + $retailCollection;

            // Dealer Chart
            $total_sales_collections = [];
            for ($i = 30; $i >= 0; $i--) {
                $day = date('Y-m-d', strtotime(now()->subDays($i)));

                $allsales = DB::table('view_product_issue_history')
                        ->select('date', 'issueAmount')
                        ->where('companyId', $company)
                        ->where('date', $day)
                        ->orderby('date', 'asc')
                        ->get();

                $total_amount = 0;
                foreach ($allsales as $sale) {
                    $total_amount += $sale->issueAmount;
                }

                $dcollection = DealerCollection::where('company_id', $company)
                        ->where('payment_date', $day)
                        ->where('remarks', '!=', 'adjust')
                        ->sum('payment_amount');

                $advanceCollection = AdvanceCollection::where('company_id', $company)
                        ->where('date', $day)
                        ->where('advance_amount', '>', 0)
                        ->sum('advance_amount');

                $retailCollection = InstallmentCollectionList::where('company_id', $company)
                        ->where('installment_collection_date', $day)
                        ->sum('installment_schedule_amount');

                $total_collection = $dcollection + $advanceCollection + $retailCollection;

                $retailCollection = InstallmentCollectionList::where('company_id', $company)
                        ->where('installment_collection_date', $day)
                        ->sum('installment_schedule_amount');

                $total_collection += $retailCollection;

                $info = [
                    'total_sales' => number_format($total_amount, 2, '.', ''),
                    'total_collection' => number_format($total_collection, 2, '.', ''),
                    'date' => date('d-m', strtotime($day)),
                ];

                $total_sales_collections[] = $info;
            }

            $showroom = auth()->user()->showroom_id;
            $Regionsales = [];

            $regions = RegionSetup::where('status', 1)
                    ->where('company_id', $company)
//                ->where('showroom_id', $showroom)
                    ->get();

            foreach ($regions as $region) {
                $regionSales = ProductIssueList::with('issue.dealer')
                        ->whereHas('issue.dealer', function ($q) use ($region) {
                            $q->where('region_id', $region->id);
                        })
                        ->where('company_id', $company)
                        ->sum('amount');

                $regionReturn = SalesReturn::with('dealer')
                        ->whereHas('dealer', function ($q) use ($region) {
                            $q->where('region_id', $region->id);
                        })
                        ->where('company_id', $company)
                        ->sum('amount');

                $actualregionSales = $regionSales - $regionReturn;

                $Regionsales[] = [
                    'name' => $region->name,
                    'amount' => number_format($actualregionSales, 2, '.', '')
                ];
            }

//            if (auth()->user()->role == 1) {

            return view('admin.index')->with(compact('title', 'total_sales_collections', 'montlysales', 'monthlycollection', 'Regionsales'));
//            } else {
//                return view('admin.blank')->with(compact('title'));
//            }
        }


        // echo $showroomId = Session::get('showroom'); exit();
        // Session::put('showroom', 7);

        if ($showrooms->count() > 1) {

            return view('admin.showroom')->with(compact(
                                    'title',
                                    'formLink',
                                    'showrooms'
            ));
        } else {
            Session::put('showroom', '');
            $montlysales = 0;
            $monthlycollection = 0;

            // Dealer Chart
            $total_sales_collections = [];

            return view('admin.index')->with(compact('title', 'total_sales_collections', 'montlysales', 'monthlycollection'));
        }

        // return view('admin.showroom')->with(compact(
        //     'title',
        //     'formLink',
        //     'showrooms'
        // ));
    }

    public function loginWithShowroom(Request $request) {
        $title = "Dashboard";
        $showroomId = $request->showroom;

        Session::put('showroom', $showroomId);
        $company = auth()->user()->company_id;


        $dealers = DealerSetup::where('company_id', $company)
                ->where('status', 1)
                ->count();

        // Monthly Slaes for flow
        $end = date('Y-m-d', strtotime(now()->subDays(30)));
        $now = date('Y-m-d');
        $dealerSales = DB::table('view_product_issue_history')
                ->where('companyId', $company)
                ->where('date', '>=', $end)
                ->where('date', '<=', $now)
                ->sum('issueAmount');

        $retailSales = RetailSales::with('sale')
                ->whereHas('sale', function ($q) use ($end, $now) {
                    $q->where('sale_date', '>=', $end)
                    ->where('sale_date', '<=', $now);
                })
                ->where('company_id', $company)
                ->sum('sales_price');

        $retailReturn = RetailSalesReturnProducts::with('return')
                ->whereHas('return', function ($q) use ($end, $now) {
                    $q->where('date', '>=', $end)
                    ->where('date', '<=', $now);
                })
                ->where('company_id', $company)
                ->sum('sales_price');

        $retailCollection = InstallmentCollectionList::where('company_id', $company)
                ->where('installment_collection_date', '>=', $end)
                ->where('installment_collection_date', '<=', $now)
                ->sum('installment_schedule_amount');


        $dealerCollection = DealerCollection::where('company_id', $company)
                ->where('payment_date', '>=', $end)
                ->where('payment_date', '<=', $now)
                ->sum('payment_amount');

        $montlysales = ($dealerSales + $retailSales) - $retailReturn;
        $monthlycollection = $dealerCollection + $retailCollection;

        // Dealer Chart
        $total_sales_collections = [];
        for ($i = 30; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime(now()->subDays($i)));

            $dealerAllSales = DB::table('view_product_issue_history')
                    ->select('date', 'issueAmount')
                    ->where('companyId', $company)
                    ->where('date', $day)
                    ->orderby('date', 'asc')
                    ->get();

            $total_amount = 0;
            foreach ($dealerAllSales as $sale) {
                $total_amount += $sale->issueAmount;
            }

            $allcollection = DealerCollection::where('payment_date', $day)
                    ->where('company_id', $company)
                    ->orderBy('payment_date', 'desc')
                    ->get();

            $total_collection = 0;
            foreach ($allcollection as $collection) {
                $total_collection += $collection->payment_amount;
            }


            $rSales = RetailSales::with('sale')
                    ->whereHas('sale', function ($q) use ($day) {
                        $q->where('sale_date', $day);
                    })
                    ->where('company_id', $company)
                    ->sum('sales_price');

            $rReturn = RetailSalesReturnProducts::with('return')
                    ->whereHas('return', function ($q) use ($day) {
                        $q->where('date', $day);
                    })
                    ->where('company_id', $company)
                    ->sum('sales_price');

            $retailActual = $rSales - $rReturn;

            $total_amount += $retailActual;

            $retailCollection = InstallmentCollectionList::where('company_id', $company)
                    ->where('installment_collection_date', $day)
                    ->sum('installment_schedule_amount');

            $total_collection += $retailCollection;

            $info = [
                'total_sales' => number_format($total_amount, 2, '.', ''),
                'total_collection' => number_format($total_collection, 2, '.', ''),
                'date' => date('d-m', strtotime($day)),
            ];

            $total_sales_collections[] = $info;
        }

        $showroom = auth()->user()->showroom_id;
        $Regionsales = [];

        $regions = RegionSetup::where('status', 1)
                ->where('company_id', $company)
//                ->where('showroom_id', $showroom)
                ->get();

        foreach ($regions as $region) {
            $regionSales = ProductIssueList::with('issue.dealer')
                    ->whereHas('issue.dealer', function ($q) use ($region) {
                        $q->where('region_id', $region->id);
                    })
                    ->where('company_id', $company)
                    ->sum('amount');

            $regionReturn = SalesReturn::with('dealer')
                    ->whereHas('dealer', function ($q) use ($region) {
                        $q->where('region_id', $region->id);
                    })
                    ->where('company_id', $company)
                    ->sum('amount');

            $actualregionSales = $regionSales - $regionReturn;

            $Regionsales[] = [
                'name' => $region->name,
                'amount' => number_format($actualregionSales, 2, '.', '')
            ];
        }

        return view('admin.index')->with(compact('title', 'showroomId', 'total_sales_collections', 'montlysales', 'monthlycollection', 'Regionsales'));
    }

    public function message_md() {
        return view('admin.settings.message_md');
    }

    public function message_md_ajax(Request $request) {
        \App\helperClass::_writeNammedFile($request->message, 'message_md.txt');

        return redirect(route('settings.message_md'))->with('message', 'updated successfully');
    }

    public function topTwelveDealers() {
        //Top 5 Dealers
        $company = auth()->user()->company_id;
        $all_dealers = DealerSetup::where('company_id', $company)
                ->where('status', 1)
                ->get();

        $allDealerInfo = [];

        foreach ($all_dealers as $dealer) {

            $sales = ProductIssueList::with('issue.dealer', 'product')
                    ->whereHas('issue.dealer', function ($q) use ($dealer) {
                        $q->where('id', $dealer->id);
                    })
                    ->get();

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

            $dcollection = DealerCollection::with('dealer')
                    ->whereHas('dealer', function ($q) use ($dealer) {
                        $q->where('id', $dealer->id);
                    })
                    ->where('remarks', '!=', 'adjust')
                    ->sum('payment_amount');

            $advanceCollection = AdvanceCollection::with('dealer')
                    ->whereHas('dealer', function ($q) use ($dealer) {
                        $q->where('id', $dealer->id);
                    })
                    ->where('advance_amount', '>', 0)
                    ->sum('advance_amount');

            $collection = $dcollection + $advanceCollection;

            $issueReturn = SalesReturn::with('dealer')
                    ->whereHas('dealer', function ($q) use ($dealer) {
                        $q->where('id', $dealer->id);
                    })
                    ->get();

            if ($sales->sum('amount') == 0 && $collection == 0 && $issueReturn->sum('amount') == 0) {
                continue;
            }


            $grandTotal = $totalSales - $totalReturn;
            $totalCollection = $collection;

            $outStanding = $grandTotal - $totalCollection;

            $averagePercent = 0;
            if ($collection != 0 && $sales->sum('amount') != 0) {
                $percent = ($collection / ($sales->sum('amount') - $issueReturn->sum('amount'))) * 100;
                $percentage = 100 - $percent;
                $averagePercent = number_format($percentage, 2, '.', '');
            } else {
                $averagePercent = 0;
            }

            $ld = [
                'dealer_name' => $dealer->name,
                'short_name' => $dealer->short_name,
                'purchase' => number_format($sales->sum('amount'), 2, '.', ''),
                'collection' => number_format($collection, 2, '.', ''),
                // 'balance' => $sales->sum('amount') - $collection - $issueReturn->sum('amount'),
                'outstanding' => number_format($outStanding, 2, '.', ''),
                'averagePercent' => $averagePercent,
            ];


            $allDealerInfo[] = $ld;
        }

        $total_sales_info = 0;
        $total_collection_info = 0;
        $total_outstanding_info = 0;
        foreach ($allDealerInfo as $allDealer) {
            $total_sales_info += $allDealer['purchase'];
            $total_collection_info += $allDealer['collection'];
            $total_outstanding_info += $allDealer['outstanding'];
        }

        $sortting = array_column($allDealerInfo, 'averagePercent');
        array_multisort($sortting, SORT_DESC, $allDealerInfo);
        $top_dealers = array_slice($allDealerInfo, 0, 12);

        return $data = [
            'top_dealers' => $top_dealers,
            'total_sales_info' => number_format($total_sales_info, 2, '.', ''),
            'total_collection_info' => number_format($total_collection_info, 2, '.', ''),
            'total_outstanding_info' => number_format($total_outstanding_info, 2, '.', ''),
            'dealers' => $all_dealers->count(),
        ];
    }

    public function topFiveDealers() {
        $company = auth()->user()->company_id;
        $all_dealers = DealerSetup::where('company_id', $company)
                ->where('status', 1)
                ->get();
        $allDealerInfo = [];

        foreach ($all_dealers as $dealer) {

            $sales = ProductIssueList::with('issue.dealer', 'product')
                    ->whereHas('issue.dealer', function ($q) use ($dealer) {
                        $q->where('id', $dealer->id);
                    })
                    ->get();

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

            $dcollection = DealerCollection::with('dealer')
                    ->whereHas('dealer', function ($q) use ($dealer) {
                        $q->where('id', $dealer->id);
                    })
                    ->where('remarks', '!=', 'adjust')
                    ->sum('payment_amount');

            $advanceCollection = AdvanceCollection::with('dealer')
                    ->whereHas('dealer', function ($q) use ($dealer) {
                        $q->where('id', $dealer->id);
                    })
                    ->where('advance_amount', '>', 0)
                    ->sum('advance_amount');

            $collection = $dcollection + $advanceCollection;

            $issueReturn = SalesReturn::with('dealer')
                    ->whereHas('dealer', function ($q) use ($dealer) {
                        $q->where('id', $dealer->id);
                    })
                    ->get();

            if ($sales->sum('amount') == 0 && $collection == 0 && $issueReturn->sum('amount') == 0) {
                continue;
            }


            $grandTotal = $totalSales - $totalReturn;
            $totalCollection = $collection;

            $outStanding = $grandTotal - $totalCollection;

            $averagePercent = 0;
            if ($collection != 0 && $sales->sum('amount') != 0) {
                $percent = ($collection / ($sales->sum('amount') - $issueReturn->sum('amount'))) * 100;
                $percentage = 100 - $percent;
                $averagePercent = number_format($percentage, 2, '.', '');
            } else {
                $averagePercent = 0;
            }

            $ld = [
                'dealer_name' => $dealer->name,
                'short_name' => $dealer->short_name,
                'purchase' => number_format($sales->sum('amount'), 2, '.', ''),
                'collection' => number_format($collection),
                // 'balance' => $sales->sum('amount') - $collection - $issueReturn->sum('amount'),
                'outstanding' => number_format($outStanding, 2, '.', ''),
                'averagePercent' => $averagePercent,
            ];


            $allDealerInfo[] = $ld;
        }

        // Top Five Dealers
        $dealer_sales_sortting = array_column($allDealerInfo, 'purchase');
        array_multisort($dealer_sales_sortting, SORT_DESC, $allDealerInfo);
        $top_five_sales = array_slice($allDealerInfo, 0, 5);

        return $top_five_sales;
    }

    public function topCollections() {
        $company = auth()->user()->company_id;
        //Top 3 Sales 
        $staffs = StaffSetup::where('company_id', $company)
                ->where('status', 1)
                ->get();

        $all_collections = [];
        foreach ($staffs as $staff) {
            $dCollections = DealerCollection::where('company_id', $company)
                    ->where('sale_by', $staff->id)
                    ->sum('payment_amount');

            $rCollection = InstallmentCollectionList::with('collection')
                    ->whereHas('collection', function ($q) use ($staff) {
                        $q->where('reference_id', $staff->id);
                    })
                    ->sum('installment_schedule_amount');

            $staff_collections = $dCollections + $rCollection;

            $info = [
                'name' => @$staff->short_name,
                'total_amount' => number_format($staff_collections, 2, '.', ''),
            ];

            $all_collections[] = $info;
        }

        $sortting = array_column($all_collections, 'total_amount');
        array_multisort($sortting, SORT_DESC, $all_collections);
        $top_collections = array_slice($all_collections, 0, 3);

        return $top_collections;
    }

    public function monthlyFlow() {
        $company = auth()->user()->company_id;
        //Yearly Chart data
        //Yearly Chart data
        $months = [];
        $t = now();
        for ($i = 11; $i >= 0; $i--) {
            $newDateTime = Carbon::now()->subMonth($i);
            $months[$i] = date('Y-m-01', strtotime($newDateTime));
        }

        $monthly_sales_collections = [];
        foreach ($months as $key => $value) {
            $first_day = $value;
            $last_day = date("Y-m-t", strtotime($first_day));

            $sales = ProductIssueList::with('issue')
                    ->whereHas('issue', function ($q) use ($first_day, $last_day) {
                        $q->where('date', '>=', $first_day)
                        ->where('date', '<=', $last_day);
                    })
                    ->where('company_id', $company)
                    ->get();

            $totalSales = ProductIssueList::with('issue')
                    ->whereHas('issue', function ($q) use ($first_day, $last_day) {
                        $q->where('date', '>=', $first_day)
                        ->where('date', '<=', $last_day);
                    })
                    ->where('company_id', $company)
                    ->sum('amount');

            $totalReturn = SalesReturn::where('company_id', $company)
                    ->where('return_date', '>=', $first_day)
                    ->where('return_date', '<=', $last_day)
                    ->sum('amount');

            $dcollection = DealerCollection::where('company_id', $company)
                    ->where('payment_date', '>=', $first_day)
                    ->where('payment_date', '<=', $last_day)
                    ->where('remarks', '!=', 'adjust')
                    ->sum('payment_amount');

            $advanceCollection = AdvanceCollection::where('company_id', $company)
                    ->where('date', '>=', $first_day)
                    ->where('date', '<=', $last_day)
                    ->where('advance_amount', '>', 0)
                    ->sum('advance_amount');


            $retailCollection = InstallmentCollectionList::where('company_id', $company)
                    ->where('installment_collection_date', '>=', $first_day)
                    ->where('installment_collection_date', '<=', $last_day)
                    ->sum('installment_schedule_amount');

            $collection = $dcollection + $advanceCollection + $retailCollection;

            $issueReturn = SalesReturn::with('dealer')
                    ->where('company_id', $company)
                    ->where('return_date', '>=', $first_day)
                    ->where('return_date', '<=', $last_day)
                    ->get();

            $retailSales = RetailSales::with('sale')
                    ->whereHas('sale', function ($q) use ($first_day, $last_day) {
                        $q->where('sale_date', '>=', $first_day)
                        ->where('sale_date', '<=', $last_day);
                    })
                    ->where('company_id', $company)
                    ->sum('sales_price');

            $retailReturn = RetailSalesReturnProducts::with('return')
                    ->whereHas('return', function ($q) use ($first_day, $last_day) {
                        $q->where('date', '>=', $first_day)
                        ->where('date', '<=', $last_day);
                    })
                    ->where('company_id', $company)
                    ->sum('sales_price');


            if ($sales->sum('amount') == 0 && $collection == 0 && $issueReturn->sum('amount') == 0) {
                continue;
            }


            $grandTotal = ($totalSales + $retailSales) - ($totalReturn + $retailReturn);

            $info = [
                'month' => date('M-y', strtotime($value)),
                'sales_amount' => number_format($grandTotal, 2, '.', ''),
                'collection_amount' => number_format($collection, 2, '.', ''),
            ];


            $monthly_sales_collections[] = $info;
        }

        $months = array_column($monthly_sales_collections, 'month');
        $sales_amount = array_column($monthly_sales_collections, 'sales_amount');
        $collection_amount = array_column($monthly_sales_collections, 'collection_amount');
        return $data = [
            'month' => $months,
            'sales_amount' => $sales_amount,
            'collection_amount' => $collection_amount
        ];
    }

    public function salesCollection() {
        $company = auth()->user()->company_id;

        //Dealer Info
        $dealerSales = ProductIssueList::where('company_id', $company)->sum('amount');
        $dealerReturn = SalesReturn::where('company_id', $company)->sum('amount');
        $dcollection = DealerCollection::where('company_id', $company)
                ->where('remarks', '!=', 'adjust')
                ->sum('payment_amount');
        $advanceCollection = AdvanceCollection::where('company_id', $company)
                ->where('advance_amount', '>', 0)
                ->sum('advance_amount');
        $dealerCollection = $dcollection + $advanceCollection;


        //Customer Info
        $retailSales = RetailSales::where('company_id', $company)->sum('sales_price');
        $retailReturn = RetailSalesReturnProducts::where('company_id', $company)->sum('sales_price');
        $retailCollection = InstallmentCollectionList::where('company_id', $company)->sum('installment_schedule_amount');


        //Total
        $totalSales = ($dealerSales + $retailSales) - ($dealerReturn + $retailReturn);
        $totalCollections = $dealerCollection + $retailCollection;
        $outstanding = $totalSales - $totalCollections;
        $info = [
            'sales' => number_format($totalSales, 2, '.', ''),
            'collections' => number_format($totalCollections, 2, '.', ''),
            'outstanding' => number_format($outstanding, 2, '.', '')
        ];

        return $info;
    }

    public function productionLift() {
        $company = auth()->user()->company_id;
        $showroom = auth()->user()->showroom_id;
        $productions = ProductionIsuuList::where('company_id', $company)
//                ->where('showroom_id', $showroom)
                ->sum('price');

        $liftings = LiftingProduct::where('company_id', $company)
//                ->where('showroom_id', $showroom)
                ->sum('price');

        $liftingReturns = LiftingReturnProduct::where('company_id', $company)
//                ->where('showroom_id', $showroom)
                ->sum('price');

        $actualLifting = $liftings - $liftingReturns;

        $payments = PaymentToCompany::where('company_id', $company)
//                ->where('showroom_id', $showroom)
                ->sum('payment_now');

        $paymentDue = $liftings - ($liftingReturns + $payments);

        $info = [
            'productions' => number_format($productions, 2, '.', ''),
            'liftings' => number_format($actualLifting, 2, '.', ''),
            'payments' => number_format($payments, 2, '.', ''),
            'paymentDue' => number_format($paymentDue, 2, '.', '')
        ];

        return $info;
    }

    public function stockStaffCus() {
        $company = auth()->user()->company_id;
        $showroom = auth()->user()->showroom_id;

        $productTypes1 = [
            'warranty_product',
            'spare_parts'
        ];
        
        $productTypes2 = [
            'consumer_product',
            'raw_product'
        ];

        $stockQty = 0;
        $stockValue = 0;
        foreach ($productTypes1 as $productType1) {
            $stock1 = Stock::StcockValue($company, $productType1);
            $stockQty += $stock1['qty'];
            $stockValue += $stock1['stockValue'];
        }
        
        foreach ($productTypes2 as $productType2) {
            $stock2 = Stock::StockConsumerValue($company, $productType2);
            $stockQty += $stock2['qty'];
            $stockValue += $stock2['stockValue'];
        }

        $staff = StaffSetup::where('company_id', $company)->where('status', 1)->count();
        $customers = CustomerRegistration::where('company_id', $company)->where('status', 1)->count();

        $info = [
            'stockQty' => $stockQty,
            'stockValue' => number_format($stockValue, 2, '.', ''),
            'staff' => $staff,
            'customers' => $customers
        ];

        return $info;
    }

}
