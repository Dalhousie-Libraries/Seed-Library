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
                <a href="{{{ URL::to('admin/returns/create') }}}" class="btn btn-small btn-info iframe"><span class="glyphicon glyphicon-plus-sign"></span> Create</a>
            </div>
        </h3>
    </div>
    <table id="returns" class="table table-striped table-hover">
        <thead>
            <tr>
                <th class="col-md-3">{{{ Lang::get('Seed') }}}</th>
		<th class="col-md-2">{{{ Lang::get('Returner') }}}</th>
                <th class="col-md-1">{{{ Lang::get('Amount (grams)') }}}</th>
                <th class="col-md-2">{{{ Lang::get('Return Date') }}}</th>
                <th class="col-md-1">{{{ Lang::get('Actions') }}}</th>
            </tr>
        </thead>
	<tbody>
	</tbody>
    </table>
@stop

@section('scripts')
    <script type="text/javascript">
        var oTable;
        $(document).ready(function() {
                oTable = $('#returns').dataTable( {
                        "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                        "sPaginationType": "bootstrap",
                        "oLanguage": {
                                "sLengthMenu": "_MENU_ records per page"
                        },
                        "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": "{{ URL::to('admin/returns/data/returned') }}",
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