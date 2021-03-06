@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="float-left">{!! trans('words.users') !!}</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> {!! trans('words.home') !!}</a></li>
            <li class="active">{!! trans('words.users') !!}</li>
        </ol>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('pedreiro::partials.message')

        <div class="clearfix"></div>

        <div class="box panel card box-primary panel-primary card-primary">
            <div class="btn-group">
                <h1 class="float-right">
                @if (Route::has('master.porteiro.users.create'))
                    <a class="btn btn-primary float-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('master.porteiro.users.create') !!}">{!! trans('words.addNew') !!}</a>
                @else
                    <a class="btn btn-primary float-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('admin.porteiro.users.create') !!}">{!! trans('words.addNew') !!}</a>
                @endif
                </h1>
            </div>
            <div class="box-body panel-body card-body">
                    @include('porteiro::admin.users.table')
            </div>
        </div>
    </div>
@endsection

