@extends('site/layouts/default')

<script>
    @if(isset($donation) && !empty($donation->checked_in_date))
        alert('You only have permission to edit pending requests.');
        history.go(-1);
    @endif
</script>

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

@section('content')
    <h1 class="page-header">{{$title}}</h1>
    <div class="content">
        {{-- Edit Donation Form --}}
        <!-- Form beginning -->
        <form class="form-horizontal" method="post" 
              action="@if (isset($donation)){{ URL::to('donations/' . $donation->id) . '/edit'}}@else # @endif" >
            <!-- CSRF Token -->
            <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
            <!-- ./ csrf token -->                

            <!-- Name -->
            <div class="form-group {{{ $errors->has('seed_name') ? 'has-error' : '' }}}">
                <div class="col-md-12">
                    <label class="control-label" for="seed_name">Seed</label>
                    <input class="form-control" type="text" name="seed_name" id="seed_name" value="{{{ Input::old('seed_name', 
                                // Leaves field blank for new records
                                !isset($donation) ? null : // For existing records: get fullname of item if item is set;
                                                           // Otherwise, get temporary name from description (looks messy but works...)
                                (!is_null($donation->item) ? $donation->item->getFullname() : (isset(explode('*###*', $donation->description)[0]) ? explode('*###*', $donation->description)[0] : null))) }}}" 
                           placeholder="Enter seed name" />
                    @if(Session::has('invalidItem'))                    
                    <input type="checkbox" name="new_seed" @if(isset($donation) && is_null($donation->item)) checked @endif /> 
                    <span class="text-info">The seed I'm trying to donate is not being listed.</span>
                    @endif
                </div>
            </div>
            <!-- ./ seed name -->
             <!-- Seed initial inventory -->
            <div class="form-group {{{ $errors->has('amount') ? 'has-error' : '' }}}">
                <div class="col-md-12">
                    <label class="control-label" for="amount">Amount (grams)</label>
                    <input class="form-control" type="number" name="amount" id="amount" value="{{{ Input::old('amount', isset($donation) ? $donation->amount : null) }}}" min="1" placeholder="Ex: 100" />
                </div>
            </div>
            <!-- ./ seed initial inventory -->                    
            <!-- Seed description -->
            <div class="form-group {{{ $errors->has('description') ? 'has-error' : '' }}}">
                <div class="col-md-12">
                    <label id="lbl_description" class="control-label" for="description" data-toggle="tooltip" 
                           data-placement="right" title="Please use this field to provide further detail about your seed. (e.g Germination ratio, grow location)">Description</label>
                    <textarea class="form-control" name="description" id="description">{{{ Input::old('description', 
                        // Leaves field blank for new records
                        !isset($donation) ? null : // For existing records: get description;
                                                   // Otherwise, removes temp name from description (looks messy but works...)
                        (!is_null($donation->item) ? $donation->description : (isset(explode('*###*', $donation->description)[1]) ? explode('*###*', $donation->description)[1] : null))) }}}</textarea>
                </div>
            </div>
            <!-- ./ seed description -->

            <!-- Form Actions -->
            <div class="form-group">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="reset" class="btn btn-default">Reset</button>
                </div>
            </div>
            <!-- ./ form actions -->
        </form>
    </div>
@stop

@section('scripts')
<script type="text/javascript">
    // Operations that are performed when page finishes loading
    $(document).ready(function(){
        // Family auto complete
        $('#seed_name').typeahead({
            name: 'SeedName',
            remote: "{{ URL::to('item/findByName/%QUERY')}}"
        });
        
        // ---- Add tooltips to fields ----
        var firstTime = true;
        $('#lbl_description').tooltip();
        // Show on focus
        $('#description').focus(function() {
            if (firstTime) {
                $('#lbl_description').tooltip('show');

                // Hides it after a few seconds
                setTimeout(function() {
                    $('#lbl_description').tooltip('hide');
                }, 5000);

                firstTime = false;
            }
        });
        // Hide on blur
        $('#description').blur(function() {
            $('#lbl_description').tooltip('hide');
        });
    });
</script>
@stop