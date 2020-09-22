@extends('layouts.app')
@section('content')
<section class="content">
	<div class="box card box-primary">
		<div class="box-header card-header">
			<h3>Edit Permission</h3>
		</div>
		<div class="box-body card-body">
			<form action="{{url('admin/permissions/update')}}" method = "post">
				{!! csrf_field() !!}
				<input type="hidden" name = "permission_id" value = "{{$permission->id}}">
				<div class="form-group">
				<label for="">permission</label>
					<input type="text" name = "name" class = "form-control" placeholder = "Name" value = "{{$permission->name}}">
				</div>
				<div class="box-footer card-footer">
					<button class = 'btn btn-primary' type = "submit">Update</button>
				</div>
			</form>
		</div>
	</div>
</section>
@endsection
