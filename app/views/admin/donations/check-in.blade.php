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
    {{-- Check in donation Form --}}
    <form id="checkinForm" class="form-horizontal" method="post" action="@if (isset($donation)){{ URL::to('admin/donations/' . $donation->id . '/check_in') }}@endif" autocomplete="off">
        
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <input type="hidden" name="id" value="{{ $donation->id }}" />
        <!-- ./ csrf token -->
        
         <!-- Borrower -->
        <div class="form-group {{{ $errors->has('donor') ? 'has-error' : '' }}}">
            <div class="col-md-12">
                <label class="control-label" for="donor">Donor</label>
                <input class="form-control" type="text" name="donor" id="donor" value="{{{ Input::old('donor', !isset($donation) || is_null($donation->user) ? null : $donation->user->name) }}}" readonly="readonly" />
            </div>
        </div>
        <!-- ./ donor -->

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