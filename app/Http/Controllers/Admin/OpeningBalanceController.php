<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\OpeningBalance;
use App\ShowroomSetup;
use App\CoaSetup;

use Auth;
use DB;
use PDF;

class OpeningBalanceController extends Controller
{
    public function index(Request $request)
    {
    	$title = "Openning Balance Entry";
        $searchFormLink  = 'openingBalanceEntry.index';
        $printFormLink = "openingBalanceEntry.print";
        $print = $request->print;

        if ($print)
        {
            $fromDate = date('Y-m-d',strtotime($request->fromDate));
            $toDate = date('Y-m-d',strtotime($request->toDate));

            $transactionLists = OpeningBalance::select('tbl_account_transactions.*',DB::raw('SUM(tbl_account_transactions.debit_amount) as totalDebitAmount,SUM(tbl_account_transactions.credit_amount) as totalCreditAmount'))
                ->orWhere(function($query) use($fromDate,$toDate){
                    if (!empty($fromDate))
                    {
                        $query->whereBetween('tbl_account_transactions.voucher_date', array($fromDate,$toDate));
                    }
                })
                ->where('tbl_account_transactions.showroom_id',$this->showroomId)
                ->where('tbl_account_transactions.voucher_type','OB')
                ->groupBY('tbl_account_transactions.voucher_no')
                ->orderBY('tbl_account_transactions.voucher_no','asc')
                ->get();
        }
        else
        {
            $transactionLists = OpeningBalance::select('tbl_account_transactions.*',DB::raw('SUM(tbl_account_transactions.debit_amount) as totalDebitAmount,SUM(tbl_account_transactions.credit_amount) as totalCreditAmount'))
                ->where('tbl_account_transactions.showroom_id',$this->showroomId)
                ->where('tbl_account_transactions.voucher_type','OB')
                ->groupBY('tbl_account_transactions.voucher_no')
                ->orderBY('tbl_account_transactions.voucher_no','asc')
                ->get();
            $fromDate = "";
            $toDate = "";
        }

    	return view('admin.openingBalanceEntry.index')->with(compact('title','searchFormLink','printFormLink','print','fromDate','toDate','transactionLists'));
    }

    public function print(Request $request)
    {
        $title = "Print Opening Balance Entry";
        $print = $request->print;

        if ($request->fromDate == "" && $request->toDate == "")
        {
            $transactionLists = OpeningBalance::select('tbl_account_transactions.*',DB::raw('SUM(tbl_account_transactions.debit_amount) as totalDebitAmount,SUM(tbl_account_transactions.credit_amount) as totalCreditAmount'))
                ->where('tbl_account_transactions.showroom_id',$this->showroomId)
                ->where('tbl_account_transactions.voucher_type','OB')
                ->groupBY('tbl_account_transactions.voucher_no')
                ->orderBY('tbl_account_transactions.voucher_no','asc')
                ->get();
            $fromDate = "";
            $toDate = "";
        }
        else
        {
            $fromDate = date('Y-m-d',strtotime($request->fromDate));
            $toDate = date('Y-m-d',strtotime($request->toDate));

            $transactionLists = OpeningBalance::select('tbl_account_transactions.*',DB::raw('SUM(tbl_account_transactions.debit_amount) as totalDebitAmount,SUM(tbl_account_transactions.credit_amount) as totalCreditAmount'))
                ->orWhere(function($query) use($fromDate,$toDate){
                    if (!empty($fromDate))
                    {
                        $query->whereBetween('tbl_account_transactions.voucher_date', array($fromDate,$toDate));
                    }
                })
                ->where('tbl_account_transactions.showroom_id',$this->showroomId)
                ->where('tbl_account_transactions.voucher_type','OB')
                ->groupBY('tbl_account_transactions.voucher_no')
                ->orderBY('tbl_account_transactions.voucher_no','asc')
                ->get();
        }

        $pdf = PDF::loadView('admin.openingBalanceEntry.print',['title'=>$title,'fromDate'=>$fromDate,'toDate'=>$toDate,'print'=>$print,'transactionLists'=>$transactionLists],[],['orientation'=>'P']);
        $pdf->stream('journal_voucher_'.$fromDate.'_'.$toDate.'.pdf');        
    }

    public function add()
    {
    	$title = "Add Journal Voucher Entry";
        $formLink = "openingBalanceEntry.save";
        $buttonName = "Save";

        $showrooms = ShowroomSetup::where('id',$this->showroomId)->where('status','1')->get();
        $coas = CoaSetup::where('transaction','1')->orderBy('head_name','asc')->get();

        // $coas = OpeningBalance::select('tbl_coa.*')
        // 	->leftJoin('tbl_coa','tbl_coa.head_code','=','tbl_account_transactions.coa_head_code')
        // 	->whereNull('tbl_coa.head_code')
        // 	->orWhere('tbl_coa.transaction','1')
        // 	->orwhere('tbl_account_transactions.voucher_type','OB')
        // 	->orWhere('tbl_account_transactions.approve','=','1')
        // 	->orderBy('tbl_coa.head_name','asc')
        // 	->get();

    	return view('admin.openingBalanceEntry.add')->with(compact('title','formLink','buttonName','showrooms','coas'));
    }

