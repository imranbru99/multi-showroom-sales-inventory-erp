<?php

namespace App\Services\StatementReport;

use App\SalesReturn;
use App\DealerCollection;
use App\ProductIssueList;
use App\AdvanceCollection;

class Dealer
{
    public static function getSaleState($dealers, $fromDate, $toDate)
    {

        $data = [];

        foreach ($dealers as $dealer) {

            $saleData = self::getSale($dealer, $fromDate, $toDate);
            $allsale = self::getAllSale($dealer);
            $returnData = self::getReturn($dealer, $fromDate, $toDate);
            $allreturn = self::getAllReturn($dealer);

            $row = [
                'dealerId' => $dealer->id,
                'dealerName' => $dealer->name,
                'prev_balance' => self::previousBalance($dealer, $fromDate),
                'purchase' => $saleData['amount'],
                'collection' => self::getCollection($dealer, $fromDate, $toDate),
                'return' => $returnData['amount'],
                'returnQty' => $returnData['qty'],
                'qty' =>  $saleData['qty'],
                'total_sales' => $allsale['amount'] ,
                'total_return' => $allreturn['amount'] ,
                'total_collection' =>  self::getAllCollection($dealer),
            ];

            $row['actualSale'] = $row['purchase'] - $row['return'];
            $row['balance'] = $row['prev_balance'] + $row['actualSale'] - $row['collection'];
            $row['actualQty'] = $row['qty'] - $row['returnQty'];

            $totalDue = $row['prev_balance'] + $row['actualSale']; //ex: 10000

            if ($totalDue == 0) {
                $totalDue = 1;
            }

            $collection = $row['collection']; // ex: 2500
            $dueMinusCollection = $totalDue - $collection; //ex: 7500
            $creditP = ($dueMinusCollection / $totalDue) * 100; //ex: 75%;

            $row['creditP'] = round($creditP, 2);


            array_push($data, $row);
        }

        return $data;
    }

    public static function getReturn($dealer, $fromDate, $toDate)
    {

        $return = SalesReturn::where('dealer_id', $dealer->id)
            ->where('return_date', '>=', $fromDate)
            ->where('return_date', '<=', $toDate)
            ->get();

        return [
            'amount' => round($return->sum('amount'), 2),
            'qty' => $return->sum('qty'),
        ];
    }

    public static function getAllReturn($dealer)
    {

        $return = SalesReturn::where('dealer_id', $dealer->id)
            ->get();

        return [
            'amount' => round($return->sum('amount'), 2)
        ];
    }

    public static function getSale($dealer, $fromDate, $toDate)
    {
        $sales = ProductIssueList::with(['issue', 'product'])
            ->whereHas('issue', function ($q) use ($fromDate, $toDate, $dealer) {
                $q->where('date', '>=', $fromDate)
                    ->where('date', '<=', $toDate)
                    ->where('dealer_id', $dealer->id);
            })
            ->get();

        return [
            'amount' => round($sales->sum('amount'), 2),
            'qty' => $sales->sum('qty'),
        ];
    }


    public static function getAllSale($dealer)
    {
        $sales = ProductIssueList::with(['issue', 'product'])
            ->whereHas('issue', function ($q) use ($dealer) {
                    $q->where('dealer_id', $dealer->id);
            })
            ->get();

        return [
            'amount' => round($sales->sum('amount'), 2)
        ];
    }

    public static function getCollection($dealer, $fromDate, $toDate)
    {

        $collection = DealerCollection::where('dealer_id', $dealer->id)
            ->where('remarks', '!=', 'adjust')
            ->where('payment_date', '>=', $fromDate)
            ->where('payment_date', '<=', $toDate)
            ->sum('payment_amount');


        $advanceCollection = AdvanceCollection::where('dealer_id', $dealer->id)
            ->where('advance_amount', '>', 0)
            ->where('date', '>=', $fromDate)
            ->where('date', '<=', $toDate)
            ->sum('advance_amount');

        $totalCollection = $collection + $advanceCollection;

        return round($totalCollection, 2);
    }

    public static function getAllCollection($dealer)
    {

        $collection = DealerCollection::where('dealer_id', $dealer->id)
            ->where('remarks', '!=', 'adjust')
            ->sum('payment_amount');


        $advanceCollection = AdvanceCollection::where('dealer_id', $dealer->id)
            ->where('advance_amount', '>', 0)
            ->sum('advance_amount');

        $totalCollection = $collection + $advanceCollection;

        return round($totalCollection, 2);
    }

    public static function previousBalance($dealer, $fromDate)
    {
        $sales = ProductIssueList::with(['issue', 'product'])
            ->whereHas('issue', function ($q) use ($fromDate, $dealer) {
                $q->where('date', '<', $fromDate)
                    ->where('dealer_id', $dealer->id);
            })
            ->sum('amount');

        $prevIssueReturn = SalesReturn::where('dealer_id', $dealer->id)->where('return_date', '<', $fromDate)->sum('amount');

        $collection = DealerCollection::where('dealer_id', $dealer->id)
            ->where('remarks', '!=', 'adjust')
            ->where('payment_date', '<', $fromDate)
            ->sum('payment_amount');

        $advanceCollection = AdvanceCollection::where('dealer_id', $dealer->id)
            ->where('advance_amount', '>', 0)
            ->where('date', '<', $fromDate)
            ->sum('advance_amount');

        $totalCollection = $collection + $advanceCollection;

        $balance =  $sales -  $prevIssueReturn - $totalCollection;

        return round($balance, 2);
    }
}
