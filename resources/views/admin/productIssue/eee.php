@extends('admin.layouts.masterIndexBercode')

@section('card_body')
    <div class="card-body printArea">

        @foreach ($postData as $data)
           @php
            $i =0;
            $pdt_code = DB::table('tbl_products')
            ->where('id', $data['product_id'])
            ->first();
        

        @endphp
        <div class="row">
        @for($i; $i<4; $i++)
           <?php

            $serial_no = $data['serial_no'];
            $getresult = substr($serial_no, 1);
            // dd($getresult);
           ?>
      
              <div class="col-md-3">
            
                  <svg  class="barcode"
                             jsbarcode-format="CODE128"
                             jsbarcode-value="<?php echo 'm'.$serial_no; ?>"
                             jsbarcode-text="<?php echo 'M'.$getresult.'-'.$pdt_code->model_no; ?>"
                             jsbarcode-lineColor="#000000"
                             jsbarcode-width = 2
                             jsbarcode-height=25
                             jsbarcode-fontSize=12
                             
                            jsbarcode-fontoptions="bold"
                        
                             
                             >
                </svg>

       
              <script type="text/javascript">

                JsBarcode(".barcode").init();

              </script>
              </div>
                          
             @endfor 

            </div>
        @endforeach   

    </div>  
@endsection

