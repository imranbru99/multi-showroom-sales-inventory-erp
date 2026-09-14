@extends('admin.layouts.masterAddEdit')

@section('custom_css')
    <style type="text/css">
        #total-target {
            vertical-align: middle;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
        }

    </style>
@endsection

@section('card_body')
    <div class="card-body">
        <div class="row d-flex justify-content-center">
            <div class="col-md-5">
                <label for="group">Dealer : </label>
                <div class="form-group">
                    <select class="form-control chosen-select" id="dealer" name="dealer">
                        <option value="">Select Dealer</option>
                        @foreach ($dealers as $dealer)
                            <option value="{{ $dealer->id }}">{{ $dealer->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        @php
            $i = 0;
        @endphp

        <table class="table table-bordered table-sm" id="targetByQty">
            <thead class="thead-dark">
                <th width="80%">Category</th>
                <th>Commission</th>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>
                            <input type="hidden" name="categoryId[]" value="{{ $category->id }}">
                            <input type="text" class="form-control" name="categoryName[]"
                                value="{{ $category->name }}" readonly>
                        </td>
                        <td>
                            <input type="number" class="form-control text-right" name="commission[]" value="0" required>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

