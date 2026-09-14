<?php

namespace App\Services\Installment\Collection;

use App\RetailSale;
use App\CustomerAgreement;
use App\InstallmentCollection;
use App\CustomerRegistrationSetup;

class CustomerInfo {

    public static function getAllCustomerWithCollection() {

        $customerIds = RetailSale::distinct('customer_id')->select(['customer_id'])->get()->pluck('customer_id')->toArray();

        $customers = CustomerRegistrationSetup::whereIn('id', $customerIds)->get();

        $data = [];

        $i = 1;
        foreach ($customers as $customer) {

            $ld = [
                'sl' => $i,
                'customerID' => $customer->id,
                'customerName' => $customer->name,
                'customerAccountCode' => $customer->code,
                'customerMobile' => $customer->phone_no,
                'totalsaleAmount' => $customer->totalsaleAmount(),
                'totalCollectionAmount' => $customer->totalCollectionAmount(),
            ];

            $ld['outstanding'] = $ld['totalsaleAmount'] - $ld['totalCollectionAmount'];

            if ($ld['outstanding'] > 0) {
                $i++;
                array_push($data, $ld);
            }
        }

        return $data;
    }

    public static function getAllCustomerByCollectorWithCollection($customerIds) {

        $customers = CustomerRegistrationSetup::whereIn('id', $customerIds)->orderBy('code', 'asc')->get();

        $data = [];

        $i = 1;
        foreach ($customers as $customer) {

            $ld = [
                'sl' => $i,
                'customerID' => $customer->id,
                'customerName' => $customer->name,
                'customerAccountCode' => $customer->code,
                'customerMobile' => $customer->phone_no,
                'totalsaleAmount' => $customer->totalsaleAmountOnlyCash() - $customer->totalReturnAmount(),
                'totalsaleDiscount' => $customer->totalsaleDiscount(),
                'totalsaleGiftVoucher' => $customer->totalsaleGiftVoucher(),
                'totalsaleExchangeCrt' => $customer->totalsaleExchangeCrt(),
                'totalCollectionAmount' => $customer->totalCollectionAmount(),
            ];

            $ld['outstanding'] = $ld['totalsaleAmount'] - ($ld['totalsaleDiscount'] + $ld['totalsaleGiftVoucher'] + $ld['totalsaleExchangeCrt'] + $ld['totalCollectionAmount']);

            // dd($ld);

            $i++;
            array_push($data, $ld);
        }


        return $data;
    }

    public static function getAllCustomerCollectionByDate($customerIds, $fromDate, $toDate) {

        $customers = CustomerRegistrationSetup::whereIn('id', $customerIds)->orderBy('code', 'asc')->get();

        $data = [];

        $i = 1;
        foreach ($customers as $customer) {

            $ld = [
                'sl' => $i,
                'customerID' => $customer->id,
                'customerName' => $customer->name,
                'customerAccountCode' => $customer->code,
                'customerMobile' => $customer->phone_no,
                'totalsaleAmount' => $customer->totalsaleAmountOnlyCashByDate($fromDate, $toDate) - $customer->totalReturnAmountByDate($fromDate, $toDate),
                'totalsaleDiscount' => $customer->totalsaleDiscountByDate($fromDate, $toDate),
                'totalsaleGiftVoucher' => $customer->totalsaleGiftVoucherByDate($fromDate, $toDate),
                'totalsaleExchangeCrt' => $customer->totalsaleExchangeCrtByDate($fromDate, $toDate),
                'totalCollectionAmount' => $customer->totalCollectionAmountByDate($fromDate, $toDate),
                'prevSales' => $customer->totalsaleAmountOnlyCashPrev($fromDate) - $customer->totalReturnAmountPrev($fromDate),
                'prevSalesDiscount' => $customer->totalsaleDiscountPrev($fromDate),
                'prevSalesGiftVoucher' => $customer->totalsaleGiftVoucherPrev($fromDate),
                'prevSalesExchangeCrt' => $customer->totalsaleExchangeCrtPrev($fromDate),
                'prevSalesCollectionAmount' => $customer->totalCollectionAmountPrev($fromDate),
                'totalsale' => $customer->totalsaleAmountOnlyCash() - $customer->totalReturnAmount(),
                'totalsaleDis' => $customer->totalsaleDiscount(),
                'totalsaleGift' => $customer->totalsaleGiftVoucher(),
                'totalsaleExcCrt' => $customer->totalsaleExchangeCrt(),
                'totalCollection' => $customer->totalCollectionAmount(),
                'saleDate' => $customer->saleDate($fromDate, $toDate),
            ];

            $ld['outstanding'] = $ld['totalsale'] - ($ld['totalsaleDis'] + $ld['totalsaleGift'] + $ld['totalsaleExcCrt'] + $ld['totalCollection']);
            $ld['previousBalance'] = $ld['prevSales'] - ($ld['prevSalesDiscount'] + $ld['prevSalesGiftVoucher'] + $ld['prevSalesExchangeCrt'] + $ld['prevSalesCollectionAmount']);


            // dd($ld);

            $i++;
            array_push($data, $ld);
        }


        return $data;
    }

