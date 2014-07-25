@extends('admin/layouts/default')

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
@stop

@section('content')
    <div class="page-header">
        <h3>
            {{{ $title }}}
            
            <div class="pull-right">
                <span class="small">Actions</span>
                <a id="btn_select_all" href="#" class="btn btn-small btn-default">Select all</a>
                <a class="btn btn-small btn-danger delete" href="#" data-toggle="modal" data-target="#confirmDelete" data-link="{{ URL::to('packet/delete') }}"
                   data-title="Delete selected packets" data-message="Are you sure you want to cancel all the selected requests? (You cannot undo this action)."><span class="glyphicon glyphicon-remove-circle"></span> Delete selected</a>
                <a href="{{{ URL::to('admin/packets/lend') }}}" class="btn btn-small btn-info iframe"><span class="glyphicon glyphicon-plus-sign"></span> Checkout selected</a>
            </div>
        </h3>
    </div>
    <table id="requests" class="table table-striped table-hover">
        <thead>
            <tr>
                <th class="col-md-1">{{{ Lang::get('Packet #') }}}</th>
                <th class="col-md-3">{{{ Lang::get('Seed') }}}</th>
		<th class="col-md-2">{{{ Lang::get('Borrower') }}}</th>                
                <th class="col-md-2">{{{ Lang::get('Request Date') }}}</th>
                <th class="col-md-2">{{{ Lang::get('Check out') }}}</th>
            </tr>
        </thead>
	<tbody>
	</tbody>
    </table>
@stop

@section('scripts')
@include('admin/layouts/delete')
<script type="text/javascript">
    // Delete button behaviour
    $('#confirmDelete').find('.modal-footer #confirm').on('click', function(){
        var link    = $(this).data('form');
        var element = $(this).data('source');

        // Check if delete button was for a single item or for all selected packets
        if (link === "{{URL::to('packet/delete')}}")
        {   // Delete all selected packets
            var checkboxes = {};
            var i = 0;
            $('#requests :input[type="checkbox"]').each(function (index, value) { 
                // Adds to array if checked
                if (value.checked)
                    checkboxes[i++] =  value.value;
            });
            
            // Try to delete selected packets via AJAX
            if (i > 0) 
            {   // Do a post request with parameters
                $.post(link, {
                    packets: checkboxes
                }).done(function(data) {
                    // Check result for each packet
                    var errors = "";
                    for(var ii = 0; ii < data.length; ii++)
                    {
                        if(!data[ii].success)
                            errors += data[ii].message + '\n';
                    }
                    
                    // Show result to packet
                    if(errors === "") { // No errors occurred
                        alert('All selected requests were successfully deleted.');
                        oTable.fnReloadAjax();
                    } else
                        alert('The following errors occurred:\n\n' + errors);
                }).fail(function() {
                    alert('Requests could not be deleted.');
                });
            }
        } else
        {   // Delete a single packet
            // Try to delete packet via AJAX
            $.get(link, function(data) {
                // Analyse results
                if (data.success)
                    oTable.fnReloadAjax();
                else
                    alert(data.message);
            }).fail(function() {
                alert('Request could not be deleted.'); // or whatever
            });
        }
    });
    
    // Select all button behaviour
    $('#btn_select_all').click(function(event) {
        // Checks all checkboxes
        if ($(this).text() === "Select all") {
            $('#requests :input[type="checkbox"]').each(function () { this.checked = true; });
            $(this).text('Deselect all');
        } else 
        { // Uncheck all checkboxes
            $('#requests :input[type="checkbox"]').each(function () { this.checked = false; });
            $(this).text('Select all');
        }        
    });
</script>
<script type="text/javascript">
    var oTable;
    $(document).ready(function() {
            oTable = $('#requests').dataTable( {
                    "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                    "sPaginationType": "bootstrap",
                    "oLanguage": {
                            "sLengthMenu": "_MENU_ records per page"
                    },
                    "bProcessing": true,
                    "bServerSide": true,
                    "sAjaxSource": "{{ URL::to('admin/packets/data/requested') }}",
                    "fnDrawCallback": function ( oSettings ) {
                        $(".iframe").colorbox({iframe:true, width:"80%", height:"80%", 
                                        onClosed: function() {
                                            oTable.fnReloadAjax();
                                        },
                        });
                    }
            });
    });
</script>
@stop