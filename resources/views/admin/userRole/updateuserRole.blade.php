@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <div class="card-body">
        <div class="row">
            <input type="hidden" name="userroleId" value="{{$userRoles->id}}">
            <div class="col-md-12">
                <label for="name">Name</label>
                <div class="form-group {{ $errors->has('name') ? ' has-danger' : '' }}">
                    <input type="text" class="form-control form-control-danger" placeholder="Name" name="name" value="{{ $userRoles->name }}" required>
                    @if ($errors->has('name'))
                        @foreach($errors->get('name') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custome-js')
    <script type="text/javascript">   
        // document.forms['editUser'].elements['role'].value = "{{$userRoles->role}}";        
    </script>
@endsection


