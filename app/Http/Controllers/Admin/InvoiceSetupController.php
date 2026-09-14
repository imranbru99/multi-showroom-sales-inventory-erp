<?php

namespace App\Http\Controllers\Admin;

use DB;
use PDF;

use MPDF;
use App\Product;
use App\RetailSale;
use App\RetailSales;
use App\InvoiceSetup;
use App\ShowroomSetup;
use App\LiftingProduct;
use App\CustomerProduct;
use App\TotalInvoiceSetup;

use Illuminate\Http\Request;
use App\CustomerRegistration;
use App\CustomerRegistrationSetup;
use App\Http\Controllers\Controller;

class InvoiceSetupController extends Controller
{
    public function index()
    {
        $title = "Invoice Setup";
        $invoices = InvoiceSetup::select('tbl_invoice.*','tbl_customers.name as customerName','tbl_showroom.name as showRoomName')
            ->leftjoin('tbl_customers','tbl_customers.id','=','tbl_invoice.customer_id')
            ->leftJoin('tbl_showroom','tbl_showroom.id','=','tbl_invoice.showroom_id')
            ->where('tbl_invoice.showroom_id',$this->showroomId)
            ->orderBy('tbl_invoice.id','asc')
            ->get();

        return view('admin.invoiceSetup.index')->with(compact('title','invoices'));
    }

    public function add(Request $request)
    {        
        $title = "Create New Invoice";
        $formLink = "invoiceSetup.save";
        $buttonName = "Create Invoice";
        $customers = CustomerRegistration::where('status',1)->get();

        $maxId = InvoiceSetup::max('id');
        $invoiceNoPrefix = "inv-".date('ymd')."-";

        if ($maxId)
        {
            $invoiceNo = $invoiceNoPrefix.str_pad($maxId + 1, 9, '0', STR_PAD_LEFT);
        }
        else
        {
            $invoiceNo = $invoiceNoPrefix."000000001";
        }

        return view('admin.invoiceSetup.add')->with(compact('title','formLink','buttonName','customers','invoiceNo'));
    }

    public function save(Request $request)
    {
        // dd($request->all());
        $invoiceDate = date('Y-m-d', strtotime($request->invoiceDate));
        $invoiceNo = $request->invoiceNo;
        $customerId = $request->customerId;
        $print = $request->print;

        $retailSalesId = $request->retailSalesId;
        $productSerial = $request->productSerial;
        $totalcustomerProductPrice = 0;

        // dd($productInfo);

        $invoice = TotalInvoiceSetup::create( [
            'showroom_id' => $this->showroomId,
            'customer_id' => $customerId,
            'invoice_date' => $invoiceDate,
            'invoice_no' => $invoiceNo,
            'created_by' => $this->userId           
        ]);

        $countInvoice = count($request->retailSalesIdArray);
        if($request->retailSalesIdArray)
        {            
            $postData = [];
            for ($i=0; $i <$countInvoice ; $i++)
            {
                $retailSales = RetailSales::where('id',$request->retailSalesIdArray[$i])->first();

                if($retailSales->purchase_type == "Cash")
                {
                    $customerProductPrice = $retailSales->sales_price;
                }

                if($retailSales->purchase_type == "Short Installment" || $retailSales->purchase_type == "Long Installment")
                {
                    $customerProductPrice = $retailSales->installment_price;
                }

                $totalcustomerProductPrice = $totalcustomerProductPrice + $customerProductPrice;

                $productInfo = Product::where('id',$retailSales->product_id)->first();

                $postData[] = [
                    'invoice_total_id' => $invoice->id,
                    'showroom_id' => $this->showroomId,
                    'invoice_date' => $invoiceDate,
                    'invoice_no' => $invoiceNo,
                    'collection_type' => $retailSales->purchase_type,
                    'customer_id' => $retailSales->customer_id,       
                    'retail_sales_id' => $request->retailSalesIdArray[$i],       
                    'product_id' => $retailSales->product_id,
                    'product_name' => $productInfo->name,
                    'product_serial_no' => $request->productSerialArray[$i],
                    'qty' => $retailSales->qty,       
                    'customer_product_price' => $customerProductPrice,       
                    'customer_product_model' => $retailSales->product_model, 
                    'customer_product_usage_address' => $retailSales->product_usage_address,       
                    'customer_product_purchase_date' => $retailSales->purchase_date,
                    'customer_product_color' => $productInfo->color,       
                    'customer_product_waranty' => $productInfo->warranty,
                    'created_by' => $this->userId
                ];
            }

            $updateInvoiceTotal = TotalInvoiceSetup::find($invoice->id);

            $updateInvoiceTotal->update([
                'total_customer_product_price'=> $totalcustomerProductPrice
            ]);
            
            InvoiceSetup::insert($postData);
        }

        return redirect(route('invoiceSetup.index'));

        // return view('admin.invoiceSetup.add')->with(compact('title','formLink','buttonName','customer','invoice','retailSales','productInfo','print'));
    }

