@extends('site/layouts/default')

@section('title')
{{{
        $title}}} :: @parent
@stop

@section('styles')
<link rel="stylesheet" href="{{URL::to('assets/css/ng-table.css')}}" />
@stop

@section('content')    
<div class="page-header">
    <h1>
        <span class="glyphicon glyphicon-shopping-cart"></span> {{{
                    $title}}}
    </h1>
</div>
<div id="wrapper">
    <!-- List seed packets -->
    <div id="packets_container" class="col-12 col-sm-12 col-lg-12"
         ng-controller="packet" ng-init="getPacketsCart()">
        <div id="packets_table" ng-show="packets.length > 0">
            <div style="text-align: right">
                <a href="{{{ URL::to('item/checkout') }}}" class="btn btn-lg btn-success"><span class="glyphicon glyphicon-shopping-cart"></span> Checkout</a>
            </div>
            <h4>Packets in cart</h4>            
            <table id="packets" ng-table="packetsTable" class="table table-striped table-hover">
                <tr ng-repeat="packet in packets" ng-model="packet">
                    <td data-title="'Seed'" class="col-md-3">@{{packet.seed}}</td>
                    <td data-title="'Grams'" class="col-md-1">@{{packet.amount}}</td>
                    <td data-title="'Germ. Ratio %'" class="col-md-1">@{{packet.germination_ratio| number : 1}}</td>
                    <td data-title="'Harvest Date'" class="col-md-1">@{{packet.date_harvest}}</td>
                    <td data-title="'Grow Location'" class="col-md-1">@{{packet.grow_location}}</td>
                    <td data-title="'Actions'" class="col-md-1">
                        <a href="#" class="btn btn-default btn-xs cart" ng-click="removeFromCart(packet)">
                            <span class="glyphicon glyphicon-remove"></span> Remove</a>
                    </td>
                </tr>
            </table>
        </div>
        <div class="alert alert-success alert-block" ng-hide="packets.length > 0">
            <p class="lead">No packets in basket.</p>
        </div>
    </div>    
</div>
@stop
