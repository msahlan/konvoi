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


    {{ HTML::script('js/spectrum.js')}}


    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/selects/select2.min.js"></script>

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
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="icon-bell2"></i>
                                <span class="visible-xs-inline-block position-right">Activity</span>
                                <span class="status-mark border-pink-300"></span>
                            </a>

                            <div class="dropdown-menu dropdown-content">
                                <div class="dropdown-content-heading">
                                    Activity
                                    <ul class="icons-list">
                                        <li><a href="#"><i class="icon-menu7"></i></a></li>
                                    </ul>
                                </div>

                                <ul class="media-list dropdown-content-body width-350">
                                    <li class="media">
                                        <div class="media-left">
                                            <a href="#" class="btn bg-success-400 btn-rounded btn-icon btn-xs"><i class="icon-mention"></i></a>
                                        </div>

                                        <div class="media-body">
                                            <a href="#">Taylor Swift</a> mentioned you in a post "Angular JS. Tips and tricks"
                                            <div class="media-annotation">4 minutes ago</div>
                                        </div>
                                    </li>

                                    <li class="media">
                                        <div class="media-left">
                                            <a href="#" class="btn bg-pink-400 btn-rounded btn-icon btn-xs"><i class="icon-paperplane"></i></a>
                                        </div>

                                        <div class="media-body">
                                            Special offers have been sent to subscribed users by <a href="#">Donna Gordon</a>
                                            <div class="media-annotation">36 minutes ago</div>
                                        </div>
                                    </li>

                                    <li class="media">
                                        <div class="media-left">
                                            <a href="#" class="btn bg-blue btn-rounded btn-icon btn-xs"><i class="icon-plus3"></i></a>
                                        </div>

                                        <div class="media-body">
                                            <a href="#">Chris Arney</a> created a new <span class="text-semibold">Design</span> branch in <span class="text-semibold">Limitless</span> repository
                                            <div class="media-annotation">2 hours ago</div>
                                        </div>
                                    </li>

                                    <li class="media">
                                        <div class="media-left">
                                            <a href="#" class="btn bg-purple-300 btn-rounded btn-icon btn-xs"><i class="icon-truck"></i></a>
                                        </div>

                                        <div class="media-body">
                                            Shipping cost to the Netherlands has been reduced, database updated
                                            <div class="media-annotation">Feb 8, 11:30</div>
                                        </div>
                                    </li>

                                    <li class="media">
                                        <div class="media-left">
                                            <a href="#" class="btn bg-warning-400 btn-rounded btn-icon btn-xs"><i class="icon-bubble8"></i></a>
                                        </div>

                                        <div class="media-body">
                                            New review received on <a href="#">Server side integration</a> services
                                            <div class="media-annotation">Feb 2, 10:20</div>
                                        </div>
                                    </li>

                                    <li class="media">
                                        <div class="media-left">
                                            <a href="#" class="btn bg-teal-400 btn-rounded btn-icon btn-xs"><i class="icon-spinner11"></i></a>
                                        </div>

                                        <div class="media-body">
                                            <strong>January, 2016</strong> - 1320 new users, 3284 orders, $49,390 revenue
                                            <div class="media-annotation">Feb 1, 05:46</div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="icon-bubble8"></i>
                                <span class="visible-xs-inline-block position-right">Messages</span>
                            </a>

                            <div class="dropdown-menu dropdown-content width-350">
                                <div class="dropdown-content-heading">
                                    Messages
                                    <ul class="icons-list">
                                        <li><a href="#"><i class="icon-menu7"></i></a></li>
                                    </ul>
                                </div>

                                <ul class="media-list dropdown-content-body">
                                    <li class="media">
                                        <div class="media-left">
                                            <img src="{{ url('limitless')}}/assets/images/placeholder.jpg" class="img-circle img-sm" alt="">
                                            <span class="badge bg-danger-400 media-badge">5</span>
                                        </div>

                                        <div class="media-body">
                                            <a href="#" class="media-heading">
                                                <span class="text-semibold">James Alexander</span>
                                                <span class="media-annotation pull-right">04:58</span>
                                            </a>

                                            <span class="text-muted">who knows, maybe that would be the best thing for me...</span>
                                        </div>
                                    </li>

                                    <li class="media">
                                        <div class="media-left">
                                            <img src="{{ url('limitless')}}/assets/images/placeholder.jpg" class="img-circle img-sm" alt="">
                                            <span class="badge bg-danger-400 media-badge">4</span>
                                        </div>

                                        <div class="media-body">
                                            <a href="#" class="media-heading">
                                                <span class="text-semibold">Margo Baker</span>
                                                <span class="media-annotation pull-right">12:16</span>
                                            </a>

                                            <span class="text-muted">That was something he was unable to do because...</span>
                                        </div>
                                    </li>

                                    <li class="media">
                                        <div class="media-left"><img src="{{ url('limitless')}}/assets/images/placeholder.jpg" class="img-circle img-sm" alt=""></div>
                                        <div class="media-body">
                                            <a href="#" class="media-heading">
                                                <span class="text-semibold">Jeremy Victorino</span>
                                                <span class="media-annotation pull-right">22:48</span>
                                            </a>

                                            <span class="text-muted">But that would be extremely strained and suspicious...</span>
                                        </div>
                                    </li>

                                    <li class="media">
                                        <div class="media-left"><img src="{{ url('limitless')}}/assets/images/placeholder.jpg" class="img-circle img-sm" alt=""></div>
                                        <div class="media-body">
                                            <a href="#" class="media-heading">
                                                <span class="text-semibold">Beatrix Diaz</span>
                                                <span class="media-annotation pull-right">Tue</span>
                                            </a>

                                            <span class="text-muted">What a strenuous career it is that I've chosen...</span>
                                        </div>
                                    </li>

                                    <li class="media">
                                        <div class="media-left"><img src="{{ url('limitless')}}/assets/images/placeholder.jpg" class="img-circle img-sm" alt=""></div>
                                        <div class="media-body">
                                            <a href="#" class="media-heading">
                                                <span class="text-semibold">Richard Vango</span>
                                                <span class="media-annotation pull-right">Mon</span>
                                            </a>

                                            <span class="text-muted">Other travelling salesmen live a life of luxury...</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>


                        <li class="dropdown dropdown-user">
                            <a class="dropdown-toggle" data-toggle="dropdown">
                                <img class="img-circle" src="{{ ( isset(Auth::user()->avatar) )?Auth::user()->avatar:'' }}" alt="">
                                <span>{{ ( isset(Auth::user()->fullname)) ?Auth::user()->fullname:'' }}</span>
                                <i class="caret"></i>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a href="{{ url('profile')}}"><i class="icon-user-plus"></i> My profile</a></li>
                                <li class="divider"></li>
                                <li><a href="{{ url('profile')}}"><i class="icon-cog5"></i> Account settings</a></li>
                                <li><a href="{{ url('logout')}}"><i class="icon-switch2"></i> Logout</a></li>
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
                        {!! Former::open_for_files_vertical($submit,'POST',array('class'=>'container')) !!}
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                              @yield('left')
                            </div>
                            <div class="col-xs-12 col-md-6">
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
