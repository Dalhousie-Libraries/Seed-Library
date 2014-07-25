@extends('site/layouts/default')

@section('title')
{{{ $title}}} :: @parent
@stop

@section('styles')
<link rel="stylesheet" href="{{asset('assets/css/ng-table.css')}}" />
<link rel="stylesheet" href="{{asset('assets/css/custom/image-gallery.css')}}" />
@stop

@section('content')    
<div class="page-header">
    <h1>
        {{{ $title}}}
    </h1>
</div>
<div id="wrapper">
    <article id="seed_description" class="col-4 col-sm-4 col-lg-4"
             ng-controller="item" ng-init="getItem({{$item->id}})">
        <p><strong>Category:</strong> @{{item.category.toLowerCase()}}</p>
        <p><strong>Family:</strong> <em>@{{item.family}}</em></p>
        <p><strong>Species:</strong> @{{item.species}}</p>
        <p><strong>Variety:</strong> @{{item.variety}}</p>
        <p><strong>Seed Saving Level:</strong> @{{item.seed_sav_level.toLowerCase()}}</p>
        <p><strong>Description:</strong> @{{item.description}}</p>
    </article>

    <!-- List seed packets -->
    <div id="packets_container" class="col-4 col-sm-4 col-lg-4 pull-right"
         ng-controller="packet" ng-init="getPackets({{$item->id}})">
        <div id="packets_table" ng-show="packets.length > 0" class="rounded lt_green_border" style="padding: 0 5px;">
            <h4>Packets</h4>
            <table id="packets" ng-table="packetsTable" class="table table-hover">
                <tr ng-repeat="packet in $data" ng-model="packet">
                    <!--<td data-title="'Amount (grams)'" class="col-md-1" sortable="'amount'">@{{packet.amount}}</td>-->
                    <td data-title="'Germ. Ratio %'" class="col-md-1" sortable="'germination_ratio'">@{{packet.germination_ratio| number : 1}}</td>
                    <td data-title="'Harvest Date'" class="col-md-1" sortable="'date_harvest'">@{{packet.date_harvest}}</td>
                    <!--<td data-title="'Grow Location'" class="col-md-1" sortable="'grow_location'">@{{packet.grow_location}}</td>-->
                    <td data-title="'Actions'" class="col-md-1">
                        <a href="#" class="btn btn-default btn-xs cart" ng-click="addToCart(packet)">
                            <span class="glyphicon glyphicon-shopping-cart"></span> @{{packet.inCart ? "Remove" : "Add to basket"}}</a>
                    </td>
                </tr>
            </table>
        </div>
        <div class="alert alert-success alert-block" ng-hide="packets.length > 0">
            <p class="lead">No packets available for this specific seed.</p>
        </div>
    </div>
    
    <!-- List images -->
    @if(isset($item) && count($item->images))
    <div class="image_list col-8 col-sm-8 col-xs-8 col-md-8 col-lg-8">
        <hr/>
        <ul class="row">
        @foreach($item->images as $image)
            <li class="col-lg-2 col-md-2 col-sm-3 col-xs-4">
                <img class="img-responsive" src="{{Croppa::url('uploads/items/' .$image->filename, 300, null)}}" title="{{$item->getFullname()}}" />
            </li>
        @endforeach
        </ul>
    </div>
    <div class="modal fade" id="imagesModal" tabindex="-1" role="dialog" aria-labelledby="imagesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">         
                <div class="modal-body">                
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->                
    @endif
</div>
@stop

@section('scripts')
<script src="{{asset('assets/js/custom/image-gallery.js')}}"></script>
@stop