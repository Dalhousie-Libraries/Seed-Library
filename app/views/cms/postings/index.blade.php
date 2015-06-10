@extends('admin/layouts/default')

@section('title')
{{{ $title }}} :: @parent
@stop

@section('content')
    <div class="page-header">
        <h3>
            {{{ $title }}}

            <div class="pull-right">
                <a href="{{{ URL::to('cms/postings/create') }}}" class="btn btn-small btn-info iframe"><span class="glyphicon glyphicon-plus-sign"></span> Create</a>
            </div>
        </h3>
    </div>
    <table id="items" class="table table-striped table-hover">
        <thead>
            <tr>
        	<th class="col-md-2">Title</th>
		<th class="col-md-2">Author</th>
		<th class="col-md-2">Created at</th>
		<th class="col-md-2">Modified at</th>
                <th class="col-md-2">Published</th>
                <th class="col-md-2">Actions</th>
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
            oTable = $('#items').dataTable( {
                    "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
                    "sPaginationType": "bootstrap",
                    "oLanguage": {
                            "sLengthMenu": "_MENU_ records per page"
                    },
                    "bProcessing": true,
                    "bServerSide": true,
                    "sAjaxSource": "{{ URL::to('cms/postings/data')}}",
                    "fnDrawCallback": function ( oSettings ) {
                            $(".iframe").colorbox({iframe:true, width:"80%", height:"80%", 
                                onClosed: function() {
                                    oTable.fnReloadAjax();
                                },
                            });
                            
                            // Publish button behaviour
                            $('.publish_btn').click(function(event) {
                                var id = $(event.target).attr("data-id");
                                
                                // Change state via AJAX
                                $.get("{{ URL::to('cms/postings')}}/" + id + '/publish', function(data) {
                                    // Analyse results
                                    if (data.success)
                                        oTable.fnReloadAjax();
                                    else
                                        alert(data.message);
                                }).fail(function() {
                                    alert('Posting could not be (un)published.');
                                });
                            });
                    }
            });
    });
    
    // Delete button behaviour
    $('#confirmDelete').find('.modal-footer #confirm').on('click', function(){
        var link    = $(this).data('form');
        var element = $(this).data('source');

        // Try to delete image via AJAX
        $.get(link, function(data) {
            // Analyse results
            if (data.success)
                oTable.fnReloadAjax();
            else
                alert(data.message);            
        }).fail(function() {
            alert('Posting could not be deleted.'); // or whatever
        });
    });
</script>
@stop