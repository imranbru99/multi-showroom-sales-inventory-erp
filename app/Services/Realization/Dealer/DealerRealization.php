<?php

namespace App\Services\Realization\Dealer;

use App\DealerSetup;
use App\SalesReturn;
use App\ProductIssue;
use App\DealerCollection;
use App\ProductIssueList;
use App\AdvanceCollection;
use Illuminate\Support\Carbon;

class DealerRealization {

    public static function getDealerRealization($dealer_type, $dealer, $month, $year, $company) {

        $dealers = self::getDealers($dealer_type, $dealer, $company);

        $data = [];

        foreach ($dealers as $dealer) {

            $row = [
                'dealerName' => $dealer->name,
                'previousBalance' => self::getPreviousBalance($dealer, $year),
                'year' => self::getYearlyInfo($dealer, $year, $month),
                'month' => self::getMonthlyInfo($dealer, $year, $month),
            ];

            $row['currentOutstanding'] = $row['previousBalance'] + $row['year']['balance'] + $row['month']['balance'];


            $totalDue = $row['previousBalance'] + $row['year']['balance'] + $row['month']['sales']; //ex: 10000

            if ($totalDue == 0) {
                $totalDue = 1;
            }
            $collection = $row['month']['collection'] + $row['month']['return']; // ex: 2500
            $dueMinusCollection = $totalDue - $collection; //ex: 7500
            $creditP = ($dueMinusCollection / $totalDue) * 100; //ex: 75%;

            $row['creditP'] = $creditP;

            array_push($data, $row);
        }

        return $data;
    }

    public static function getMonthlyInfo($dealer, $year, $month) {

        $firstDateOfYear = Carbon::create($year, $month)->firstOfMonth()->format('Y-m-d');
        $lastDateOfYear = Carbon::create($year, $month)->lastOfMonth()->format('Y-m-d');

        $sales = ProductIssueList::with(['issue', 'product'])
                ->whereHas('issue', function ($q) use ($firstDateOfYear, $lastDateOfYear, $dealer) {
                    $q->where('date', '>=', $firstDateOfYear)
                    ->where('date', '<=', $lastDateOfYear)
                    ->where('dealer_id', $dealer->id);
                })
                ->sum('amount');

        $return = SalesReturn::where('dealer_id', $dealer->id)
                ->where('return_date', '>=', $firstDateOfYear)
                ->where('return_date', '<=', $lastDateOfYear)
                ->sum('amount');

        $collection = DealerCollection::where('dealer_id', $dealer->id)
                ->where('remarks', '!=', 'adjust')
                ->where('payment_date', '>=', $firstDateOfYear)
                ->where('payment_date', '<=', $lastDateOfYear)
                ->sum('payment_amount');

        $advanceCollection = AdvanceCollection::where('dealer_id', $dealer->id)
                ->where('advance_amount', '>', 0)
                ->where('date', '>=', $firstDateOfYear)
                ->where('date', '<=', $lastDateOfYear)
                ->sum('advance_amount');

        $totalCollection = $collection + $advanceCollection;


        return [
            'sales' => $sales,
            'collection' => $totalCollection,
            'return' => $return,
            'balance' => $sales - $return - $totalCollection,
        ];
    }

    public static function getYearlyInfo($dealer, $year, $month) {

        $firstDateOfMonth = Carbon::create($year)->firstOfYear()->format('Y-m-d');
        $lastDateOfMonth = Carbon::create($year, $month)->firstOfMonth()->subDays(1)->format('Y-m-d');
        // $lastDateOfMonth = Carbon::create($year)->lastOfYear()->format('Y-m-d');

        $sales = ProductIssueList::with(['issue', 'product'])
                ->whereHas('issue', function ($q) use ($firstDateOfMonth, $lastDateOfMonth, $dealer) {
                    $q->where('date', '>=', $firstDateOfMonth)
                    ->where('date', '<=', $lastDateOfMonth)
                    ->where('dealer_id', $dealer->id);
                })
                ->sum('amount');

        $return = SalesReturn::where('dealer_id', $dealer->id)
                ->where('return_date', '>=', $firstDateOfMonth)
                ->where('return_date', '<=', $lastDateOfMonth)
                ->sum('amount');

        $collection = DealerCollection::where('dealer_id', $dealer->id)
                ->where('remarks', '!=', 'adjust')
                ->where('payment_date', '>=', $firstDateOfMonth)
                ->where('payment_date', '<=', $lastDateOfMonth)
                ->sum('payment_amount');

        $advanceCollection = AdvanceCollection::where('dealer_id', $dealer->id)
                ->where('advance_amount', '>', 0)
                ->where('date', '>=', $firstDateOfMonth)
                ->where('date', '<=', $lastDateOfMonth)
                ->sum('advance_amount');

        $totalCollection = $collection + $advanceCollection;

        return [
            'sales' => $sales,
            'collection' => $totalCollection,
            'return' => $return,
            'balance' => $sales - $return - $totalCollection,
        ];
    }

    public static function getPreviousBalance($dealer, $year) {

        $firstDateOfYear = Carbon::create($year)->firstOfYear()->format('Y-m-d');

        $sales = ProductIssueList::with(['issue'])
                ->whereHas('issue', function ($q) use ($firstDateOfYear, $dealer) {
                    $q->where('date', '<', $firstDateOfYear)
                    ->where('dealer_id', $dealer->id);
                })
                ->sum('amount');


        $return = SalesReturn::where('dealer_id', $dealer->id)
                ->where('return_date', '<', $firstDateOfYear)
                ->sum('amount');

        $collection = DealerCollection::where('dealer_id', $dealer->id)
                ->where('remarks', '!=', 'adjust')
                ->where('payment_date', '<', $firstDateOfYear)
                ->sum('payment_amount');

        $advanceCollection = AdvanceCollection::where('dealer_id', $dealer->id)
                ->where('advance_amount', '>', 0)
                ->where('date', '<', $firstDateOfYear)
                ->sum('advance_amount');

        $totalCollection = $collection + $advanceCollection;

        $balance = $sales - $return - $totalCollection;

        return round($balance, 2);
    }

    public static function getDealers($dealer_type, $dealer, $company) {
        $dealers = DealerSetup::where('status', '1')
                ->where('company_id', $company)
//                ->where('showroom_id', $this->showroomId)
                ->orderBy('name', 'asc');

        if ($dealer_type) {
            $dealers = $dealers->where('type', $dealer_type);
        }

        if ($dealer) {
            $dealers = $dealers->whereIn('id', $dealer);
        }

        $dealers = $dealers->get();

        return $dealers;
    }

}
