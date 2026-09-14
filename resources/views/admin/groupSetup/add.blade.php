@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <style type="text/css">
        .chosen-single {
            height: 35px !important;
        }

    </style>

    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group {{ $errors->has('name') ? ' has-danger' : '' }}">
                    <label for="group-name">Group Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                    @if ($errors->has('name'))
                        @foreach ($errors->get('name') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group {{ $errors->has('teamLeader') ? ' has-danger' : '' }}">
                    <label for="team-lead">Team Leader</label>
                    <select class="form-control chosen-select" id="teamLeader" name="teamLeader">
                        <option value="">Select Team Leader</option>
                        @foreach ($staffs as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('teamLeader'))
                        @foreach ($errors->get('teamLeader') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <label for="team-member">Team Members</label>
                <div id="team-leader-menu">
                    <div class="form-group {{ $errors->has('teamMember') ? ' has-danger' : '' }}">
                        <select class="form-control chosen-select" data-placeholder="Select Team Members" id="teamMember"
                            name="teamMember[]" multiple>
                            @foreach ($staffs as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('teamMember'))
                            @foreach ($errors->get('teamMember') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            {{-- <div class="col-md-6">
                        <label for="team-member">Team Members</label>
                        <div id="team-leader-menu">
                            <div class="form-group {{ $errors->has('teamMember') ? ' has-danger' : '' }}">
                                <select class="form-control chosen-select" data-placeholder="Select Team Members" id="teamMember" name="teamMember[]" multiple>
                                </select>
                                @if ($errors->has('teamMember'))
                                    @foreach ($errors->get('teamMember') as $error)
                                        <div class="form-control-feedback">{{ $error }}</div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
    				</div> --}}
        </div>
    </div>
@endsection

@section('custom-js')
    {{-- <script type="text/javascript">
        $(document).on('change', '#teamLeader', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var staffId = $('#teamLeader').val();

            $.ajax({
                type: 'post',
                url: '{{ route('groupSetup.getAllStaff') }}',
                data: {
                    staffId: staffId
                },
                success: function(data) {
                    $('#team-leader-menu').html(data);
                    $(".chosen-select").chosen();
                }
            });
        });
    </script> --}}
@endsection
