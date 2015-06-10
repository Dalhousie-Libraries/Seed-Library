@extends('admin/layouts/default')

@section('title')
{{$title}} :: @parent
@stop

{{-- Notifications --}}
@section('notifications')
@stop

@section('content')
    <div class="page-header">
        <h3>
            {{$title}}
        
            <div class="pull-right">
                <span class="small">Actions</span>
                <a id="btn_select_all" href="#" class="btn btn-small btn-default">Select all</a>
                <a class="btn btn-small btn-danger delete" href="#" data-toggle="modal" data-target="#confirmDelete" data-link="{{ URL::to('admin/packets/delete') }}"
                   data-title="Delete selected packets" data-message="Are you sure you want to delete all the selected packets? (You cannot undo this action)."><span class="glyphicon glyphicon-remove-circle"></span> Delete selected</a>
                <a href="{{{ URL::to('admin/packets/lend') }}}" class="btn btn-small btn-info iframe"><span class="glyphicon glyphicon-plus-sign"></span> Lend selected</a>
            </div>
        </h3>
    </div>
    <!-- Search form -->
    {{Form::open(array('url' => 'packets/{%$id}/lend'))}}
        <div class="form-group">
            <label class="control-label" for="search">Search</label>
            <input class="form-control" type="text" name="search" id="search" placeholder="Enter a seed name to search for packets" />
        </div>
        
        {{Form::submit('Search', array('class' => 'btn btn-info pull-right', 'id' => 'search_btn'))}}
        
    {{Form::close()}}
    <!-- ./ search form -->
    
    <!-- Search results -->
    <div id="search-results">
        <br/>
        <table id="packets" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th class="col-md-1">{{{ Lang::get('Packet #') }}}</th>
                    <th class="col-md-3">{{{ Lang::get('Seed') }}}</th>
                    <th class="col-md-1">{{{ Lang::get('Amount (grams)') }}}</th>
                    <th class="col-md-1">{{{ Lang::get('Germ. Ratio %') }}}</th>
                    <th class="col-md-1">{{{ Lang::get('Harvest Date') }}}</th>
                    <th class="col-md-1">{{{ Lang::get('Grow Location') }}}</th>
                    <th class="col-md-1">{{{ Lang::get('Actions') }}}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <!-- ./ search results -->
@stop

@section('scripts')
@include('admin/layouts/delete')
<script type="text/javascript">
    var oTable;
    $(document).ready(function() {
        oTable = $('#packets').dataTable( {
            "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
            "sPaginationType": "bootstrap",
            "oLanguage": {
                    "sLengthMenu": "_MENU_ records per page"
            },
            "bProcessing": true,
            "bFilter": false,
            "bServerSide": true,
            "sAjaxSource": "{{ URL::to('admin/packets/listByName') }}",
            "fnDrawCallback": function ( oSettings ) {
                $(".iframe").colorbox({
                    iframe:true, width:"50%", height:"50%",
                    onClosed:function(){
                        // reload datatable
                        oTable.fnReloadAjax();
                    }
                });
            },
            "aoColumns": [
                { "sClass": "text-center" }, null, null, null, null, null, { "sClass": "text-center" }
            ]
        });

        // Auto-suggestion field
        $('#search').typeahead({
            remote: {
                url: "{{ URL::to('item/findByName/%QUERY')}}"
                /*replace: function () {
                    var query = "{{ URL::to('admin/items/families')}}" + '/' + $('#search').val();
                    if ($('#filter').val() === 'family') {
                        query = "{{ URL::to('admin/items/families')}}" + '/' + $('#search').val();
                    } else {
                        query = "{{ URL::to('admin/items/varieties')}}" + '/' + $('#search').val();
                    }
                    return query;
                }*/
            },
            limit: 10
        });
        // Corrects style for typeahead search field
        //$('#search').parent().css('display', 'inline');

        // Adds listener to search button
        $('#search_btn').click(function(event) {
            if ($('#search').val().length > 0) {
                oTable.fnReloadAjax("{{ URL::to('admin/packets/listByName') }}" + "/" + $('#search').val());
            }
            event.preventDefault();
        });
    });
        
    // Delete button behaviour
    $('#confirmDelete').find('.modal-footer #confirm').on('click', function(){
        var link    = $(this).data('form');
        var element = $(this).data('source');

        // Check if delete button was for a single item or for all selected packets
        if (link === "{{URL::to('admin/packets/delete')}}")
        {   // Delete all selected packets
            var checkboxes = {};
            var i = 0;
            $('#packets :input[type="checkbox"]').each(function (index, value) { 
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
                        alert('All selected packets were successfully deleted.');
                        oTable.fnReloadAjax();
                    } else
                        alert('The following errors occurred:\n\n' + errors);
                }).fail(function() {
                    alert('Packets could not be deleted.');
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
                alert('Packet could not be deleted.'); // or whatever
            });
        }
    });
    
    // Select all button behaviour
    $('#btn_select_all').click(function(event) {
        // Checks all checkboxes
        if ($(this).text() === "Select all") {
            $('#packets :input[type="checkbox"]').each(function () { this.checked = true; });
            $(this).text('Deselect all');
        } else 
        { // Uncheck all checkboxes
            $('#packets :input[type="checkbox"]').each(function () { this.checked = false; });
            $(this).text('Select all');
        }        
    });
</script>
@stop