    public function view($id)
    {
        $title = "View Customer Invoice";

        $totalInvoice = TotalInvoiceSetup::select('tbl_invoice_total.*','tbl_showroom.name as showRoomName','tbl_customers.name as customerName','tbl_customers.phone_no as customerPhone','tbl_customers.present_address as customerPresentAddress')
            ->leftJoin('tbl_showroom','tbl_showroom.id','=','tbl_invoice_total.showroom_id')
            ->leftJoin('tbl_customers','tbl_customers.id','=','tbl_invoice_total.customer_id')
            ->where('tbl_invoice_total.id',$id)
            ->first();

        $invoices = InvoiceSetup::select('tbl_invoice.*','tbl_products.code as productCode','tbl_products.name as productName')
            ->leftJoin('tbl_products','tbl_products.id','=','tbl_invoice.product_id')
            ->where('tbl_invoice.invoice_total_id',$id)
            ->get();

        return view('admin.invoiceSetup.view')->with(compact('title','totalInvoice','invoices'));
    }

    public function getAllProduct(Request $request)
    {
        $output = '';

        $soldOutProducts = RetailSales::select('tbl_retails_sales.*','tbl_products.name as productName','tbl_products.model_no as productModelNo')
            ->leftJoin('tbl_products','tbl_products.id','=','tbl_retails_sales.product_id')
            ->leftJoin('tbl_invoice','tbl_invoice.retail_sales_id','=','tbl_retails_sales.id')
            ->whereNull('tbl_invoice.retail_sales_id')
            ->where('tbl_retails_sales.showroom_id',$this->showroomId)
            ->where('tbl_retails_sales.customer_id',$request->customerId)
            ->get();

        if ($soldOutProducts)
        {
            $output .= '<select class="form-control chosen-select retailSalesId" name="retailSalesId" id="retailSalesId">';
            // $output .= '<option value="">Select Product</option>';
            foreach ($soldOutProducts as $soldOutProduct)
            {
                $output .= '<option value="'.$soldOutProduct->id.'">'.$soldOutProduct->productName.' ('.$soldOutProduct->productModelNo.')</option>';
            }
            $output .= '</select>';         
        }
        else
        {
            $output .= '<select class="form-control chosen-select retailSalesId" name="retailSalesId" id="retailSalesId">';
            $output .= '<option value="">Select Product</option>';
            $output .= '</select>';
        }  

        echo $output;
    }

    public function getAllProductSerial(Request $request)
    {
        $output = '';
        $results = '';

        $retailSales = RetailSales::where('id',$request->retailSalesId)->where('status','1')->first();

        $productSerials = LiftingProduct::select('tbl_lifting_products.*')
            ->leftJoin('tbl_invoice','tbl_invoice.product_serial_no','=','tbl_lifting_products.serial_no')
            ->whereNull('tbl_invoice.product_serial_no')
            ->where('tbl_lifting_products.showroom_id',$this->showroomId)
            ->where('tbl_lifting_products.product_id',$retailSales->product_id)
            ->where('tbl_lifting_products.status','1')
            ->get();

        if ($productSerials)
        {
            $output .= '<select class="form-control chosen-select productSerial" name="productSerial" id="productSerial">';
            $output .= '<option value="">Select Product Serial</option>';          
            foreach ($productSerials as $productSerial)
            {
                $output .= '<option value="'.$productSerial->serial_no.'">'.$productSerial->serial_no.'</option>';
            }
            $output .= '<option value="No Serial Number">No Serial Number</option>';
            $output .= '</select>';         
        }
        else
        {
            $output .= '<select class="form-control chosen-select productSerial" name="productSerial" id="productSerial">';
            $output .= '<option value="">Select Product Serial</option>';
            $output .= '<option value="No Serial Number">No Serial Number</option>';
            $output .= '</select>';
        }  

        echo $output;
    }

    public function printInvoice($id)
    {
        $title = "Invoice";

        $saleData = RetailSale::where('id', $id)->with(['products.product', 'customer'])->first();
       
        $pdf = PDF::loadView('admin.invoiceSetup.printInvoice',['title'=>$title,'saleData'=>$saleData]);

        return $pdf->stream($saleData->invoice_no.'.pdf');
    }

    public function printChalan($id)
    {
        $title = "Print Challan";

        $saleData = RetailSale::where('id', $id)->with(['products.product', 'customer', 'saleBy'])->first();

        $chalanNo = str_replace('inv', 'chalan', $saleData->invoice_no);

        $pdf = PDF::loadView('admin.invoiceSetup.printChalan',['title'=>$title,'saleData'=>$saleData]);

        return $pdf->stream($chalanNo.'.pdf');
    }

    public function delete(Request $request)
    {
        $invoice = InvoiceSetup::where('id',$request->invoiceId)->delete();
    }
}
