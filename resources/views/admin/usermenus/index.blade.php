@extends('admin.layouts.masterIndex')

<?php
    use App\UserMenu;
?>

@section('card_body')
    <div class="card-body">
        
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped"  name="menusTable">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Name</th>
                        <th>Parent</th>
                        <th>Link</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    @php
                        $sl = 0;                                
                    @endphp

                	@foreach($menus as $menu)
                        @php
                            $sl++;
                            $parentMenu = UserMenu::where('id',$menu->parentMenu)->first();
                        @endphp

                    	<tr class="row_{{ $menu->id }}">
                            <td>{{ $sl }}</td>
                            <td>{{ $menu->menuName }}</td>
                            <td>{{ @$parentMenu->menuName }}</td>
                            <td>{{ $menu->menuLink }}</td>
                            <td>{{ $menu->orderBy }}</td>
                            <td>
                                <?php echo \App\Link::status($menu->id,$menu->menuStatus)?>
                            </td>
                            <td class="text-nowrap">
                            <?php echo \App\Link::action($menu->id)?>
                            </td>
                        </tr>
                	@endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('custom-js')
    <script>
        $(document).ready(function() {
            var updateThis ;

            //ajax            

            //ajax delete code
            $('#dataTable tbody').on( 'click', 'i.fa-trash', function () {
                $.ajaxSetup({
                  headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
                });

                usermenuId = $(this).parent().data('id');
                var menu = this;
                swal({   
                    title: "Are you sure?",   
                    text: "You will not be able to recover this imaginary file!",   
                    type: "warning",   
                    showCancelButton: true,   
                    confirmButtonColor: "#DD6B55",   
                    confirmButtonText: "Yes, delete it!",   
                    cancelButtonText: "No, cancel plx!",   
                    closeOnConfirm: false,   
                    closeOnCancel: false 
                }, function(isConfirm){   
                    if (isConfirm) {     
                       $.ajax({
                            type: "POST",
                           url : "{{ route('usermenu-delete') }}",
                            data : {usermenuId:usermenuId},
                           
                            success: function(response) {
                                swal({
                                    title: "<small class='text-success'>Success!</small>", 
                                    type: "success",
                                    text: "Menu Deleted Successfully!",
                                    timer: 1000,
                                    html: true,
                                });
                                $('.row_'+usermenuId).remove();
                            },
                            error: function(response) {
                                error = "Failed.";
                                swal({
                                    title: "<small class='text-danger'>Error!</small>", 
                                    type: "error",
                                    text: error,
                                    timer: 1000,
                                    html: true,
                                });
                            }
                        });    
                    } else { 
                        swal({
                            title: "Cancelled", 
                            type: "error",
                            text: "Your menu is safe :)",
                            timer: 1000,
                            html: true,
                        });    
                    } 
                });
            }); 

        });
                
        //ajax status change code
        function statusChange(menu_id) {
            $.ajax({
                    type: "GET",
                    url: "{{ route('usermenu.status', 0) }}",
                    data: "menu_id=" + menu_id,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        swal({
                            title: "<small class='text-success'>Success!</small>", 
                            type: "success",
                            text: "Status successfully updated!",
                            timer: 1000,
                            html: true,
                        });
                    },
                    error: function(response) {
                        error = "Failed.";
                        swal({
                            title: "<small class='text-danger'>Error!</small>", 
                            type: "error",
                            text: error,
                            timer: 2000,
                            html: true,
                        });
                    }
                });
            }

            function summernote(){
                $('.summernote').summernote({
                    height: 200, // set editor height
                    minHeight: null, // set minimum height of editor
                    maxHeight: null, // set maximum height of editor
                    focus: false // set focus to editable area after initializing summernote
                });
            }
    </script>
@endsection