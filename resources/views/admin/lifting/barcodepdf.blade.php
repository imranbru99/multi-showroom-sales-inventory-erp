@extends('admin.layouts.masterBercodePrint')
@php
error_reporting(0);
use Milon\Barcode\DNS1D;
@endphp
@section('custome-css')
<style type="text/css">
    #lifting-info{
        font-family: Times, "Times New Roman", serif;
        width: 100%;
        border-collapse: collapse;
        border-style: dotted;
    }

    #lifting-info td{
        padding: 5px;
        border-bottom: 1px solid #ddd;
    }
    .showroom_name{
        font-size: 9px; 
        margin-bottom: 10px;
        margin-top: 2px; 
        text-align:center; 
        letter-spacing: 40%; 
        width: 100%; 
    }
</style>
@endsection
@section('content')


@foreach ($postData as $data)
<?php
$i = 0;
$pdt_code = DB::table('tbl_products')
        ->where('id', $data['product_id'])
        ->first();

for ($i; $i < 3; $i++) {
    $allSerial[] = $data['serial_no'];
}
?>
@endforeach 
<?php
$k = 1;
?>
@foreach($allSerial as $serial)
<?php
if ($k <= 4) {
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
    $serial_no = $serial;
//    $getresult = substr($serial_no, 1);
    $getresult = $serial_no;
    ?>
    <div class="col-md-4">
        <?php
        echo '<h4 class="showroom_name">' . $showroom->name . '</h4>';
        echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($lifting->model_no . '-' . $getresult, 'C128A', 1, 50, array(1, 1, 1), true) . '" alt="barcode"   />';
        echo '<p style="font-size: 10px; margin-bottom: 3px;margin-top: 2px; text-align:center; letter-spacing: 80%;">' . $lifting->model_no . '-' . $getresult . '</p>';
        ?>
    </div>
    @endforeach  
</div>

@endsection
