@extends('layouts.login')

@section('content')
    <?php
        use App\Helpers\Prefs;
    ?>
    <form class="form-horizontal login-form" role="form" method="POST" action="{{ url('/register') }}">
        {{ csrf_field() }}

        <div class="panel panel-body">
            <div class="text-center">
                <div class="icon-object border-slate-300 text-slate-300"><i class="icon-user-plus"></i></div>
                <h5 class="content-group">Register <small class="display-block">Enter your informations below</small></h5>
            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="name" type="name" class="form-control" placeholder="Full Name" name="name" value="{{ old('name') }}">

                <div class="form-control-feedback">
                    <i class="icon-user text-muted"></i>
                </div>
                @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="email" type="email" class="form-control" placeholder="Email Address" name="email" value="{{ old('email') }}">

                <div class="form-control-feedback">
                    <i class="icon-envelop text-muted"></i>
                </div>
                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="password" type="password" class="form-control" placeholder="Password" name="password" value="{{ old('password') }}">

                <div class="form-control-feedback">
                    <i class="icon-lock2 text-muted"></i>
                </div>
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group has-feedback has-feedback-left">
                <input id="password_confirmation" type="password" placeholder="Confirm Password" class="form-control" name="password_confirmation" value="{{ old('password_confirmation') }}">

                <div class="form-control-feedback">
                    <i class="icon-lock2 text-muted"></i>
                </div>
                @if ($errors->has('password_confirmation'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </span>
                @endif
                <input type="hidden" name="roleId" value="{!! Prefs::getRoleId('Member') !!}" />
            </div>

            <div class="form-group">
                <button type="submit" class="btn bg-indigo-400 btn-block">Register <i class="icon-user-plus position-right"></i></button>
            </div>

        </div>

    </form>
@endsection
