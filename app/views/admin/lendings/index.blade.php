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
                <a href="{{{ URL::to('admin/packets/lend') }}}" class="btn btn-small btn-info"><span class="glyphicon glyphicon-plus-sign"></span> Lend Seed</a>
            </div>
        </h3>
    </div>
    <table id="donations" class="table table-striped table-hover">
        <thead>
            <tr>
                <th class="col-md-1">{{{ Lang::get('Packet #') }}}</th>
                <th class="col-md-3">{{{ Lang::get('Seed') }}}</th>
		<th class="col-md-2">{{{ Lang::get('Borrower') }}}</th>
                <th class="col-md-1">{{{ Lang::get('Checked out Date') }}}</th>
                <th class="col-md-1">{{{ Lang::get('Actions') }}}</th>
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
                oTable = $('#donations').dataTable( {
                        "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                        "sPaginationType": "bootstrap",
                        "oLanguage": {
                                "sLengthMenu": "_MENU_ records per page"
                        },
                        "bProcessing": true,
                        "bServerSide": true,
                        "sAjaxSource": "{{ URL::to('admin/packets/data/lent') }}",
                        "fnDrawCallback": function ( oSettings ) {
                            $(".iframe").colorbox({iframe:true, width:"80%", height:"80%",
                                            onClosed: function() {
                                                oTable.fnReloadAjax();
                                            },
                            });
                        }
                });
        });
		
	// Delete button behaviour
    $('#confirmDelete').find('.modal-footer #confirm').on('click', function(){
        var link    = $(this).data('form');
        var element = $(this).data('source');

	    // Delete a single packet
		// Try to delete packet via AJAX
		$.get(link, function(data) {
			// Analyse results
			if (data.success)
				oTable.fnReloadAjax();
			else
				alert(data.message);
		}).fail(function() {
			alert('Checkout could not be undone.'); // or whatever
		});
    });
    </script>
@stop
