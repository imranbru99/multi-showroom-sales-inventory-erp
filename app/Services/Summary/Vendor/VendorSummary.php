<?php

namespace App\Services\Summary\Vendor;

use App\Lifting;
use App\VendorSetup;
use App\LiftingReturn;
use App\PaymentToCompany;
use Illuminate\Support\Carbon;

class VendorSummary {

    public static function Summary($year, $month, $venInformation) {

        $data = [];

        foreach ($venInformation as $venfo) {

            $row = [
                'vendorName' => $venfo->name,
                'previousBalance' => self::getPreviousBalance($venfo, $year),
                'year' => self::getYearlyInfo($venfo, $year),
                'month' => self::getMonthlyInfo($venfo, $year, $month),
            ];

            $row['marketOutstanding'] = $row['previousBalance'] + $row['year']['balance'];

            array_push($data, $row);
        }


        return $data;
    }

    public static function getPreviousBalance($vendor, $year) {

        $firstDateOfYear = Carbon::create($year)->firstOfYear()->format('Y-m-d');

        $liftingAmount = Lifting::where('vendor_id', $vendor->id)
                ->where('vouchar_date', '<', $firstDateOfYear)
                ->sum('total_price');

        $paymentAmount = PaymentToCompany::where('vendor_id', $vendor->id)
                ->where('payment_date', '<', $firstDateOfYear)
                ->sum('payment_now');

        return $liftingAmount - $paymentAmount;
    }

    public static function getYearlyInfo($vendor, $year) {

        $firstDateOfYear = Carbon::create($year)->firstOfYear()->format('Y-m-d');
        $lastDateOfYear = Carbon::create($year)->lastofYear()->format('Y-m-d');

        $liftingAmount = Lifting::where('vendor_id', $vendor->id)
                ->where('vouchar_date', '>=', $firstDateOfYear)
                ->where('vouchar_date', '<=', $lastDateOfYear)
                ->sum('total_price');

        $paymentAmount = PaymentToCompany::where('vendor_id', $vendor->id)
                ->where('payment_date', '>=', $firstDateOfYear)
                ->where('payment_date', '<=', $lastDateOfYear)
                ->sum('payment_now');

        $returnAmount = LiftingReturn::where('vendor_id', $vendor->id)
                ->where('date', '>=', $firstDateOfYear)
                ->where('date', '<=', $lastDateOfYear)
                ->sum('total_price');

        return [
            'lifting' => $liftingAmount,
            'payment' => $paymentAmount,
            'return' => $returnAmount,
            'balance' => $liftingAmount - ($paymentAmount + $returnAmount),
        ];
    }

    public static function getMonthlyInfo($vendor, $year, $month) {

        $firstDateOfMonth = Carbon::create($year, $month)->firstOfMonth()->format('Y-m-d');
        $lastDateOfMonth = Carbon::create($year, $month)->lastOfMonth()->format('Y-m-d');

        $liftingAmount = Lifting::where('vendor_id', $vendor->id)
                ->where('vouchar_date', '>=', $firstDateOfMonth)
                ->where('vouchar_date', '<=', $lastDateOfMonth)
                ->sum('total_price');

        $paymentAmount = PaymentToCompany::where('vendor_id', $vendor->id)
                ->where('payment_date', '>=', $firstDateOfMonth)
                ->where('payment_date', '<=', $lastDateOfMonth)
                ->sum('payment_now');

        $returnAmount = LiftingReturn::where('vendor_id', $vendor->id)
                ->where('date', '>=', $firstDateOfMonth)
                ->where('date', '<=', $lastDateOfMonth)
                ->sum('total_price');

        return [
            'lifting' => $liftingAmount,
            'payment' => $paymentAmount,
            'return' => $returnAmount,
            'balance' => $liftingAmount - ($paymentAmount + $returnAmount),
        ];
    }

}
