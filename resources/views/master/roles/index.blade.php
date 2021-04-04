@extends('layouts.app')

@section('content')

    @if (session('message'))
        <div class="">
            {{ session('message') }}
        </div>
    @endif

    <h1>{{ _t('Role Admin') }}/h1>

    <a href="/root/roles/create">{{ _t('Create New Role') }}</a>

    <form id="" method="post" action="/root/roles/search">
        {!! csrf_field() !!}
        <input name="search" placeholder="Search">
    </form>

    @if ($roles->count() > 0)
        <table>
            <thead>
                <th>{{ _t('Name') }}</th>
                <th>{{ _t('Label') }}</th>
                <th>{{ _t('Actions') }}</th>
            </thead>
            <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->label }}</td>
                        <td>
                            <a href="{{ url('root/roles/'.$role->id.'/edit') }}"><span class="fa fa-edit"> { _t('Edit') }}</span></a>
                            <form method="post" action="{{ url('root/roles/'.$role->id) }}">
                                {!! csrf_field() !!}
                                {!! method_field('DELETE') !!}
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this role?')"><i class="fa fa-trash"></i> { _t('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Sorry no roles</p>
    @endif

    <a href="/dashboard">Dashboard</a>
@endsection
