@extends('site/layouts/default')

@section('content')
<div id="content" class="col-12 col-sm-12 col-lg-12">
    <article id="donation">
        <h1 class="page-header">{{$title}}</h1>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut eleifend felis ut diam elementum pellentesque. Ut ultrices dignissim condimentum. Etiam pellentesque auctor pulvinar. Sed sed vestibulum risus. Donec posuere lorem eu posuere tristique. Sed pulvinar eu sapien et hendrerit. Donec convallis feugiat mauris at scelerisque. Quisque at lectus placerat, euismod libero sed, venenatis purus. Etiam interdum tortor vitae enim ultrices, nec auctor nisi malesuada. Fusce euismod malesuada tellus, et tincidunt diam pulvinar a. In congue lectus a sodales placerat. Duis id elit velit. Proin auctor molestie diam, pharetra adipiscing nulla facilisis ac. Aenean blandit interdum varius. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Aliquam lorem urna, porta eu diam eget, convallis pulvinar turpis.</p>
        <p>Praesent mi odio, volutpat a iaculis vel, egestas eu lacus. Pellentesque dictum sit amet arcu eget blandit. Sed rutrum, lacus eget venenatis vehicula, orci turpis viverra eros, lacinia commodo ipsum magna et velit. Cras venenatis venenatis rutrum. Maecenas lobortis dui ut elit porta pretium. Nam posuere dignissim dolor, id gravida justo commodo in. Proin facilisis mi non odio porttitor auctor. Etiam neque nisl, porta sed diam nec, fermentum consequat eros. Vivamus accumsan condimentum erat et venenatis. Nam dui sapien, aliquet at elit ut, adipiscing mattis diam. Phasellus interdum fringilla velit, sit amet varius massa sagittis a. Curabitur leo neque, porta vitae scelerisque eu, commodo eget erat. Praesent gravida auctor volutpat. Sed dictum lobortis erat, vitae ultricies enim ornare vitae.
            <img src="http://placehold.it/255x255" alt="Borrow" title="Borrow" style="float: right; padding: 10px">
        </p>
        
        <h3>How to borrow seed?</h3>
        <p>Praesent commodo purus eu lectus tempus, nec blandit felis facilisis. Nunc ultricies adipiscing neque a scelerisque. Aenean iaculis, eros vitae suscipit ultricies, metus leo adipiscing nibh, et tristique enim mauris in purus. Etiam in egestas nisl, id convallis ipsum. Duis rhoncus ac justo sit amet vulputate. Vestibulum adipiscing suscipit metus, vel sollicitudin lectus auctor sed. Integer ac ante pellentesque odio blandit convallis ut ac ligula. Pellentesque pellentesque dignissim velit eget facilisis. Sed vel ligula vehicula, fringilla felis non, tincidunt dolor. Integer vulputate, eros ut ornare ultricies, urna magna consectetur ante, eu tincidunt eros mauris et enim. Suspendisse et sollicitudin ipsum. Suspendisse metus sapien, pharetra id turpis quis, pretium convallis quam. Praesent in lorem pellentesque, condimentum odio id, cursus erat.</p>
        <p>Nulla varius odio quis felis adipiscing, id sagittis quam tempor. Suspendisse interdum egestas tincidunt. In lobortis massa non tincidunt commodo. Suspendisse potenti. In iaculis tristique nisi vitae posuere. Cras commodo tincidunt nisi, at elementum tortor elementum ullamcorper. Phasellus sagittis ipsum a urna pretium dapibus ac et lectus. Donec ut vulputate neque. Praesent blandit lectus non venenatis posuere. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed vehicula et est a mollis. Praesent massa sapien, pulvinar eget nibh faucibus, posuere ultrices quam. Ut malesuada est eu gravida dictum. Nunc ullamcorper non sem a fringilla. Phasellus eu est a urna pellentesque blandit et et ante. In vitae diam ut libero auctor consectetur.</p>
        
        <div class="pull-right">
            <a href="{{{ URL::to('item/search') }}}" class="btn btn-lg btn-success iframe"><span class="glyphicon glyphicon-"></span> Search seed</a>
        </div>
    </article>
</div>
@stop