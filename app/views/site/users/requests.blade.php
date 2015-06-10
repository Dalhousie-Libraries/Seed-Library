@extends('site/layouts/default')

@section('title')
{{{ $title}}} :: @parent
@stop

@section('styles')
<link rel="stylesheet" href="{{URL::to('assets/css/ng-table.css')}}" />
@stop

@section('content')
<div class="page-header">
    <h1>
        {{{ $title}}}
    </h1>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs">
    <li class="active"><a href="#tab-lendings" data-toggle="tab">Lendings</a></li>
    <li><a href="#tab-donations" data-toggle="tab">Donations</a></li>
    <li><a href="#tab-returns" data-toggle="tab">Returns</a></li>                
</ul>
<!-- ./ tabs -->   

<div class="tab-content">
    <div class="tab-pane active" id="tab-lendings">
        <div ng-controller="user" ng-init="getRequests('lendings', {{Auth::user()->id}})" main>
            <br/>
            <div ng-show="requests.length > 0">
                <div class="form-group">
                    <div class="col-md-12 btn-group">
                        <label for="lendings_type_0" class="btn btn-default" onclick="updateTables(0)">
                            <input type="radio" name='lendings_type' id="lendings_type_0" value="0" ng-model='lendings_type' ng-init="lendings_type = lendings_type || 0" /> Pending
                        </label>
                        <label for="lendings_type_1" class="btn btn-default" onclick="updateTables(0)">
                            <input type="radio" name='lendings_type' id="lendings_type_1" value="1" ng-model='lendings_type' ng-init="lendings_type = lendings_type || 0" /> Checked out
                        </label>
                    </div>
                </div>
                <br/><br/><br/>
                <table id="requests_l"  ng-table="requestsTable" class="table table-hover">
                    <tr ng-repeat="request in $data" ng-model="request" ng-show="(lendings_type == 0 && !request.checked_out_date) || (lendings_type == 1 && request.checked_out_date)" repeat>
                        <td data-title="'Seed'" class="col-md-2" sortable="'family'">@{{request.family}} (@{{request.species}}) - @{{request.variety}} </td>
                        <td data-title="'Amount (grams)'" class="col-md-1 text-center" sortable="'amount'">@{{request.amount}}</td>
                        <td data-title="'Requested at'" class="col-md-1" sortable="'requested_at'" ng-show="lendings_type == 0">
                            @{{request.requested_at| date:'MM/dd/yyyy @ HH:mm' }}
                        </td>
                        <td data-title="'Reserved until'" class="col-md-1" sortable="'reserved_until'" ng-show="lendings_type == 0">
                            @{{request.reserved_until| date:'MM/dd/yyyy @ HH:mm' }}
                        </td>
                        <td data-title="'Checked out date'" class="col-md-1" sortable="'checked_out_date'" ng-show="lendings_type == 1">
                            @{{request.checked_out_date| date:'MM/dd/yyyy @ HH:mm' }}
                        </td>
                        <td data-title="'Actions'" class="col-md-1 text-center" ng-show="lendings_type == 0">
                            <a href="#" class="btn btn-danger btn-xs delete" data-toggle="modal" data-target="#confirmDelete" data-link="{{ URL::to('packet/')}}/@{{request.id}}/delete" 
                                        data-title="Delete request" data-message="Are you sure you want to delete this request?">Delete</a>
                        </td>
                    </tr>
                </table>
            </div>            
            <div id="no_req_l" ng-hide="requests.length > 0">
                 <br/><br/>
                <p class="text-info">No lendings.</p>
            </div>
        </div>
    </div>

    <div class="tab-pane" id="tab-donations">
        <div id="requests" ng-controller="user" ng-init="getRequests('donations', {{Auth::user()->id}})" main>
            <br/>
            <div ng-show="requests.length > 0">
                <div class="form-group">
                    <div class="col-md-12 btn-group">
                        <label for="donation_type_0" class="btn btn-default" onclick="updateTables(1)">
                            <input type="radio" name='donation_type' id="donation_type_0" value="0" ng-model='donation_type' ng-init="donation_type = donation_type || 0" /> Pending
                        </label>
                        <label for="donation_type_1" class="btn btn-default" onclick="updateTables(1)">
                            <input type="radio" name='donation_type' id="donation_type_1" value="1" ng-model='donation_type' ng-init="donation_type = donation_type || 0" /> Checked in
                        </label>
                    </div>
                </div>
                <br/><br/><br/>
                <table id="requests_d"  ng-table="requestsTable" class="table table-hover">
                    <tr ng-repeat="request in $data" ng-model="request" ng-show="(donation_type == 0 && !request.checked_in_date) || (donation_type == 1 && request.checked_in_date)" repeat>
                        <td data-title="'Seed'" class="col-md-2" sortable="'family'">@{{request.family}} (@{{request.species}}) - @{{request.variety}} </td>
                        <td data-title="'Amount (grams)'" class="col-md-1 text-center" sortable="'amount'">@{{request.amount}}</td>
                        <td data-title="'Requested at'" class="col-md-1" sortable="'requested_at'" ng-show="donation_type == 0">
                            @{{request.requested_at| date:'MM/dd/yyyy @ HH:mm' }}
                        </td>
                        <td data-title="'Checked in date'" class="col-md-1" sortable="'checked_in_date'" ng-show="donation_type == 1">
                            @{{request.checked_in_date| date:'MM/dd/yyyy @ HH:mm' }}
                        </td>
                        <td data-title="'Actions'" class="col-md-1 text-center" ng-show="donation_type == 0">
                            <a href="{{URL::to('donations')}}/@{{request.id}}/edit" class="btn btn-default btn-xs">Edit</a>
                            <a href="#" class="btn btn-danger btn-xs delete" data-toggle="modal" data-target="#confirmDelete" data-link="{{ URL::to('donations/')}}/@{{request.id}}/delete" 
                                        data-title="Delete request" data-message="Are you sure you want to delete this request?">Delete</a>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="no_req_d" ng-hide="requests.length > 0">
                <br/><br/>
                <p class="text-info">No donations.</p>
            </div>
        </div>
    </div>

    <div class="tab-pane" id="tab-returns">
        <div ng-controller="user" ng-init="getRequests('returns', {{Auth::user()->id}})" main>
            <br/>
            <div ng-show="requests.length > 0">
                <div class="form-group">
                    <div class="col-md-12 btn-group">
                        <label for="return_type_0" class="btn btn-default" onclick="updateTables(2)">
                            <input type="radio" name='return_type' id="return_type_0" value="0" ng-model='return_type' ng-init="return_type = return_type || 0" /> Pending
                        </label>
                        <label for="return_type_1" class="btn btn-default" onclick="updateTables(2)">
                            <input type="radio" name='return_type' id="return_type_1" value="1" ng-model='return_type' ng-init="return_type = return_type || 0" /> Checked in
                        </label>
                    </div>
                </div>
                <br/><br/><br/>
                <table id="requests_r"  ng-table="requestsTable" class="table table-hover">
                    <tr ng-repeat="request in $data" ng-model="request" ng-show="(return_type == 0 && !request.checked_in_date) || (return_type == 1 && request.checked_in_date)" repeat>
                        <td data-title="'Seed'" class="col-md-2" sortable="'family'">@{{request.family}} (@{{request.species}}) - @{{request.variety}} </td>
                        <td data-title="'Amount (grams)'" class="col-md-1 text-center" sortable="'amount'">@{{request.amount}}</td>
                        <td data-title="'Requested at'" class="col-md-1" sortable="'requested_at'" ng-show="return_type == 0">
                            @{{request.requested_at| date:'MM/dd/yyyy @ HH:mm' }}
                        </td>
                        <td data-title="'Checked in date'" class="col-md-1" sortable="'checked_in_date'" ng-show="return_type == 1">
                            @{{request.checked_in_date| date:'MM/dd/yyyy @ HH:mm' }}
                        </td>
                        <td data-title="'Actions'" class="col-md-1 text-center" ng-show="return_type == 0">
                            <a href="{{URL::to('returns')}}/@{{request.id}}/edit" class="btn btn-default btn-xs">Edit</a>
                            <a href="#" class="btn btn-danger btn-xs delete" data-toggle="modal" data-target="#confirmDelete" data-link="{{ URL::to('returns/')}}/@{{request.id}}/delete" 
                                        data-title="Delete request" data-message="Are you sure you want to delete this request?">Delete</a>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="no_req_r" ng-hide="requests.length > 0">
                <br/><br/>
                <p class="text-info">No returns.</p>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')
