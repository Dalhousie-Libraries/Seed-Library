@extends('admin/layouts/modal')

@section('title')
{{{ $title }}} :: @parent
@stop

@section('styles')
@if(isset($item) && count($item->images))
<link rel="stylesheet" href="{{asset('assets/css/custom/image-gallery.css')}}" />
@endif
<style>

#drop_zone {
    border: 2px dashed #bbb;
    -moz-border-radius: 5px;
    -webkit-border-radius: 5px;
    border-radius: 5px;
    text-align: center;
    font: 20pt bold 'Vollkorn';
    color: #bbb;
}
</style>
@stop

{{-- Notifications --}}
@section('notifications')
    <!-- Saving success -->
    @if ( Session::has('success') )
        <div class="alert alert-success alert-block">
            <p>{{Session::get('success')}}</p>
        </div>
    @endif
    
    <!-- Saving warning -->
    @if ( Session::has('warning') )
        <div class="alert alert-warning alert-block">
            <p>{{Session::get('warning')}}</p>
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
    {{-- Edit Item Form --}}
    <!-- Form beginning -->
    <form class="form-horizontal" method="post" autocomplete="off" enctype="multipart/form-data"
          action="@if (isset($item)){{ URL::to('admin/items/' . $item->id) . '/edit'}}@endif" >
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->
        
        <!-- Tabs -->
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab">General</a></li>
            <li><a href="#tab-images" data-toggle="tab">Images</a></li>
        </ul>
	<!-- ./ tabs -->       
        
        <!-- Tabs Content -->
        <div class="tab-content">
            <!-- General tab -->
            <div class="tab-pane active" id="tab-general">
                <!-- Seed category -->
                <div class="form-group">
                    <div class="col-md-12">
                        <input type="hidden" name="_category" id="_category" value="{{{ Input::old('category', isset($item) ? $item->category : null) }}}" />
                        <label class="control-label" for="category">Category</label>
                        <select class="form-control" name="category" id="category">
                            <option selected="selected">EDIBLE</option>
                        </select>
                        <!--value="{{{ Input::old('category', isset($item) ? $item->category : null) }}}" />-->
                    </div>
                </div>
                <!-- ./ seed category -->
                
                <!-- Seed familiy -->
                <div class="form-group {{{ $errors->has('family') ? 'has-error' : '' }}}">
                    <div class="col-md-12">
                        <label class="control-label" for="family">Family</label>
                        <input class="form-control" type="text" name="family" id="family" value="{{{ Input::old('family', isset($item) ? $item->family : null) }}}" />
                    </div>
                </div>
                <!-- ./ seed family -->
                
                <!-- Seed species -->
                <div class="form-group {{{ $errors->has('species') ? 'has-error' : '' }}}">
                    <div class="col-md-12">
                        <label class="control-label" for="species">Species</label>
                        <input class="form-control" type="text" name="species" id="species" value="{{{ Input::old('species', isset($item) ? $item->species : null) }}}" />
                    </div>
                </div>
                <!-- ./ seed species -->
                
                <!-- Seed variety -->
                <div class="form-group {{{ $errors->has('variety') ? 'has-error' : '' }}}">
                    <div class="col-md-12">
                        <label class="control-label" for="variety">Variety</label>
                        <input class="form-control" type="text" name="variety" id="variety" value="{{{ Input::old('variety', isset($item) ? $item->variety : null) }}}" />
                    </div>
                </div>
                <!-- ./ seed variety -->

                <!-- Description -->
                <div class="form-group">
                    <div class="col-md-12">
                        <label class="control-label" for="description">Description</label>
                        <textarea class="form-control" name="description" value="description" rows="5">{{{ Input::old('description', isset($item) ? $item->description : null) }}}</textarea>
                    </div>
                </div>
                <!-- ./ content -->
                
                <!-- seed saving level -->
                <div class="form-group">
                    <div class="col-md-12">
                        <input type="hidden" name="_seed_sav_level" id="_seed_sav_level" value="{{{ Input::old('seed_sav_level', isset($item) ? $item->seed_sav_level : null) }}}" />
                        <label class="control-label" for="seed_sav_level">Seed Saving Level</label>
                        <select class="form-control" name="seed_sav_level" id="seed_sav_level">
                            <option selected="selected">EASY</option>
                        </select>
                    </div>
                </div>
                <!-- ./ seed saving level -->
            </div>
            <!-- ./ general tab -->
            
            <!-- Images tab -->
            <div class="tab-pane" id="tab-images">
                <br/>
                <div id="drop_zone" class="bg-info" style="height: 100px">
                    <input name="files[]" id="files" type="file" multiple accept="image/*" style="opacity: 0; position: absolute;"/>
                    <br/>
                    <span>Drop images here</span>
                </div>
                <output id="list"></output>
                
                @if(isset($item) && count($item->images))
                <div class="image_list">
                    <hr/>
                    <ul class="row">
                    @foreach($item->images as $image)
                        <li class="col-lg-2 col-md-2 col-sm-3 col-xs-4">
                            <img class="img-responsive" src="{{asset('uploads/items/' .$image->filename)}}" title="{{$item->getFullname()}}" />
                            <a href="#" class="delete" data-toggle="modal" data-target="#confirmDelete" 
                               data-link="{{URL::to('admin/images/' . $image->id . '/delete')}}" data-title="Delete image"
                               data-message="Are you sure you want to delete this image?">
                                <span class="glyphicon glyphicon-remove-circle"></span>
                            </a>
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
                <br/>
            </div>
            <!-- ./ images tab -->
        </div>
        <!-- ./ tabs content -->

        <!-- Form Actions -->
        <div class="form-group">
            <div class="col-md-12">
                <button type="submit" class="btn btn-success">Update</button>
                <button type="reset" class="btn btn-default">Reset</button>
                <button class="btn btn-cancel close_popup">Cancel</button>
            </div>
        </div>
        <!-- ./ form actions -->
    </form>
