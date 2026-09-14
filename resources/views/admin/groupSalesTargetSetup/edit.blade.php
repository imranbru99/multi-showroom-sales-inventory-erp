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
        <input type="hidden" name="groupSalesTargetId" value="{{ $groupSalesTarget->id }}">
        <div class="row">
            <div class="col-md-6">
                <label for="group">Group</label>
                <div class="form-group">
                    <select class="form-control chosen-select" id="group" name="group">
                        <option value="">Select Group</option>
                        @foreach ($groups as $group)
                            @php
                                if ($group->id == $groupSalesTarget->group_id) {
                                    $select = 'selected';
                                } else {
                                    $select = '';
                                }
                            @endphp
                            <option value="{{ $group->id }}" {{ $select }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="from-date">Month</label>
                        <div class="form-group">
                            @php
                                $select = '';
                                $months = ['1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'];
                            @endphp
                            <select class="form-control" id="month" name="month">
                                @foreach ($months as $key => $value)
                                    @php
                                        if ($key == $groupSalesTarget->month) {
                                            $select = 'selected';
                                        } else {
                                            $select = '';
                                        }
                                    @endphp
                                    <option value="{{ $key }}" {{ $select }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="from_date">Year</label>
                        <select class="form-control" id="year" name="year">
                            @php
                                $currentYear = date('Y');
                            @endphp
                            <option value="">Select Year</option>
                            @for ($i = $currentYear; $i >= 1900; $i--)
                                @php
                                    if ($i == $groupSalesTarget->year) {
                                        $select = 'selected';
                                    } else {
                                        $select = '';
                                    }
                                @endphp
                                <option value="{{ $i }}" {{ $select }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row d-flex justify-content-end">
            <div class="col-md-3">
                <label class="form-check-label mr-2">Target Type</label>
                <div class="form-check-inline">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input targetType" name="targetType" value="0" @if ($groupSalesTarget->target_type == 0) checked @endif>Both
                    </label>
                </div>
                <div class="form-check-inline">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input targetType" name="targetType" value="1" @if ($groupSalesTarget->target_type == 1) checked @endif>Amount
                    </label>
                </div>
            </div>
        </div>


        @if ($groupSalesTarget->target_type == 0)
            @php
                $i = 0;
            @endphp

            <table class="table table-bordered table-sm" id="targetByQty">
                <thead class="thead-dark">
                    <th>Category</th>
                    <th>Target</th>
                    <th>Target Amount</th>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        @php
                            $i++;
                            $target = 0;
                            foreach ($groupSalesTargetCategories as $groupSalesTargetCategory) {
                                if ($category->id == $groupSalesTargetCategory->category_id) {
                                    $target = $groupSalesTargetCategory->target;
                                    $targetAmt = $groupSalesTargetCategory->target_amt;
                                }
                            }
                        @endphp
                        <tr>
                            <td>
                                <input type="hidden" name="categoryId[]" value="{{ $category->id }}">
                                <input type="text" class="form-control" name="categoryName_{{ $i }}"
                                    value="{{ $category->name }}" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control target" name="targets[]"
                                    value="{{ $target }}" oninput="countTarget()">
                            </td>
                            <td>
                                <input type="number" class="form-control targetAmt" name="targets_amt[]"
                                    value="{{ $targetAmt }}" oninput="countTargetAmount()">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td id="total-target">Total Target</td>
                        <td>
                            <input type="number" class="form-control" id="totalTarget" name="totalTarget"
                                value="{{ $groupSalesTarget->total_target }}" readonly>
                        </td>
                        <td>
                            <input type="number" class="form-control" id="totalTargetAmt" name="totalTargetAmt"
                                value="{{ $groupSalesTarget->total_target_amt }}" readonly>
                        </td>
                    </tr>
                </tfoot>
            </table>


            <table class="table table-bordered table-sm" id="targetByAmount" style="display: none;">
                <thead class="thead-dark">
                    <th>Category</th>
                    <th>Target Amount</th>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        @php
                            $i++;
                            $target = 0;
                            foreach ($groupSalesTargetCategories as $groupSalesTargetCategory) {
                                if ($category->id == $groupSalesTargetCategory->category_id) {
                                    $target = $groupSalesTargetCategory->target;
                                    $targetAmt = $groupSalesTargetCategory->target_amt;
                                }
                            }
                        @endphp
                        <tr>
                            <td>
                                <input type="hidden" name="categoryId[]" value="{{ $category->id }}">
                                <input type="text" class="form-control" name="categoryName_{{ $i }}"
                                    value="{{ $category->name }}" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control targetAmt" name="targets_amt[]"
                                    value="{{ $targetAmt }}" oninput="countTargetAmount()">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td id="total-target">Total Target</td>
                        <td>
                            <input type="number" class="form-control" id="totalTargetAmt" name="totalTargetAmt"
                                value="{{ $groupSalesTarget->total_target_amt }}" readonly>
                        </td>
                    </tr>
                </tfoot>
            </table>
        @endif


        @if ($groupSalesTarget->target_type == 1)
            {{-- <table class="table table-bordered table-sm" id="targetByAmount">
                <thead class="thead-dark" width="100%">
                    <tr>
                        <th colspan="2" class="text-center">Target</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="60%">Target Amount</td>
                        <td><input type="number" class="form-control" name="totalTargetAmount"
                                value="{{ $groupSalesTarget->total_target_amt }}"></td>
                    </tr>
                </tbody>
            </table> --}}

            <table class="table table-bordered table-sm" id="targetByAmount">
                <thead class="thead-dark">
                    <th>Category</th>
                    <th>Target Amount</th>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        @php
                            $i++;
                            $target = 0;
                            foreach ($groupSalesTargetCategories as $groupSalesTargetCategory) {
                                if ($category->id == $groupSalesTargetCategory->category_id) {
                                    $target = $groupSalesTargetCategory->target;
                                    $targetAmt = $groupSalesTargetCategory->target_amt;
                                }
                            }
                        @endphp
                        <tr>
                            <td>
                                <input type="hidden" name="categoryId[]" value="{{ $category->id }}">
                                <input type="text" class="form-control" name="categoryName_{{ $i }}"
                                    value="{{ $category->name }}" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control targetAmt" name="targets_amt[]"
                                    value="{{ $targetAmt }}" oninput="countTargetAmount()">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td id="total-target">Total Target</td>
                        <td>
                            <input type="number" class="form-control" id="totalTargetAmt" name="totalTargetAmt"
                                value="{{ $groupSalesTarget->total_target_amt }}" readonly>
                        </td>
                    </tr>
                </tfoot>
            </table>



            @php
                $i = 0;
            @endphp

            <table class="table table-bordered table-sm" id="targetByQty"  style="display: none;">
                <thead class="thead-dark">
                    <th>Category</th>
                    <th>Target Quantity</th>
                    <th>Target Amount</th>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        @php
                            $i++;
                        @endphp
                        <tr>
                            <td>
                                <input type="hidden" name="categoryId[]" value="{{ $category->id }}">
                                <input type="text" class="form-control" name="categoryName_{{ $i }}"
                                    value="{{ $category->name }}" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control target" name="targets[]" value="0"
                                    oninput="countTarget()">
                            </td>
                            <td>
                                <input type="number" class="form-control targetAmt" name="targets_amt[]" value="0"
                                    oninput="countTargetAmount()">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td id="total-target">Total Target</td>
                        <td><input type="number" class="form-control" id="totalTarget" name="totalTarget" value="0"
                                readonly>
                        </td>
                        <td><input type="number" class="form-control" id="totalTargetAmt" name="totalTargetAmt" value="0"
                                readonly>
                        </td>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>
@endsection

@section('custom-js')
    <script type="text/javascript">
        function countTarget(i) {
            var totalTarget = 0;
            $(".target").each(function() {
                var target = parseInt($(this).val());
                totalTarget += isNaN(target) ? 0 : target;
                console.log(totalTarget);
            });

            $('#totalTarget').val(totalTarget);
        }

        function countTargetAmount() {
            var totalTarget = 0;
            $(".targetAmt").each(function() {
                var target = parseInt($(this).val());
                totalTarget += isNaN(target) ? 0 : target;
            });

            $('#totalTargetAmt').val(totalTarget);
        }
    </script>

    <script>
        $('.targetType').click(function(event) {
            var targetType = $("input[name='targetType']:checked").val();

            if (targetType == 0) {
                $('#targetByQty').show();
                $('#targetByAmount').hide();
            }

            if (targetType == 1) {
                $('#targetByAmount').show();
                $('#targetByQty').hide();
            }
        })
    </script>
@endsection
