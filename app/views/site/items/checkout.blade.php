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
    <h1>{{{$title}}}</h1>
</div>
<div id="wrapper">
    <!-- Packets in the basket -->
    <div id="packets_container_pre" class="container"
         ng-controller="packet" ng-init="getPacketsCart()">
        <div id="packets_table" ng-show="packets.length > 0">
            <div class="alert alert-block">
                <h4 class="page-header">Reservation information</h4>
                <p>
                    The packets will be reserved for you and must be picked up by <em>{{Carbon::now()->addWeek()->format('l jS \\of F Y')}}</em>.
                </p>
                <br/>
                <a href="#" class="btn btn-primary" ng-controller="cart" ng-click="doCheckout({{Auth::user()->id}})">Confirm reservation</a>
            </div>
            <!--<div  class="col-8 col-sm-8 col-lg-8">
                <table id="packets" ng-table="packetsTable" class="table table-striped table-hover table-bordered" template-pagination="nopager">
                    <tr ng-repeat="packet in packets" ng-model="packet">
                        <td data-title="'Seed'" class="col-md-2">@{{packet.seed}}</td>
                        <td data-title="'Amount (grams)'" class="col-md-1 text-center">@{{packet.amount}}</td>
                    </tr>
                </table>
            </div>-->
        </div>
        <div class="alert alert-success alert-block" ng-hide="packets.length > 0">
            <p class="lead">No packets in the basket.</p>
        </div>
    </div>
    
    <!-- Reserved packets -->
    <div id="packets_container_post" class="container" style="display: none">
        <div id="packets_table_post">
            <div  class="col-12 col-sm-12 col-lg-12">
                <br/>
                <p class="lead">Congratulations! Your request has been processed and your seeds reserved!</p>
                <p>Check your request details below:</p>
                <table id="packets_post" class="table table-hover table-bordered">
                    <thead>
                        <th>Seed</th>
                        <th class="text-center">Amount (grams)</th>
                        <th>Status</th>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <p>
                    <a href="{{URL::to('user/requests')}}">
                        <span class="glyphicon glyphicon-search"></span>
                        View all your requests.
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@stop
