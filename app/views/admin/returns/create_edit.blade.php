@extends('admin/layouts/modal')

@section('title')
{{{ $title }}} :: @parent
@stop

@section('styles')
<link rel="stylesheet" href="{{asset("assets/css/custom/tables.css")}}" />
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
                @if (isset($return) && isset($return->checked_in_date)) <li><a href="#tab-packets" data-toggle="tab">Packets</a></li> @endif
        </ul>
	<!-- ./ tabs -->       
        
        <!-- Tabs General Info -->
        <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
                {{-- Edit Return Form --}}
                <!-- Form beginning -->
                <form class="form-horizontal" method="post" autocomplete="off" 
                      action="@if (isset($return)){{ URL::to('admin/returns/' . $return->id) . '/edit'}}@endif" >
                    <!-- CSRF Token -->
                    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                    <!-- ./ csrf token -->
                    
                    <!-- Returner -->
                    <div class="form-group {{{ $errors->has('returner') ? 'has-error' : '' }}}">
                        <div class="col-md-12">
                            <label class="control-label" for="returner">Returner</label>
                            <input class="form-control" type="text" name="returner" id="returner" value="{{{ Input::old('returner', !isset($return) || is_null($return->user) ? null : $return->user->name) }}}" tabindex="1" placeholder="Enter a name to search for packets" />
                        </div>
                    </div>
                    <!-- ./ returner -->

					<!-- Date -->
					<div class="form-group {{{ $errors->has('date') ? 'has-error' : '' }}}">
						<div class="col-md-12">
							<label class="control-label" for="return_date">Return Date</label>
							<input class="form-control span2" type="text" name="return_date" id="return_date" value="{{Carbon::now()->format('Y-m-d');}}" />
						</div>
					</div>
					<!-- ./ date -->					
                    
                    <div class="form-group {{{ $errors->has('amount') ? 'has-error' : '' }}}">
                        <div class='col-md-12'>
                            <label for='amount'>Amount (grams)</label>
                            <input class='form-control' type='number' name='amount' value="{{{ Input::old('amount', !isset($return) ? 1 : $return->amount) }}}" id='amount' min='1' tabindex="2" placeholder='Ex: 100' />
                        </div>
                    </div>
                    
                    <!-- Seed description -->
                    <div class="form-group {{{ $errors->has('description') ? 'has-error' : '' }}}">
                        <div class="col-md-12">
                            <label id="lbl_description" class="control-label" for="description" data-toggle="tooltip" 
                           data-placement="right" title="Please use this field to provide further detail about your seed. (e.g Germination ratio, grow location)">Description</label>
                            <textarea class="form-control" tabindex="3" name="description" id="description">{{{ Input::old('description', isset($return) ? $return->description : null) }}}</textarea>
                        </div>
                    </div>
                    <!-- ./ seed description -->
                    
                    <input class="btn btn-info pull-right" tabindex="4" id="search_btn" type="submit" value="Search">
                    
                    <div class="inner-table" @if (!isset($return)) style="display: none" @endif>
                        <!-- Parent packet -->
                        <div class="form-group {{{ $errors->has('parent_packet') ? 'has-error' : '' }}}">
                            <div class="col-md-12">
                                <label class="control-label" for="parent_packet">Parent packet</label>
                                <input readonly="readonly" class="form-control" type="number" name="parent_packet" id="parent_packet" value="" />
                            </div>
                        </div>
                        <!-- ./ parent packet -->
                        
                        <label class="control-label" for="candidate_packets">Candidate packets</label>
                        <table id="candidate_packets" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="col-md-1">{{{ Lang::get('Packet #') }}}</th>
                                    <th class="col-md-3">{{{ Lang::get('Seed') }}}</th>
                                    <th class="col-md-1">{{{ Lang::get('Amount (grams)') }}}</th>
                                    <th class="col-md-1">{{{ Lang::get('Germ. Ratio %') }}}</th>
                                    <th class="col-md-1">{{{ Lang::get('Harvest Date') }}}</th>
                                    <th class="col-md-1">{{{ Lang::get('Grow Location') }}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>                    
                    <br/>

                    <!-- Form Actions -->
                    <div class="form-group">
                        <div class="col-md-12">
                            <button id="update_btn" type="submit" class="btn btn-success disabled">Update</button>
                            <button type="reset" class="btn btn-default">Reset</button>
                            <button class="btn btn-cancel close_popup">Cancel</button>
                        </div>
                    </div>
                    <!-- ./ form actions -->
                </form>
            </div>
            <!-- ./ general tab -->
            
            @if (isset($return) && isset($return->checked_in_date))
            <!-- Packets tab -->            
            <div class="tab-pane" id="tab-packets">
                <div class="pull-right">
                    <a href="#" id="add_packet" class="btn btn-small btn-info"><span class="glyphicon glyphicon-plus-sign"></span> Add packet</a>
                </div>
                <br/>
                <div id="packets_container">
                @foreach($return->packets as $packet)
                    <!-- Packet structure -->                    
                    <form method="POST" action="{{{ URL::to('admin/packets/' . $packet->id . '/edit/return')}}}">
                        <br/><br/>
                        <fieldset>
                            <legend>Packet #{{$packet->id}}</legend>
                            <a class="text-info pull-right" href="{{URL::to('admin/packets/' . $packet->id . '/history')}}">
                                <span class="glyphicon glyphicon-search"></span> See history
                            </a>
                            <input type='hidden' name='accession_id' value="{{{ $return->id}}}" id='accession_id' />
                            <div class='form-group{{{ $errors->has('pct_amount') && Session::get('old_packet_error') == $packet->id ? 'has-error' : '' }}}'>
                                <div class='col-md-12'>
                                    <label for='pct_amount'>Amount (grams)</label>
                                    <input class='form-control' type='number' name='pct_amount' value="{{{ $packet->amount}}}" id='pct_amount' min='1'  placeholder='Ex: 100' />
                                </div>
                            </div>
                            <div class='form-group {{{ $errors->has('date_harvest') && Session::get('old_packet_error') == $packet->id ? 'has-error' : '' }}}'>
                                <div class='col-md-12'>
                                    <label for='date_harvest'>Harvest Date</label>
                                    <input class='form-control ' type='text' name='date_harvest' value="{{{ $packet->date_harvest}}}" id='date_harvest' placeholder='Ex: 2014-06-07'/>
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
                                    <input class='form-control' type='number' name='germination_ratio' value="{{{ round($packet->germination_ratio, 2)}}}" id='germination_ratio' min='0' max='100'placeholder='Ex: 94.5' />
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
    @if (isset($return))
    // Packet id for the next packet
    //var packetId = 0;
    // Packet HTML structure
    var packetStruct = 
    "<form method='POST' action='{{{ URL::to('admin/packets/create/return')}}}'>" +
    "<br/><br/>" +
    "<fieldset><legend>New Packet</legend>" +
    "<input type='hidden' name='accession_id_new' value='{{{ $return->id}}}' id='accession_id_new' />" +
    "<div class='form-group {{{ $errors->has('pct_amount_new') ? 'has-error' : '' }}}'><div class='col-md-12'>" +
        "<label for='pct_amount_new'>Amount (grams)</label>" +
        "<input class='form-control' type='number' name='pct_amount_new' id='pct_amount_new' min='1' placeholder='Ex: 100' value='{{{ Input::old('pct_amount_new') }}}'/></div>" +
    "</div>" +
    "<div class='form-group {{{ $errors->has('date_harvest_new') ? 'has-error' : '' }}}'><div class='col-md-12'>" +
        "<label for='date_harvest_new'>Harvest Date</label>" +
        "<input class='form-control ' type='text' name='date_harvest_new' id='date_harvest_new' placeholder='Ex: 2014-06-07' value='{{{ Input::old('date_harvest_new') }}}'/>" +
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
        "<input class='form-control' type='number' name='germination_ratio_new' id='germination_ratio_new' min='1' max='100' step='any' placeholder='Ex: 94.5' value='{{{ round(Input::old('germination_ratio_new'), 2) ? round(Input::old('germination_ratio_new'), 2) : null }}}'/></div>" +
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
    var oTable;
    $(document).ready(function(){
        // Auto complete
        $('#returner').typeahead({
            name: 'Returners',
            remote: "{{ URL::to('admin/users/borrowers/%QUERY')}}"
        });
        
        // Sets up datatables 
        oTable = $('#candidate_packets').dataTable( {
                "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                        "sLengthMenu": "_MENU_ records per page"
                },
                "bProcessing": true,
                "bServerSide": true,
                @if (isset($return))
                   "sAjaxSource": "{{ URL::to('admin/returns/userPackets/' . $return->user->name) }}",
                @else
                   "sAjaxSource": "{{ URL::to('admin/returns/userPackets/0') }}",
                @endif
                "fnDrawCallback": function ( oSettings ) {
                        $(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
                },
                "fnInitComplete": function ( oSettings ) {
                    // Populates parent packet field if in EDIT MODE
                    @if (isset($return))
                        $('#candidate_packets td:first-child').each(function() {
                            if ($(this).text() == {{$return->parent_id}})
                                $(this).click();
                        });
                    @endif
                }
        });
        
        // Listener for search button
        $('#search_btn').click(function(event) {
            // Updates table with user borrowed packets
            if ($('#returner').val().length > 0) {
                // Reloads data table
                oTable.fnReloadAjax("{{ URL::to('admin/returns/userPackets') }}" + "/" + $('#returner').val());
                // Displays table
                $('.inner-table').css('display', 'block');
                
                // Cleans parent packet field to avoid inconsistency
                $('#parent_packet').val('');
                // Disable submit button
                $('#update_btn').addClass('disabled');
            }
        
            event.preventDefault();
        });
        
        // Adds selection behaviour
        $('#candidate_packets').on( 'click', 'tr:not(:first)', function () {
            // Change style of selected table row to make user aware of it
            oTable.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
            
            // Change value of seed field (read-only)
            var packet_id = $('tr.selected').find('td:first').html();
            if (!isNaN(packet_id)) {
                // Update parent packet field
                $('#parent_packet').val(packet_id);
                // Enable submit button
                $('#update_btn').removeClass('disabled');
            }
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
	
	// Date Picker js
	if (top.location != location) {
    top.location.href = document.location.href ;
  }
		$(function(){
			window.prettyPrint && prettyPrint();
			$('#return_date').datepicker({
				format: 'yyyy-mm-dd'
			});
		});
		// END Date Picker js	
</script>
@stop