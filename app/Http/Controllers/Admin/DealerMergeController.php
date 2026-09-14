<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DealerSetup;
use App\ProductIssue;
use App\SalesReturn;
use App\AdvanceCollection;
use App\DealerCollection;

class DealerMergeController extends Controller
{
    public function index(Request $request)
    {
        $title = "Dealer Merge";

        $dealers = DealerSetup::get();


        return view('admin.dealerMerge.index')->with(compact('title', 'dealers'));
    }


    public function save(Request $request)
    {

        ProductIssue::whereIn('dealer_id', $request->dealer_id)->update([
            'dealer_id' => $request->dealer,
        ]);

        AdvanceCollection::whereIn('dealer_id', $request->dealer_id)->update([
            'dealer_id' => $request->dealer,
        ]);

        DealerCollection::whereIn('dealer_id', $request->dealer_id)->update([
            'dealer_id' => $request->dealer,
        ]);

        SalesReturn::whereIn('dealer_id', $request->dealer_id)->update([
            'dealer_id' => $request->dealer,
        ]);

        DealerSetup::whereIn('id', $request->dealer_id)->delete();


        return redirect(route('dealerMerge.index'))->with('msg', 'Dealer Merged Successfully');
    }
}
