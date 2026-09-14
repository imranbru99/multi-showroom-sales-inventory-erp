@extends('layouts.app')

@section('title')
<title>{{ $title }}</title>
@endsection

@section('content')
<div class="container ">
    <div class="row" style="display: flex; justify-content: center; padding-top: 150px ">
        <div class="col-md-7">
            <div class="panel panel-default">

                <div class="panel-body" style="padding-top: 35px">
                    <form class="form-horizontal" method="POST" action="{{ route('admin.login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="username" class="col-md-4 control-label">Username</label>

                            <div class="col-md-6">
                                <input id="username" type="username" class="form-control" name="username"
                                       value="{{ old('username') }}" required autofocus>

                                @if ($errors->has('username'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('username') }}</strong>
                                </span>
                                @endif

                                <?php
                                $message = Session::get('msg');
                                if (isset($message)) {
                                    echo $message;
                                }

                                Session::forget('msg');
                                ?>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                @endif

                                <?php
                                $passwordMessage = Session::get('passwordMessage');
                                if (isset($passwordMessage)) {
                                    echo $passwordMessage;
                                }

                                Session::forget('passwordMessage');
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>

                                <a class="btn btn-link" href="{{ route('admin.password.forget') }}">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
