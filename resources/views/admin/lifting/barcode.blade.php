@extends('admin.layouts.masterIndexBercode')

@section('card_body')
        @foreach ($postData as $data)
          @php
            $i =0;
            $pdt_code = DB::table('tbl_products')
            ->where('id', $data['product_id'])
            ->first();
          @endphp
          @for($i; $i<3; $i++)
            @php
              $allSerial[]= $data['serial_no'];
            @endphp
          @endfor
        @endforeach 
           <?php
              $k= 1;
           ?>
          @foreach($allSerial as $serial)
            <?php
              if($k <= 4){
                $rowVal[] = $serial;
              }
              $k++;
            ?>
          @endforeach
        
        <div class="row printArea">
        @foreach($allSerial as $serial)
           <?php
            $lifting = DB::table('tbl_lifting_products')
            ->where('serial_no', $serial)
            ->first();

            // dd($lifting);

            $serial_no =$serial;
            $getresult = substr($serial_no, 1);
         
           ?>
              <div class="col-md-3">
                  <svg  class="barcode"
                             jsbarcode-format="CODE128"
                             jsbarcode-value="<?php echo 'm'.$serial_no; ?>"
                             jsbarcode-text="<?php echo 'M'.$getresult.'-'.$lifting->model_no; ?>"
                             jsbarcode-lineColor="#000000"
                             jsbarcode-width =2
                             jsbarcode-height=25
                             jsbarcode-fontSize=12
                            jsbarcode-fontoptions="bold"      
                             >
                  </svg>
                  <script type="text/javascript">
                    JsBarcode(".barcode").init();
                  </script>
              </div>
          @endforeach  
          </div>
 
@endsection

