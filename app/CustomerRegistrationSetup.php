<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerRegistrationSetup extends Model {

    protected $table = "tbl_customers";
    protected $fillable = [
        'freeze', 'project_id', 'code', 'showroom_id', 'name', 'nick_name', 'nid', 'age', 'phone_no', 'marital_status', 'spouse_name', 'fathers_name', 'mothers_name', 'gender', 'current_residence', 'residence_duration', 'total_family_member', 'present_address', 'permanent_address', 'profession_name', 'profession_duration', 'total_earning_member', 'designation', 'monthly_income', 'work_place_address', 'status', 'is_audit', 'is_close', 'created_by', 'updated_by', 'image'
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function agreement() {
        return $this->hasOne(CustomerAgreement::class, 'customer_id', 'id');
    }

    public function sale() {
        return $this->hasOne(RetailSale::class, 'customer_id', 'id');
    }

    public function close() {
        return $this->hasOne(CloseAccount::class, 'customer_id', 'id');
    }

    public function hasSale() {
        $retailSales = RetailSale::where('customer_id', $this->id)->count();

        if ($retailSales > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function totalsaleAmount() {
        $retailSales = RetailSale::where('customer_id', $this->id)->with(['products'])->get();

        $totalSale = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $qty = $product->qty;
                if ($retailSale->sale_type == 'Cash') {
                    $price = $product->cash_price;
                } else if ($retailSale->sale_type == 'Short Installment') {
                    $price = $product->mrp_price;
                } else {
                    $price = $product->hire_price;
                }

                $salePrice = $qty * $price;

                $totalSale += $salePrice;
            }
        }

        return $totalSale;
    }

    public function totalsaleAmountOnlyCash() {
        $retailSales = RetailSale::where('customer_id', $this->id)->with(['products'])->get();

        $totalSale = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $qty = $product->qty;

                $price = $product->sales_price;

                $salePrice = $qty * $price;

                $totalSale += $salePrice;
            }
        }

        return $totalSale;
    }

    public function totalsaleAmountReschedule() {
        $retailSales = RetailSale::where('customer_id', $this->id)->with(['products'])->get();

        $totalSale = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $qty = $product->qty;
                if ($retailSale->sale_type == 'Cash') {
                    $price = $product->cash_price;
                } elseif ($retailSale->sale_type == 'Short Installment') {
                    $price = $product->mrp_price;
                } elseif ($retailSale->sale_type == 'Long Installment') {
                    $price = $product->hire_price;
                } else {
                    $price = $product->cash_price;
                }
                $salePrice = $qty * $price;

                $totalSale += $salePrice;
            }
        }

        return $totalSale;
    }

    public function totalsaleAmountOnlyCashByDate($fromDate, $toDate) {
        $retailSales = RetailSale::where('customer_id', $this->id)->with(['products'])
                ->whereBetween('sale_date', [$fromDate, $toDate])
                ->get();

        $totalSale = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $qty = $product->qty;

                $price = $product->sales_price;

                $salePrice = $qty * $price;

                $totalSale += $salePrice;
            }
        }

        return $totalSale;
    }

    public function saleDate($fromDate, $toDate) {
        $retailSales = RetailSale::where('customer_id', $this->id)->with(['products'])
                ->whereBetween('sale_date', [$fromDate, $toDate])
                ->orderBy('sale_date', 'asc')
                ->get();

        $date = '';

        if (!empty($retailSales) && count($retailSales) > 0) {
            $date = date('d-m-Y', strtotime($retailSales[0]->sale_date));
        }


        return $date;
    }

    public function totalsaleAmountOnlyCashPrev($fromDate) {
        $retailSales = RetailSale::where('customer_id', $this->id)->with(['products'])
                ->where('sale_date', '<', $fromDate)
                ->get();

        $totalSale = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $qty = $product->qty;

                $price = $product->sales_price;

                $salePrice = $qty * $price;

                $totalSale += $salePrice;
            }
        }

        return $totalSale;
    }

    public function totalCollectionAmount() {

        $collectionIds = InstallmentCollection::where('customer_id', $this->id)->select('id')->get()->pluck('id');

        $collectionAmount = InstallmentCollectionList::whereIn('installment_collection_id', $collectionIds)->sum('installment_schedule_amount');

        return $collectionAmount;
    }

    public function totalCollectionAmountByDate($fromDate, $toDate) {

        $collectionIds = InstallmentCollection::where('customer_id', $this->id)->select('id')->get()->pluck('id');

        $collectionAmount = InstallmentCollectionList::whereIn('installment_collection_id', $collectionIds)
                ->whereBetween('installment_collection_date', [$fromDate, $toDate])
                ->sum('installment_schedule_amount');

        return $collectionAmount;
    }

    public function totalCollectionAmountPrev($fromDate) {

        $collectionIds = InstallmentCollection::where('customer_id', $this->id)->select('id')->get()->pluck('id');

        $collectionAmount = InstallmentCollectionList::whereIn('installment_collection_id', $collectionIds)
                ->where('installment_collection_date', '<', $fromDate)
                ->sum('installment_schedule_amount');

        return $collectionAmount;
    }

    public function totalsaleAmountOnlyCashAmountPrev($fromDate) {

        $collectionIds = InstallmentCollection::where('customer_id', $this->id)->select('id')->get()->pluck('id');

        $collectionAmount = InstallmentCollectionList::whereIn('installment_collection_id', $collectionIds)
                ->where('installment_collection_date', '<', $fromDate)
                ->sum('installment_schedule_amount');

        return $collectionAmount;
    }

    public function totalCollectionDetails() {

        $collectionIds = InstallmentCollection::where('customer_id', $this->id)->select('id')->get()->pluck('id');

        $collectionAmount = InstallmentCollectionList::whereIn('installment_collection_id', $collectionIds)->orderBy('installment_collection_date', 'asc')->get();

        return $collectionAmount;
    }

    public function totalSaleAmountWithDateRange($startDate, $endDate) {
        $retailSales = RetailSale::where('customer_id', $this->id)
                ->with(['products'])
                ->where('sale_date', '>=', $startDate)
                ->where('sale_date', '<=', $endDate)
                ->get();

        $totalSale = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $qty = $product->qty;
                $price = $product->sales_price;


                // if ($retailSale->sale_type == 'Cash') {
                // 	$price = $product->cash_price;
                // } else if ($retailSale->sale_type == 'Short Installment') {
                // 	$price = $product->mrp_price;
                // } else {
                // 	$price = $product->hire_price;
                // }

                $salePrice = $qty * $price;

                $totalSale += $salePrice;
            }
        }

        return $totalSale;
    }

    public function totalSaleDiscountWithDateRange($startDate, $endDate) {
        $retailSales = RetailSale::where('customer_id', $this->id)
                ->with(['products'])
                ->where('sale_date', '>=', $startDate)
                ->where('sale_date', '<=', $endDate)
                ->get();

        $totalSale = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $price = $product->discount;

                $totalSale += $price;
            }
        }

        return $totalSale;
    }

    public function totalSaleGiftVoucherWithDateRange($startDate, $endDate) {
        $retailSales = RetailSale::where('customer_id', $this->id)
                ->with(['products'])
                ->where('sale_date', '>=', $startDate)
                ->where('sale_date', '<=', $endDate)
                ->get();

        $totalSale = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $price = $product->gift_voucher;

                $totalSale += $price;
            }
        }

        return $totalSale;
    }

    public function totalSaleExchangeCRTWithDateRange($startDate, $endDate) {
        $retailSales = RetailSale::where('customer_id', $this->id)
                ->with(['products'])
                ->where('sale_date', '>=', $startDate)
                ->where('sale_date', '<=', $endDate)
                ->get();

        $totalSale = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $price = $product->exchange_crt;

                $totalSale += $price;
            }
        }

        return $totalSale;
    }

    public function totalCollectionAmountWithDateRange($startDate, $endDate) {

        $collectionIds = InstallmentCollection::where('customer_id', $this->id)->select('id')->get()->pluck('id');

        $collectionAmount = InstallmentCollectionList::whereIn('installment_collection_id', $collectionIds)
                ->where('installment_collection_date', '>=', $startDate)
                ->where('installment_collection_date', '<=', $endDate)
                ->sum('installment_schedule_amount');

        return $collectionAmount;
    }

    public function totalsaleAmountWithCollector($id) {
        $retailSales = RetailSale::where('customer_id', $id)->with(['products'])->get();

        $totalSale = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $qty = $product->qty;
                if ($retailSale->sale_type == 'Cash') {
                    $price = $product->cash_price;
                } else if ($retailSale->sale_type == 'Short Installment') {
                    $price = $product->mrp_price;
                } else {
                    $price = $product->hire_price;
                }


                $discount = $product->discount;
                $gift = $product->gift_voucher;
                $excrt = $product->exchange_crt;

                $salePrice = $qty * $price;

                $totalSale += $salePrice - ($discount + $gift + $excrt);
            }
        }


        return $totalSale;
    }

    public function totalsaleDiscount() {
        $retailSales = RetailSale::where('customer_id', $this->id)->with(['products'])->get();

        $totalDiscount = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $totalDiscount += $product->discount;
            }
        }


        return $totalDiscount;
    }

    public function totalsaleDiscountByDate($fromDate, $toDate) {
        $retailSales = RetailSale::where('customer_id', $this->id)->with(['products'])
                ->whereBetween('sale_date', [$fromDate, $toDate])
                ->get();

        $totalDiscount = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $totalDiscount += $product->discount;
            }
        }


        return $totalDiscount;
    }

    public function totalsaleDiscountPrev($fromDate) {
        $retailSales = RetailSale::where('customer_id', $this->id)->with(['products'])
                ->where('sale_date', '<', $fromDate)
                ->get();

        $totalDiscount = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $totalDiscount += $product->discount;
            }
        }


        return $totalDiscount;
    }

    public function totalsaleGiftVoucher() {
        $retailSales = RetailSale::where('customer_id', $this->id)->with(['products'])->get();

        $totalGiftVoucher = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $totalGiftVoucher += $product->gift_voucher;
            }
        }


        return $totalGiftVoucher;
    }

    public function totalsaleGiftVoucherByDate($fromDate, $toDate) {
        $retailSales = RetailSale::where('customer_id', $this->id)->with(['products'])
                ->whereBetween('sale_date', [$fromDate, $toDate])
                ->get();

        $totalGiftVoucher = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $totalGiftVoucher += $product->gift_voucher;
            }
        }


        return $totalGiftVoucher;
    }

    public function totalsaleGiftVoucherPrev($fromDate) {
        $retailSales = RetailSale::where('customer_id', $this->id)->with(['products'])
                ->where('sale_date', '<', $fromDate)
                ->get();

        $totalGiftVoucher = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $totalGiftVoucher += $product->gift_voucher;
            }
        }


        return $totalGiftVoucher;
    }

    public function totalsaleExchangeCrt() {
        $retailSales = RetailSale::where('customer_id', $this->id)->with(['products'])->get();

        $totalExchangeCrt = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $totalExchangeCrt += $product->exchange_crt;
            }
        }

        return $totalExchangeCrt;
    }

    public function totalsaleExchangeCrtByDate($fromDate, $toDate) {
        $retailSales = RetailSale::where('customer_id', $this->id)->with(['products'])
                ->whereBetween('sale_date', [$fromDate, $toDate])
                ->get();

        $totalExchangeCrt = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $totalExchangeCrt += $product->exchange_crt;
            }
        }

        return $totalExchangeCrt;
    }

    public function totalsaleExchangeCrtPrev($fromDate) {
        $retailSales = RetailSale::where('customer_id', $this->id)->with(['products'])
                ->where('sale_date', '<', $fromDate)
                ->get();

        $totalExchangeCrt = 0;

        foreach ($retailSales as $retailSale) {

            foreach ($retailSale->products as $product) {

                $totalExchangeCrt += $product->exchange_crt;
            }
        }

        return $totalExchangeCrt;
    }

    public function totalSaleAmountByCollector($collectorId) {
        $installment = Installment::where('customer_id', $this->id)->where('installment_collector_id', $collectorId)->get();
        $installmentAmount = $installment->sum('installment_price') + $installment->sum('booking_amount');

        return $installmentAmount;
    }

    public function totalCollectionAmountByCollector($collectorId) {
        $installmentIds = Installment::where('customer_id', $this->id)->where('installment_collector_id', $collectorId)->select('id')->get()->pluck('id');

        $collectionIds = InstallmentCollection::whereIn('installment_id', $installmentIds)->select('id')->get()->pluck('id');

        $collectionAmount = InstallmentCollectionList::whereIn('installment_collection_id', $collectionIds)->sum('installment_schedule_amount');

        return $collectionAmount;
    }

    public function totalReturnAmount() {

        $retailSaleReturnids = RetailSalesReturn::where('customer_id', $this->id)
                ->select('id')
                ->get()
                ->pluck('id')
                ->toArray();

        $retailSaleReturnAmount = RetailSalesReturnProducts::whereIn('id', $retailSaleReturnids)->sum('sales_price');

        return $retailSaleReturnAmount;
    }

    public function totalReturnAmountByDate($fromDate, $toDate) {

        $retailSaleReturnids = RetailSalesReturn::where('customer_id', $this->id)->whereBetween('date', [$fromDate, $toDate])
                ->select('id')
                ->get()
                ->pluck('id')
                ->toArray();

        $retailSaleReturnAmount = RetailSalesReturnProducts::whereIn('id', $retailSaleReturnids)->sum('sales_price');

        return $retailSaleReturnAmount;
    }

    public function totalReturnAmountPrev($fromDate) {

        $retailSaleReturnids = RetailSalesReturn::where('customer_id', $this->id)->where('date', '<', $fromDate)
                ->select('id')
                ->get()
                ->pluck('id')
                ->toArray();

        $retailSaleReturnAmount = RetailSalesReturnProducts::whereIn('id', $retailSaleReturnids)->sum('sales_price');

        return $retailSaleReturnAmount;
    }

    public function totalReturnAmountWithDateRange($startDate, $endDate) {

        $retailSaleReturnids = RetailSalesReturn::where('customer_id', $this->id)
                ->where('date', '>=', '$startDate')
                ->where('date', '<=', '$endDate')
                ->select('id')
                ->get()
                ->pluck('id')
                ->toArray();

        $retailSaleReturnAmount = RetailSalesReturnProducts::whereIn('id', $retailSaleReturnids)->sum('sales_price');

        return $retailSaleReturnAmount;
    }

}
