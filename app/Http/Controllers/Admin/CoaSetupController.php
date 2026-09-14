<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CoaSetup;
use DB;

class CoaSetupController extends Controller {

    public function loadCoaTree($head_name = 'COA') {

        $coaLists = CoaSetup::where('tbl_coa.active', '1')
                ->select(
                        'tbl_coa.parent_head_name',
                        'tbl_coa.head_name',
                        'tbl_coa.head_code',
                        'tbl_coa.head_level'
                        // DB::raw('IFNULL(count(coa.head_code),0) as child')
                )
                ->with('children')
                // ->leftjoin('tbl_coa as coa', 'coa.parent_head_name', '=', 'tbl_coa.head_name')
                ->where('company_id', $this->company)
                ->orderBy('tbl_coa.id', 'asc')
                ->orderBy('tbl_coa.head_code', 'asc')
                ->where('tbl_coa.parent_head_name', $head_name)
                ->groupBy('tbl_coa.id')
                ->get();

        $coa = CoaSetupController::menuUi($coaLists);

        $html = '<li>COA<ul>' . $coa . '</ul></li>';

        return $html;
    }

    public static function menuUi($coa) {
        $html = ' ';
        foreach ($coa as $key => $element) {
            if (count($element->children) > 0) {
                // $html .= '<li>' . $element->head_name . '';
                $html .= '<li><a href="javascript:" onclick="loadData(' . $element->head_code . ')">' . $element->head_name . '</a>';
                $subchild = CoaSetupController::subMenu($element->children);
                if (!empty($subchild)) {
                    $html .= '<ul>' . $subchild . '</ul>';
                }
                $html .= '</li>';
            } else {
                $html .= '<li><a href="javascript:" onclick="loadData(' . $element->head_code . ')">' . $element->head_name . '</a>';
                $html .= '</li>';
            }
        }
        return $html;
    }

    public static function subMenu($child) {
        $html = ' ';
        if (!empty($child)) {
            foreach ($child as $key => $element) {
                if (count($element->children) > 0) {
                    $html .= '<li><a href="javascript:" onclick="loadData(' . $element->head_code . ')">' . $element->head_name . '</a>';
                    $subchild = CoaSetupController::subMenu($element->children);
                    if (!empty($subchild)) {
                        $html .= '<ul>' . $subchild . '</ul>';
                    }
                    $html .= '</li>';
                } else {
                    $html .= '<li><a href="javascript:" onclick="loadData(' . $element->head_code . ')">' . $element->head_name . '</a>';
                    $html .= '</li>';
                }
            }
        }
        return $html;
    }

    public function loadCoaTree2() {

        $coaLists = CoaSetup::where('active', '1')
                ->select(['parent_head_name', 'head_name', 'head_code'])
                ->orderBy('id', 'asc')
                ->orderBy('head_code', 'asc')
                ->get();


        $visit = array();
        $i = 0;

        foreach ($coaLists as $coaList) {
            $visit[$i] = false;
            $i++;
        }

        $coaTree = \App\HelperClass::makeTree("COA", "0", $coaLists, $visit, 0);

        return $coaTree;
    }

    public function index() {
        $title = "Chart Of Accounts";
        $formLink = "coaSetup.action";
        $coaLists = [];
        // $coaLists = CoaSetup::where('active', '1')
        //     ->select(['parent_head_name', 'head_name', 'head_code'])
        //     ->orderBy('id', 'asc')
        //     ->orderBy('head_code', 'asc')
        //     ->get();

        return view('admin.coaSetup.index')->with(compact('title', 'formLink', 'coaLists'));
    }

