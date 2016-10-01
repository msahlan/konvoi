@extends('layouts.login')

@section('content')
    <?php
        use App\Helpers\Prefs;
        //use Route;

        $route = Route::current()->getUri();

        //print $route;
        //var_dump($route);

    ?>
    <style type="text/css">
        .bootstrap-select:not([class*="col-"]):not([class*="form-control"]):not(.input-group-btn) {
            width: 100%;
            padding-left: 32px;
        }        

        .help-block{
            color:red;
        }
    </style>
    <form class="form-horizontal login-form" role="form" method="POST" action="{{ url('/register') }}">
        {{ csrf_field() }}
        <div class="panel panel-body">

            @if($route == 'creditor/register')
                <style type="text/css">
                    .login-container .page-container .login-form {
                      width: 740px !important;
                    }
                    .form-horizontal .form-group {
                        margin-left: 8px;
                        margin-right: 8px;
                    }

                </style>

                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 pad">

                        @include('auth.register_pic')

                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 pad">

                        @include('auth.register_creditor')

                    </div>
                </div>


            @else

                @include('auth.register_member')

            @endif

            <div class="form-group">
                <button type="submit" class="btn bg-indigo-400 btn-block">Register <i class="icon-user-plus position-right"></i></button>
            </div>

        </div>

    </form>
@endsection
