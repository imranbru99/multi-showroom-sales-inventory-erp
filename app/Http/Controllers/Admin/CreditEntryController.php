<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CreditEntry;
use App\ShowroomSetup;
use App\CoaSetup;
use Auth;
use DB;
use PDF;

class CreditEntryController extends Controller {

    public function index(Request $request) {
        $title = "Credit Voucher Entry";
        $searchFormLink = 'creditEntry.index';
        $printFormLink = "creditEntry.print";
        $print = $request->print;
        $fromDate = date('Y-m-d', strtotime($request->fromDate));
        $toDate = date('Y-m-d', strtotime($request->toDate));

        if ($print) {
            $transactionLists = CreditEntry::select('tbl_account_transactions.*', 'view_all_account.debitHeadname as debitHeadname', 'view_all_account.creditHeadName as creditHeadName')
                    ->leftJoin('view_all_account', 'view_all_account.voucherNo', '=', 'tbl_account_transactions.voucher_no')
                    ->orWhere(function($query) use($fromDate, $toDate) {
                        if (!empty($fromDate)) {
                            $query->whereBetween('tbl_account_transactions.voucher_date', array($fromDate, $toDate));
                        }
                    })
                    ->where('tbl_account_transactions.showroom_id', $this->showroomId)
                    ->where('tbl_account_transactions.voucher_type', 'CV')
                    ->where('tbl_account_transactions.credit_amount', '0')
                    ->where('tbl_account_transactions.approve', '0')
                    ->groupBY('view_all_account.voucherNo')
                    ->orderBY('tbl_account_transactions.voucher_date', 'desc')
                    ->get();
        } else {
            $transactionLists = CreditEntry::select('tbl_account_transactions.*', 'view_all_account.debitHeadname as debitHeadname', 'view_all_account.creditHeadName as creditHeadName')
                    ->where('tbl_account_transactions.showroom_id', $this->showroomId)
                    ->leftJoin('view_all_account', 'view_all_account.voucherNo', '=', 'tbl_account_transactions.voucher_no')
                    ->where('tbl_account_transactions.voucher_type', 'CV')
                    ->where('tbl_account_transactions.credit_amount', '0')
                    ->where('tbl_account_transactions.approve', '0')
                    ->groupBY('view_all_account.voucherNo')
                    ->orderBY('tbl_account_transactions.voucher_date', 'desc')
                    ->get();
            $fromDate = "";
            $toDate = "";
        }

        return view('admin.creditEntry.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'fromDate', 'toDate', 'transactionLists'));
    }

    public function print(Request $request) {
        $title = "Print Credit Voucher Entry";
        $print = $request->print;

        if ($request->fromDate == "" && $request->toDate == "") {
            $transactionLists = CreditEntry::select('tbl_account_transactions.*', 'view_all_account.debitHeadname as debitHeadname', 'view_all_account.creditHeadName as creditHeadName')
                    ->where('tbl_account_transactions.showroom_id', $this->showroomId)
                    ->leftJoin('view_all_account', 'view_all_account.voucherNo', '=', 'tbl_account_transactions.voucher_no')
                    ->where('tbl_account_transactions.voucher_type', 'CV')
                    ->where('tbl_account_transactions.credit_amount', '0')
                    ->where('tbl_account_transactions.approve', '0')
                    ->groupBY('view_all_account.voucherNo')
                    ->orderBY('tbl_account_transactions.voucher_date', 'desc')
                    ->get();
            $fromDate = "";
            $toDate = "";
        } else {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
            $toDate = date('Y-m-d', strtotime($request->toDate));

            $transactionLists = CreditEntry::select('tbl_account_transactions.*', 'view_all_account.debitHeadname as debitHeadname', 'view_all_account.creditHeadName as creditHeadName')
                    ->leftJoin('view_all_account', 'view_all_account.voucherNo', '=', 'tbl_account_transactions.voucher_no')
                    ->orWhere(function($query) use($fromDate, $toDate) {
                        if (!empty($fromDate)) {
                            $query->whereBetween('tbl_account_transactions.voucher_date', array($fromDate, $toDate));
                        }
                    })
                    ->where('tbl_account_transactions.showroom_id', $this->showroomId)
                    ->where('tbl_account_transactions.voucher_type', 'CV')
                    ->where('tbl_account_transactions.credit_amount', '0')
                    ->where('tbl_account_transactions.approve', '0')
                    ->groupBY('view_all_account.voucherNo')
                    ->orderBY('tbl_account_transactions.voucher_date', 'desc')
                    ->get();
        }

        $pdf = PDF::loadView('admin.creditEntry.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'print' => $print, 'transactionLists' => $transactionLists], [], ['orientation' => 'P']);
        $pdf->stream('credit_voucher_' . $fromDate . '_' . $toDate . '.pdf');
    }

    public function add() {
        $title = "Add Credit Voucher Entry";
        $formLink = "creditEntry.save";
        $buttonName = "Save";

        $voucherNo = $this->getVoucharNo($this->showroomId);
        $showrooms = ShowroomSetup::where('company_id', $this->company)->where('id', $this->showroomId)->where('status', '1')->get();
        $debitCoas = CoaSetup::where('company_id', $this->company)->where('head_code', 'LIKE', '%1020%')->where('transaction', '1')->orderBy('head_name', 'asc')->get();
        $coas = CoaSetup::where('company_id', $this->company)->where('transaction', '1')->orderBy('head_name', 'asc')->get();

        return view('admin.creditEntry.add')->with(compact('title', 'formLink', 'buttonName', 'showrooms', 'coas', 'debitCoas', 'voucherNo'));
    }

    public function save(Request $request) {
        // dd($request->all());
        $date = date('Y-m-d', strtotime($request->transactionDate));

        CreditEntry::create([
            'voucher_no' => $request->voucharNo,
            'voucher_type' => "CV",
            'voucher_date' => $date,
            'coa_head_code' => $request->debitAccountHead,
            'showroom_id' => $this->showroomId,
            'narration' => $request->remarks,
            'debit_amount' => $request->totalCredit,
            'credit_amount' => "0",
            'posted' => "I",
            'created_by' => $this->userId
        ]);

        $countCredit = count($request->credit);
        if ($request->credit) {
            $postData = [];
            for ($i = 0; $i < $countCredit; $i++) {
                $postData[] = [
                    'voucher_no' => $request->voucharNo,
                    'voucher_type' => "CV",
                    'voucher_date' => $date,
                    'coa_head_code' => $request->coa[$i],
                    'showroom_id' => $this->showroomId,
                    'narration' => $request->remarks,
                    'debit_amount' => "0",
                    'credit_amount' => $request->credit[$i],
                    'posted' => "I",
                    'created_by' => $this->userId
                ];
            }
            CreditEntry::insert($postData);
        }

        return redirect(route('creditEntry.index'))->with('msg', 'Credit Voucher Added Successfully');
    }

    public function edit($creditEntryId) {
        $title = "Edit Credit Voucher Entry";
        $formLink = "creditEntry.update";
        $buttonName = "Update";

        $info = $this->findCreditInfo($creditEntryId);
        $creditEntry = $info[0];
        $creditEntries = $info[1];

        $creditCoas = CoaSetup::where('company_id', $this->company)->where('head_code', 'LIKE', '%1020%')->where('transaction', '1')->orderBy('head_name', 'asc')->get();
        $coas = CoaSetup::where('company_id', $this->company)->where('transaction', '1')->orderBy('head_name', 'asc')->get();

        return view('admin.creditEntry.edit')->with(compact('title', 'formLink', 'buttonName', 'creditEntry', 'creditEntries', 'coas', 'creditCoas'));
    }

    public function update(Request $request) {
        // dd($request->all());
        $date = date('Y-m-d', strtotime($request->transactionDate));

        CreditEntry::where('voucher_no', $request->voucharNo)->delete();

        CreditEntry::create([
            'voucher_no' => $request->voucharNo,
            'voucher_type' => "CV",
            'voucher_date' => $date,
            'coa_head_code' => $request->debitAccountHead,
            'showroom_id' => $this->showroomId,
            'narration' => $request->remarks,
            'debit_amount' => $request->totalCredit,
            'credit_amount' => "0",
            'posted' => "I",
            'created_by' => $request->createdBy,
            'updated_by' => $this->userId
        ]);

        $countCredit = count($request->credit);
        if ($request->credit) {
            $postData = [];
            for ($i = 0; $i < $countCredit; $i++) {
                $postData[] = [
                    'voucher_no' => $request->voucharNo,
                    'voucher_type' => "CV",
                    'voucher_date' => $date,
                    'coa_head_code' => $request->coa[$i],
                    'showroom_id' => $this->showroomId,
                    'narration' => $request->remarks,
                    'debit_amount' => "0",
                    'credit_amount' => $request->credit[$i],
                    'posted' => "I",
                    'created_by' => $request->createdBy,
                    'updated_by' => $this->userId
                ];
            }
            CreditEntry::insert($postData);
        }

        return redirect(route('creditEntry.index'))->with('msg', 'Credit Voucher Updated Successfully');
    }

    public function view($creditEntryId) {
        $title = "View Credit Voucher Entry";

        $info = $this->findCreditInfo($creditEntryId);
        $creditEntry = $info[0];
        $creditEntries = $info[1];

        return view('admin.creditEntry.view')->with(compact('title', 'creditEntry', 'creditEntries'));
    }

    public function printCreditVoucher($creditEntryId) {
        $title = "Print Credit Voucher";

        $info = $this->findCreditInfo($creditEntryId);
        $creditEntry = $info[0];
        $creditEntries = $info[1];

        $pdf = PDF::loadView('admin.creditEntry.printCreditVoucher', ['title' => $title, 'creditEntry' => $creditEntry, 'creditEntries' => $creditEntries], [], ['orientation' => 'P']);
        $pdf->stream('credit_voucher_' . $creditEntry->voucher_no . '.pdf');
    }

    public function findCreditInfo($creditEntryId) {
        $creditEntry = CreditEntry::select('tbl_account_transactions.*', 'tbl_showroom.name as showroomName', 'tbl_coa.head_name as creditHeadName')
                ->leftJoin('tbl_showroom', 'tbl_showroom.id', '=', 'tbl_account_transactions.showroom_id')
                ->leftJoin('tbl_coa', 'tbl_coa.head_code', '=', 'tbl_account_transactions.coa_head_code')
                ->where('tbl_account_transactions.showroom_id', $this->showroomId)
                ->where('tbl_account_transactions.id', $creditEntryId)
                ->first();

        $creditEntries = CreditEntry::select('tbl_account_transactions.*', 'tbl_coa.head_name as creditHeadName')
                ->leftJoin('tbl_coa', 'tbl_coa.head_code', '=', 'tbl_account_transactions.coa_head_code')
                ->where('tbl_account_transactions.showroom_id', $this->showroomId)
                ->where('voucher_no', $creditEntry->voucher_no)
                ->where('debit_amount', '0')
                ->get();

        return array($creditEntry, $creditEntries);
    }

    public function changePublish(Request $request) {
        $creditEntryId = $request->creditEntryId;

        $creditEntry = CreditEntry::find($creditEntryId);

        if ($creditEntry->active == 0) {
            CreditEntry::where('voucher_no', $creditEntry->voucher_no)->update(['active' => 1]);
        } else {
            CreditEntry::where('voucher_no', $creditEntry->voucher_no)->update(['active' => 0]);
        }
    }

    public function delete(Request $request) {
        $creditEntry = CreditEntry::find($request->creditEntryId);

        CreditEntry::where('voucher_no', $creditEntry->voucher_no)->delete();
    }

    public function getVoucharNo($showroomId) {
        // $showroomId = $request->showroomId;

        $showroomPrefix = ShowroomSetup::where('id', $showroomId)->first();

        $str = "CV-" . $showroomPrefix->prefix . "-";

        $serialNo = CreditEntry::where('voucher_type', 'CV')->where('voucher_no', 'LIKE', '%' . $str . '%')->max('voucher_no');

        if ($serialNo) {
            $serialNo = substr($serialNo, strlen($str));
            $voucherNo = $str . ($serialNo + 1);
        } else {
            $voucherNo = $str . "1000000001";
        }


        return $voucherNo;
    }

    public function getCoa(Request $request) {
        $output = '';
        $total = $request->total;

        $coas = CoaSetup::where('company_id', $this->company)->where('transaction', '1')->orderBy('head_name', 'asc')->get();

        if ($coas) {
            $output .= '<select class="chosen-select coa coa_' . $total . '" id="coa" name="coa[]">';
            $output .= '<option value="">Select Account Name</option>';
            foreach ($coas as $coas) {
                $output .= '<option value="' . $coas->head_code . '">' . $coas->head_name . '</option>';
            }
            $output .= '</select>';
        } else {
            $output .= '<select class="chosen-select coa coa_' . $total . '" id="coa" name="coa[]">';
            $output .= '<option value="">Select Account Name</option>';
            $output .= '</select>';
        }

        echo $output;
    }

}
