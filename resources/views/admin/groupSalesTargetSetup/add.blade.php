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
    <div class="row">
        <div class="col-md-6">
            <label for="group">Group</label>
            <div class="form-group">
                <select class="form-control chosen-select" id="group" name="group" required>
                    <option value="">Select Group</option>
                    @foreach ($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
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
                        $months = ['1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May',
                        '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' =>
                        'November', '12' => 'December'];
                        $month = date('m');
                        @endphp
                        <select class="form-control" id="month" name="month">
                            @foreach ($months as $key => $value)
                            @php
                            if ($key == $month) {
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
                        <option value="">Select Year</option>
                        @php
                        $currentYear = date('Y');
                        @endphp
                        @for ($i = $currentYear; $i >= 1900; $i--)
                        @php
                        if ($i == $currentYear) {
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
                    <input type="radio" class="form-check-input targetType" name="targetType" value="0" checked>Both
                </label>
            </div>
            <div class="form-check-inline">
                <label class="form-check-label">
                    <input type="radio" class="form-check-input targetType" name="targetType" value="1">Amount
                </label>
            </div>
        </div>
    </div>



    @php
    $i = 0;
    @endphp

    <table class="table table-bordered table-sm" id="targetByQty">
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
                    <input type="text" class="form-control" name="categoryName_{{ $i }}" value="{{ $category->name }}"
                        readonly>
                </td>
                <td>
                    <input type="number" class="form-control target" name="targets[]" value="0" oninput="countTarget()">
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
                <td><input type="number" class="form-control" id="totalTarget" name="totalTarget" value="0" readonly>
                </td>
                <td><input type="number" class="form-control" id="totalTargetAmt" name="totalTargetAmt" value="0"
                        readonly>
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
            @endphp
            <tr>
                <td>
                    <input type="hidden" name="category_id[]" value="{{ $category->id }}">
                    <input type="text" class="form-control" name="categoryName_{{ $i }}" value="{{ $category->name }}"
                        readonly>
                </td>
                <td>
                    <input type="number" class="form-control targets_amont" name="targets_amont[]" value="0"
                        oninput="countTargetAmont()">
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td id="total-target">Total Target</td>
                <td><input type="number" class="form-control" id="totalTargetAmont" name="totalTargetAmont"
                        readonly>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection

@section('custom-js')
<script type="text/javascript">
    function countTarget() {
            var totalTarget = 0;
            $(".target").each(function() {
                var target = parseInt($(this).val());
                totalTarget += isNaN(target) ? 0 : target;
            });

            $('#totalTarget').val(totalTarget);
        }

        function countTargetAmount() {
            var totalTarget = 0;
            $(".targetAmt").each(function() {
                var target = parseInt($(this).val());
                totalTarget += isNaN(target) ? 0 : target;
                console.log(target);
            });

            $('#totalTargetAmt').val(totalTarget);
        }

        function countTargetAmont() {
            var totalTarget = 0;
            $(".targets_amont").each(function() {
                var target = parseInt($(this).val());
                totalTarget += isNaN(target) ? 0 : target;
                console.log(target);
            });

            $('#totalTargetAmont').val(totalTarget);
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