@include('admin/layouts/delete')
<script type="text/javascript">
    // Delete button behaviour
    $('#confirmDelete').find('.modal-footer #confirm').on('click', function(){
        var link    = $(this).data('form');
        var element = $(this).data('source');

        // Try to delete image via AJAX
        $.get(link, function(data) {
            // Analyse results
            if (data.success) {
                alert('Your request has been canceled.');
                location.reload();
            }
            else
                alert(data.message);
        }).fail(function() {
            alert('Record could not be deleted.'); // or whatever
        });
    });
    
    // Script that is executed after table renderization
    // Angular script for table
    var tables = [['#requests_l', '#no_req_l'], 
                  ['#requests_d', '#no_req_d'], 
                  ['#requests_r', '#no_req_r']];
    app
    .directive('repeat', function() {
        return function(scope, element, attrs) {
            if (scope.$last){
                scope.$emit('LastElem');
            }        
        };
    })
    .directive('main', ['$timeout', function(timer) {
        return function(scope, element, attrs) {
            scope.$on('LastElem', function(event){
                // OBS: we do this to process it only after renderization
                timer(function() {
                    // Check wheter tables should be rendered or not
                    for (var i = 0; i < tables.length; i++) {
                        updateTable(tables[i][0], tables[i][1]);
                    }
                }, 0);
            });
        };
    }]);

    // Update a table visibility
    function updateTable(id, noResultId)
    {
        if(($(id).children('tbody').children('.ng-valid:not(.ng-hide)')).length <= 0) {
            $(id).addClass('ng-hide');
            $(noResultId).removeClass('ng-hide');
        }
        else {
            $(id).removeClass('ng-hide');
            $(noResultId).addClass('ng-hide');
        }
    }
    
    // Add behaviour to 'Pending', 'Checked In' and 'Checked Out' buttons
    function updateTables(index)
    {
        updateTable(tables[index][0], tables[index][1]);
    }
</script>
@stop