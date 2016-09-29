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

	<!-- Core JS files -->
	<script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/loaders/pace.min.js"></script>
	<script type="text/javascript" src="{{ url('limitless')}}/assets/js/core/libraries/jquery.min.js"></script>
	<script type="text/javascript" src="{{ url('limitless')}}/assets/js/core/libraries/bootstrap.min.js"></script>
	<script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/loaders/blockui.min.js"></script>
	<script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/ui/nicescroll.min.js"></script>
	<script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/ui/drilldown.js"></script>
	<script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/ui/fab.min.js"></script>
	<!-- /core JS files -->


	<!-- Theme JS files -->
	<script type="text/javascript" src="{{ url('limitless')}}/assets/js/core/app.js"></script>

	<script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/ui/ripple.min.js"></script>

    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
    <script type="text/javascript" src="{{ url('limitless')}}/assets/js/pages/form_bootstrap_select.js"></script>

	<!-- /theme JS files -->

</head>

<body class="navbar-bottom login-container">

	<!-- Main navbar -->
	<div class="navbar navbar-inverse bg-indigo has-shadow">
		<div class="navbar-header">
			<a class="navbar-brand" href="index.html">{{ config('site.name') }}</a>

			<ul class="nav navbar-nav pull-right visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-grid3"></i></a></li>
			</ul>
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">

			<div class="navbar-right">
				<ul class="nav navbar-nav navbar-right">
                    <li><a href="{{url('member/register')}}">Register Member</a></li>
                    <li><a href="{{url('creditor/register')}}">Register Creditor</a></li>
                    <li><a href="#">Help Center</a></li>
				</ul>
			</div>
		</div>
	</div>
	<!-- /main navbar -->


	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Simple login form -->

            @yield('content')

				<!-- /simple login form -->

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

	</div>
	<!-- /page container -->


	<!-- Footer -->
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
	<!-- /footer -->

</body>
</html>
