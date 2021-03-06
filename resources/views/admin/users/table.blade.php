<table class="table table-striped" id="users-table">
    <thead>
        <th>{!! trans('words.name') !!}</th>
        <th>{!! trans('words.email') !!}</th>
        <th>{!! trans('words.admin') !!}</th>
        <th colspan="3">{!! trans('words.action') !!}</th>
    </thead>
    <tbody>
        @if (!empty($users))
            @foreach($users as $user)
                <tr>
                    <td>{!! $user->name !!}</td>
                    <td>{!! $user->email !!}</td>
                    <td>{!! $user->admin !!}</td>
                    <td>
                        <?php
                        try {
                            ?>
                        {!! Form::open(['route' => ['admin.porteiro.users.destroy', $user->id], 'method' => 'delete']) !!}
                        <div class='btn-group'>
                            <a href="{!! route('master.porteiro.users.show', [$user->id]) !!}" class='btn btn-secondary btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                            <a href="{!! route('master.porteiro.users.edit', [$user->id]) !!}" class='btn btn-secondary btn-xs'><i class="fa fa-edit"></i> Edit</a>
                            {!! Form::button('<i class="fa fa-trash"></i> Delete', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('".trans('phrases.areYouSure')."')"]) !!}
                        </div>
                        {!! Form::close() !!}
                        <?php
                    } catch (\Throwable $th) {
                        //throw $th;
                    } 
                    ?>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>