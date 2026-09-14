<?php

namespace App\Services\Collection;

use App\Installment;
use App\InstallmentCollectionList;

class UpComingCollection
{

    public static function Report($start_date, $end_date, $staff)
    {

        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));

        $installments = Installment::with(['schedule', 'customer'])
            ->whereHas('schedule', function ($q) use ($start_date, $end_date) {
                $q->where('installment_schedule_date',  '>=', $start_date)
                    ->where('installment_schedule_date',  '<=', $end_date);
            });

        if ($staff) {
            $installments = $installments->where('installment_collector_id', $staff);
        }

        $installments = $installments->get();

        $data['installments'] = $installments;

        return $data;
    }
}
