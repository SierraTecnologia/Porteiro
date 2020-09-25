@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            {!! trans('words.user') !!}
        </h1>
    </section>
    <div class="content">
        <div class="box panel card box-primary panel-primary card-primary">
            <div class="box-body panel-body card-body">
                <div class="row" style="padding-left: 20px">
                    @include('facilitador::admin.users.show_fields')
                    <a href="{!! route('admin.users.index') !!}" class="btn btn-secondary">{!! trans('words.back') !!}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
