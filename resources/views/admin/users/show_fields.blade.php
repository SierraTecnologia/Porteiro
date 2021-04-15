<!-- Id Field -->
<div class="form-group">
    {!! Form::label('id', trans('words.id').':') !!}
    <p>{!! $user->id !!}</p>
</div>

<!-- Updated At Field -->
<div class="form-group">
    {!! Form::label('updated_at', trans('words.updatedAt').':') !!}
    <p>{!! $user->updated_at !!}</p>
</div>

<!-- Created At Field -->
<div class="form-group">
    {!! Form::label('created_at', trans('words.createdAt').':') !!}
    <p>{!! $user->created_at !!}</p>
</div>

<!-- Deleted At Field -->
<div class="form-group">
    {!! Form::label('deleted_at', trans('words.deletedAt').':') !!}
    <p>{!! $user->deleted_at !!}</p>
</div>

<!-- Name Field -->
<div class="form-group">
    {!! Form::label('name', trans('words.name').':') !!}
    <p>{!! $user->name !!}</p>
</div>

<!-- Clients Id Field -->
<div class="form-group">
    {!! Form::label('clients_id', trans('words.client').':') !!}
    <p>{!! $user->clients_id !!}</p>
</div>

<!-- Dominios Id Field -->
<div class="form-group">
    {!! Form::label('dominios_id', trans('words.dominio').':') !!}
    <p>{!! $user->dominios_id !!}</p>
</div>

<!-- User Category Id Field -->
<div class="form-group">
    {!! Form::label('user_category_id', trans('dashboard.user.userCategoryId').':') !!}
    <p>{!! $user->user_category_id !!}</p>
</div>