    public static function getCustomerByCollectorWithCollection($id) {

        $customer = CustomerRegistrationSetup::where('id', $id)->first();
        $retailSale = RetailSale::where('customer_id', $id)->first();

        $data = '';

        $i = 1;

        $data = [
            'sl' => $i,
            'customerID' => $customer->id,
            'customerName' => $customer->name,
            'customerAccountCode' => $customer->code,
            'customerMobile' => $customer->phone_no,
            'saleDate' => @$retailSale->sale_date,
            'totalsaleAmount' => $customer->totalsaleAmountOnlyCash() - $customer->totalReturnAmount(),
            'reScheduleSaleAmount' => $customer->totalsaleAmountReschedule() - $customer->totalReturnAmount(),
            'totalsaleDiscount' => $customer->totalsaleDiscount(),
            'totalsaleGiftVoucher' => $customer->totalsaleGiftVoucher(),
            'totalsaleExchangeCrt' => $customer->totalsaleExchangeCrt(),
            'totalCollectionAmount' => $customer->totalCollectionAmount(),
            'installments' => $customer->totalCollectionDetails(),
        ];

        $data['outstanding'] = $data['totalsaleAmount'] - ($data['totalsaleDiscount'] + $data['totalsaleGiftVoucher'] + $data['totalsaleExchangeCrt'] + $data['totalCollectionAmount']);
        $data['reScheduleOutstanding'] = $data['reScheduleSaleAmount'] - ($data['totalsaleDiscount'] + $data['totalsaleGiftVoucher'] + $data['totalsaleExchangeCrt'] + $data['totalCollectionAmount']);



        return $data;
    }

    
    public static function getAllCustomerByCollector($collectorId) {

        // Agreement customer Ids
        $agreementCustomerIds = CustomerAgreement::where('employee_id', $collectorId)->select(['customer_id'])->groupBy('customer_id')->get()->pluck('customer_id')->toArray();

        $RetailCustomerIds = RetailSale::whereIn('customer_id', $agreementCustomerIds)->select(['customer_id'])->groupBy('customer_id')->get()->pluck('customer_id')->toArray();

        // collectionCustomerIds 
        $collectionCustomerIds = InstallmentCollection::whereIn('customer_id', $agreementCustomerIds)->select(['customer_id'])->groupBy('customer_id')->get()->pluck('customer_id')->toArray();

        // join customer ids in one array 
        $customerIds = array_merge($RetailCustomerIds, $agreementCustomerIds, $collectionCustomerIds);

        $customers = CustomerRegistrationSetup::whereIn('id', $customerIds)
                // ->where('code', '!=', 'cash')
                ->orderBy('code', 'asc')
                ->cursor();


        $data = [];

        $i = 1;
        foreach ($customers as $customer) {

            $ld = [
                'sl' => $i,
                'customerID' => $customer->id,
                'customerName' => $customer->name,
                'customerAccountCode' => $customer->code,
                'customerMobile' => $customer->phone_no,
                'totalsaleAmount' => $customer->totalsaleAmountWithCollector($customer->id),
                'totalCollectionAmount' => $customer->totalCollectionAmount(),
            ];

            $ld['outstanding'] = $ld['totalsaleAmount'] - $ld['totalCollectionAmount'];

            // dd($ld);
            // if ($ld['totalsaleAmount'] > 0) {
            $i++;
            array_push($data, $ld);
            // }
        }


        return $data;
    }

