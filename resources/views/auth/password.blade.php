@extends(Templeiro::loadRelativeView('layouts.master'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="card card-default">
                    <div class="box-header panel-heading card-header">Reset Password</div>
                    <div class="box-body panel-body card-body">
                        {!! Form::open(array('url' => url('password/email'), 'method' => 'post', 'files'=> true)) !!}
                        <div class="form-group  {{ $errors->has('email') ? 'has-error' : '' }}">
                            {!! Form::label('email', "E-Mail Address", array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::text('email', null, array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('email', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Send Password Reset Link
                                </button>
                            </div>
                        </div>
                        {!! FOrm::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
