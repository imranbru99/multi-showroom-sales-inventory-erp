<?php

namespace App\Services\Collection;

use App\InstallmentCollection;
use App\CustomerRegistrationSetup;
use App\RetailSale;

class CustomerWiseSale
{

    public static function getSaleAmount($sale)
    {

        $totalAmount = 0;

        foreach ($sale->products as $product){

            $qty = $product->qty;
            $price = null;
        
            if($sale->sale_type == 'cash'){
                $price = $product->cash_price;
            }
            if($sale->sale_type == 'Short Installment'){
                $price = $product->mrp_price;
            }
            if($sale->sale_type == 'Long Installment'){
                $price = $product->hire_price;
            }

            $salePrice = $qty * $price;

            $totalAmount += $salePrice;

        }

        return $totalAmount;
        
    }

    public static function get($customerId)
    {

        $sales = RetailSale::where('customer_id', $customerId)->with('products')->get();

        $data = [];
        $data['sale'] = [];
        $totalSale = 0;


        foreach ($sales as $sale) {

            $totalAmount = self::getSaleAmount($sale);

            $ld = [
                'id' => $sale->id,
                'date' => date('d-m-Y', strtotime($sale->sale_date)),
                'amount' => $totalAmount,
                'memo_no' => $sale->invoice_no
            ];

            $totalSale += $totalAmount;

            array_push($data['sale'], $ld);
        }

        $data['totalSale'] = $totalSale;
        $data['customer'] = CustomerRegistrationSetup::find($customerId);

        return $data;
    }




    public static function getWithDate($customerId, $rawDate)
    {

        $date = date('Y-m-d', strtotime($rawDate));

        $sales = RetailSale::where('customer_id', $customerId)
            ->with(['products']);

        if ($rawDate != '') {
            $sales = $sales->where('sale_date', $date);
        }

        $sales = $sales->get();

        $data = [];
        $data['sale'] = [];
        $totalSale = 0;

        foreach ($sales as $sale) {

            if ($sale->sale_type == 'Cash') {
                $price = $sale->products->sum('cash_price');
            } else if ($sale->sale_type == 'Short Installment') {
                $price = $sale->products->sum('mrp_price');
            } else {
                $price = $sale->products->sum('hire_price');
            }

            $ld = [
                'id' => $sale->id,
                'date' => date('d-m-Y', strtotime($sale->sale_date)),
                'amount' => $price,
                'memo_no' => $sale->invoice_no,
            ];

            $totalSale += $price;

            array_push($data['sale'], $ld);
        }

        $data['totalSale'] = $totalSale;
        $data['customer'] = CustomerRegistrationSetup::find($customerId);

        return $data;
    }
}
