@extends('admin.layouts.masterPrint')

@section('content')

            
         
                        <svg class="barcode"
                             jsbarcode-format="CODE128"
                             jsbarcode-value="123456789012"
                             jsbarcode-lineColor="#000000"
                             jsbarcode-width=3
                             jsbarcode-height=40>
                        </svg>
                  
                 
                      <script type="text/javascript">

                        JsBarcode(".barcode").init();

                      </script>
               



    

                    



@endsection
