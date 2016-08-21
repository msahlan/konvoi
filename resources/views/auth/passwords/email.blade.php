@extends('layouts.login')

<!-- Main Content -->
@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form class="form-horizontal login-form" role="form" method="POST" action="{{ url('/password/email') }}">

        {{ csrf_field() }}

        <div class="panel panel-body">
            <div class="text-center">
                <div class="icon-object border-slate-300 text-slate-300"><i class="icon-reset"></i></div>
                <h5 class="content-group">Reset Password <small class="display-block">Enter your email address below</small></h5>
            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">

                <div class="form-control-feedback">
                    <i class="icon-envelop text-muted"></i>
                </div>
                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group">
                <button type="submit" class="btn bg-indigo-400 btn-block">SEND <i class="icon-circle-right2 position-right"></i></button>
            </div>

        </div>
    </form>

@endsection