@stop

@section('scripts')
@include('admin/layouts/delete')
<script type="text/javascript">
$('#confirmDelete').find('.modal-footer #confirm').on('click', function(){
    var link    = $(this).data('form');
    var element = $(this).data('source');
    
    // Try to delete image via AJAX
    $.get(link, function(data) {
        // Analyse results
        if (data.success) {
            $(element).parent().fadeOut("slow", function() {
                $(this).remove();
            });
        } else
            alert(data.message);
    });
});
  
$(document).ready(function(){    
    // Seed category
    var category = {
        "category": [
            "HERB", "ORNAMENTAL"
        ]
    };
    // Seed Saving dificulty levels
    var levels = {
        "levels": [
            "MODERATE", "MASTER"
        ]
    };
    
    // Find correct category
    var content = "";
    for(var i = 0; i < category.category.length; i++)
    {
        content += "<option";
        if (category.category[i] == ($('#_category').val()))
        {
             content += " selected='selected'";
        }
        content += ">" + category.category[i] + "</option>";
    }
    // Insert options in the select structure
    $("#category").append(content);
    
    // Find correct dificulty level
    var content = "";
    for(var i = 0; i < levels.levels.length; i++)
    {
        content += "<option";
        if (levels.levels[i] == ($('#_seed_sav_level').val()))
        {
             content += " selected='selected'";
        }
        content += ">" + levels.levels[i] + "</option>";
    }
    // Insert options in the select structure
    $("#seed_sav_level").append(content);
    
    // Auto complete
    /*$('#supplier').typeahead({
        name: 'States',
        remote: "{{ URL::to('users/autocomplete/%QUERY')}}"
    });*/
        
    // Resizes input file
    $('#files').css('width', $('.container').css('width'));
    $('#files').css('height', $('#drop_zone').css('height'));
});
</script>
<script>
  // Drag in drop handler as seen in HTML5ROCKS.COM
  function handleFileSelect(evt) {
    var files = evt.target.files; // FileList object

    // files is a FileList of File objects. List some properties.
    var output = [];
    for (var i = 0, f; f = files[i]; i++) {
      output.push('<li><strong>', escape(f.name), '</strong> (', f.type || 'n/a', ')',
                  '</li>');
    }
    document.getElementById('list').innerHTML = '<ul>' + output.join('') + '</ul>';
  }

  document.getElementById('files').addEventListener('change', handleFileSelect, false);
</script>
@if(isset($item) && count($item->images))
<script src="{{asset('assets/js/custom/image-gallery.js')}}"></script>
@endif
@stop