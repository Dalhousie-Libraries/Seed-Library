<!DOCTYPE html>
<html lang="en" @yield('angular-app')>
<head id="Starter-Site">
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
    <link rel="stylesheet" href="{{asset('assets/css/wysihtml5/bootstrap-wysihtml5.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/datatables-bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/colorbox.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/typeahead-custom.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/custom/style.css')}}">
	<link rel="stylesheet" href="{{asset('assets/css/datepicker.css')}}">
	
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
    <div class="container">
        <!-- Notifications -->
        <div class="notifications">
            @yield('notifications')
        </div>
        <!-- ./ Notifications -->
        
        <div class="page-header">
            <h3>
                {{ $title }}
                <div id="back_btn" class="pull-right" style="display: none">
                    <button class="btn btn-default btn-small btn-inverse" id="btn_back_modal" onclick="goBack()"><span class="glyphicon glyphicon-circle-arrow-left"></span> Back</button>
                </div>
            </h3>
        </div>
        
        <!-- Content -->
        <div class="content">
            @yield('content')
        </div>
        <!-- ./ Content -->
    </div>
    
    <!-- Javascripts -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
    <script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
    <script src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
    <script src="{{asset('assets/js/datatables-bootstrap.js')}}"></script>
    <script src="{{asset('assets/js/datatables.fnReloadAjax.js')}}"></script>
    <script src="{{asset('assets/js/jquery.colorbox.js')}}"></script>
    <script src="{{asset('assets/js/jquery.cookie.js')}}"></script>
    <script src="{{asset('assets/js/prettify.js')}}"></script>
    <script src="{{asset('assets/js/typeahead.min.js')}}"></script>
	<script src="{{asset('assets/js/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('assets/js/custom/util.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.close_popup').click(function(event){
                //parent.oTable.fnReloadAjax();
                parent.jQuery.fn.colorbox.close();
                event.preventDefault();
                return false;
            });
            $('#deleteForm').submit(function(event) {
                var form = $(this);
                $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize()
                }).done(function() {
                    parent.jQuery.colorbox.close();
                    parent.oTable.fnReloadAjax();
                }).fail(function() {
                });
                event.preventDefault();
            });
            
            // Check if 'Back' button is needed
            var url = document.URL;
            var iframe = $(parent.document).find('iframe');
            if(iframe.attr('src')) // If inside iframe
            {
                var urlH = iframe.attr('src') + '#';
                if(iframe.attr('src') !== url && urlH !== url)
                    $('#back_btn').show();
            } else
            { // If not inside a frame
                if(history.length > 1)
                    $('#back_btn').show();
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
