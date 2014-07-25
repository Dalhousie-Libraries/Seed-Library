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
                <a class="btn btn-small btn-danger delete" href="#" data-toggle="modal" data-target="#confirmDelete" data-link="{{ URL::to('admin/users/delete') }}"
                   data-title="Delete selected users" data-message="Are you sure you want to delete all the selected users?"><span class="glyphicon glyphicon-remove-circle"></span> Delete selected</a>
                <a href="{{{ URL::to('admin/users/create') }}}" class="btn btn-small btn-info iframe"><span class="glyphicon glyphicon-plus-sign"></span> Create</a>
            </div>
        </h3>
    </div>
    <table id="users" class="table table-striped table-hover">
        <thead>
            <tr>
                <th class="col-sm-1"></th>
        	<th class="col-md-2">{{{ Lang::get('Name') }}}</th>
		<th class="col-md-2">{{{ Lang::get('Email') }}}</th>
		<th class="col-md-2">{{{ Lang::get('Gardening Exp.') }}}</th>
		<th class="col-md-2">{{{ Lang::get('Seed Saving Exp.') }}}</th>
                <th class="col-md-2">{{{ Lang::get('Actions') }}}</th>
            </tr>
        </thead>
	<tbody>
	</tbody>
    </table>
@stop

@section('scripts')
@include('admin/layouts/delete')
<script type="text/javascript">
    var oTable;
    $(document).ready(function() {
        oTable = $('#users').dataTable( {
            "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
            "sPaginationType": "bootstrap",
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "{{ URL::to('admin/users/data') }}",
            "fnDrawCallback": function ( oSettings ) {
                $(".iframe").colorbox({iframe:true, width:"80%", height:"80%", 
                    onClosed: function() {
                        oTable.fnReloadAjax();
                    },
                });
            },
            "aoColumns": [
                { "sClass": "text-center" }, null, null, null, null, null
            ]
        });
    });

    // Delete button behaviour
    $('#confirmDelete').find('.modal-footer #confirm').on('click', function(){
        var link    = $(this).data('form');
        var element = $(this).data('source');

        // Check if delete button was for a single item or for all selected users
        if (link === "{{URL::to('admin/users/delete')}}")
        {   // Delete all selected users
            var checkboxes = {};
            var i = 0;
            $('#users :input[type="checkbox"]').each(function (index, value) { 
                // Adds to array if checked
                if (value.checked)
                    checkboxes[i++] =  value.value;
            });
            
            // Try to delete selected users via AJAX
            if (i > 0) 
            {   // Do a post request with parameters
                $.post(link, {
                    users: checkboxes
                }).done(function(data) {
                    // Check result for each user
                    var errors = "";
                    for(var ii = 0; ii < data.length; ii++)
                    {
                        if(!data[ii].success)
                            errors += data[ii].message + '\n';
                    }
                    
                    // Show result to user
                    if(errors === "") { // No errors occurred
                        alert('All selected users were successfully deleted.');
                        oTable.fnReloadAjax();
                    } else
                        alert('The following errors occurred:\n\n' + errors);
                }).fail(function() {
                    alert('Users could not be deleted.');
                });
            }
        } else
        {   // Delete a single user
            // Try to delete user via AJAX
            $.get(link, function(data) {
                // Analyse results
                if (data.success)
                    oTable.fnReloadAjax();
                else
                    alert(data.message);
            }).fail(function() {
                alert('User could not be deleted.'); // or whatever
            });
        }
    });
    
    // Select all button behaviour
    $('#btn_select_all').click(function(event) {
        // Checks all checkboxes
        if ($(this).text() === "Select all") {
            $('#users :input[type="checkbox"]').each(function () { this.checked = true; });
            $(this).text('Deselect all');
        } else 
        { // Uncheck all checkboxes
            $('#users :input[type="checkbox"]').each(function () { this.checked = false; });
            $(this).text('Select all');
        }        
    });
</script>
@stop