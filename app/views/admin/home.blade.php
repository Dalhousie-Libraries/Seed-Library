@extends('admin/layouts/default')

@section('title')
    Seed Lending Library
@stop

@section('content')
    <!-- Content -->
    <div class="content">
        <h2 class="page-header">Welcome to the Seed Lending Library Administration!</h2>
        <br/>
        <p class="text-info">Use the top navigation bar to access the features you want to use.</p>
        <!--<h4>Actions</h4>
        <div id="action-list">
            <ul>
                <li><a href="{{{URL::to('admin/items')}}}" title="List or register items">Items</a></li>
                <li><a href="{{{URL::to('admin/users')}}}" title="List or register users">Users</a></li>
                <li><a href="{{{URL::to('admin/donations')}}}" title="List or register donations">List or register donations</a></li>
                <li><a href="{{{URL::to('admin/packets')}}}" title="List lendings">Lendings</a></li>
                <li><a href="{{{URL::to('admin/packets/lend')}}}" title="Lend seed">Lend Seed</a></li>
                <li><a href="{{{URL::to('admin/returns')}}}" title="List or register returns">List available items</a></li>
            </ul>
        </div>-->
        <!--<div id="icons" style="display: table; width: 100%;">
            <div class="icons-text"  style="display: table-row">
                <div style="width: 16%; display: table-cell;  text-align: center">
                    <a class="icon" href="{{{URL::to('admin/items')}}}">
                        <img src="assets/img/icons/bullets.png" alt="Items" title="Items" />
                        <p>Items</p>
                    </a>
                </div>
                <div style="width: 16%; display: table-cell; text-align: center">
                    <a class="icon" href="{{{URL::to('admin/users')}}}">
                        <img src="assets/img/icons/suit.png" alt="Users" title="Users"/>
                        <p>Users</p>
                    </a>
                </div>
                <div style="width: 16%; display: table-cell; text-align: center">
                    <a class="icon" href="{{{URL::to('admin/donations')}}}">
                        <img src="assets/img/icons/import.png" alt="Donations" title="Donations"/>
                        <p>Donations</p>
                    </a>
                </div>
                <div style="width: 16%; display: table-cell;  text-align: center">
                    <a class="icon" href="{{{URL::to('admin/packets')}}}">
                        <img src="assets/img/icons/bullets.png" alt="List Lendings" title="List Lendings" />
                        <p>List Lendings</p>
                    </a>
                </div>
                <div style="width: 16%; display: table-cell; text-align: center">
                    <a class="icon" href="{{{URL::to('admin/packets/lend')}}}">
                        <img src="assets/img/icons/suit.png" alt="Lend Seed" title="Lend Seed"/>
                        <p>Lend Seed</p>
                    </a>
                </div>
                <div style="width: 16%; display: table-cell; text-align: center">
                    <a class="icon" href="{{{URL::to('admin/returns')}}}">
                        <img src="assets/img/icons/import.png" alt="Returns" title="Returns"/>
                        <p>Returns</p>
                    </a>
                </div>
            </div>
        </div>-->
    </div>
    <!-- ./ Content -->
@stop