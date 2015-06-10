@extends('admin/layouts/modal')

@section('title')
{{{ $title}}} :: @parent
@stop

@section('styles')
<link type='text/css' rel="stylesheet" href="{{asset('assets/css/custom/timeline.css')}}"/>
@stop

{{-- Content --}}
@section('content')
        <br/>
        <div id="timeline" class="content" ng-app="lendingSeed" ng-controller="packet" ng-init="getPacketHistory({{$id}})">
            <div class="timeline" ng-repeat="packet in history | orderBy:'accession.checked_in_date'">
                <div class="date">@{{packet.date_harvest}}</div>
                <div class="timeline-entry g5-group" style="border-color: rgb(196, 225, 99);">
                    <div class="metadata">
                        <p class="text-info">
                            <span class="group">Packet #@{{packet.id}}</span>
                            - <span class="version">@{{packet.accession.type}}</span>
                        </p>
                    </div>
                    <div class="content">
                        <ul>
                            <li>@{{packet.accession.type === 'RETURN' ? 'Returned' : 'Donated'}} by @{{packet.accession.user.name}}
                                on @{{packet.accession.checked_in_date}}
                            </li>
                            <li ng-show="packet.borrower_id">
                                Borrowed by @{{packet.borrower.name}} on @{{packet.checked_out_date}}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
@stop

@section('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.16/angular.min.js"></script>
<script src="{{asset('assets/js/angularjs/ng-table.min.js')}}"></script>
<script src="{{asset('assets/js/custom/main-app.js')}}"></script>
<script src="{{asset('assets/js/custom/util.js')}}"></script>
<script src="{{asset('assets/js/custom/mod-cart.js')}}"></script>
<script src="{{asset('assets/js/custom/services.js')}}"></script>
@stop