@extends('site/layouts/default')

<script>
    @if(isset($return) && !empty($return->checked_in_date))
        alert('You only have permission to edit pending requests.');
        history.go(-1);
    @endif
</script>

@section('title')
{{{ $title }}} :: @parent
@stop

@section('styles')
<link rel="stylesheet" href="{{asset("assets/css/custom/tables.css")}}" />
<link data-require="ng-table@*" data-semver="0.3.0" rel="stylesheet" href="http://bazalt-cms.com/assets/ng-table/0.3.0/ng-table.css" />
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
    <h1 class="page-header">{{$title}}</h1>

    <!-- Tabs General Info -->
    <div class="tab-content">
        {{-- Edit Return Form --}}
        <!-- Form beginning -->
        <form class="form-horizontal" method="post" autocomplete="off" 
              action="@if (isset($return)){{ URL::to('returns/' . $return->id . '/edit')}}@else # @endif">
            <!-- CSRF Token -->
            <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
            <!-- ./ csrf token -->

            <div class="form-group {{{ $errors->has('amount') ? 'has-error' : '' }}}">
                <div class='col-md-12'>
                    <label for='amount'>Amount (grams)</label>
                    <input class='form-control' type='number' name='amount' id='amount' min='1' placeholder='Ex: 100' value="{{{Input::old('amount', isset($return) ? $return->amount : null)}}}" />
                </div>
            </div>
            
            <!-- Parent packet -->
            <div class="form-group {{{ $errors->has('parent_packet') ? 'has-error' : '' }}}">
                <div class="col-md-12">
                    <label class="control-label" for="parent_packet">Parent packet</label>
                    <input readonly="readonly" class="form-control" type="number" name="parent_packet" id="parent_packet" value="" />
                </div>
            </div>
            <!-- ./ parent packet -->
            
            <!-- Seed description -->
            <div class="form-group {{{ $errors->has('description') ? 'has-error' : '' }}}">
                <div class="col-md-12">
                    <label id="lbl_description" class="control-label" for="description" data-toggle="tooltip" 
                           data-placement="right" title="Please use this field to provide further detail about your seed. (e.g Germination ratio, grow location)">Description</label>
                    <textarea class="form-control" name="description" id="description">{{{ Input::old('description', isset($return) ? $return->description : null) }}}</textarea>
                </div>
            </div>
            <!-- ./ seed description -->

            <label class="control-label" for="candidate_packets">Candidate packets</label>
            <br/><br/>
            <table id="candidate_packets" ng-table="packetsTable" show-filter="true" class="table table-striped table-hover table-bordered"
                   ng-controller="packet" ng-init="getUserPackets({{Auth::user()->id}}@if(isset($return)), true@endif)" main>
                <tr ng-repeat="packet in packets" ng-model="packet" ng-show="packet.checked_out_date" ng-init="alert(packet.id)" repeat>
                    <td class="col-md-1" data-title="'Packet #'" sortable="'id'">@{{packet.id}}</td>
                    <td class="col-md-3" data-title="'Seed'" sortable="'family'" filter="{ 'variety': 'text' }">@{{packet.family}} (@{{packet.species}}) - @{{packet.variety}}</td>
                    <td class="col-md-1" data-title="'Amount'" sortable="'amount'">@{{packet.amount}}</td>
                    <td class="col-md-1" data-title="'Germ. Ratio %'" sortable="'germination_ratio'">@{{packet.germination_ratio | number: 1}}</td>
                    <td class="col-md-1" data-title="'Harvest Date'" sortable="'date_harvest'" filter="{ 'date_harvest': 'text' }">@{{packet.date_harvest}}</td>
                    <td class="col-md-1" data-title="'Grow Location'" sortable="'grow_location'" filter="{ 'grow_location': 'text' }">@{{packet.grow_location}}</td>
                </tr>
            </table>                
            <br/>

            <!-- Form Actions -->
            <div class="form-group">
                <div class="col-md-12">
                    <button id="update_btn" type="submit" class="btn btn-success disabled">Save</button>
                    <button type="reset" class="btn btn-default">Reset</button>
                </div>
            </div>
            <!-- ./ form actions -->
        </form>            
    </div>
    <!-- ./ tabs content -->
@stop

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){        
        // Adds selection behaviour
        $('#candidate_packets').on( 'click', 'tr:not(:first):not(:last)', function () {
            // Change style of selected table row to make user aware of it
            $('tr.selected').removeClass('selected');
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
    });
    
    // Script that run after table is rendered
    @if(isset($return))
    // Angular script for table
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
                    $('#candidate_packets td:first-child').each(function() {
                        if ($(this).text() == {{$return->parent_id}})
                            $(this).click();
                    });  
                }, 0);
            });
        };
    }]);
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
</script>
@stop