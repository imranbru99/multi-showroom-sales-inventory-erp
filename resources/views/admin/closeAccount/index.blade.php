@extends('admin.layouts.master')
@php
use Carbon\Carbon;
@endphp
@section('content')
    <div class="card">
        <div class="card-body pb-0">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-4">
                        <h4 class="card-title">{{ $title }}</h4>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group d-flex">
                            <label class="font-weight-bold text-nowrap mt-2 mr-1" for="project">Project</label>
                            <select name="project" id="project" class="form-control chosen-select">
                                <option value="">Select Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}" @if ($project_id == $project->id) selected @endif>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <a style="font-size: 16px;" class="btn btn-outline-info btn-lg add_new" href="{{ route($addNewLink) }}">
                            <i class="fa fa-plus-circle"></i> Add New
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="row">

                <div class="col-md-12">
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table id="dtbwyj" class="table table-bordered table-striped" name="showroomTable">
                                <thead>
                                    <tr class="text-center">
                                        <th style="background-color:#00c292; font-size: 12px;font-weight: bold;
                                                                    padding: 5px;
                                                                    font-size: 11px;">SL#</th>
                                        <th>A/C No.</th>
                                        <th>Customer Name</th>
                                        <th>Reference</th>
                                        <th>Invoice Amnt.</th>
                                        <th>Int. Recv.</th>
                                        <th>Collection</th>
                                        <th>Dis. Amnt.</th>
                                        <th>Missing. Amnt.</th>
                                        <th>Balance</th>
                                        <th>Agree. Amnt.</th>
                                        <th>User</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('custom-js')
    <script>
        $(document).ready(function() {


            $(function() {

                var table = $('#dtbwyj').DataTable({

                    initComplete: function() {
                        // Apply the search
                        this.api().columns().every(function() {
                            var that = this;

                            $('input', this.footer()).on('keyup change clear',
                                function() {
                                    if (that.search() !== this.value) {
                                        that
                                            .search(this.value)
                                            .draw();
                                    }
                                });
                        });
                    },
                    pageLength: 20,
                    processing: false,
                    serverSide: true,
                    ajax: "{{ route('closeAccount.index') }}?project={{ $project_id }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'customer_code',
                            name: 'customer_code',
                            searchable: true
                        },
                        {
                            data: 'name',
                            name: 'name',
                            searchable: false
                        },
                        {
                            data: 'reference',
                            name: 'reference',
                            searchable: false
                        },
                        {
                            data: 'invoice_amount',
                            name: 'invoice_amount',
                            searchable: false
                        },
                        {
                            data: 'interest_receive',
                            name: 'interest_receive',
                            searchable: false
                        },
                        {
                            data: 'collection',
                            name: 'collection',
                            searchable: false
                        },
                        {
                            data: 'discount_amount',
                            name: 'discount_amount',
                            searchable: false
                        },
                        {
                            data: 'missing_amount',
                            name: 'missing_amount',
                            searchable: false
                        },
                        {
                            data: 'balance',
                            name: 'balance',
                            searchable: false
                        },
                        {
                            data: 'agreement_amount',
                            name: 'agreement_amount',
                            searchable: false
                        },
                        {
                            data: 'user',
                            name: 'user',
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            searchable: false
                        },
                    ]

                });

            });

            var updateThis;
        });
    </script>

    <script>
        $(function() {
            $('#project').change(function(e) {
                e.preventDefault();

                var project = $('#project').val();

                window.location.href = '{{ route('closeAccount.index') }}' + "?project=" +
                    project;

            });

        });
    </script>

    <script>
        var href = $('.add_new').attr('href');
        $('.add_new').attr('href', href + '?project={{ @$project_id }}');
    </script>
@endsection
