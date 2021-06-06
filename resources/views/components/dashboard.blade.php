@extends('layouts.app')
@section('title','Dashboard')
@section('content')
<section class="content-header">
	<h1>
		{!! trans('words.dashboard') !!}
		<small>{!! trans('words.controlPane') !!}</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> {!! trans('words.home') !!}</a></li>
		<li class="active">{!! trans('words.dashboard') !!}</li>
	</ol>
</section>
<section class="content">
	<div class="row">

		@foreach($boxComponents as $boxComponent)
			{!! $boxComponent->render() !!}
		@endforeach
    </div>
</section>
@endsection
