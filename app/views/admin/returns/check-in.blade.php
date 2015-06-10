@extends('admin/layouts/modal')

@section('title')
{{{ $title }}} :: @parent
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

{{-- Content --}}
@section('content')
    @if ( !Session::has('success') )
    {{-- Check in return Form --}}
    <form id="checkinForm" class="form-horizontal" method="post" action="@if (isset($return)){{ URL::to('admin/returns/' . $return->id . '/check_in') }}@endif" autocomplete="off">
        
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <input type="hidden" name="id" value="{{ $return->id }}" />
        <!-- ./ csrf token -->
        
         <!-- Borrower -->
        <div class="form-group {{{ $errors->has('returner') ? 'has-error' : '' }}}">
            <div class="col-md-12">
                <label class="control-label" for="returner">Returner</label>
                <input class="form-control" type="text" name="returner" id="returner" value="{{{ Input::old('returner', !isset($return) || is_null($return->user) ? null : $return->user->name) }}}" readonly="readonly" />
            </div>
        </div>
        <!-- ./ returner -->

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls">
                <button id="btn_checkin" type="submit" class="btn btn-danger">Check in</button>
                <button class="btn btn-cancel close_popup">Cancel</button>
            </div>
        </div>
        <!-- ./ form actions -->
    </form>
    @endif
@stop