    public function save(Request $request)
    {
        // dd($request->all());
        $date = date('Y-m-d', strtotime($request->transactionDate));

        $countDebit = count($request->debit);
        if($request->debit)
        {
            $postData = [];
            for ($i=0; $i <$countDebit ; $i++)
            {
		    	if ($request->debit[$i] != 0 || $request->credit[$i] != 0)
		    	{ 
	                $postData[] = [
	                    'voucher_no' => $request->voucharNo,
	                    'voucher_type' => "OB",
	                    'voucher_date' => $date,
	                    'coa_head_code' => $request->coaId[$i],
	                    'showroom_id' => $request->showroom,           
	                    'narration' => $request->remarks,
	                    'debit_amount' => $request->debit[$i],          
	                    'credit_amount' => $request->credit[$i],          
	                    'posted' => "I",
	                    'created_by' => $this->userId
	                ];
		    	}
            }                
            OpeningBalance::insert($postData);
        }

        return redirect(route('openingBalanceEntry.index'))->with('msg','Opening Balance Added Successfully');
    }

    public function view($openingBalanceId)
    {
        $title = "Opening Balances";
        
        $info = $this->findOpeningBalanceInfo($openingBalanceId);
        $openingBalance = $info[0];
        $openingBalances = $info[1];

        return view('admin.openingBalanceEntry.view')->with(compact('title','openingBalance','openingBalances'));
    }

    public function printOpeningBalanceVoucher($openingBalanceId)
    {
        $title = "Print Journal Voucher";

        $info = $this->findOpeningBalanceInfo($openingBalanceId);
        $openingBalance = $info[0];
        $openingBalances = $info[1];

        $pdf = PDF::loadView('admin.openingBalanceEntry.printOpeningBalanceVoucher',['title'=>$title,'openingBalance'=>$openingBalance,'openingBalances'=>$openingBalances],[],['orientation'=>'P']);
        $pdf->stream('opening_balances_'.$openingBalance->voucher_no.'.pdf');
    }

    public function findOpeningBalanceInfo($openingBalanceId)
    {
        $openingBalance = OpeningBalance::select('tbl_account_transactions.*','tbl_showroom.name as showroomName')
            ->leftJoin('tbl_showroom','tbl_showroom.id','=','tbl_account_transactions.showroom_id')
            ->where('tbl_account_transactions.showroom_id',$this->showroomId)
            ->where('tbl_account_transactions.id',$openingBalanceId)
            ->first();

        $openingBalances = OpeningBalance::select('tbl_account_transactions.*','tbl_coa.head_name as accountHeadName')
            ->leftJoin('tbl_coa','tbl_coa.head_code','=','tbl_account_transactions.coa_head_code')
            ->where('tbl_account_transactions.showroom_id',$this->showroomId)
            ->where('voucher_no',$openingBalance->voucher_no)
            ->orderBy('tbl_account_transactions.id','asc')
            ->get();

        return array($openingBalance,$openingBalances);
    }

    public function changePublish(Request $request)
    {
        $openingBalanceId = $request->openingBalanceId;

        $openingBalance = OpeningBalance::find($openingBalanceId);

        if ($openingBalance->active == 0)
        {
            OpeningBalance::where('voucher_no',$openingBalance->voucher_no)->update(['active'=>1]);
        }
        else
        {
            OpeningBalance::where('voucher_no',$openingBalance->voucher_no)->update(['active'=>0]);
        }
    }

    public function delete(Request $request)
    {
        $openingBalance = OpeningBalance::find($request->openingBalanceId);

        OpeningBalance::where('voucher_no',$openingBalance->voucher_no)->delete();
    }

    public function getVoucharNo(Request $request)
    {
        $showroomId = $request->showroomId;

        $showroomPrefix = ShowroomSetup::where('id',$showroomId)->first();

        $str = "OB-".$showroomPrefix->prefix."-";

        $serialNo = OpeningBalance::where('voucher_type','OB')->where('voucher_no','LIKE','%'.$str.'%')->max('voucher_no');

        if ($serialNo)
        {
            $serialNo = substr($serialNo, strlen($str));
            $voucherNo = $str.($serialNo + 1);
        }
        else
        {
            $voucherNo = $str."1000000001";
        }
        

        echo $voucherNo;
    }
}
