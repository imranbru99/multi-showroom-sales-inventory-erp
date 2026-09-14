@extends('admin.layouts.master')

@section('content')
 @php
     use App\CustomerRegistrationSetup;
     $applicantsLastId = CustomerRegistrationSetup::max('id');
     if(@$applicantsLastId){
        $apllicantsId = $applicantsLastId+1;
     }else{
        $apllicantsId = 1;
     }

    $maritalStatus = array('Unmarried' => 'Unmarried', 'Married' => 'Married');
 @endphp
    <style type="text/css">
        .blockTitle{
            color: #333;
            font-family: tahoma;
            border-bottom: 1px solid #a2a2a2;
            display: inline-block;
            padding-bottom: 6px;
        }
    </style>

    <div style="padding-bottom: 10px;"></div>

    <form class="form-horizontal" action="{{ route($formLink) }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" name="customerId" value="{{$customer->id}}">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
                    <div class="col-md-6 text-right">
                        <a class="btn btn-outline-info btn-lg" onclick="GoBack()">
                            <i class="fa fa-arrow-circle-left"></i> Go Back
                        </a>                        
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect"><i class="fa fa-save"></i> {{ $buttonName }}</button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <input type="hidden" name="customerId" value="{{ $customer->id }}">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="text-center" style="font-weight: bold;font-family: tahoma">Personal Information</h4>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('name') ? ' has-danger' : '' }}">
                            <label for="name">Applicant's Name</label>
                            <input type="hidden" id="applicants_id" value="{{$apllicantsId}}">
                            <input type="text" class="form-control" name="name" value="{{ $customer->name }}">
                            @if ($errors->has('name'))
                                @foreach($errors->get('name') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('code') ? ' has-danger' : '' }}">
                            <label for="code">Applicant's Code</label>
                            <input type="text" class="form-control" id="apllicantsCode" value="{{ $customer->code }}" name="code">
                            @if ($errors->has('code'))
                                @foreach($errors->get('code') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('fathersName') ? ' has-danger' : '' }}">
                            <label for="fathers-name">Father's Name</label>
                            <input type="text" class="form-control" name="fathersName" value="{{ $customer->fathers_name }}">
                            @if ($errors->has('fathersName'))
                                @foreach($errors->get('fathersName') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('mothersName') ? ' has-danger' : '' }}">
                            <label for="mothers-name">Mother's Name</label>
                            <input type="text" class="form-control" name="mothersName" value="{{ $customer->mothers_name }}">
                            @if ($errors->has('mothersName'))
                                @foreach($errors->get('mothersName') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>                    
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group {{ $errors->has('nickName') ? ' has-danger' : '' }}">
                            <label for="nick-name">Nick Name</label>
                            <input type="text" class="form-control" name="nickName" value="{{ $customer->nick_name }}">
                            @if ($errors->has('nickName'))
                                @foreach($errors->get('nickName') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-1">
                        <div class="form-group {{ $errors->has('age') ? ' has-danger' : '' }}">
                            <label for="age">Age</label>
                            <input type="number" class="form-control" name="age" value="{{ $customer->age }}">
                            @if ($errors->has('age'))
                                @foreach($errors->get('age') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('phoneNo') ? ' has-danger' : '' }}">
                            <label for="phone-no">Phone No</label>
                            <input type="text" class="form-control" id="applicants_phone_no" name="phoneNo" value="{{ $customer->phone_no }}" oninput="ApplicantCode()">
                            @if ($errors->has('phoneNo'))
                                @foreach($errors->get('phoneNo') as $error)
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
                                  if($customer->marital_status == $value){
                                    $selected = "selected";
                                  }else{
                                    $selected = "";
                                  }
                              @endphp
                                 <option value="{{$value}}" {{@$selected}}>{{$value}}</option>
                              @endforeach
                            </select>
                            @if ($errors->has('maritalStatus'))
                                @foreach($errors->get('maritalStatus') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('spouseName') ? ' has-danger' : '' }}">
                            <label for="spouse-name">Spouse Name</label>
                            <input type="text" class="form-control spouceName" name="spouseName" value="{{ $customer->spouse_name }}">
                            @if ($errors->has('spouseName'))
                                @foreach($errors->get('spouseName') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group {{ $errors->has('nid') ? ' has-danger' : '' }}">
                            <label for="total-family-member">National ID No</label>
                            <input type="number" class="form-control" name="nid" value="{{ $customer->nid }}">
                            @if ($errors->has('nid'))
                                @foreach($errors->get('nid') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label for="gender">Gender</label>
                        <div class="form-group {{ $errors->has('gender') ? ' has-danger' : '' }}" style="height: 40px; line-height: 40px;">
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" value="Male" name="gender" {{ $customer->gender == "Male" ? 'checked' : '' }} required> Male
                                </label>
                            </div>

                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" value="Female" name="gender" {{ $customer->gender == "Female" ? 'checked' : '' }}> Female
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="current-residence">Current Residence</label>
                        <div class="form-group {{ $errors->has('currentResidence') ? ' has-danger' : '' }}" style="height: 40px; line-height: 40px;">
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" value="own" name="currentResidence" {{ $customer->current_residence == "own" ? 'checked' : '' }}> Own Residence
                                </label>
                            </div>

                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" value="rent" name="currentResidence" {{ $customer->current_residence == "rent" ? 'checked' : '' }}> Rent
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group {{ $errors->has('residenceDuration') ? ' has-danger' : '' }}">
                            <label for="residence-duration">Residence Duration</label>
                            <input type="number" class="form-control" name="residenceDuration" value="{{ $customer->residence_duration }}" placeholder="write no of year">
                            @if ($errors->has('residenceDuration'))
                                @foreach($errors->get('residenceDuration') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group {{ $errors->has('totalFamilyMember') ? ' has-danger' : '' }}">
                            <label for="total-family-member">Family Members</label>
                            <input type="number" class="form-control" name="totalFamilyMember" value="{{ $customer->total_family_member }}">
                            @if ($errors->has('totalFamilyMember'))
                                @foreach($errors->get('totalFamilyMember') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('presentAddress') ? ' has-danger' : '' }}">
                            <label for="present-address">Present Address</label>
                            <textarea class="form-control" name="presentAddress" rows="2">{{ $customer->present_address }}</textarea>
                            @if ($errors->has('presentAddress'))
                                @foreach($errors->get('presentAddress') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('permanentAddress') ? ' has-danger' : '' }}">
                            <label for="permanent-address">Permanent Address</label>
                            <textarea class="form-control" name="permanentAddress"rows="2">{{ $customer->permanent_address }}</textarea>
                            @if ($errors->has('permanentAddress'))
                                @foreach($errors->get('permanentAddress') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="text-center" style="font-weight: bold;font-family: tahoma">Professional Information</h4>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('professionName') ? ' has-danger' : '' }}">
                            <label for="profession-name">Profession's Name</label>
                            <input type="text" class="form-control" name="professionName" value="{{ $customer->profession_name }}">
                            @if ($errors->has('professionName'))
                                @foreach($errors->get('professionName') as $error)
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
                                    <input type="number" class="form-control" name="professionDuration" value="{{ $customer->profession_duration }}" placeholder="write number of year">
                                    @if ($errors->has('professionDuration'))
                                        @foreach($errors->get('professionDuration') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                 <div class="form-group {{ $errors->has('totalEarningMember') ? ' has-danger' : '' }}">
                                    <label for="total-earning-member">Total Earning Member</label>
                                    <input type="number" class="form-control" name="totalEarningMember" value="{{ $customer->total_earning_member }}">
                                    @if ($errors->has('totalEarningMember'))
                                        @foreach($errors->get('totalEarningMember') as $error)
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
                                    <input type="text" class="form-control" name="designation" value="{{ $customer->designation }}">
                                    @if ($errors->has('designation'))
                                        @foreach($errors->get('designation') as $error)
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
                                    <input type="number" class="form-control" name="monthlyIncome" value="{{ $customer->monthly_income }}">
                                    @if ($errors->has('monthlyIncome'))
                                        @foreach($errors->get('monthlyIncome') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="work-place-address">Work Place address</label>
                        <textarea name="workPlaceAddress" class="form-control" style="min-height: 122px;">{{$customer->work_place_address}}</textarea>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-outline-info btn-lg waves-effect"><i class="fa fa-save"></i> {{ $buttonName }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('custom-js')
    <script type="text/javascript">
        $(document).ready(function() {
        /*code for spouce info*/
        
            if($('.maritalStatus').val() == "Married"){
                    $(".spouceName").prop('required',true);
                    $(".spouceName").prop('disabled', false);
                }else{
                     $(".spouceName").prop('disabled', true);
                     $(".spouceName").prop('required',true);
                }

            $('.maritalStatus').click(function(event) {
                var maritalStatus = $('.maritalStatus').val();
                if(maritalStatus == "Married"){
                   $(".spouceName").prop('required',true);
                    $(".spouceName").prop('disabled', false);
                }else{
                     $(".spouceName").prop('disabled', true);
                     $(".spouceName").prop('required',false);
                     $(".spouceName").val('');
                }
            })

        /*end code for spouce info*/
            
        });
    </script>

    <script type="text/javascript">
    /*code for applicants code*/
        function ApplicantCode() {
            var applicantsId = document.getElementById("applicants_id").value;
            var applicantsPhoneNo = document.getElementById("applicants_phone_no").value;
             lastThreeDigitofPhoneNo = applicantsPhoneNo % 1000;
            document.getElementById("apllicantsCode").value = "mnh-" +applicantsId+"-"+lastThreeDigitofPhoneNo;
        }
    /*end code for applicants code*/
    </script>

@endsection