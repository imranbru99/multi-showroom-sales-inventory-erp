@extends('admin.layouts.masterAddEdit')

@section('custom_css')
    <style type="text/css">
        .blockTitle {
            color: #333;
            font-family: tahoma;
            border-bottom: 1px solid #a2a2a2;
            display: inline-block;
            padding-bottom: 6px;
        }

    </style>
@endsection

@section('card_body')
    @php
    $maritalStatus = ['Unmarried' => 'Unmarried', 'Married' => 'Married'];
    @endphp
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <h4 class="text-center py-3" style="font-weight: bold;font-family: tahoma; background-color: #ddd;">Personal
                    Information</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <input class="form-control" type="hidden" name="customerId" value="{{ $customer->id }}">
            </div>
        </div>

        <div class="row">

            <div class="col-md-5">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('code') ? ' has-danger' : '' }}">
                            <label for="code">Project</label>
                            <select class="form-control select_project" name="project_id" required>
                                <option value="">Select Project</option>
                                @foreach ($showroomProjects as $showroomProject)
                                    <option value="{{ $showroomProject->id }}" @if ($showroomProject->id == $customer->project_id) selected @endif>
                                        {{ $showroomProject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('code') ? ' has-danger' : '' }}">
                            <label for="code">ACCOUNT NO.</label>
                            <input type="text" class="form-control" id="apllicantsCode" name="code"
                                value="{{ $customer->code }}">
                            @if ($errors->has('code'))
                                @foreach ($errors->get('code') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('name') ? ' has-danger' : '' }}">
                    <label for="name">Applicant's Name</label>
                    <input type="text" class="form-control" name="name" value="{{ $customer->name }}">
                    @if ($errors->has('name'))
                        @foreach ($errors->get('name') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('phoneNo') ? ' has-danger' : '' }}">
                    <label for="phone-no">Phone No</label>
                    <input type="text" class="form-control" id="applicants_phone_no" name="phoneNo"
                        value="{{ $customer->phone_no }}" oninput="ApplicantCode()">
                    @if ($errors->has('phoneNo'))
                        @foreach ($errors->get('phoneNo') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>


        </div>

        <div class="row">

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('nickName') ? ' has-danger' : '' }}">
                    <label for="nick-name">Nick Name</label>
                    <input type="text" class="form-control" name="nickName" value="{{ $customer->nick_name }}">
                    @if ($errors->has('nickName'))
                        @foreach ($errors->get('nickName') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('fathersName') ? ' has-danger' : '' }}">
                    <label for="fathers-name">Father's Name</label>
                    <input type="text" class="form-control" name="fathersName" value="{{ $customer->fathers_name }}">
                    @if ($errors->has('fathersName'))
                        @foreach ($errors->get('fathersName') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('mothersName') ? ' has-danger' : '' }}">
                    <label for="mothers-name">Mother's Name</label>
                    <input type="text" class="form-control" name="mothersName" value="{{ $customer->mothers_name }}">
                    @if ($errors->has('mothersName'))
                        @foreach ($errors->get('mothersName') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>

        <div class="row">

            <div class="col-md-2">
                <div class="form-group {{ $errors->has('age') ? ' has-danger' : '' }}">
                    <label for="age">Age</label>
                    <input type="number" class="form-control" name="age" value="{{ $customer->age }}">
                    @if ($errors->has('age'))
                        @foreach ($errors->get('age') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>



            <div class="col-md-2">
                <div class="form-group {{ $errors->has('maritalStatus') ? ' has-danger' : '' }}">
                    <label for="mrital-status">Marital Status</label>
                    <select name="maritalStatus" class="form-control maritalStatus" style="height: 41%">
                        @foreach ($maritalStatus as $key => $value)
                            @php
                                if ($value == $customer->marital_status) {
                                    $select = 'selected';
                                } else {
                                    $select = '';
                                }
                            @endphp
                            <option value="{{ $value }}" {{ $select }}>{{ $key }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('maritalStatus'))
                        @foreach ($errors->get('maritalStatus') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>


            <div class="col-md-4">
                <div class="form-group {{ $errors->has('spouseName') ? ' has-danger' : '' }}">
                    <label for="spouse-name">Spouse Name</label>
                    <input type="text" class="form-control spouceName" name="spouseName"
                        value="{{ $customer->spouse_name }}">
                    @if ($errors->has('spouseName'))
                        @foreach ($errors->get('spouseName') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group {{ $errors->has('residenceDuration') ? ' has-danger' : '' }}">
                    <label for="residence-duration">Residence Duration</label>
                    <input type="number" class="form-control" name="residenceDuration"
                        value="{{ $customer->residence_duration }}" placeholder="Distance">
                    @if ($errors->has('residenceDuration'))
                        @foreach ($errors->get('residenceDuration') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group {{ $errors->has('totalFamilyMember') ? ' has-danger' : '' }}">
                    <label for="total-family-member">Family Members</label>
                    <input type="number" class="form-control" name="totalFamilyMember"
                        value="{{ $customer->total_family_member }}">
                    @if ($errors->has('totalFamilyMember'))
                        @foreach ($errors->get('totalFamilyMember') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>

        <div class="row">

            <div class="col-md-2">
                <div class="form-group {{ $errors->has('agree_date') ? ' has-danger' : '' }}">
                    <label class="___class_+?54___" for="agree_amnt">Agree.Date</label>
                    <input type="text" class="form-control datepicker" name="agree_date"
                        value="{{ !empty($customerAgree->date) ? date('d-m-Y', strtotime($customerAgree->date)) : '' }}">
                    @if ($errors->has('agree_date'))
                        @foreach ($errors->get('agree_date') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group {{ $errors->has('agree_amnt') ? ' has-danger' : '' }}">
                    <label class="___class_+?57___" for="agree_amnt">Agree.Amount</label>
                    <input type="number" class="form-control" name="agree_amnt"
                        value="{{ @$customerAgree->agreement_amount }}">
                    @if ($errors->has('agree_amnt'))
                        @foreach ($errors->get('agree_amnt') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group {{ $errors->has('mr_no') ? ' has-danger' : '' }}">
                    <label for="mr_no">MR. No </label>
                    <input type="number" class="form-control" name="mr_no" value="{{ @$customerAgree->money_r_no }}">
                    @if ($errors->has('mr_no'))
                        @foreach ($errors->get('mr_no') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group {{ $errors->has('reference') ? ' has-danger' : '' }}">
                    <label for="reference">Reference</label>
                    <select name="reference" class="form-control reference chosen-select" style="height: 41%">
                        @foreach ($refers as $refer)
                            <?php
                            $select = '';
                            if ($refer->id == @$customerAgree->employee_id) {
                                $select = 'selected';
                            }
                            ?>
                            <option value="{{ $refer->id }}" {{ $select }}>{{ $refer->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('reference'))
                        @foreach ($errors->get('reference') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group {{ $errors->has('nid') ? ' has-danger' : '' }}">
                    <label for="total-family-member">National ID No</label>
                    <input type="number" class="form-control" name="nid" value="{{ $customer->nid }}">
                    @if ($errors->has('nid'))
                        @foreach ($errors->get('nid') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>



            <div class="col-md-4 mt-4">
                <div class="form-group {{ $errors->has('gender') ? ' has-danger' : '' }} d-flex">
                    <div><label for="gender">Gender</label></div>
                    <div class="ml-4">
                        <input type="radio" class="form-control_" value="Male" name="gender"
                            {{ $customer->gender == 'Male' ? 'checked' : '' }}> Male
                        <input type="radio" class="form-control_ ml-2" value="Female" name="gender"
                            {{ $customer->gender == 'Female' ? 'checked' : '' }}> Female
                    </div>
                </div>
            </div>


            <div class="col-md-4 mt-4">
                <div class="form-group {{ $errors->has('currentResidence') ? ' has-danger' : '' }} d-flex">
                    <div><label for="gender">RESIDENCE</label></div>
                    <div class="ml-4">
                        <input type="radio" class="form-control_" value="own" name="currentResidence"
                            {{ $customer->current_residence == 'own' ? 'checked' : '' }}> Own Residence
                        <input type="radio" class="form-control_ ml-2" value="rent" name="currentResidence"
                            {{ $customer->current_residence == 'rent' ? 'checked' : '' }}> Rent
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('image') ? ' has-danger' : '' }}">
                    <label for="image">Image: </label>
                    <input type="file" class="form-control" name="image">
                </div>
                @if($customer->image)
                <img src="{{ asset($customer->image) }}" width="100px">
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('presentAddress') ? ' has-danger' : '' }}">
                    <label for="present-address">Present Address</label>
                    <textarea class="form-control" name="presentAddress"
                        rows="2">{{ $customer->present_address }}</textarea>
                    @if ($errors->has('presentAddress'))
                        @foreach ($errors->get('presentAddress') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group {{ $errors->has('permanentAddress') ? ' has-danger' : '' }}">
                    <label for="permanent-address">Permanent Address</label>
                    <textarea class="form-control" name="permanentAddress"
                        rows="2">{{ $customer->permanent_address }}</textarea>
                    @if ($errors->has('permanentAddress'))
                        @foreach ($errors->get('permanentAddress') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h4 class="text-center py-3" style="font-weight: bold;font-family: tahoma; background-color: #ddd">
                    Professional Information</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('professionName') ? ' has-danger' : '' }}">
                    <label for="profession-name">Profession's Name</label>
                    <input type="text" class="form-control" name="professionName"
                        value="{{ $customer->profession_name }}">
                    @if ($errors->has('professionName'))
                        @foreach ($errors->get('professionName') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('professionDuration') ? ' has-danger' : '' }}">
                            <label for="profession-duration">Duration of Profession</label>
                            <input type="number" class="form-control" name="professionDuration"
                                value="{{ $customer->profession_duration }}" placeholder="write number of year">
                            @if ($errors->has('professionDuration'))
                                @foreach ($errors->get('professionDuration') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('totalEarningMember') ? ' has-danger' : '' }}">
                            <label for="total-earning-member">Total Earning Member</label>
                            <input type="number" class="form-control" name="totalEarningMember"
                                value="{{ $customer->total_earning_member }}">
                            @if ($errors->has('totalEarningMember'))
                                @foreach ($errors->get('totalEarningMember') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group {{ $errors->has('designation') ? ' has-danger' : '' }}">
                            <label for="designation">Designation</label>
                            <input type="text" class="form-control" name="designation"
                                value="{{ $customer->designation }}">
                            @if ($errors->has('designation'))
                                @foreach ($errors->get('designation') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group {{ $errors->has('monthlyIncome') ? ' has-danger' : '' }}">
                            <label for="monthly-income">Monthly Income</label>
                            <input type="number" class="form-control" name="monthlyIncome"
                                value="{{ $customer->monthly_income }}">
                            @if ($errors->has('monthlyIncome'))
                                @foreach ($errors->get('monthlyIncome') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <label for="work-place-address">Work Place address</label>
                <textarea name="workPlaceAddress" class="form-control"
                    style="min-height: 122px;">{{ $customer->work_place_address }}</textarea>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".spouceName").prop('disabled', true);
            $('.maritalStatus').click(function(event) {
                var maritalStatus = $('.maritalStatus').val();
                if (maritalStatus == "Married") {
                    $(".spouceName").prop('disabled', false);
                } else {
                    $(".spouceName").prop('disabled', true);
                }
            })
        });
    </script>

    <script>
        var href = $('.go_back').attr('href');
        $('.go_back').attr('href', href + '?project={{ @$customer->project_id }}');

        $('.select_project').change(function() {
            var selected = $('.select_project').val();
            $('.go_back').attr('href', href + '?project=' + selected);
        });
    </script>
@endsection
