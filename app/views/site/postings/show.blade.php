@extends('site/layouts/default')

@section('title')
{{$posting->title}} ::@parent
@stop

@section('content')
<div id="content" class="col-12 col-sm-12 col-lg-12">
    <article id="{{$posting->slug}}}">
        <h1 class="page-header">{{$posting->title}}</h1>
        {{$posting->content}}
    </article>
</div>
@stop