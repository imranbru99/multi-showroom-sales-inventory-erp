@extends('admin.layouts.masterIndex')

@section('card_body')
    <div class="card-body">
        <table class="table table-striped gridTable" >
            <thead>
                <tr>
                    <th>Alphabet</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tbody">
                <tr>
                    <td>
                        <select name="product_id_1" class="form-control">
                            <option value=" ">Select Item</option>
                            @foreach ($alphabets as $alphabet)
                            	<option value="{{$alphabet->id}}">{{$alphabet->name}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
            </tbody>

            <tfoot>
                <tr>
                	<td></td>
                    <td align="center">
                        <input type="hidden" class="row_count" value="1">
                        <span class="btn btn-success add_item">
                            <i class="fa fa-plus-circle"></i> Add More
                        </span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

	<div id="itemList" style="display:none">
	    <div class="select">
            <select>
                <option value=" ">Select Item</option>
                @foreach ($alphabets as $alphabet)
                	<option value="{{$alphabet->id}}">{{$alphabet->name}}</option>
                @endforeach
            </select>
	    </div>
	</div>	
@endsection

@section('custom-js')
	<script type="text/javascript">
	    $(".add_item").click(function () {
	        var row_count = $('.row_count').val();
	        var total = parseInt(row_count) + 1; 
	        $(".gridTable tbody").append('<tr id="itemRow_' + total + '">' +
	            '<td>'+
	            '<select name="product_id_'+total+'" class="form-control itemList_'+total+'">'+
	            '</select>'+
	            '</td>'+
	            '<td align="center"><span class="btn btn-danger item_remove" onclick="itemRemove(' + total + ')"><i class="fa fa-trash"></i> Delete</span></td>'+
	            '</tr>');
	        $('.row_count').val(total);
	        var itemList = $("#itemList div select").html();
	        $('.itemList_'+total).html(itemList);
	    });

	    function itemRemove(i) {	        
	        $("#itemRow_" + i).remove();
	    }	      
	</script>
@endsection