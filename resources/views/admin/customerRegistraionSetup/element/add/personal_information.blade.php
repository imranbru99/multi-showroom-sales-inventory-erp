 @php
     use App\CustomerRegistrationSetup;
     $applicantsLastId = CustomerRegistrationSetup::max('id');
     if(@$applicantsLastId){
        $apllicantsId = $applicantsLastId+1;
     }else{
        $apllicantsId = 1;
     }
 @endphp
 <div class="card-body">
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
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                    @if ($errors->has('name'))
                        @foreach($errors->get('name') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('nickName') ? ' has-danger' : '' }}">
                    <label for="nick-name">Nick Name</label>
                    <input type="text" class="form-control" name="nickName" value="{{ old('nickName') }}">
                    @if ($errors->has('nickName'))
                        @foreach($errors->get('nickName') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('fathersName') ? ' has-danger' : '' }}">
                    <label for="fathers-name">Father's Name</label>
                    <input type="text" class="form-control" name="fathersName" value="{{ old('fathersName') }}">
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
                    <input type="text" class="form-control" name="mothersName" value="{{ old('mothersName') }}">
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
                <div class="form-group {{ $errors->has('code') ? ' has-danger' : '' }}">
                    <label for="code">Applicant's Code</label>
                    <input type="text" class="form-control" id="apllicantsCode" name="code">
                    @if ($errors->has('code'))
                        @foreach($errors->get('code') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-1">
                <div class="form-group {{ $errors->has('age') ? ' has-danger' : '' }}">
                    <label for="age">Age</label>
                    <input type="number" class="form-control" name="age" value="{{ old('age') }}">
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
                    <input type="text" class="form-control" id="applicants_phone_no" name="phoneNo" value="{{ old('phoneNo') }}" oninput="ApplicantCode()">
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
                         <option value="{{$value}}">{{$value}}</option>
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
                    <input type="text" class="form-control spouceName" name="spouseName" value="{{ old('spouseName') }}">
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
                    <input type="number" class="form-control" name="nid" value="{{ old('nid') }}">
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
                            <input type="radio" value="Male" name="gender"> Male
                        </label>
                    </div>

                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" value="Female" name="gender"> Female
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <label for="current-residence">Current Residence</label>
                <div class="form-group {{ $errors->has('currentResidence') ? ' has-danger' : '' }}" style="height: 40px; line-height: 40px;">
                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" value="own" name="currentResidence"> Own Residence
                        </label>
                    </div>

                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" value="rent" name="currentResidence"> Rent
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group {{ $errors->has('residenceDuration') ? ' has-danger' : '' }}">
                    <label for="residence-duration">Residence Duration</label>
                    <input type="number" class="form-control" name="residenceDuration" value="{{ old('residenceDuration') }}" placeholder="Write No Of Year">
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
                    <input type="number" class="form-control" name="totalFamilyMember" value="{{ old('totalFamilyMember') }}">
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
                    <textarea class="form-control" name="presentAddress" rows="2">{{ old('presentAddress') }}</textarea>
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
                    <textarea class="form-control" name="permanentAddress"rows="2">{{ old('permanentAddress') }}</textarea>
                    @if ($errors->has('permanentAddress'))
                        @foreach($errors->get('permanentAddress') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
