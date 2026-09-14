@extends('admin.layouts.masterIndex')

@section('card_body')
    <div class="row d-flex justify-content-center">
        <div class="col-md-5">
            <div class="form-group d-flex">
                <label class="font-weight-bold text-nowrap mt-2 mr-1" for="project">Select Project *</label>
                <select name="project" id="project" class="form-control chosen-select">
                    <option value="">Select Project *</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @if ($project_id == $project->id) selected @endif>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>




    <div class="card-body">
        <div class="table-responsive">
            <table id="dtbwyj" class="table table-bordered table-striped" name="showroomTable">
                <thead>
                    <tr>
                        <th width="20px" style="background: #00c292;font-weight: bold;padding: 5px;">SL</th>
                        <th width="100px">Date</th>
                        <th width="100px">Invoice</th>
                        <th width="150px">Customer Code</th>
                        <th width="150px">Customer Name</th>
                        <th width="80px">Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>

        </div>
    </div>
@endsection

@section('custom-js')
    <script>
        $(document).ready(function() {


            $(function() {

                var table = $('#dtbwyj').DataTable({

                    pageLength: 20,
                    processing: false,
                    serverSide: true,
                    ajax: "{{ route('productExchange.index') }}?project={{ $project_id }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'date',
                            name: 'date'
                        },
                        {
                            data: 'invoice_no',
                            name: 'invoice_no'
                        },
                        {
                            data: 'customer_code',
                            name: 'customer_code'
                        },
                        {
                            data: 'customer_name',
                            name: 'customer_name'
                        },
                        
                        {
                            data: 'action',
                            name: 'action',
                            searchable: false
                        },
                    ]

                });

            });

        });

    </script>

    <script>
        $(function() {
            $('#project').change(function(e) {
                e.preventDefault();

                var project = $('#project').val();

                window.location.href = '{{ route('productExchange.index') }}' + "?project=" +
                    project;

            });

        });
    </script>


    <script>
        var href = $('.add_new').attr('href');
        $('.add_new').attr('href', href + '?project={{ @$project_id }}');
    </script>
@endsection
