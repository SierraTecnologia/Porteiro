@extends('layouts.app')
@section('content')
<section class="content">
	<div class="box card box-primary">
		<div class="box-header card-header">
			<h3>Create new Permission</h3>
		</div>
		<div class="box-body card-body">
			<form action="{{url('admin/permissions/store')}}" method = "post">
				{!! csrf_field() !!}
				<div class="form-group">
				<label for="">Permission</label>
					<input type="text" name = "name" class = "form-control" placeholder = "Name">
				</div>
				<div class="box-footer card-footer">
					<button class = 'btn btn-primary' type = "submit">Create</button>
				</div>
			</form>
		</div>
	</div>
</section>
@endsection
