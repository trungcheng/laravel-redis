@extends('layouts.master')

@section('main_content')
    <section class="content-header">
        <h1>
            403 Error Page
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">403 error</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="error-page">
            <h2 class="headline text-yellow"> 403</h2>
            <div class="error-content">
                <h3><i class="fa fa-warning text-yellow"></i> {{ trans('home.403_error') }}</h3>
                <p>
                    {{trans('home.403_msg')}} <a
                            href="/home">{{ trans('home.back_dashboard') }}</a> {{ trans('home.try_search') }}
                </p>
                <form class="search-form">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search">
                        <div class="input-group-btn">
                            <button type="submit" name="submit" class="btn btn-warning btn-flat"><i
                                        class="fa fa-search"></i></button>
                        </div>
                    </div><!-- /.input-group -->
                </form>
            </div><!-- /.error-content -->
        </div><!-- /.error-page -->
    </section><!-- /.content -->
@stop