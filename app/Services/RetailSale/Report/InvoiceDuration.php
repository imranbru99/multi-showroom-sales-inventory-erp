<?php

namespace App\Services\RetailSale\Report;

use App\RetailSale;
use App\RetailSales;

class InvoiceDuration
{

    public static function Report($startDate, $endDate, $staff)
    {

        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));

        $retailSales = RetailSale::with(['customer', 'installment.collections.collections', 'products'])
            ->where('sale_date', '>=', $startDate)
            ->where('sale_date', '<=', $endDate);

        if ($staff) {
            $retailSales = $retailSales->where('reference_id', $staff);
        }

        $retailSales = $retailSales->get();

        return $retailSales;

    }
}
