<?php

namespace App\Services\Collection;

use App\InstallmentCollection;
use App\CustomerRegistrationSetup;


class CustomerWiseCollection
{
    public static function get($customerId)
    {

        $collections = InstallmentCollection::where('customer_id', $customerId)
        ->with(['collection' => function ($query){
            // dd($query);
            $query->orderBy('installment_collection_date', 'asc');
        }])
        ->get();

        $data = [];
        $data['collection'] = [];
        $totalCollection = 0;

        foreach ($collections as $collection) {

            $ld = [
                'id' => $collection->id,
                'date' => date('d-m-Y', strtotime(@$collection->collection->installment_collection_date)),
                'amount' => $collection->booking_amount,
                'memo_no' => $collection->invoice_no,
            ];

            $totalCollection += $collection->booking_amount;

            array_push($data['collection'], $ld);
        }

        $data['totalCollection'] = $totalCollection;
        $data['customer'] = CustomerRegistrationSetup::find($customerId);

        return $data;
    }

    public static function getWithDate($customerId, $rawStartDate, $rawEndDate, $memo)
    {
        $startDate = date('Y-m-d', strtotime($rawStartDate));
        $endDate = date('Y-m-d', strtotime($rawEndDate));

        $collections = InstallmentCollection::query()
            ->where('customer_id', $customerId)
            ->with(['collection'])
            ->whereHas('collection', function ($q) use ($startDate, $endDate, $rawStartDate, $rawEndDate, $memo) {
                if ($rawStartDate != '') {
                    $q->where('installment_collection_date', '>=', $startDate);
                }
                if ($rawEndDate != '') {
                    $q->where('installment_collection_date', '<=', $endDate);
                }
                if ($memo != '') {
                    $q->where('invoice_no', $memo);
                }
            })
            ->get();


        $data = [];
        $data['collection'] = [];
        $totalCollection = 0;

        foreach ($collections as $collection) {

            $ld = [
                'id' => $collection->id,
                'date' => date('d-m-Y', strtotime($collection->collection->installment_collection_date)),
                'amount' => $collection->booking_amount,
                'memo_no' => $collection->invoice_no,
            ];

            $totalCollection += $collection->booking_amount;

            array_push($data['collection'], $ld);
        }

        $data['totalCollection'] = $totalCollection;
        $data['customer'] = CustomerRegistrationSetup::find($customerId);

        return $data;
    }
}
