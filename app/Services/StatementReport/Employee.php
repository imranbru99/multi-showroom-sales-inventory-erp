<?php

namespace App\Services\StatementReport;

use App\SalesReturn;
use App\DealerCollection;
use App\ProductIssueList;
use App\AdvanceCollection;

class Employee {

    public static function getSaleState($employees, $fromDate, $toDate) {

        $data = [];

        foreach ($employees as $employee) {

            $saleData = self::getSale($employee, $fromDate, $toDate);
            $getTotalPurchase = self::getTotalPurchase($employee);
            $getTotaReturn = self::getAllReturn($employee);
            $returnData = self::getReturn($employee, $fromDate, $toDate);

            $row = [
                'employeeName' => $employee->name,
                'prev_balance' => self::previousBalance($employee, $fromDate),
                'purchase' => $saleData['amount'],
                'collection' => self::getCollection($employee, $fromDate, $toDate),
                'return' => $returnData['amount'],
                'returnQty' => $returnData['qty'],
                'qty' => $saleData['qty'],
                'total_purchase' => $getTotalPurchase['amount'],
                'total_return' => $getTotaReturn['amount'],
                'total_collection' => self::getAllCollection($employee),
            ];

            $row['actualSale'] = $row['purchase'] - $row['return'];
            $row['balance'] = $row['actualSale'] + $row['prev_balance'] - $row['collection'];
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

    public static function getReturn($employee, $fromDate, $toDate) {

        $return = SalesReturn::where('sales_by', $employee->id)
                ->where('return_date', '>=', $fromDate)
                ->where('return_date', '<=', $toDate)
                ->get();

        return [
            'amount' => round($return->sum('amount'), 2),
            'qty' => $return->sum('qty'),
        ];
    }

    public static function getAllReturn($employee) {

        $return = SalesReturn::where('sales_by', $employee->id)
                ->get();

        return [
            'amount' => round($return->sum('amount'), 2),
            'qty' => $return->sum('qty'),
        ];
    }

    public static function getSale($employee, $fromDate, $toDate) {
        $sales = ProductIssueList::with(['issue', 'product'])
                ->whereHas('issue', function ($q) use ($fromDate, $toDate, $employee) {
                    $q->where('date', '>=', $fromDate)
                    ->where('date', '<=', $toDate)
                    ->where('sales_by', $employee->id);
                })
                ->get();

        return [
            'amount' => round($sales->sum('amount'), 2),
            'qty' => $sales->sum('qty'),
        ];
    }

    public static function getTotalPurchase($employee) {
        $sales = ProductIssueList::with(['issue', 'product'])
                ->whereHas('issue', function ($q) use ($employee) {
                    $q->where('sales_by', $employee->id);
                })
                ->get();

        return [
            'amount' => round($sales->sum('amount'), 2),
            'qty' => $sales->sum('qty'),
        ];
    }

    public static function getCollection($employee, $fromDate, $toDate) {

        $collection = DealerCollection::where('sale_by', $employee->id)
                ->where('remarks', '!=', 'adjust')
                ->where('payment_date', '>=', $fromDate)
                ->where('payment_date', '<=', $toDate)
                ->sum('payment_amount');


        $advanceCollection = AdvanceCollection::where('sale_by', $employee->id)
                ->where('advance_amount', '>', 0)
                ->where('date', '>=', $fromDate)
                ->where('date', '<=', $toDate)
                ->sum('advance_amount');

        $totalCollection = $collection + $advanceCollection;

        return round($totalCollection, 2);
    }

    public static function getAllCollection($employee) {

        $collection = DealerCollection::where('sale_by', $employee->id)
                ->where('remarks', '!=', 'adjust')
                ->sum('payment_amount');


        $advanceCollection = AdvanceCollection::where('sale_by', $employee->id)
                ->where('advance_amount', '>', 0)
                ->sum('advance_amount');

        $totalCollection = $collection + $advanceCollection;

        return round($totalCollection, 2);
    }

    public static function previousBalance($employee, $fromDate) {
        $sales = ProductIssueList::with(['issue', 'product'])
                ->whereHas('issue', function ($q) use ($fromDate, $employee) {
                    $q->where('date', '<', $fromDate)
                    ->where('sales_by', $employee->id);
                })
                ->sum('amount');

        $prevIssueReturn = SalesReturn::where('sales_by', $employee->id)->where('return_date', '<', $fromDate)->sum('amount');
        ;


        $collection = DealerCollection::where('sale_by', $employee->id)
                ->where('remarks', '!=', 'adjust')
                ->where('payment_date', '<', $fromDate)
                ->sum('payment_amount');

        $advanceCollection = AdvanceCollection::where('sale_by', $employee->id)
                ->where('advance_amount', '>', 0)
                ->where('date', '<', $fromDate)
                ->sum('advance_amount');

        $totalCollection = $collection + $advanceCollection;

        $balance = $sales - ($totalCollection + $prevIssueReturn);

        return round($balance, 2);
    }

}
