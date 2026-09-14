@extends('admin.layouts.master')

@section('content')
<style type="text/css">
    .chosen-single {
        height: 35px !important;
    }
</style>

<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#dealerInformation" style="font-weight: bold;">Information</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#documents_tab" style="font-weight: bold;">Documents</a>
    </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div id="dealerInformation" class="tab-pane active p-0">
        <div class="card-pad"></div>
        
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6"><h4 class="card-title">Add Information</h4></div>
                    <div class="col-md-6 text-right">
                        <a class="btn btn-outline-info btn-lg" href="{{ route($goBackLink) }}">
                            <i class="fa fa-arrow-circle-left"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>
            <form class="form-horizontal" action="{{ route('dealerSetup.save') }}" method="POST" enctype="multipart/form-data" id="" name="">
            {{ csrf_field() }}
                <div class="card-body">
                    <div class="information">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="district">Region</label>
                                    <select class="form-control chosen-select" name="districtId" id="districtId">
                                        <option value="">Select Region</option>
                                        @foreach ($districts as $district)
                                        <option value="{{ $district->id }}" @if(@@$dealer->region_id == $district->id) selected @endif>{{ $district->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="upazila">Area</label>
                                <div class="form-group" id="upazila-select-menu">
                                    <select class="form-control chosen-select" name="upazilaId" id="upazilaId">
                                        <option value="">Select Area</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="teritori">Teritory</label>
                                <div class="form-group" id="teritori-select-menu">
                                    <select class="form-control chosen-select" name="territoryId" id="territoryId">
                                        <option value="">Select Teritory</option>
                                        @foreach ($territories as $territorie)
                                        <option value="{{ $territorie->id }}" @if(@$dealer->territory_id == $territorie->id) selected @endif>{{ $territorie->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                @php
                                @$dealerTypes = [
                                'Internal'=>'Internal',
                                'External'=>'External'
                                ];
                                @endphp
                                <div class="form-group {{ $errors->has('dealerType') ? ' has-danger' : '' }}">
                                    <label for="dealer-type">Dealer Type</label>
                                    <select class="form-control" name="dealerType">
                                        <option value="">Select Type</option>
                                        @foreach (@$dealerTypes as $key => $value)
                                        <option value="{{ $key }}" @if(@$dealer->type == $key) selected @endif>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('dealerType'))
                                    @foreach($errors->get('dealerType') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('code') ? ' has-danger' : '' }}">
                                    <label for="code">Code</label>
                                    <input type="text" class="form-control" name="code" value="{{ @$dealer->code }}" required>
                                    @if ($errors->has('code'))
                                    @foreach($errors->get('code') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('dealerName') ? ' has-danger' : '' }}">
                                    <label for="dealer-name">Dealer Name</label>
                                    <input type="text" class="form-control" name="dealerName" value="{{ @$dealer->name }}" required>
                                    @if ($errors->has('dealerName'))
                                    @foreach($errors->get('dealerName') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>


                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('short_name') ? ' has-danger' : '' }}">
                                    <label for="short_name">Dealer Display Name</label>
                                    <input type="text" class="form-control" name="short_name" value="{{ @$dealer->short_name }}" maxlength="20" required>
                                    @if ($errors->has('short_name'))
                                    @foreach($errors->get('short_name') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('contactPerson') ? ' has-danger' : '' }}">
                                    <label for="contact-person">Contact Person</label>
                                    <input type="text" class="form-control" name="contactPerson" value="{{ @$dealer->contact_person }}"
                                        required>
                                    @if ($errors->has('contactPerson'))
                                    @foreach($errors->get('contactPerson') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('creditLimit') ? ' has-danger' : '' }}">
                                    <label for="credit-limit">Credit Limit</label>
                                    <input type="text" class="form-control" name="creditLimit" value="{{ @$dealer->credit_limit }}">
                                    @if ($errors->has('creditLimit'))
                                    @foreach($errors->get('creditLimit') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('mobile') ? ' has-danger' : '' }}">
                                    <label for="mobile">Mobile</label>
                                    <input type="text" class="form-control" name="mobile" value="{{ @$dealer->mobile }}" required>
                                    @if ($errors->has('mobile'))
                                    @foreach($errors->get('mobile') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('email') ? ' has-danger' : '' }}">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" value="{{ @$dealer->email }}" required>
                                    @if ($errors->has('email'))
                                    @foreach($errors->get('email') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('reference') ? ' has-danger' : '' }}">
                                    <label for="reference">Reference By</label>
                                    <select name="reference" class="form-control chosen-select">
                                        <option value="">Select Reference</option>
                                        @foreach($staffs as $staff)
                                            <option value="{{ $staff->id }}" @if($staff->id == @$dealer->reference_by) selected @endif>{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('address') ? ' has-danger' : '' }}">
                                    <label for="address">Address</label>
                                    <input type="text" class="form-control" name="address" value="{{ @$dealer->address }}" required>
                                    @if ($errors->has('address'))
                                    @foreach($errors->get('address') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('courier_address') ? ' has-danger' : '' }}">
                                    <label for="courier_address">Courier Address</label>
                                    <input type="text" class="form-control" name="courier_address"
                                        value="{{ @$dealer->courier_address }}" required>
                                    @if ($errors->has('courier_address'))
                                    @foreach($errors->get('courier_address') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-info">
                                <i class="fa fa-save"></i>
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="documents_tab" class="tab-pane fade p-0">
        <form class="form-horizontal" action="javascript:;" method="get" id="document-form" name="document-form">
            <div class="card-pad"></div>

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="card-title">Edit Information</h4>
                        </div>
                        <div class="col-md-6 text-right">
                            <a class="btn btn-outline-info btn-lg" href="{{ route($goBackLink) }}">
                                <i class="fa fa-arrow-circle-left"></i> Go Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <input type="hidden" id="dealer_id" name="dealer_id" value="<?= isset($_GET["id"]) ? $_GET["id"] : '' ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="title">Document Title</label>
                            <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                                <input type="text" class="form-control" name="title" value="" placeholder="Document Title" id="docTitle">
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="document">Dealer Document</label>
                            <div class="form-group {{ $errors->has('document') ? ' has-error' : '' }}">
                                <input type="file" class="form-control" aria-describedby="fileHelp" name="document" id="dealerDoc">
                            </div>
                        </div>
                    </div>

                    <div id="documents">
                        <div class="col-md-12 mt-2 p-0">
                            <table id="document" class="table table-bordered table-striped documents">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Document</th>
                                        <th width='15%'>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty(@$dealer->documents) && count(@$dealer->documents) > 0)
                                    @foreach(@$dealer->documents as $document)
                                    <tr id="doc_{{ $document->id }}">
                                        <td>{{ $document->title }}</td>
                                        <td>{{ $document->document }}</td>
                                        <td>
                                            <a href="{{ route('dealerDocument.download', $document->id) }}" data-toggle="tooltip" data-original-title="Download"> <i class="fa fa-download m-r-10"></i> </a>
                                            <a href="{{ route('dealerDocument.view', $document->id) }}" data-toggle="tooltip" data-original-title="View" target="_blank"> <i class="fa fa-eye text-success m-r-10"></i> </a>
                                            <a href="javascript:void(0)" data-toggle="tooltip" data-original-title="Delete" class="" onclick="removeDocument({{ $document->id }})" style="width: 100%;"><i class="fa fa-trash text-danger"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-info">
                                <i class="fa fa-save"></i>
                                Upload Document
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('custom-js')

