@extends('admin.layouts.masterAddEdit')

@section('card_body')
    @php
    use App\Product;
    use App\GroupSetup;
       $commissionType = array('Dealer Commission' => 'Dealer Commission', 'Staff Commission' => 'Staff Commission');
    @endphp
    <style type="text/css">
        .gridTable{
           padding-left: 5px !important;
        }
    </style>
    <div style="padding-bottom: 10px;"></div>

    <form class="form-horizontal" action="{{ route($formLink) }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="customer-product-name">Commission Type</label>
                        <div class="form-group" id="prodct-select-menu">
                            <select class="form-control commissionType" name="commissionType" required="">
                                <option value="">Select One</option>
                                @foreach ($commissionType as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 defaultBox">
                        <label for="customer-product-name">Name</label>
                        <div class="form-group" id="prodct-select-menu">
                            <select class="form-control">
                                <option value="">At First Select Commission Type</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 dealerId">
                        <label for="customer-product-name">Dealer Name</label>
                        <div class="form-group" id="prodct-select-menu">
                            <select class="form-control chosen-select commissionInfo dealer" name="dealerId">
                                <option value="">Select Dealer Name</option>
                                @foreach ($delaerList as $dealer)
                                    <option value="{{$dealer->id}}">{{$dealer->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 staffId">
                        <label for="customer-product-name">Staff Name</label>
                        <div class="form-group" id="prodct-select-menu">
                            <select class="form-control chosen-select commissionInfo staff" name="staffId">
                                <option value="">Select Saff Name</option>
                                @foreach ($staffList as $staff)
                                @php
                                    $getGroups = GroupSetup::whereRaw('FIND_IN_SET(?,team_member)',[$staff->id])
                                        ->orWhere('team_leader',$staff->id)->first();
                                @endphp

                                @if (!$getGroups)
                                    <option value="{{$staff->id}}">{{$staff->name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped">
                            <thead style="background-color: #00c292;">
                                <tr>
                                    <th width="20px">SL</th>
                                    <th>Product Category Name</th>
                                    <th width="150px">Commission Rate (%)</th>
                                </tr>
                            </thead>
                            <tbody id="">
                                @php
                                    $sl = 1;
                                @endphp
                                @foreach ($categoryList as $category)
                                    @php
                                        $product = Product::where('category_id',$category->id)->get();
                                    @endphp
                                    @if (count($product) > 0)
                                        <tr class="row_{{ $category->id }}">
                                            <td>{{ $sl++ }}</td>
                                            <td>
                                                {{ $category->name }}
                                                <input type="hidden" name="categoryId[]" value="{{ $category->id }}">
                                                <input type="hidden" name="categoryName[]" value="{{ $category->name }}">
                                            </td>
                                            <td>
                                                <input type="text" value="0.00" name="commissionRate[]" class="categoryRow categoryRow_{{$category->id}}" style="text-align: center;">
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>


@endsection

@section('custom-js')

<script type="text/javascript">

    // start code for commission type view
    $('.dealerId').hide();
    $('.staffId').hide();

            
     $('.commissionType').click(function(event) {
        var commissionType =  $(".commissionType").val();
        if(commissionType == 'Dealer Commission'){
            $('.dealerId').show();
            $('.defaultBox').hide();
            $('.staffId').hide();
        }else if(commissionType == 'Staff Commission'){
            $('.staffId').show();
            $('.dealerId').hide();
            $('.defaultBox').hide();
        }else{
            $('.dealerId').hide();
            $('.staffId').hide();
            $('.defaultBox').show();
        }
    })

     $(document).ready(function () {
        $("form").submit(function(e){
            if($(".commissionType").val() == 'Dealer Commission' && $('.dealer').val() == ''){
                e.preventDefault();
                swal("Please Select Dealer Name", "", "warning");
            }else if($(".commissionType").val() == 'Staff Commission' && $('.staff').val() == ''){
                e.preventDefault();
                swal("Please Select Staff Name", "", "warning");
            }
        });
    });

    /*end code for commission type view*/

    $(document).on('change', '.commissionInfo', function(){
        var commissionType =  $(".commissionType").val();
        var dealerId = $('.dealer').val();
        var staffId = $('.staff').val();
         $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type:'post',
            url:'{{ route('commissionConfiguration.info') }}',
            data:{
                commissionType:commissionType,
                dealerId:dealerId,
                staffId:staffId
            },
            success:function(data){
                var commissions = data.commissions;
                var buttonName = data.buttonName;
                if(commissions != '')
                {
                    $('.buttonName').val('Update');
                    $('.buttonName').html('<i class="fa fa-save"></i> Update');
                    for(row of commissions)
                    {
                        $('.categoryRow_'+row.category_id).val(row.commission_rate);
                    }
                }
                else
                {
                    $('.buttonName').val('Save');
                    $('.buttonName').html('<i class="fa fa-save"></i> Save');
                    $('.categoryRow').val('0.00'); 
                }
            }
        });
    })
    
</script>
@endsection
