@extends('site/layouts/modal')

@section('title')
Login
@stop

{{-- Notifications --}}
@section('notifications')
    <!-- Saving success -->
    @if ( Session::has('success') )
        <div class="alert alert-success alert-block">
            <p>{{Session::get('success')}}</p>
        </div>
    @endif

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
    {{Form::open(array('id' => 'login_form', 'url' => URL::to('login/true'), 'class' => 'form-horizontal', 'target' => '_top'))}}
        <div class="tab-content">
            <div class="form-group">
                <div class="col-md-12">
                    {{Form::label('email', 'Email:', array('class' => 'control-label'))}}
                    {{Form::text('email', '', array('class' => 'form-control', 'placeholder' => 'Enter your e-mail'))}}
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    {{Form::label('password', 'Password:', array('class' => 'control-label'))}}
                    {{Form::password('password', array('class' => 'form-control', 'placeholder' => 'Enter your password'))}}
                </div>
            </div>    

            <div class="form-group">
                <div class="col-md-12">
                    {{Form::submit('Login', array('id' => 'login', 'class' => 'btn btn-success'))}}
                </div>
            </div>            
        </div>        
    {{Form::close()}}
    <p>Not registered yet? <a href="{{URL::to('signup')}}" target="_top">Sign up now!</a> 
       If you're already registered but don't have a password <a href="{{URL::to('getpass')}}" target="_top"> click here</a> to retrieve it.
    </p>
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#email').focus();
    });
    
    // Makes login check work inside an iframe
    $('#login').click(function(event) {
        // Prevents form submit
        //event.preventDefault ? event.preventDefault() : event.returnValue = false;
        event.preventDefault();
        event = $.event.fix(event);
        
        $.post( "{{URL::to('login/true')}}", {
            email: $('#email').val(),
            password: $('#password').val(),
        }).done(function(data) {
            if (data.search('login') >= 0)
            {
                document.open();
                document.write(data);
                document.close();
            } else {
                $('#login_form').submit();
            }
        });
    });
</script>
@stop