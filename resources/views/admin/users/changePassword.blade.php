@extends('admin.layouts.masterAddEdit')

@section('card_body')
    <div class="card-body">
        <input type="hidden" name="userId" value="{{ $users->id }}">

        <div class="row">
            <div class="col-md-12">                                        
                <div class="form-group {{ $errors->has('password') ? ' has-danger' : '' }}">
                    <label for="password">Password</label>
                    <input type="password" class="form-control form-control-danger" name="password" value="" required>
                    @if ($errors->has('password'))
                        @foreach($errors->get('password') as $error)
                            <div class="form-control-feedback">{{ $error }}</div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection


