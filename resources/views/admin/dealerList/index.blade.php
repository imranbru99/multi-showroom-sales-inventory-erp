@extends('admin.layouts.master')

@section('content')
<form class="form-horizontal" id="search" action="{{ route($searchFormLink) }}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <input type="hidden" name="print" value="print">
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <label for="region">Region</label>
                    <div class="form-group">
                        <select class="form-control chosen-select" name="region" id="region">
                            <option value="">Select Region</option>
                            @foreach($regions as $reg)
                            <option value="{{ $reg->id }}" @if($reg->id == $region) selected @endif>{{ $reg->name }}</option>
                            @endforeach
                        </select>
                    </div>  
                </div>
                <div class="col-md-3">
                    <label for="area">Area</label>
                    <div class="form-group">
                        <select class="form-control chosen-select" id="area" name="area">
                            @if($areas)
                            <option value="">Select Area</option>
                            @foreach($areas as $are)
                            <option value="{{ $are->id }}" @if($are->id == $area) selected @endif>{{ $are->name }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>  
                </div>

                <div class="col-md-3">
                    <label for="territory">Territory</label>
                    <div class="form-group">
                        <select class="form-control chosen-select" id="territory" name="territory">
                            @if($territories)
                            <option value="">Select Territory</option>
                            @foreach($territories as $terry)
                            <option value="{{ $terry->id }}" @if($terry->id == $territory) selected @endif>{{ $terry->name }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>                                  
                </div>
                <div class="col-md-3">
                    <label for="region">Reference By</label>
                    <div class="form-group">
                        <select class="form-control chosen-select" name="reference">
                            <option value="">Select Reference</option>
                            @foreach($staffs as $staff)
                            <option value="{{ $staff->id }}" @if($staff->id == $reference) selected @endif>{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>  
                </div>
            </div>                              
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button type="submit" class="btn btn-outline-info btn-lg waves-effect"><i class="fa fa-search"></i> Search</button>
                </div>
            </div>              
        </div>
    </div>
</form>


<div class="card" style="margin-bottom: 0px;">              
    <div class="card-header">
        <div class="row">
            <div class="col-md-6"><h4 class="card-title">Searched Report</h4></div>
            <div class="col-md-6 text-right">
                <form class="form-horizontal" id="print" action="{{ route($printFormLink) }}" target="_blank" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}


                    <input type="hidden" id="print_value" name="reference" value="{{ $reference }}">
                    <input type="hidden" id="print_value" name="region" value="{{ $region }}">
                    <input type="hidden" id="print_value" name="area" value="{{ $area }}">
                    <input type="hidden" id="print_value" name="territory" value="{{ $territory }}">
                    <input type="hidden" id="print_value" name="print" value="{{ $print }}">

                    <button type="submit" class="btn btn-outline-info btn-lg"><i class="fa fa-print"></i> Print</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th width="20px">Sl</th>
                        <th>Code</th>
                        <th>Dealer Name</th>
                        <th>Mobile</th>
                        <th>Address</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($dealers as $dealer)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $dealer->code }}</td>
                        <td>{{ $dealer->name }}</td>
                        <td>{{ $dealer->mobile }}</td>
                        <td>{{ $dealer->address }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>                         
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script type="text/javascript">
    $('#region').change(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var region = $(this).val();

            $('#area option').remove();
            $('#territory option').remove();
            $.ajax({
                type:'post',
                url:'{{ route('dealerList.area') }}',
                data:{region:region},
                success:function(data){
                    $('#area').append(`<option value="">Select Area</option>`);
                    data.forEach(function (item, index) {
                        var option = `<option value="${item.id}">${item.name}</option>`;
                        $('#area').append(option);
                    });

                    $('.chosen-select').chosen();
                    $('.chosen-select').trigger("chosen:updated");
                }
            });
        });
        
        
        $('#area').change(function(){
        
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#territory option').remove();
            
            var area = $(this).val();

            $.ajax({
                type:'post',
                url:'{{ route('dealerList.territory') }}',
                data:{area:area},
                success:function(data){
                    $('#territory').append(`<option value="">Select Territory</option>`);
                     data.forEach(function (item, index) {
                        var option = `<option value="${item.id}">${item.name}</option>`;
                        $('#territory').append(option);
                    });

                    $('.chosen-select').chosen();
                    $('.chosen-select').trigger("chosen:updated");
                }
            });
        });

</script>
@endsection