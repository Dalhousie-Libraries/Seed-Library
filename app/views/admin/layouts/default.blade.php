<!DOCTYPE html>
<html lang="en" ng-app="lendingSeed">
<head id="Seed-Lending">
    <meta charset="UTF-8">

    <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>
            @section('title')
                    Seed Lending Library
            @show
    </title>

    <meta name="keywords" content="@yield('keywords')" />
    <meta name="author" content="@yield('author')" />
    <!-- Google will often use this as its description of your page/site. Make it good. -->
    <meta name="description" content="@yield('description')" />

    <!-- Speaking of Google, don't forget to set your site up: http://google.com/webmasters -->
    <meta name="google-site-verification" content="">

    <!-- Dublin Core Metadata : http://dublincore.org/ -->
    <meta name="DC.title" content="Seed Lending Library">
    <meta name="DC.subject" content="@yield('description')">
    <meta name="DC.creator" content="@yield('author')">

    <!--  Mobile Viewport Fix -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <!-- This is the traditional favicon.
     - size: 16x16 or 32x32
     - transparency is OK
     - see wikipedia for info on browser support: http://mky.be/favicon/ -->
    <link rel="shortcut icon" href="{{{ asset('assets/ico/favicon.png') }}}">

    <!-- iOS favicons. -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{{ asset('assets/ico/apple-touch-icon-144-precomposed.png') }}}">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{{ asset('assets/ico/apple-touch-icon-114-precomposed.png') }}}">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{{ asset('assets/ico/apple-touch-icon-72-precomposed.png') }}}">
    <link rel="apple-touch-icon-precomposed" href="{{{ asset('assets/ico/apple-touch-icon-57-precomposed.png') }}}">

	<!-- CSS -->
    <!--<link rel="stylesheet" href="{{asset('http://netdna.bootstrapcdn.com/bootstrap/2.3.2/css/bootstrap.min.css')}}">-->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap-theme.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/datatables-bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/colorbox.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/typeahead-custom.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/custom/style.css')}}">
    
    <!-- Fonts -->
    <link href="http://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css" rel="stylesheet">

    <style>
    body {
            padding: 60px 0;
    }
    </style>

    @yield('styles')

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

</head>
<body>
    <div class="container" ng-controller="main">
        <!-- Navbar -->
        <div class="navbar navbar-default navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse navbar-ex1-collapse">
                    <ul class="nav navbar-nav">
                        <li{{ (Request::is('/') ? ' class="active"' : '') }}><a href="{{{ URL::to('/') }}}"><span class="glyphicon glyphicon-home"></span> Home page</a></li>
                        <li class="{{ (Request::is('cms*') ? ' active' : '') }}"><a href="{{{ URL::to('cms/postings') }}}"><span class="glyphicon glyphicon-"></span> CMS</a></li>
                        <li{{ (Request::is('admin/items*') ? ' class="active"' : '') }}><a href="{{{ URL::to('admin/items') }}}"><span class="glyphicon glyphicon-"></span> Items</a></li>
                        <li{{ (Request::is('admin/users*') ? ' class="active"' : '') }}><a href="{{{ URL::to('admin/users') }}}"><span class="glyphicon glyphicon-"></span> Users</a></li>
                        <li class="dropdown {{ (Request::is('admin/donations*') ? ' active' : '') }}">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Donations <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="{{{ URL::to('admin/donations') }}}"><span class="glyphicon glyphicon-"></span> Checked in</a></li>
                                <li><a href="{{{ URL::to('admin/donations/requests') }}}"><span class="glyphicon glyphicon-"></span> Requested</a></li>
                            </ul>
                        </li>
                        <li class="dropdown {{ (Request::is('admin/packets*') ? ' active' : '') }}">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Check Out <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="{{{ URL::to('admin/packets/lend') }}}"><span class="glyphicon glyphicon-"></span> Lend</a></li>
                                <li class="divider"></li>
                                <li><a href="{{{ URL::to('admin/packets') }}}"><span class="glyphicon glyphicon-"></span> Checked out</a></li>
                                <li><a href="{{{ URL::to('admin/packets/requests') }}}"><span class="glyphicon glyphicon-"></span> Requested</a></li>
                            </ul>
                        </li>
                        <li class="dropdown {{ (Request::is('admin/returns*') ? ' active' : '') }}">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Check In <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="{{{ URL::to('admin/returns') }}}"><span class="glyphicon glyphicon-"></span> Checked in</a></li>
                                <li><a href="{{{ URL::to('admin/returns/requests') }}}"><span class="glyphicon glyphicon-"></span> Pending Returns</a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav pull-right">
                        <!--<li class="divider-vertical"></li>-->
                        <li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('admin') }}}"><span class="glyphicon glyphicon-"></span> Admin panel</a></li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <span class="glyphicon glyphicon-user"></span> {{{ Auth::user()->name }}}	<span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="{{{ URL::to('admin') }}}"><span class="glyphicon glyphicon-"></span> Admin panel</a></li>
                                <li><a href="{{{ URL::to('user/profile') }}}"><span class="glyphicon glyphicon-"></span> Profile</a></li>
                                <li><a href="{{{ URL::to('user/requests') }}}"><span class="glyphicon glyphicon-"></span> My requests</a></li>
                                <li class="divider"></li>
                                <li><a href="{{{ URL::to('logout') }}}" ng-controller="cart" ng-click="clearCart()"><span class="glyphicon glyphicon-share"></span> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- ./ navbar -->
        
        <!-- Notifications -->
        <div class="notifications">
            @yield('notifications')
        </div>
        <!-- ./ Notifications -->
        
        <!-- Content -->
        <div class="content">
            @yield('content')
        </div>
        <!-- ./ Content -->
    </div>
    <!-- Footer -->
    <footer class="clearfix">
        <br/>
        <p class="text-center">
            <span class="glyphicon glyphicon-copyright-mark"></span> Seed Lending Library
        </p>
        @yield('footer')
    </footer>
    <!-- ./ Footer -->
    
    <!-- Javascripts -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.16/angular.min.js"></script>
    <script src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
    <script src="{{asset('assets/js/angularjs/ng-table.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/js/datatables-bootstrap.js')}}"></script>
    <script src="{{asset('assets/js/datatables.fnReloadAjax.js')}}"></script>
    <script src="{{asset('assets/js/jquery.colorbox.js')}}"></script>
    <script src="{{asset('assets/js/jquery.cookie.js')}}"></script>
    <script src="{{asset('assets/js/prettify.js')}}"></script>
    <script src="{{asset('assets/js/typeahead.min.js')}}"></script>
    <script src="{{asset('assets/js/custom/main-app.js')}}"></script>
    <script src="{{asset('assets/js/custom/util.js')}}"></script>
    <script src="{{asset('assets/js/custom/mod-cart.js')}}"></script>
    <script src="{{asset('assets/js/custom/services.js')}}"></script>
    @yield('scripts')
</body>
</html>
