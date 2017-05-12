<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Editor Feedy - CMS</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css?v=1.0') }}">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <link rel="stylesheet" href="{{ asset('dist/css/AdminLTE.min.css') }}">
        <link rel="stylesheet" href="{{ asset('dist/css/bootstrap-datetimepicker.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/iCheck/square/blue.css') }}">
        <link rel="stylesheet" href="{{ asset('dist/css/skins/skin-blue.min.css') }}">
        <link rel="stylesheet" href="{{ asset('dist/css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/sweetalert/sweetalert.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datepicker/datepicker3.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/timepicker/bootstrap-timepicker.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('dist/css/tag.css') }}">
        <link rel="stylesheet" href="{{ asset('css/live.css') }}">
        <link rel="stylesheet" href="{{ asset('dist/css/jquery-ui.css') }}"  type="text/css"/>


        <link rel="stylesheet" href="http://blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
        <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
        <link rel="stylesheet" href="{{ asset('css/jquery.fileupload.css') }}">
        <link rel="stylesheet" href="{{ asset('css/jquery.fileupload-ui.css') }}">
        <!-- CSS adjustments for browsers with JavaScript disabled -->


        <noscript><link rel="stylesheet" href="{{ asset('css/jquery.fileupload-noscript.css') }}"></noscript>
        <noscript><link rel="stylesheet" href="{{ asset('css/jquery.fileupload-ui-noscript.css') }}"></noscript>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        @yield('custom_header')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div id="append_script"></div>
    </head>