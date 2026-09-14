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
                <input type="text" class="form-control" name="professionName" value="{{ old('professionName') }}">
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
                        <input type="number" class="form-control" name="professionDuration" value="{{ old('professionDuration') }}" placeholder="write number of year">
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
                        <input type="number" class="form-control" name="totalEarningMember" value="{{ old('totalEarningMember') }}">
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
                        <input type="text" class="form-control" name="designation" value="{{ old('designation') }}">
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
                        <input type="number" class="form-control" name="monthlyIncome" value="{{ old('monthlyIncome') }}">
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
            <textarea name="workPlaceAddress" class="form-control" style="min-height: 122px;">{{ old('workPlaceAddress') }}</textarea>
        </div>
    </div>
</div>