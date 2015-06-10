@extends('site/layouts/default')

@section('styles')
 <link data-require="ng-table@*" data-semver="0.3.0" rel="stylesheet" href="http://bazalt-cms.com/assets/ng-table/0.3.0/ng-table.css" />
@stop

@section('content')
    <h1 class="page-header">{{$title}}</h1>    
    <p class="lead text-primary pull-left">Find the seed you've been looking for!</p>
    <div class="pull-right btn-group" data-toggle='buttons'>
        <label id="lbl_avail" class="btn btn-info active" for="rd_avail">
            <input type="radio" name="search_type" id="rd_avail" /> 
            Available packets only
        </label>
        <label id="lbl_all" class="btn btn-info" for="rd_all">
            <input type="radio" name="search_type" id="rd_all" /> 
            All seeds
        </label>
    </div>
    <br/><br/><br/>
    <!-- List seed packets -->
    <div id="packets_container" class="col-12 col-sm-12 col-lg-12">
        <table id="items" class="table table-striped table-hover table-bordered">
            <thead>
                <th class="col-md-5">Seed name</th>
                <th>Category</th>
                <th>Difficulty level</th>
                <th>Details</th>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@stop

@section('scripts')
<script type="text/javascript">    
    // Datatable
    var oTable;
    $(document).ready(function() {
        oTable = $('#items').dataTable( {
            "sDom": "<'table_wrapper rounded'<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>>",
            "sPaginationType": "bootstrap",
            "sFilter": " form-control",
            "sLength": "25",
            "oLanguage": {
                    "sLengthMenu": "_MENU_ records per page"
            },
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "{{ URL::to('item/listAvail') }}",
        });
        
        // Change filters css
        $('div.dataTables_filter input').addClass('form-control').attr('placeholder', 'Enter a seed name');
        $('div.dataTables_length select').addClass('form-control');
    });
    
    // Adds listener to search buttons
    $('#lbl_avail').click(function(event) {
        if (!$(this).hasClass('active'))
            oTable.fnReloadAjax("{{ URL::to('item/listAvail') }}");
    });
    
    $('#lbl_all').click(function(event) {
        if (!$(this).hasClass('active'))
            oTable.fnReloadAjax("{{ URL::to('item/list') }}");
    });
</script>
@stop