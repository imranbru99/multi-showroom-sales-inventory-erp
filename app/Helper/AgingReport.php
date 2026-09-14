<?php

namespace App\Helper;

use Carbon\Carbon;
use App\DealerSetup;
use App\SalesReturn;
use App\ProductIssue;
use App\DealerCollection;
use App\ProductIssueList;
use App\AdvanceCollection;

class AgingReport {

    public static function getData($company) {
        $data = [];
        $dealers = DealerSetup::where('company_id', $company)
                ->orderBy('name', 'asc')
                ->get();

        foreach ($dealers as $dealer) {
            // dd($dealer);

            $ld = [
                'DealerName' => $dealer->name,
                '30' => self::DayDue($dealer, 30, 0),
                '60' => self::DayDue($dealer, 60, 31),
                '90' => self::DayDue($dealer, 90, 61),
                'o90' => self::DayDue($dealer, 10000, 91),
            ];

            $ld['dealerTotalDue'] = self::DayDue($dealer, 10000, 0);

            if ($ld['dealerTotalDue'] > 0) {
                array_push($data, $ld);
            }
        }

        return $data;
    }

    public static function DayDue($dealer, $end = null, $start = null) {

        if ($end != null) {
            $end = Carbon::now()->subDays($end)->format('Y-m-d');
        }

        $start = Carbon::now()->subDays($start)->format('Y-m-d');

        $dealerSale = self::DaySale($dealer, $start, $end);
        $dealerCollection = self::DayCollection($dealer, $start, $end);
        $dealerReturn = self::DayReturn($dealer, $start, $end);
        $dealerDue = $dealerSale - $dealerReturn - $dealerCollection;

        if ($dealerDue < 0) {
            $dealerDue = 0;
        }

        return number_format((float) $dealerDue, 2, '.', '');
    }

    public static function DaySale($dealer, $startDate, $endDate) {

        $sales = ProductIssueList::with(['issue', 'product'])
                ->whereHas('issue', function ($q) use ($startDate, $endDate, $dealer) {
                    $q->where('date', '<=', $startDate)
                    ->where('date', '>=', $endDate)
                    ->where('dealer_id', $dealer->id);
                })
                ->sum('amount');

        $result = $sales;

        return $result;
    }

    public static function DayReturn($dealer, $startDate, $endDate) {

        $return = SalesReturn::where('dealer_id', $dealer->id)
                ->where('return_date', '<=', $startDate)
                ->where('return_date', '>=', $endDate)
                ->sum('amount');

        return $return;
    }

    public static function DayCollection($dealer, $startDate, $endDate) {

        $collection = DealerCollection::where('dealer_id', $dealer->id)
                ->where('remarks', '!=', 'adjust')
                ->where('payment_date', '<=', $startDate)
                ->where('payment_date', '>=', $endDate)
                ->sum('payment_amount');


        $advanceCollection = AdvanceCollection::where('dealer_id', $dealer->id)
                ->where('advance_amount', '>', 0)
                ->where('date', '<=', $startDate)
                ->where('date', '>=', $endDate)
                ->sum('advance_amount');

        $totalCollectionAmount = $collection + $advanceCollection;

        $result = $totalCollectionAmount;

        if ($totalCollectionAmount < 0) {
            $result = 0;
        }

        return $result;
    }

}
