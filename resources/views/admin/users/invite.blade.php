@include('pedreiro::partials.errors')

@if (session('message'))
    <div class="">
        {{ session('message') }}
    </div>
@endif

<h1>User: Invite</h1>

<form method="POST" action="/admin/users/invite">
    {!! csrf_field() !!}

    <div class="">
        @input_maker_label('Email')
        @input_maker_create('email', ['type' => 'string'])
    </div>

    <div class="">
        @input_maker_label('Name')
        @input_maker_create('name', ['type' => 'string'])
    </div>

    <div class="">
        @input_maker_label('Role')
        @input_maker_create('roles', ['type' => 'relationship', 'model' => 'Porteiro\Models\Role', 'label' => 'label', 'value' => 'name'])
    </div>

    <div class="">
        <a href="{{ URL::previous() }}">{{ __('pedreiro::generic.cancel') }}</a>
        <button type="submit">Save</button>
    </div>
</form>

<a href="/root/users">User Admin</a>