    public function action(Request $request) {
        // dd($request->all());

        $btnAction = $request->btnAction;

        if ($btnAction == 'save') {
            // echo "Save"; exit();
            CoaSetup::create([
                'company_id' => $this->company,
                'head_code' => $request->headCode,
                'head_name' => $request->headName,
                'parent_head_name' => $request->parentHead,
                'head_level' => $request->headLevel,
                'active' => $request->active,
                'transaction' => $request->transaction == "" ? 0 : $request->transaction,
                'general_ledger' => $request->generalLedger == "" ? 0 : $request->generalLedger,
                'head_type' => $request->headType,
                'created_by' => $this->userId
            ]);

            return redirect(route('coaSetup.index'))->with('msg', 'Chart Of Account Successfuly Saved');
        }

        if ($btnAction == 'update') {
            $coa = CoaSetup::where('id', $request->headId)->first();

            $coa->update([
                'head_code' => $request->headCode,
                'head_name' => $request->headName,
                'parent_head_name' => $request->parentHead,
                'head_level' => $request->headLevel,
                'active' => $request->active,
                'transaction' => $request->transaction == "" ? 0 : $request->transaction,
                'general_ledger' => $request->generalLedger == "" ? 0 : $request->generalLedger,
                'head_type' => $request->headType,
                'updated_by' => $this->userId
            ]);

            return redirect(route('coaSetup.index'))->with('msg', 'Chart Of Account Successfuly Updated');
        }
    }

    public function newCoaData(Request $request) {
        $data = array();
        $headCode = $request->headCode;

        $headCodeQuery = CoaSetup::where('head_code', $headCode)->first();

        if ($headCodeQuery) {
            $headQuery = CoaSetup::where('parent_head_name', 'LIKE', '%' . $headCodeQuery->head_name . '%')
                    ->where('company_id', $this->company)
                    ->get();

            $n = count($headQuery->toarray()) + 1;

            if ($n / 10 < 1) {
                $headCode = $headCode . "0" . $n;
            } else {
                $headCode = $headCode . $n;
            }

            if ($headCodeQuery->general_ledger == 1) {
                $transaction = TRUE;
            } else {
                $transaction = FALSE;
            }


            $data = [
                'headCode' => $headCode,
                'parentHead' => $headCodeQuery->head_name,
                'headLevel' => $headCodeQuery->head_level + 1,
                'headType' => $headCodeQuery->head_type,
                'transaction' => $transaction,
                'generalLedger' => $headCodeQuery->general_ledger,
                'active' => $headCodeQuery->active
            ];
        }

        echo json_encode($data);
    }

    public function loadCoaData(Request $request) {
        $data = array();
        $headCode = $request->headCode;

        $headQuery = CoaSetup::where('company_id', $this->company)->where('head_code', $headCode)->first();

        // echo $headQuery;

        if ($headQuery) {
            $parentHeadQuery = CoaSetup::where('company_id', $this->company)->where('head_name', $headQuery->parent_head_name)->first();

            $HeadttQuery = CoaSetup::where('company_id', $this->company)->where('head_code', 'LIKE', '%' . $headCode . '%')->where('transaction', 1)->get();

            $headCount = count($HeadttQuery);

            if ($headCount > 0) {
                $subGeneralLedgertransection = 1;
            } else {
                $subGeneralLedgertransection = 0;
            }

            if ($parentHeadQuery && $parentHeadQuery->general_ledger == 1) {
                $presentGeneralLedger = 1;
            } else {
                $presentGeneralLedger = 0;
            }

            if ($headQuery->general_ledger == 1) {
                $currentGeneralLedger = 1;
            } else {
                $currentGeneralLedger = 0;
            }

            $data = [
                'presentGeneralLedger' => $presentGeneralLedger,
                'currentGeneralLedger' => $currentGeneralLedger,
                'id' => $headQuery->id,
                'headCode' => $headQuery->head_code,
                'headName' => $headQuery->head_name,
                'parentHead' => $headQuery->parent_head_name,
                'headLevel' => $headQuery->head_level,
                'headType' => $headQuery->head_type,
                'transaction' => $headQuery->transaction,
                'generalLedger' => $headQuery->general_ledger,
                'active' => $headQuery->active,
                'subGeneralLedgertransection' => $subGeneralLedgertransection
            ];
        }

        echo json_encode($data);
    }

}
