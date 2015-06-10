@extends('site/layouts/default')

@section('title')
{{$title}}
@stop

@section('notifications')
    <!-- Saving error -->
    @if ( Session::has('error') )
        <div class="alert alert-danger alert-block">
            <p>{{Session::get('error')}}</p>
        </div>
    @endif
    
    <!-- Form errors -->
    @if ( $errors->count() > 0 )
        <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4>Error</h4>
            <p>The following errors have occurred:</p>
            <ul>
                @foreach( $errors->all() as $message )
                  <li>{{ $message }}</li>
                @endforeach
              </ul>
        </div>
    @endif
@stop

@section('content')
    <div class="page-header">
        <h3>{{ $title }}</h3>
    </div>

    {{Form::open(array('url' => URL::to('getpass'), 'class' => 'form-horizontal'))}}
        <div class="tab-content">
            <div class="form-group">
                <div class="col-md-12">
                    {{Form::label('email', 'Email:', array('class' => 'control-label'))}}
                    {{Form::text('email', '', array('class' => 'form-control', 'placeholder' => 'Enter your e-mail address'))}}
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    {{Form::submit('Create password', array('id' => 'btn_create', 'class' => 'btn btn-success'))}}
                </div>
            </div>
        </div>        
    {{Form::close()}}
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#email').focus();
    });
</script>
@stop