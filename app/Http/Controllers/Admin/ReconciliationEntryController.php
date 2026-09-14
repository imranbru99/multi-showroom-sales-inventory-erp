<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\JournalEntry;
use App\ShowroomSetup;
use App\CoaSetup;
use Auth;
use DB;
use PDF;

class ReconciliationEntryController extends Controller {

    public function index(Request $request) {
        $title = "Bank Reconciliation Entry";
        $searchFormLink = 'reconciliationEntry.index';
        $printFormLink = "reconciliationEntry.print";
        $print = $request->print;

        if ($print) {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
            $toDate = date('Y-m-d', strtotime($request->toDate));

            $transactionLists = JournalEntry::select('tbl_account_transactions.*', DB::raw('SUM(tbl_account_transactions.debit_amount) as totalDebitAmount,SUM(tbl_account_transactions.credit_amount) as totalCreditAmount'))
                    ->orWhere(function ($query) use ($fromDate, $toDate) {
                        if (!empty($fromDate)) {
                            $query->where('tbl_account_transactions.voucher_date', '>=', $fromDate)
                            ->where('tbl_account_transactions.voucher_date', '<=', $toDate);
                        }
                    })
                    ->where('tbl_account_transactions.showroom_id', $this->showroomId)
                    ->where('tbl_account_transactions.voucher_type', 'BR')
                    ->where('tbl_account_transactions.approve', '0')
                    ->groupBY('tbl_account_transactions.voucher_no')
                    ->orderBY('tbl_account_transactions.voucher_date', 'desc')
                    ->orderBY('tbl_account_transactions.id', 'desc')
                    ->get();
        } else {
            $transactionLists = [];
            $fromDate = "";
            $toDate = "";
        }

        return view('admin.bankReconcilition.index')->with(compact('title', 'searchFormLink', 'printFormLink', 'print', 'fromDate', 'toDate', 'transactionLists'));
    }

    public function print(Request $request) {
        $title = "Print Journal Voucher Entry";
        $print = $request->print;

        if ($request->fromDate == "" && $request->toDate == "") {
            $transactionLists = JournalEntry::select('tbl_account_transactions.*', DB::raw('SUM(tbl_account_transactions.debit_amount) as totalDebitAmount,SUM(tbl_account_transactions.credit_amount) as totalCreditAmount'))
                    // ->where('tbl_account_transactions.showroom_id',$this->showroomId)
                    ->where('tbl_account_transactions.voucher_type', 'BR')
                    ->where('tbl_account_transactions.approve', '0')
                    ->where('tbl_account_transactions.showroom_id', $this->showroomId)
                    ->groupBY('tbl_account_transactions.voucher_no')
                    ->orderBY('tbl_account_transactions.voucher_date', 'desc')
                    ->orderBY('tbl_account_transactions.id', 'desc')
                    ->get();
            $fromDate = "";
            $toDate = "";
        } else {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
            $toDate = date('Y-m-d', strtotime($request->toDate));

            $transactionLists = JournalEntry::select('tbl_account_transactions.*', DB::raw('SUM(tbl_account_transactions.debit_amount) as totalDebitAmount,SUM(tbl_account_transactions.credit_amount) as totalCreditAmount'))
                    ->orWhere(function ($query) use ($fromDate, $toDate) {
                        if (!empty($fromDate)) {
                            $query->whereBetween('tbl_account_transactions.voucher_date', array($fromDate, $toDate));
                        }
                    })
                    // ->where('tbl_account_transactions.showroom_id',$this->showroomId)
                    ->where('tbl_account_transactions.voucher_type', 'BR')
                    ->where('tbl_account_transactions.approve', '0')
                    ->where('tbl_account_transactions.showroom_id', $this->showroomId)
                    ->groupBY('tbl_account_transactions.voucher_no')
                    ->orderBY('tbl_account_transactions.voucher_date', 'desc')
                    ->orderBY('tbl_account_transactions.id', 'desc')
                    ->get();
        }

        $pdf = PDF::loadView('admin.bankReconcilition.print', ['title' => $title, 'fromDate' => $fromDate, 'toDate' => $toDate, 'print' => $print, 'transactionLists' => $transactionLists], [], ['orientation' => 'P']);
        $pdf->stream('reconciliation_voucher_' . $fromDate . '_' . $toDate . '.pdf');
    }

    public function add() {
        $title = "Add Reconciliation Voucher Entry";
        $formLink = "reconciliationEntry.save";
        $buttonName = "Save";


        $voucherNo = $this->getVoucharNo();
        $showrooms = ShowroomSetup::where('id', $this->company)->where('status', '1')->get();
        $coas = CoaSetup::where('transaction', '1')
                ->where('company_id', $this->company)
                ->orderBy('head_name', 'asc')
                ->get();

        return view('admin.bankReconcilition.add')->with(compact('title', 'formLink', 'buttonName', 'showrooms', 'coas', 'voucherNo'));
    }

