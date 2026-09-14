<?php

namespace App\Http\Controllers\Admin;

use PDF;
use App\Helper\AgingReport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AgingReportController extends Controller {

    public function index(Request $request) {
        $title = "Ageing Report";

        $data = AgingReport::getData($this->company);

        if ($request->has('print')) {
            $pdf = PDF::loadView('admin.aging_report.print', compact('title', 'data'));

            return $pdf->stream('ageing_report.pdf');
        }

        return view('admin.aging_report.index', compact('title', 'data'));
    }

}
