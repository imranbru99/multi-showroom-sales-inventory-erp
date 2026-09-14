<?php

namespace App\Services\Statement\Vendor;

use App\Lifting;
use App\LiftingReturn;
use Carbon\CarbonPeriod;
use App\PaymentToCompany;
use App\VendorSetup;

class Statement
{

    public static function previousBalance($vendor, $fromDate)
    {

        $liftingAmount = Lifting::whereIn('vendor_id', $vendor)->where('vouchar_date', '<', $fromDate)->sum('total_price');

        $paymentAmount = PaymentToCompany::where('vendor_id', $vendor)->where('payment_date', '<', $fromDate)->sum('payment_now');

        $returnAmount = LiftingReturn::where('vendor_id', $vendor)->where('date', '<', $fromDate)->sum('total_price');

        return $liftingAmount - ($returnAmount + $paymentAmount);
    }


    public static function Statement($vendor, $fromDate, $toDate, $previousBalance)
    {

        $balance = $previousBalance;

        $vendorInfo = VendorSetup::where('id', $vendor)->first();

        $dateRange = CarbonPeriod::create($fromDate, $toDate);

        $statements = [];

        foreach ($dateRange as $date) {

            $d = $date->format('Y-m-d');
            $remarks = [];
            $liftingAmount = Lifting::whereIn('vendor_id', $vendor)
                ->where('vouchar_date',  $d)
                ->sum('total_price');

            $payment = PaymentToCompany::where('vendor_id', $vendor)
                ->where('payment_date', $d)
                ->get();

            $paymentAmount = $payment->sum('payment_now');
            $purchase = '';
            if ($liftingAmount > 0) {
                $purchase = 'Purchase';
            }

            if (!empty($payment) && count($payment) > 0) {
                foreach ($payment as $pay) {
                    $remarks[] = [
                        'payment' => !empty($pay->remarks) ? $pay->remarks . ' (Payment)' : 'Payment',
                        'purchase' => $purchase
                    ];
                }
            } else {
                $remarks[] = [
                    'payment' => '',
                    'purchase' => $purchase
                ];
            }





            $returnAmount = LiftingReturn::where('vendor_id', $vendor)
                ->where('date', $d)
                ->sum('total_price');

            $row = [
                'vendorName' => $vendorInfo->name,
                'date' => $date->format('d-m-Y'),
                'lifting' => $liftingAmount,
                'payment' => $paymentAmount,
                'return' => $returnAmount,
                'balance' => ($balance + $liftingAmount) - ($returnAmount + $paymentAmount),
                'remarks' => $remarks,
            ];

            $balance = $row['balance'];

            if ($row['lifting'] > 0 || $row['payment'] > 0 || $row['return'] > 0) {
                array_push($statements, $row);
            }
        }

        // dd($statements);

        return $statements;
    }
}