    public function save(Request $request) {

        $date = date('Y-m-d', strtotime($request->transactionDate));


        if ($request->debit) {
            $countDebit = count($request->debit);
            $postData = [];
            for ($i = 0; $i < $countDebit; $i++) {
                $postData[] = [
                    'voucher_no' => $request->voucharNo,
                    'voucher_type' => "BR",
                    'voucher_date' => $date,
                    'coa_head_code' => $request->coa[$i],
                    'showroom_id' => $this->showroomId,
                    'narration' => $request->remarks,
                    'debit_amount' => $request->debit[$i],
                    'credit_amount' => $request->credit[$i],
                    'posted' => "I",
                    'bank_reconciliation' => "pending",
                ];
            }
            JournalEntry::insert($postData);
        }

        return redirect(route('reconciliationEntry.index'))->with('msg', 'Journal Voucher Added Successfully');
    }

    public function edit($id) {
        $title = "Edit Reconciliation Voucher Entry";
        $formLink = "journalEntry.update";
        $buttonName = "Update";

        $info = $this->findJournalInfo($id);
        $journalEntry = $info[0];
        $journalEntries = $info[1];

        $coas = CoaSetup::where('transaction', '1')
                ->where('company_id', $this->company)
                ->orderBy('head_name', 'asc')
                ->get();

        return view('admin.bankReconcilition.edit')->with(compact('title', 'formLink', 'buttonName', 'coas', 'journalEntry', 'journalEntries'));
    }

    public function update(Request $request) {

        $date = date('Y-m-d', strtotime($request->transactionDate));
        JournalEntry::where('voucher_no', $request->voucharNo)->delete();

        if ($request->debit) {
            $postData = [];
            $countDebit = count($request->debit);
            for ($i = 0; $i < $countDebit; $i++) {
                $postData[] = [
                    'voucher_no' => $request->voucharNo,
                    'voucher_type' => "BR",
                    'voucher_date' => $date,
                    'coa_head_code' => $request->coa[$i],
                    'showroom_id' => $this->showroomId,
                    'narration' => $request->remarks,
                    'debit_amount' => $request->debit[$i],
                    'credit_amount' => $request->credit[$i],
                    'posted' => "I",
                ];
            }
            JournalEntry::insert($postData);
        }

        return redirect(route('reconciliationEntry.index'))->with('msg', 'Journal Voucher Updated Successfully');
    }

    public function view($journalEntryId) {
        $title = "View Reconciliation Voucher Entry";

        $info = $this->findJournalInfo($journalEntryId);
        $journalEntry = $info[0];
        $journalEntries = $info[1];

        return view('admin.bankReconcilition.view')->with(compact('title', 'journalEntry', 'journalEntries'));
    }

    public function printJournalVoucher($journalEntryId) {
        $title = "Print Reconciliation Voucher";

        $info = $this->findJournalInfo($journalEntryId);
        $journalEntry = $info[0];
        $journalEntries = $info[1];

        $pdf = PDF::loadView('admin.bankReconcilition.printJournalVoucher', ['title' => $title, 'journalEntry' => $journalEntry, 'journalEntries' => $journalEntries], [], ['orientation' => 'P']);
        $pdf->stream('journal_voucher_' . $journalEntry->voucher_no . '.pdf');
    }

    public function findJournalInfo($id) {
        $journalEntry = JournalEntry::select('tbl_account_transactions.*', 'tbl_showroom.name as showroomName')
                ->leftJoin('tbl_showroom', 'tbl_showroom.id', '=', 'tbl_account_transactions.showroom_id')
                // ->where('tbl_account_transactions.showroom_id',$this->showroomId)
                ->where('tbl_account_transactions.id', $id)
                ->first();

        $journalEntries = JournalEntry::select('tbl_account_transactions.*', 'tbl_coa.head_name as accountHeadName')
                ->leftJoin('tbl_coa', 'tbl_coa.head_code', '=', 'tbl_account_transactions.coa_head_code')
                // ->where('tbl_account_transactions.showroom_id',$this->showroomId)
                ->where('voucher_no', $journalEntry->voucher_no)
                ->orderBy('tbl_account_transactions.id', 'asc')
                ->get();

        return array($journalEntry, $journalEntries);
    }

    public function changePublish(Request $request) {
        $journalEntryId = $request->journalEntryId;

        $journalEntry = JournalEntry::find($journalEntryId);

        if ($journalEntry->active == 0) {
            JournalEntry::where('voucher_no', $journalEntry->voucher_no)->update(['active' => 1]);
        } else {
            JournalEntry::where('voucher_no', $journalEntry->voucher_no)->update(['active' => 0]);
        }
    }

    public function delete(Request $request) {
        $journalEntry = JournalEntry::find($request->journalEntryId);

        JournalEntry::where('voucher_no', $journalEntry->voucher_no)->delete();
    }

    public function getVoucharNo() {
        // $showroomId = $showroomId;
//        $showroomPrefix = ShowroomSetup::where('id', $showroomId)->first();

        $str = "BR-";

        $serialNo = JournalEntry::where('voucher_type', 'JV')
                ->where('showroom_id', $this->showroomId)
                ->where('voucher_no', 'LIKE', '%' . $str . '%')
                ->max('voucher_no');

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

        $coas = CoaSetup::where('transaction', '1')
                ->where('company_id', $this->company)
                ->orderBy('head_name', 'asc')
                ->get();

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
