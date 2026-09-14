<?php

namespace App\Http\Controllers\Admin;

use DB;

use App\DealerSetup;
use App\StaffSetup;
use App\DealerTransfer;
use App\SalesReturn;
use App\ProductIssue;
use App\DealerCollection;
use App\AdvanceCollection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DealerTransferController extends Controller
{

    public function index()
    {
        $title = "Dealer Transfer";

        $dealerTransfers = DealerTransfer::with('dealer', 'transfer_from', 'transfer_to', 'user')
            ->where('showroom_id', $this->showroomId)
            ->get();

        return view('admin.dealerTransfer.index')->with(compact('title', 'dealerTransfers',));
    }

    public function add()
    {
        $title = "Add Dealer Transfer";

        $formLink = 'dealerTransfer.save';
        $buttonName = 'Save';

        $dealers = DealerSetup::where('status', 1)->get();
        $employees = StaffSetup::where('status', 1)->get();


        return view('admin.dealerTransfer.add')->with(compact('title', 'employees', 'dealers', 'formLink', 'buttonName'));
    }

    public function save(Request $request)
    {
        try {
            DealerTransfer::create([
                'showroom_id' => $this->showroomId,
                'dealer_id' => $request->dealer_id,
                'transfer_from' => $request->transfer_from,
                'transfer_to' => $request->transfer_to,
                'transfer_date' => date('Y-m-d', strtotime($request->date)),
                'created_by' => auth()->user()->id,
            ]);


            $sales =  ProductIssue::where('sales_by', $request->transfer_from)
                ->where('dealer_id', $request->dealer_id)
                ->get();

            foreach ($sales as $sale) {
                $sale->update([
                    'sales_by' => $request->transfer_to,
                ]);
            }

            // exit;

            $collections = DealerCollection::where('sale_by', $request->transfer_from)
                ->where('dealer_id', $request->dealer_id)
                ->get();

            foreach ($collections as $collection) {
                $collection->update([
                    'sale_by' => $request->transfer_to,
                ]);
            }

            $advanceCollections = AdvanceCollection::where('sale_by', $request->transfer_from)
                ->where('dealer_id', $request->dealer_id)
                ->get();

            foreach ($advanceCollections as $advanceCollection) {
                $advanceCollection->update([
                    'sale_by' => $request->transfer_to,
                ]);
            }

            $salesReturns = SalesReturn::where('sales_by', $request->transfer_from)
                ->where('dealer_id', $request->dealer_id)
                ->get();

            foreach ($salesReturns as $salesReturn) {
                $salesReturn->update([
                    'sale_by' => $request->transfer_to,
                ]);
            }

            DB::commit();

            return redirect(route('dealerTransfer.index'))->with('msg', 'Dealer Transfer Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect(route('dealerTransfer.index'))->with('msg', 'Something Went Wrong!');
        }
    }


    public function edit($id)
    {
        $title = "Add Dealer Transfer";

        $formLink = 'dealerTransfer.update';
        $buttonName = 'Update';

        $dealers = DealerSetup::where('status', 1)->get();
        $employees = StaffSetup::where('status', 1)->get();

        $dealerTransfer = DealerTransfer::find($id);


        return view('admin.dealerTransfer.edit')->with(compact('title', 'dealerTransfer', 'employees', 'dealers', 'formLink', 'buttonName'));
    }


    public function update(Request $request)
    {

        try {
            $dealerTransfer = DealerTransfer::find($request->id);
            $dealerTransfer->update([
                'showroom_id' => $this->showroomId,
                'dealer_id' => $request->dealer_id,
                'transfer_from' => $request->transfer_from,
                'transfer_to' => $request->transfer_to,
                'transfer_date' => date('Y-m-d', strtotime($request->date)),
                'created_by' => auth()->user()->id,
            ]);


            $sales =  ProductIssue::where('sales_by', $request->transfer_from)
                ->where('dealer_id', $request->dealer_id)
                ->get();

            foreach ($sales as $sale) {
                $sale->update([
                    'sales_by' => $request->transfer_to,
                ]);
            }

            $collections = DealerCollection::where('sale_by', $request->transfer_from)
                ->where('dealer_id', $request->dealer_id)
                ->get();

            foreach ($collections as $collection) {
                $collection->update([
                    'sale_by' => $request->transfer_to,
                ]);
            }

            $advanceCollections = AdvanceCollection::where('sale_by', $request->transfer_from)
                ->where('dealer_id', $request->dealer_id)
                ->get();

            foreach ($advanceCollections as $advanceCollection) {
                $advanceCollection->update([
                    'sale_by' => $request->transfer_to,
                ]);
            }

            $salesReturns = SalesReturn::where('sales_by', $request->transfer_from)
                ->where('dealer_id', $request->dealer_id)
                ->get();

            foreach ($salesReturns as $salesReturn) {
                $salesReturn->update([
                    'sale_by' => $request->transfer_to,
                ]);
            }

            DB::commit();

            return redirect(route('dealerTransfer.index'))->with('msg', 'Dealer Transfer Updated Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect(route('dealerTransfer.index'))->with('msg', 'Something Went Wrong!');
        }
    }



    public function getEmployee(Request $request)
    {
        $employees = StaffSetup::where('status', 1)
            ->whereNotIn('id', [$request->id])
            ->get();

        return $employees;
    }
}
