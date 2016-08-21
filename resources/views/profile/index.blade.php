@extends('layout.limitless')

@section('content')
<style type="text/css">
    .table-content-wrapper{
        display: table !important;
        margin-right: 20px;
    }

    .command-bar{
        padding:15px 20px;
    }

    .command-bar .btn{
        margin:0px 8px 8px 0px;
    }

</style>

<div class="panel panel-flat command-bar">
    <div class="row">
        <div class="col-md-6">
            <dl>
                <dt>Full Name</dt>
                    <dd>{{ Auth::user()->salutation }} {{ Auth::user()->fullname }}</dd>
                <dt>Email</dt><dd>{{ Auth::user()->email }}</dd>
                <dt>Mobile</dt><dd>{{ Auth::user()->mobile }}</dd>
                <dt>Address</dt><dd>{{ Auth::user()->address_1 }}
                    @if(Auth::user()->address_2 != '')
                        <br />{{ Auth::user()->address_2 }}
                    @endif
                </dd>
                <dt>City</dt><dd>{{ Auth::user()->city }}</dd>
                <dt>State / Provice</dt><dd>{{ Auth::user()->state }}</dd>
                <dt>Country</dt><dd>{{ Auth::user()->countryOfOrigin }}</dd>
            </dl>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 command-bar">
            <img src="{{ Auth::user()->avatar}}" alt="avatar" class="avatar-medium" />
        </div>
    </div>
</div>
@stop