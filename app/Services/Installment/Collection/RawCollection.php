<?php

namespace App\Services\Installment\Collection;

use App\Installment;
use App\InstallmentSchedule;
use App\InstallmentCollection;
use App\CustomerRegistrationSetup;
use App\InstallmentCollectionList;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RawCollection {

    public static function formatCollectedCustomerSaveData($request) {
        $collectedCustomers = [];
        $count = count($request->receiveAmount);
        for ($i = 0; $i < $count; $i++) {

            $collectedCustomers[] = [
                'customerId' => $request->customer_id[$i],
                'receiveAmount' => $request->receiveAmount[$i],
                'mrpNo' => $request->mrpNo[$i],
            ];
        }

        return $collectedCustomers;
    }

    public static function saveMultiple($collectedCustomers, $request, $company, $showroom) {

        foreach ($collectedCustomers as $collectedCustomer) {
            self::save($collectedCustomer, $request, $company, $showroom);
        }
    }

    public static function save($collectedCustomer, $request, $company, $showroom) {

        $collectionAmount = $collectedCustomer['receiveAmount'];

        $customerID = $collectedCustomer['customerId'];

        // find if customer have any installments available
        //        $unpaidInstallmentScheduleIds = self::incompleteCollections($collectedCustomer['customerId']);
        $unpaidInstallmentScheduleId = InstallmentSchedule::with('installment')
                ->whereHas('installment', function ($q) use ($customerID) {
                    $q->where('customer_id', $customerID);
                })
                ->orderBy('installment_schedule_date', 'asc')
                ->where('status', 1)
                ->select('id')
                ->first();

        if (!empty($unpaidInstallmentScheduleId)) {
            // ========== if have installments then complete them and make them inactive ==========
            //            foreach ($unpaidInstallmentScheduleIds as $unpaidInstallmentScheduleId) {

            if ($collectionAmount != 0) {
                //                    break;
                // get installment for installment amount

                $installmentSchedule = InstallmentSchedule::where('id', $unpaidInstallmentScheduleId->id)->with(['installment'])->first();
                $installmentAmount = $installmentSchedule->installment_schedule_amount;

                //                $amount = 0;
                //                if ($collectionAmount > $installmentAmount) {
                //                    $amount = $installmentAmount;
                //                    $collectionAmount -= $installmentAmount;
                //                } else {
                $amount = $collectionAmount;
                //                }
                // dd($installmentSchedule);
                // get or create customer collection parent row

                $collection = InstallmentCollection::where('customer_id', $collectedCustomer['customerId'])->with(['sale'])->first();

                if (!$collection) {

                    $collection = InstallmentCollection::create([
                                'reference_id' => $request->collector,
                                'company_id' => $company,
                                'showroom_id' => $showroom,
                                'project_id' => $request->project_id,
                                'installment_id' => $installmentSchedule->installment->id,
                                'customer_id' => $collectedCustomer['customerId'],
                                'invoice_no' => $installmentSchedule->installment->invoice_no,
                                'installment_price' => $installmentSchedule->installment->installment_price,
                                'booking_amount' => $installmentSchedule->installment->booking_amount,
                                'installment_qty' => $installmentSchedule->installment->installment_qty,
                                'installment_amount' => $installmentSchedule->installment->installment_amount,
                                'created_by' => Auth::user()->id,
                    ]);
                }

                // make collection
                InstallmentCollectionList::create([
                    'company_id' => $company,
                    'showroom_id' => $showroom,
                    'project_id' => $request->project_id,
                    'installment_id' => $installmentSchedule->installment->id,
                    'installment_schedule_id' => $installmentSchedule->id,
                    'installment_collection_id' => $collection->id,
                    'invoice_no' => $installmentSchedule->installment->invoice_no,
                    'installment_schedule_date' => $installmentSchedule->installment_schedule_date,
                    'installment_collection_date' => date('Y-m-d', strtotime($request->date)),
                    'installment_schedule_amount' => $amount,
                    'invoice_no' => $collectedCustomer['mrpNo'],
                    'created_by' => Auth::user()->id,
                ]);

                // make installment schedule inactive

                $installmentSchedule->update([
                    'status' => 0
                ]);
            }
            //            }
        } else {
            // if not have installment then collect without installment

            $collection = InstallmentCollection::where('customer_id', $collectedCustomer['customerId'])
                    ->where('reference_id', $request->collector)
                    ->first();

            if (!$collection) {

                $collection = InstallmentCollection::create([
                            'company_id' => $company,
                            'showroom_id' => $showroom,
                            'project_id' => $request->project_id,
                            'installment_id' => 0,
                            'customer_id' => $collectedCustomer['customerId'],
                            'reference_id' => $request->collector,
                            'invoice_no' => 0,
                            'installment_price' => 0,
                            'booking_amount' => 0,
                            'installment_qty' => 0,
                            'installment_amount' => 0,
                            'created_by' => Auth::user()->id,
                ]);
            }

            InstallmentCollectionList::create([
                'company_id' => $company,
                'showroom_id' => $showroom,
                'project_id' => $request->project_id,
                'installment_id' => 0,
                'installment_schedule_id' => 0,
                'installment_collection_id' => $collection->id,
                'invoice_no' => 0,
                'installment_schedule_date' => 0,
                'installment_collection_date' => date('Y-m-d', strtotime($request->date)),
                'installment_schedule_amount' => $collectionAmount,
                'invoice_no' => $collectedCustomer['mrpNo'],
                'created_by' => Auth::user()->id,
            ]);
        }

        if (!empty($request->collector)) {


            $RetailCustomerId = \App\RetailSale::where('customer_id', $collectedCustomer['customerId'])
                    // ->select('customer_id')
                    ->update([
                    // 'reference_id' => $request->collector,
            ]);


            // collectionCustomerIds 
            //            $collectionCustomerId = InstallmentCollection::where('customer_id', $collectedCustomer['customerId'])
            //                ->update([
            //                     'reference_id' => $request->collector,
            //                ]);



            $agreeCustomer = \App\CustomerAgreement::where('customer_id', $collectedCustomer['customerId'])->first();

            if (!empty($agreeCustomer)) {
                $agreeCustomer->update([
                    'employee_id' => $request->collector,
                ]);
            } else {
                $newEmp = \App\CustomerRegistration::where('id', $collectedCustomer['customerId'])->first();

                \App\CustomerAgreement::create([
                    'company_id' => $company,
                    'showroom_id' => $showroom,
                    'customer_name' => $newEmp->customer_name,
                    'account_no' => $newEmp->code,
                    'customer_id' => $newEmp->id,
                    'nid' => $newEmp->nid,
                    'mobile_no' => $newEmp->mobile_no,
                    'address' => $newEmp->address,
                    'employee_id' => $request->collector,
                ]);
            }
        }
    }

    public static function incompleteCollections($customer) {

        $scheduleIds = InstallmentSchedule::with(['installment'])
                ->whereHas('installment', function ($q) use ($customer) {
                    $q->where('customer_id', $customer);
                })
                ->orderBy('installment_schedule_date', 'asc')
                ->where('status', 1)
                ->select(['id'])
                ->get()
                ->pluck('id')
                ->toArray();



        $inCompleteCollectionIds = $scheduleIds;

        return $inCompleteCollectionIds;
    }

}
