@extends('admin.layouts.master')

@section('content')
@php
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
        <input type="hidden" name="guarantorId" value="{{$guarantor->id}}">
        <input type="hidden" name="customerId" value="{{$guarantor->customer_id}}">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6"><h4 class="card-title">{{ $title }}</h4></div>
                    <div class="col-md-6 text-right">
                        <a class="btn btn-outline-info btn-lg" onclick="GoBack()">
                            <i class="fa fa-arrow-circle-left"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="text-center" style="font-weight: bold;font-family: tahoma">Update Guarantor Information</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('gurantorName') ? ' has-danger' : '' }}">
                            <label for="guarantor-name">Guarantor Name</label>
                            <input type="text" class="form-control" name="gurantorName" value="{{ $guarantor->gurantor_name }}">
                            @if ($errors->has('gurantorName'))
                                @foreach($errors->get('gurantorName') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                     <div class="col-md-6">
                        <div class="form-group {{ $errors->has('gurantorPhoneNo') ? ' has-danger' : '' }}">
                            <label for="guarantor-phone-no">Guarantor Phone No</label>
                            <input type="text" class="form-control" name="gurantorPhoneNo" value="{{ $guarantor->gurantor_phone_no }}">
                            @if ($errors->has('gurantorPhoneNo'))
                                @foreach($errors->get('gurantorPhoneNo') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group {{ $errors->has('guarantorFatherName') ? ' has-danger' : '' }}">
                                    <label for="guarantor-spouse-name">Father Name</label>
                                    <input type="text" class="form-control firstGuarantorFather" name="guarantorFatherName" value="{{ $guarantor->guarantor_father_name }}">
                                    @if ($errors->has('guarantorFatherName'))
                                        @foreach($errors->get('guarantorFatherName') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('gurantorAge') ? ' has-danger' : '' }}">
                                    <label for="guarantor-age">Guarantor Age</label>
                                    <input type="number" class="form-control" name="gurantorAge" value="{{ $guarantor->gurantor_age }}">
                                    @if ($errors->has('gurantorAge'))
                                        @foreach($errors->get('gurantorAge') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group {{ $errors->has('guarantorMaritalStatus') ? ' has-danger' : '' }}">
                                    <label for="guarantor-mrital-status">Marital Status</label>
                                    <select name="guarantorMaritalStatus" class="form-control firstGuarantorMaritalStatus" style="height: 41%">
                                      @foreach ($maritalStatus as $key => $value)
                                      @php
                                          if($guarantor->guarantor_marital_status == $value){
                                            $selected = "selected";
                                          }else{
                                            $selected = "";
                                          }
                                      @endphp
                                         <option value="{{$value}}" {{@$selected}}>{{$value}}</option>
                                      @endforeach
                                    </select>
                                    @if ($errors->has('guarantorMaritalStatus'))
                                        @foreach($errors->get('guarantorMaritalStatus') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group {{ $errors->has('guarantorSpouseName') ? ' has-danger' : '' }}">
                                    <label for="guarantor-spouse-name">Spouse Name</label>
                                    <input type="text" class="form-control firstGuarantorSpouce" name="guarantorSpouseName" value="{{ $guarantor->guarantor_spouse_name }}">
                                    @if ($errors->has('guarantorSpouseName'))
                                        @foreach($errors->get('guarantorSpouseName') as $error)
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
                        <div class="form-group {{ $errors->has('guarantorPresentAddress') ? ' has-danger' : '' }}">
                            <label for="guarantor-present-address">Present Address</label>
                            <textarea name="guarantorPresentAddress" class="form-control" style="min-height: 123px;">{{ $guarantor->guarantor_present_address }}</textarea>
                            @if ($errors->has('guarantorPresentAddress'))
                                @foreach($errors->get('guarantorPresentAddress') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('guarantorPermanentAddress') ? ' has-danger' : '' }}">
                            <label for="guarantor-permanent-address">Permanent Address</label>
                            <textarea name="guarantorPermanentAddress" class="form-control" style="min-height: 123px;">{{ $guarantor->guarantor_permanent_address }}</textarea>
                            @if ($errors->has('guarantorPermanentAddress'))
                                @foreach($errors->get('guarantorPermanentAddress') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h4 class="blockTitle">Guarantor Professional Information</h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('guarantorProfessionName') ? ' has-danger' : '' }}">
                            <label for="guarantor-profession-name">Profession's Name</label>
                            <input type="text" class="form-control" name="guarantorProfessionName" value="{{ $guarantor->guarantor_profession_name }}">
                            @if ($errors->has('guarantorProfessionName'))
                                @foreach($errors->get('guarantorProfessionName') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('guarantorDesignation') ? ' has-danger' : '' }}">
                            <label for="guarantor-designation">Designation</label>
                            <input type="text" class="form-control" name="guarantorDesignation" value="{{ $guarantor->guarantor_designation }}">
                            @if ($errors->has('guarantorDesignation'))
                                @foreach($errors->get('guarantorDesignation') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('guarantorWorkplacePhoneNo') ? ' has-danger' : '' }}">
                                    <label for="guarantor-workplace-phoneNo">Phone No</label>
                                    <input type="number" class="form-control" name="guarantorWorkplacePhoneNo" value="{{ $guarantor->guarantor_workplace_phone_no }}">
                                    @if ($errors->has('guarantorWorkplacePhoneNo'))
                                        @foreach($errors->get('guarantorWorkplacePhoneNo') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group {{ $errors->has('guarantorMonthlyIncome') ? ' has-danger' : '' }}">
                                    <label for="guarantor-monthly-income">Monthly Income</label>
                                    <input type="number" class="form-control" name="guarantorMonthlyIncome" value="{{ $guarantor->guarantor_monthly_income}}">
                                    @if ($errors->has('guarantorMonthlyIncome'))
                                        @foreach($errors->get('guarantorMonthlyIncome') as $error)
                                            <div class="form-control-feedback">{{ $error }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="monthly-income">Work Place address</label>
                        <textarea name="guarantorWorkPlaceAddress" class="form-control" style="min-height: 122px;">{{ $guarantor->guarantor_work_place_address}}</textarea>
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
        /*code for guarantor info*/
        
        if($('.firstGuarantorMaritalStatus').val() == "Married"){
                 $(".firstGuarantorSpouce").prop('disabled', false);
            }else{
                $(".firstGuarantorSpouce").prop('disabled', true);
            }

            $('.firstGuarantorMaritalStatus').click(function(event) {
                var firstGuarantorMaritalStatus = $('.firstGuarantorMaritalStatus').val();
                if(firstGuarantorMaritalStatus == "Married"){
                     $(".firstGuarantorSpouce").prop('disabled', false);
                }else{
                     $(".firstGuarantorSpouce").prop('disabled', true);
                    $(".firstGuarantorSpouce").val('');
                }
            })

        /*end code for guarantor info*/
            
        });
    </script>

@endsection