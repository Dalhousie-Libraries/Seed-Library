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
    {{-- Lend packet Form --}}
    <form id="checkinForm" class="form-horizontal" method="post" action="@if (isset($packet)){{ URL::to('admin/packets/' . $packet->id . '/lend') }}@endif" autocomplete="off">
        
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <input type="hidden" name="id" value="{{ $packet->id }}" />
        <!-- ./ csrf token -->
        
         <!-- Borrower -->
        <div class="form-group {{{ $errors->has('borrower') ? 'has-error' : '' }}}">
            <div class="col-md-12">
                <label class="control-label" for="borrower">Borrower</label>
                <input class="form-control" type="text" name="borrower" id="borrower" value="{{{ Input::old('borrower', !isset($packet) || is_null($packet->borrower) ? null : $packet->borrower->name) }}}" />
            </div>
        </div>
        <!-- ./ borrower -->

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls">
                <button id="btn_lend" type="submit" class="btn btn-danger">Lend</button>
                <button class="btn btn-cancel close_popup">Cancel</button>
            </div>
        </div>
        <!-- ./ form actions -->
    </form>
    @endif
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        // Auto complete
        $('#borrower').typeahead({
            name: 'Users',
            remote: "{{ URL::to('admin/users/borrowers/%QUERY')}}"
        });
    })
    
    // Clears cart to prevent logic problems
    $('#btn_lend').click(function(event) {
        $.removeCookie('packets', { path: '/' });
    });
</script>
@stop