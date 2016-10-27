<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ Config::get('site.name')}}</title>

    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="{{ url('limitless')}}/assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
    <link href="{{ url('limitless')}}/assets/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="{{ url('limitless')}}/assets/css/core.css" rel="stylesheet" type="text/css">
    <link href="{{ url('limitless')}}/assets/css/components.css" rel="stylesheet" type="text/css">
    <link href="{{ url('limitless')}}/assets/css/colors.css" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->

    {{ HTML::style('font-awesome/css/font-awesome.css') }}
    {{ HTML::style('css/blueimp-gallery.min.css') }}

    <!-- Core JS files -->
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/loaders/pace.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/core/libraries/jquery.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/core/libraries/bootstrap.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/loaders/blockui.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/ui/nicescroll.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/ui/drilldown.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/ui/fab.min.js"></script>
    <!-- /core JS files -->


    <script src="{{ url('makeadmin')}}/assets/global/plugins/jquery-ui/jquery-ui-1.11.2.min.js"></script>

    <!-- moment.js (date library) -->
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/ui/moment/moment.min.js"></script>


    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/notifications/jgrowl.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/pickers/daterangepicker.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/pickers/anytime.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/pickers/pickadate/picker.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/pickers/pickadate/picker.date.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/pickers/pickadate/picker.time.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/pickers/pickadate/legacy.js"></script>

    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/pickers/color/spectrum.js"></script>



    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/selects/select2.min.js"></script>

    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>

    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/notifications/pnotify.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>

    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/pages/form_bootstrap_select.js"></script>

    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/pages/form_multiselect.js"></script>

    <!-- /core JS files -->

    @yield('page_js')

    <script type="text/javascript">
      var base = '{{ url('/') }}/';
    </script>

    @include('layouts.modaljs')
    @include('layouts.js')


    <style type="text/css">
        .img-circle{
            border-radius: 50%;
        }

        .avatar{
            width: 58px;
            height: 58px;
        }

        .small-help{
            font-size: 0.95em;
            display: block;
            text-align: right;
        }

        a.btn, input.btn.btn-raised.btn-primary{
            margin-top: 16px !important;
        }

        label{
            margin-top: 8px !important;
            margin-bottom: 0px !important;
            font-weight: bold !important;
        }

        .sp-replacer{
            display: block !important;
        }

    </style>


</head>

<body class="navbar-bottom">

    <!-- Page header -->
    <div class="page-header page-header-inverse {{ getenv('NAV_PRIMARY_COLOR') }}">

        <!-- Main navbar -->
        <div class="navbar navbar-inverse navbar-transparent">
            <div class="navbar-header">
                <a class="navbar-brand" href="{{ url('/')}}" style="text-transform:uppercase">{{ Config::get('site.name')}}</a>

                <ul class="nav navbar-nav pull-right visible-xs-block">
                    <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-grid3"></i></a></li>
                </ul>
            </div>

            <div class="navbar-collapse collapse" id="navbar-mobile">

                <ul class="nav navbar-nav">
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-inbox-alt"></i>
                            <span class="visible-xs-inline-block position-right">New Order</span>
                            <span class="badge bg-warning-400">9</span>
                        </a>
                    </li>
                </ul>

                <div class="navbar-right">
                    <ul class="nav navbar-nav">


                        <li class="dropdown dropdown-user">
                            <a class="dropdown-toggle" data-toggle="dropdown">
                                <img class="img-circle" src="{{ ( isset(Auth::user()->avatar) )?Auth::user()->avatar:'' }}" alt="">
                                <span>{{ ( isset(Auth::user()->name)) ?Auth::user()->name:'' }}</span>
                                <i class="caret"></i>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a href="{{ url('profile')}}"><i class="icon-user-plus"></i> My profile</a></li>
                                <li class="divider"></li>
                                <li><a href="{{ url('profile')}}"><i class="icon-cog5"></i> Account settings</a></li>
                                <li><a href="{{ url('/logout')}}"><i class="icon-switch2"></i> Logout</a></li>
                            </ul>

                        </li>

                    </ul>
                </div>


            </div>


        </div>
        <!-- /main navbar -->


        <!-- Page header content -->
        <div class="page-header-content">
            <div class="page-title">
                <h4>{{ $title }}</h4>
            </div>

            <div class="heading-elements">
                {!! $crumb->render() !!}
            </div>
        </div>
        <!-- /page header content -->



        @include('partials.lsecondnav')

        @include('partials.fab')


    </div>
    <!-- /page header -->

    <!-- Page container -->
    <div class="page-container">

        <!-- Page content -->
        <div class="page-content">

            <!-- Main content -->
            <div class="content-wrapper">

                <div class="panel panel-flat ">

                    <div class="panel-body form-panel">
                        @if ($errors->has())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        {!! Former::open_for_files_vertical($submit,'POST',array('class'=>'')) !!}
                        <div class="row">
                            <div class="col-xs-12 col-md-6 col-6">
                              @yield('left')
                            </div>
                            <div class="col-xs-12 col-md-6 col-6">
                              @yield('right')
                            </div>
                        </div>
                        {!! Former::close() !!}

                    </div>

                </div>

            </div>
            <!-- /main content -->

        </div>
        <!-- /page content -->

    </div>
    <!-- /page container -->

    @yield('modal')

    <!-- Footer -->
    {{--
    <div class="navbar navbar-default navbar-fixed-bottom footer">
        <ul class="nav navbar-nav visible-xs-block">
            <li><a class="text-center collapsed" data-toggle="collapse" data-target="#footer"><i class="icon-circle-up2"></i></a></li>
        </ul>

        <div class="navbar-collapse collapse" id="footer">
            <div class="navbar-text">
                &copy; 2015. <a href="#" class="navbar-link">Limitless Web App Kit</a> by <a href="http://themeforest.net/user/Kopyov" class="navbar-link" target="_blank">Eugene Kopyov</a>
            </div>

            <div class="navbar-right">
                <ul class="nav navbar-nav">
                    <li><a href="#">About</a></li>
                    <li><a href="#">Terms</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
        </div>
    </div>

    --}}
    <!-- /footer -->

</body>
</html>