<script type="text/javascript">
    $(document).on('change', '#districtId', function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var districtId = $('#districtId').val();

            $.ajax({
                type:'post',
                url:'{{ route('dealerSetup.getAllUpazilaByDistrict') }}',
                data:{districtId:districtId},
                success:function(data){
                    $('#upazila-select-menu').html(data);
                    $(".chosen-select").chosen();
                }
            });
        });
        $(document).on('change', '#upazilaId', function(){
        
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var upazilaId = $('#upazilaId').val();

            $.ajax({
                type:'post',
                url:'{{ route('dealerSetup.getAllTeritoryByArea') }}',
                data:{upazilaId:upazilaId},
                success:function(data){
                    $('#teritori-select-menu').html(data);
                    $(".chosen-select").chosen();
                }
            });
        });

</script>



<script type="text/javascript">
    $(document).ready(function () {
        var updateThis;
        //ajax upload image
        $("form[name='document-form']").on("submit", function (event) {
            $('.has-danger').removeClass('has-danger');
            var formData = new FormData(this);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('dealerDocument.save') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    var document = response.document;
                    swal({
                        title: "<small class='text-success'>Success!</small>",
                        type: "success",
                        text: "Document Successfully Uploded!",
                        timer: 2000,
                        html: true,
                    });
                    $('#document tbody').append(document);
                },
                error: function (response) {

                }
            });
            
            $("#document-form")[0].reset();
        });
    });
    //ajax remove image
    function removeDocument(docId) {
        // e.preventDefault();
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
        },
                function (isConfirm) {
                    if (isConfirm)
                    {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: "{{ route('dealerDocument.delete') }}",
                    data: {id: docId},
                    success: function (response) {
                        swal({
                            icon: 'success',
                            title: "<small class='text-success'>Success!</small>",
                            text: "Document Successfully Deleted!",
                            type: "success",
                            timer: 1000,
                        });
                        $('#doc_' + docId).remove();
                    }
                })

            } else {
                        swal({
                            title: "Cancelled",
                            type: "error",
                            text: "Your Image is safe :)",
                            timer: 1000,
                            html: true,
                        });
                    }
                });
    }



</script>





@endsection
