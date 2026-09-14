<?php

namespace App\Services\Statement\Customer;

use App\RetailSale;
use Carbon\CarbonPeriod;
use App\CustomerAgreement;
use App\RetailSalesReturn;
use App\InstallmentCollection;
use App\CustomerRegistrationSetup;
use App\InstallmentCollectionList;

class Statement
{

    public static function getPreviousBalance($customer, $date)
    {
        $customer = CustomerRegistrationSetup::where('id', $customer)->first();

        $prevDue = 0;

        if ($customer) {
            $totalSale = $customer->totalSaleAmountWithDateRange('0000-00-00', $date);
            $totalCollection = $customer->totalCollectionAmountWithDateRange('0000-00-00', $date);
            $totalReturn = $customer->totalReturnAmountWithDateRange('0000-00-00', $date);

            $prevDue = $totalSale - ($totalCollection + $totalReturn);
        }

        return $prevDue;
    }


    public static function getCustomerStatement($customer, $fromDate, $toDate, $previousBalance)
    {

        $customer = CustomerRegistrationSetup::where('id', $customer)->first();

        $dates = CarbonPeriod::create($fromDate, $toDate);

        $data = [];

        $i = 1;


        $agreements = CustomerAgreement::where('customer_id', $customer->id)->get();

        foreach ($agreements as $agreement) {

            $ld = [
                'sl' => $i,
                'customer_name' => $customer->name,
                'customer_code' => $customer->code,
                'customer_address' => $customer->address,
                'customer_phone' => $customer->phone_no,
                'invoice_no' => $agreement->money_r_no,
                'date' =>  date('d-m-Y', strtotime($agreement->date)),
                'sale' => 0,
                'return' => 0,
                'discount' => 0,
                'giftVoucher' => 0,
                'exchangeCRT' => 0,
                'collection' => 0,
                'agreement' => $agreement->agreement_amount,
            ];


            $ld['due'] = 0;

            array_push($data, $ld);

            $i++;
        }


        foreach ($dates as $date) {

            $formattedDate = $date->format('Y-m-d');


            $retailSales = RetailSale::where('customer_id', $customer->id)
                ->with(['products'])
                ->where('sale_date',  $formattedDate)
                ->get();


            $collectionIds = InstallmentCollection::where('customer_id', $customer->id)->select('id')->get()->pluck('id');

            $collectionAmount = InstallmentCollectionList::whereIn('installment_collection_id', $collectionIds)
                ->where('installment_collection_date', $formattedDate)
                ->get();


            $returns = RetailSalesReturn::where('customer_id', $customer->id)
                ->where('date', $formattedDate)
                ->with(['products'])
                ->get();


            // loop sales
            foreach ($retailSales as $retailSale) {

                $totalPrice = 0 ;
                foreach ($retailSale->products as $product) {

                    $qty = $product->qty;
                    // $price = $product->cash_price;

                    if ($retailSale->sale_type == 'Cash') {
                        $price = $product->cash_price;
                    } else if ($retailSale->sale_type == 'Short Installment') {
                        $price = $product->mrp_price;
                    } else {
                        $price = $product->hire_price;
                    }

                    $price = $qty * $price;

                    $totalPrice += $price;

                }

                $ld = [
                    'sl' => $i,
                    'customer_name' => $customer->name,
                    'customer_code' => $customer->code,
                    'invoice_no' => $retailSale->invoice_no,
                    'date' => $date->format('d-m-Y'),
                    'sale' => $totalPrice,
                    'return' => 0,
                    'discount' => $product->discount,
                    'giftVoucher' => $product->gift_voucher,
                    'exchangeCRT' => $product->exchange_crt,
                    'collection' => 0,
                    'agreement' => 0,
                ];

                // dd($ld);


                if ($ld['sale'] != 0) {

                    $ld['due'] = ($previousBalance + $ld['sale']) - ($ld['collection'] + $ld['return'] + $ld['discount'] + $ld['giftVoucher'] + $ld['exchangeCRT']);
                    $previousBalance = $ld['due'];

                    array_push($data, $ld);

                    $i++;
                }
            }

            // loop collection
            foreach ($collectionAmount as $collectionA) {

                $ld = [
                    'sl' => $i,
                    'customer_name' => $customer->name,
                    'customer_code' => $customer->code,
                    'invoice_no' => $collectionA->invoice_no,
                    'date' => $date->format('d-m-Y'),
                    'sale' => 0,
                    'return' => 0,
                    'discount' => 0,
                    'giftVoucher' => 0,
                    'exchangeCRT' => 0,
                    'collection' => $collectionA->installment_schedule_amount,
                    'agreement' => 0,
                ];

                // dd($ld);


                if ($ld['collection'] != 0) {

                    $ld['due'] = ($previousBalance + $ld['sale']) - ($ld['collection'] + $ld['return'] + $ld['discount'] + $ld['giftVoucher'] + $ld['exchangeCRT']);
                    $previousBalance = $ld['due'];

                    array_push($data, $ld);

                    $i++;
                }
            }


            // loop returns
            foreach ($returns as $return) {

                $ld = [
                    'sl' => $i,
                    'customer_name' => $customer->name,
                    'customer_code' => $customer->code,
                    'invoice_no' => $collectionA->invoice_no,
                    'date' => $date->format('d-m-Y'),
                    'sale' => 0,
                    'return' => $return->products->sum('cash_price'),
                    'discount' => 0,
                    'giftVoucher' => 0,
                    'exchangeCRT' => 0,
                    'collection' => $collectionA->installment_schedule_amount,
                    'agreement' => 0,
                ];


                if ($ld['return'] != 0) {

                    $ld['due'] = ($previousBalance + $ld['sale']) - ($ld['collection'] + $ld['return'] + $ld['discount'] + $ld['giftVoucher'] + $ld['exchangeCRT']);
                    $previousBalance = $ld['due'];

                    array_push($data, $ld);

                    $i++;
                }
            }
        }

        return $data;
    }
}
