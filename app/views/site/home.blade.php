@extends('site/layouts/default')

@section('styles')
<link rel="stylesheet" href="{{asset('assets/css/aw-showcase.css')}}"/>
@stop

@section('content')
    <div id="welcome_wrapper" class="col-6 col-sm-6 col-lg-6">
        <article id="welcome">
            <h1 class="page-header">Welcome to the Seed Lending Library's website!</h1>
            <p>Access to diverse varieties of plants is important to our community. By selecting and saving seeds from plants which thrive, over generations, we have an opportunity to cultivate a collection of seeds that flourish in our community.</p>
        </article>
    </div>
    <div id="showcase_wrapper" class="col-6 col-sm-6 col-lg-6">
        <div id="showcase" class="showcase">
            <!-- Each child div in #showcase represents a slide -->
            <div class="showcase-slide">
                <!-- Put the slide content in a div with the class .showcase-content. -->
                <div class="showcase-content">
                    <!-- If the slide contains multiple elements you should wrap them in a div with the class .showcase-content-wrapper. -->
                    <div class="showcase-content-wrapper">
                        <!-- For dynamic height to work in webkit you need to set the width and height of images in the source.
                         Usually works to only set the dimension of the first slide in the showcase. -->
                        <img src="{{asset('assets/img/slider_image_01.jpg')}}" alt="01" width="450px" height="320px" />                    
                    </div>
                    <div class="showcase-caption">
                        <a href="{{URL::to('item/search')}}" class='lead text-info'>Search for your favorite seed!</a>
                    </div>
                </div>
            </div>
            <!-- Each child div in #showcase represents a slide -->
            <div class="showcase-slide">
                <div class="showcase-content">
                    <div class="showcase-content-wrapper">
                        <img src="{{asset('assets/img/slider_image_02.jpg')}}" alt="02" width="450px" height="320px" />
                    </div>
                    <div class="showcase-caption">
                        <a href="{{URL::to('article/about-lendings')}}" class='lead text-info'>Learn how to borrow seed!</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <article>
            <div class="col-md-6 col-sm-6 col-lg-6">
                <h2 class="page-header">How does it work?</h2>
                <div class="text-center">
                    <img src="{{asset('assets/img/seed_lending_process.png')}}" title="Seed Lending Process" width="500" />
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-lg-6">
                <br/><br/><br/><br/>
                <h3>What are the benefit of becoming a member?</h3>
                <ul>
                    <li>Access a free collection of diverse seeds</li>
                    <li>Be part of a community sustained project</li>
                    <li>Participate in workshops and educational events intended to support you throughout the growing season</li>
                    <li>Share the stories and the seeds of the plants you cultivate so others may discover them</li>
                </ul>
            </div>
        </article>
    </div>
@stop

@section('scripts')
<script src="{{asset('assets/js/jquery.aw-showcase.min.js')}}"></script>
<script type="text/javascript">

$(document).ready(function()
{
	$("#showcase").awShowcase(
	{
		content_width:			450,
		content_height:			320,
		fit_to_parent:			false,
		auto:				true,
		interval:			3000,
		continuous:			false,
		loading:			true,
		tooltip_width:			200,
		tooltip_icon_width:		32,
		tooltip_icon_height:            32,
		tooltip_offsetx:		18,
		tooltip_offsety:		0,
		arrows:				true,
		buttons:			true,
		btn_numbers:			false,
		keybord_keys:			true,
		mousetrace:			false, /* Trace x and y coordinates for the mouse */
		pauseonover:			true,
		stoponclick:			false,
		transition:			'fade', /* hslide/vslide/fade */
		transition_delay:		0,
		transition_speed:		500,
		show_caption:			'onload', /* onload/onhover/show */
		thumbnails_position:            'outside-last', /* outside-last/outside-first/inside-last/inside-first */
		thumbnails_direction:           'horizontal', /* vertical/horizontal */
		thumbnails_slidex:		0, /* 0 = auto / 1 = slide one thumbnail / 2 = slide two thumbnails / etc. */
		dynamic_height:			true, /* For dynamic height to work in webkit you need to set the width and height of images in the source. Usually works to only set the dimension of the first slide in the showcase. */
		speed_change:			true, /* Set to true to prevent users from swithing more then one slide at once. */
		viewline:			false, /* If set to true content_width, thumbnails, transition and dynamic_height will be disabled. As for dynamic height you need to set the width and height of images in the source. */
	});
});

</script>
@stop