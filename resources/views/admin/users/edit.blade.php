@extends('admin.layouts.masterAddEdit')

@section('card_body')
    @php $auth = auth()->user(); @endphp
    <div class="card-body">
        <input type="hidden" name="userId" value="{{ $users->id }}">
        <input type="hidden" name="previousUserImage" value="{{ $users->image }}">

        <div class="row">
            <div class="@if ($auth->role == 1) col-md-4 @else col-md-6 @endif">
                <div class="form-group {{ $errors->has('parent') ? ' has-danger' : '' }}">
                    <label for="role">User Role</label>
                    <select class="form-control" name="role" required>
                        <option value="">Select Role</option>
                        @foreach ($userRoles as $role)
                            <?php
                            if ($role->id == $users->role) {
                                $select = 'selected';
                            } else {
                                $select = '';
                            }
                            ?>
                            <option value="{{ $role->id }}" {{ $select }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('parent'))
                        @foreach ($errors->get('parent') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            @if ($auth->role == 1)
                <div class="col-md-4">
                    <div class="form-group {{ $errors->has('company') ? ' has-danger' : '' }}">
                        <label for="company">Company</label>
                        <select class="form-control chosen-select" id="company" name="company" required>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('company'))
                            @foreach ($errors->get('company') as $error)
                                <div class="form-control-feedback">{{ $error }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif

            <div class="@if ($auth->role == 1) col-md-4 @else col-md-6 @endif">
                <div class="form-group {{ $errors->has('showrooms') ? ' has-danger' : '' }}">
                    <label for="role">Showrooms</label>
                    <select class="form-control chosen-select" id="showrooms" name="showrooms[]" multiple>
                        <?php
                        $showroomsId = explode(',', $users->showroomId);
                        ?>
                        @foreach ($showrooms as $showroom)
                            <?php
                            if (in_array($showroom->id, $showroomsId)) {
                                $select = 'selected';
                            } else {
                                $select = '';
                            }
                            ?>
                            <option value="{{ $showroom->id }}" {{ $select }}>{{ $showroom->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('name') ? ' has-danger' : '' }}">
                    <label for="name">Name</label>
                    <input type="text" class="form-control form-control-danger" name="name" value="{{ $users->name }}"
                        required>
                    @if ($errors->has('name'))
                        @foreach ($errors->get('name') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group {{ $errors->has('username') ? ' has-danger' : '' }}">
                    <label for="user-name">User Name</label>
                    <input type="text" class="form-control form-control-danger" name="username"
                        value="{{ $users->username }}" required readonly>
                    @if ($errors->has('username'))
                        @foreach ($errors->get('username') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('email') ? ' has-danger' : '' }}">
                    <label for="email">Email</label>
                    <input type="email" class="form-control form-control-danger" name="email" value="{{ $users->email }}"
                        required>
                    @if ($errors->has('email'))
                        @foreach ($errors->get('email') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group {{ $errors->has('userImage') ? ' has-danger' : '' }}">
                            <label for="user-image">User Image</label>
                            <input type="file" class="form-control-file border" name="userImage">
                            @if ($errors->has('userImage'))
                                @foreach ($errors->get('userImage') as $error)
                                    <div class="form-control-feedback">{{ $error }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        @if ($users->image)
                            <img src="{{ asset('$users->image') }}" class="img-thumbnail" alt="User Image" width="100px"
                                height="100px">
                        @else
                            <img src="{{ asset('/public/others_images/no_image.png') }}" class="img-thumbnail"
                                alt="User Image" width="100px" height="100px">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        document.forms['editUser'].elements['role'].value = "{{ $users->role }}";
    </script>
@endsection