    public static function getAddCustomerById($cusomerId) {

        $RetailCustomerId = RetailSale::where('customer_id', $cusomerId)->select(['customer_id'])->groupBy('customer_id')->get()->pluck('customer_id')->toArray();

        // Agreement customer Ids
        $agreementCustomerId = CustomerAgreement::where('customer_id', $cusomerId)->select(['customer_id'])->groupBy('customer_id')->get()->pluck('customer_id')->toArray();
        $collectorId = CustomerAgreement::where('customer_id', $cusomerId)->select(['employee_id'])->first();

        // collectionCustomerIds 
        $collectionCustomerId = InstallmentCollection::where('customer_id', $cusomerId)->select(['customer_id'])->groupBy('customer_id')->get()->pluck('customer_id')->toArray();

        // join customer ids in one array 
        $customerId = array_merge($RetailCustomerId, $agreementCustomerId, $collectionCustomerId);

        $customer = CustomerRegistrationSetup::whereIn('id', $customerId)
                // ->where('code', '!=', 'cash')
                ->orderBy('code', 'asc')
                ->first();


        $data = [];

        $i = 1;

        $ld = [
            'sl' => $i,
            'customerID' => $customer->id,
            'customerName' => $customer->name,
            'customerAccountCode' => $customer->code,
            'customerMobile' => $customer->phone_no,
            'totalsaleAmount' => $customer->totalsaleAmountWithCollector($customer->id),
            'totalCollectionAmount' => $customer->totalCollectionAmount(),
        ];

        $ld['outstanding'] = $ld['totalsaleAmount'] - $ld['totalCollectionAmount'];

        // dd($ld);
        // if ($ld['totalsaleAmount'] > 0) {

        array_push($data, $ld);
        // }



        return $data;
    }

    public static function getAllCustomerWithOutSale($customerIds, $amount, $fromDate, $toDate) {

        $amount = explode('-', $amount);
        $startAmount = $amount[0];
        $endAmount = $amount[1];

        $customers = CustomerRegistrationSetup::whereIn('id', $customerIds)->orderBy('code', 'asc')->get();

        $data = [];

        $i = 1;
        foreach ($customers as $customer) {

            if ($customer->hasSale()) {
                continue;
            }

            $ld = [
                'sl' => $i,
                'customerID' => $customer->id,
                'customerName' => $customer->name,
                'customerAccountCode' => $customer->code,
                'customerMobile' => $customer->phone_no,
//                'totalCollectionAmount' => $customer->totalCollectionAmount(),
                'totalCollectionAmount' => $customer->totalCollectionAmountByDate($fromDate, $toDate),
            ];

            if ($ld['totalCollectionAmount'] >= $startAmount && $ld['totalCollectionAmount'] <= $endAmount) {

                if ($ld['totalCollectionAmount'] > 0) {
                    $i++;
                    array_push($data, $ld);
                }
            }
        }

        return $data;
    }

    public static function getCustomerRetailSaleSavings($customerId) {

        $customer = CustomerRegistrationSetup::where('id', $customerId)->first();
        $savings = [
            'totalSavings' => $customer->totalCollectionAmount(),
        ];
        return $savings;
    }

}
