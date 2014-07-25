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

@section('content')
        <!-- Tabs -->
        <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-general" data-toggle="tab">General</a></li>
                @if (isset($donation) && ($donation->checked_in_date)) <li><a href="#tab-packets" data-toggle="tab">Packets</a></li> @endif
        </ul>
	<!-- ./ tabs -->
        <!-- Tabs General Info -->
        <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
                {{-- Edit Donation Form --}}
                <!-- Form beginning -->
                <form class="form-horizontal" method="post" autocomplete="off" 
                      action="@if (isset($donation)){{ URL::to('admin/donations/' . $donation->id) . '/edit'}}@endif" >
                    <!-- CSRF Token -->
                    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                    <!-- ./ csrf token -->
                     <!-- Name -->
                    <div class="form-group {{{ $errors->has('seed_name') ? 'has-error' : '' }}}">
                        <div class="col-md-12">
                            <label class="control-label" for="seed_name">Seed</label>
                            <input class="form-control" type="text" name="seed_name" id="seed_name" value="{{{ Input::old('seed_name', !isset($donation) || is_null($donation->item) ? null : $donation->item->getFullname()) }}}" 
                                   placeholder="Enter seed name" />
                            <a href="{{URL::to("admin/items/create")}}" class="pull-right"><span class="glyphicon glyphicon-plus"></span> New seed</a>
                        </div>
                    </div>
                    <!-- ./ seed name -->
                    <!-- Donor -->
                    <div class="form-group {{{ $errors->has('donor') ? 'has-error' : '' }}}">
                        <div class="col-md-12">
                            <label class="control-label" for="donor">Donor</label>
                            <input class="form-control" type="text" name="donor" id="donor" value="{{{ Input::old('donor', !isset($donation) || is_null($donation->user) ? null : $donation->user->name) }}}" 
                                   placeholder="Enter donor's name" />
                            <a href="{{URL::to("admin/users/create")}}" class="pull-right"><span class="glyphicon glyphicon-plus"></span> New user</a>
                        </div>
                    </div>
                    <!-- ./ donor -->
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
                                   data-placement="right" title="Use this field to provide further detail about donated seed. (e.g Germination ratio, grow location)">Description</label>
                            <textarea class="form-control" name="description" id="description">{{{ Input::old('description', 
                                        // Leave it blank if new donation
                                        !isset($donation) ? null : // Otherwise, if item is not set, tries to make user aware of suggested seed name
                                                                   // by including it in the description. (looks awful, though...)
                                        (!is_null($donation->item) ? $donation->description : 'Seed name: ' . (isset(explode('*###*', $donation->description)[0]) ? explode('*###*', $donation->description)[0] : null) . ' 
' . (isset(explode('*###*', $donation->description)[1]) ? explode('*###*', $donation->description)[1] : null) )) }}}</textarea>
                        </div>
                    </div>
                    <!-- ./ seed description -->
                    <!-- Form Actions -->
                    <div class="form-group">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success">Update</button>
                            <button type="reset" class="btn btn-default">Reset</button>
                            <button class="btn btn-cancel close_popup">Cancel</button>
                        </div>
                    </div>
                    <!-- ./ form actions -->
                </form>
            </div>
            <!-- ./ general tab -->
            
            @if (isset($donation) && ($donation->checked_in_date))
            <!-- Packets tab -->
            <div class="tab-pane" id="tab-packets">
                <div class="pull-right">
                    <a href="#" id="add_packet" class="btn btn-small btn-info"><span class="glyphicon glyphicon-plus-sign"></span> Add packet</a>
                </div>
                <br/>
                <div id="packets_container">
                @foreach($donation->packets as $packet)
                    <!-- Packet structure -->
                    <form method="POST" action="{{{ URL::to('admin/packets/' . $packet->id . '/edit/donation')}}}">
                        <br/><br/>
                        <fieldset>
                            <legend>Packet #{{$packet->id}}</legend>
                            <a class="text-info pull-right" href="{{URL::to('admin/packets/' . $packet->id . '/history')}}">
                                <span class="glyphicon glyphicon-search"></span> See history
                            </a>
                            <input type='hidden' name='accession_id' value="{{{ $donation->id}}}" id='accession_id' />
                            <div class='form-group{{{ $errors->has('pct_amount') && Session::get('old_packet_error') == $packet->id ? 'has-error' : '' }}}'>
                                <div class='col-md-12'>
                                    <label for='pct_amount'>Amount (grams)</label>
                                    <input class='form-control' type='number' name='pct_amount' value="{{{ $packet->amount}}}" id='pct_amount' min='1'  placeholder='Ex: 100' step='any' />
                                </div>
                            </div>
                            <div class='form-group {{{ $errors->has('date_harvest') && Session::get('old_packet_error') == $packet->id ? 'has-error' : '' }}}'>
                                <div class='col-md-12'>
                                    <label for='date_harvest'>Harvest Date</label>
                                    <input class='form-control ' type='text' name='date_harvest' value="{{{ $packet->date_harvest}}}" id='date_harvest' placeholder='Ex: 2014-06-26'/>
                                </div>
                            </div>
                            <div class='form-group {{{ $errors->has('grow_location') && Session::get('old_packet_error') == $packet->id ? 'has-error' : '' }}}'>
                                <div class='col-md-12'>
                                    <label for='grow_location'>Grow Location</label>
                                    <input class='form-control' type='text' name='grow_location' value="{{{ $packet->grow_location}}}" id='grow_location' placeholder='Grow Location' />
                                </div>
                            </div>
                            <div class='form-group {{{ $errors->has('physical_location') && Session::get('old_packet_error') == $packet->id ? 'has-error' : '' }}}'>
                                <div class='col-md-12'>
                                    <label for='physical_location'>Physical Location</label>
                                    <input class='form-control' type='text' name='physical_location' value="{{{ $packet->physical_location}}}" id='physical_location' placeholder='Physical Location' />
                                </div>
                            </div>
                            <div class='form-group {{{ $errors->has('germination_ratio') && Session::get('old_packet_error') == $packet->id ? 'has-error' : '' }}}'>
                                <div class='col-md-12'>
                                    <label for='germination_ratio'>Germination Ratio</label>
                                    <input class='form-control' type='number' name='germination_ratio' value="{{{ round($packet->germination_ratio, 2)}}}" id='germination_ratio' min='1' max='100' step='any' placeholder='Germination Ratio' />
                                </div>
                            </div>
                        </fieldset>
                        <br/>
                        <!-- Form Actions -->
                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success">Update</button>
                                <button type="reset" class="btn btn-default">Reset</button>
                            </div>
                        </div>
                        <!-- ./ form actions -->
                    </form>
                    <br/>
                    <!-- ./ packet structure -->
                @endforeach
                </div>
            </div>
            <!-- ./ packets tab -->
            @endif            
        </div>
        <!-- ./ tabs content -->
@stop

@section('scripts')
<script type="text/javascript">
    @if (isset($donation))
    // Packet id for the next packet
    //var packetId = 0;
    // Packet HTML structure
    var packetStruct = 
    "<form method='POST' action='{{{ URL::to('admin/packets/create/donation')}}}'>" +
    "<br/><br/>" +
    "<fieldset><legend>New Packet</legend>" +
    "<input type='hidden' name='accession_id_new' value='{{{ $donation->id}}}' id='accession_id_new' />" +
    "<div class='form-group {{{ $errors->has('pct_amount_new') ? 'has-error' : '' }}}'><div class='col-md-12'>" +
        "<label for='pct_amount_new'>Amount (grams)</label>" +
        "<input class='form-control' type='number' name='pct_amount_new' id='pct_amount_new' min='1' placeholder='Ex: 100' value='{{{ Input::old('pct_amount_new') }}}'/></div>" +
    "</div>" +
    "<div class='form-group {{{ $errors->has('date_harvest_new') ? 'has-error' : '' }}}'><div class='col-md-12'>" +
        "<label for='date_harvest_new'>Harvest Date</label>" +
        "<input class='form-control ' type='text' name='date_harvest_new' id='date_harvest_new' placeholder='Ex: 2014-06-26' value='{{{ Input::old('date_harvest_new') }}}'/>" +
    "</div></div>" +
    "<div class='form-group {{{ $errors->has('grow_location_new') ? 'has-error' : '' }}}'><div class='col-md-12'>" +
        "<label for='grow_location_new'>Grow Location</label>" +
        "<input class='form-control' type='text' name='grow_location_new' id='grow_location_new' placeholder='Grow Location'  value='{{{ Input::old('grow_location_new') }}}'/>" +
    "</div></div>" +
    "<div class='form-group {{{ $errors->has('physical_location_new') ? 'has-error' : '' }}}'><div class='col-md-12'>" +
        "<label for='physical_location_new'>Physical Location</label>" +
        "<input class='form-control' type='text' name='physical_location_new' id='physical_location_new' placeholder='Physical Location'  value='{{{ Input::old('physical_location_new') }}}'/>" +
    "</div></div>" +
    "<div class='form-group {{{ $errors->has('germination_ratio_new') ? 'has-error' : '' }}}'><div class='col-md-12'>" +
        "<label for='germination_ratio_new'>Germination Ratio</label>" +
        "<input class='form-control' type='number' name='germination_ratio_new' id='germination_ratio_new' min='0' max='100' step='any' placeholder='Ex: 94.5' value='{{{ round(Input::old('germination_ratio_new'), 2) ? round(Input::old('germination_ratio_new'), 2) : null }}}'/></div>" +
    "</div>" +
    "</fieldset><br/><!-- Form Actions -->" +
    "<div class='form-group'><div class='col-md-12'>" +
        "<button type='submit' class='btn btn-success'>Update</button> " +
        "<button type='reset' class='btn btn-default'>Reset</button> " +
        "<button class='btn btn-cancel cancel_packet'>Cancel</button> " +
    "</div></div><!-- ./ form actions --></form>";

    // Add a new packet when add packet button is clicked
    $('#add_packet').click(function() {
        // Show new packet
        $('#packets_container').prepend(packetStruct);
        // Disable add packet button (allows only one new packet at a time)
        $('#add_packet').attr("disabled", "disabled");
        
        // Focus on first field
        $("#pct_amount_new").focus();
       
        // Handles new packet creation cancel
        $('.cancel_packet').click(function(){
            // Disable add packet button (allows only one new packet at a time)
            $('#add_packet').removeAttr('disabled');
            // Deletes form
            $('#packets_container').find('form').first().remove();
            // Prevents submit function
            return false;
        });
    });
    @endif
    
    // Operations that are performed when page finishes loading
    $(document).ready(function(){
        // Auto complete
        $('#donor').typeahead({
            name: 'Donors',
            remote: "{{ URL::to('admin/users/donors/%QUERY')}}"
        });

        // Family auto complete
        $('#seed_name').typeahead({
            name: 'SeedName',
            remote: "{{ URL::to('item/findByName/%QUERY')}}"
        });
        
        // Create form if there were erros in the packet
        @if (Session::has('new_packet_error'))
            $('#add_packet').click();
        @endif
        
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