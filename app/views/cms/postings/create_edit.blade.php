@extends('admin/layouts/modal')

@section('title')
{{{ $title }}} :: @parent
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

@section('styles')
<link rel="stylesheet" href="{{asset('assets/css/bootstrap-tagsinput.css')}}" />
@stop

@section('content')
    {{-- Edit Posting Form --}}
    <!-- Form beginning -->
    <form class="form-horizontal" method="post"
          action="@if (isset($posting)){{ URL::to('cms/postings/' . $posting->id) . '/edit'}} @endif" >
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->

        <!-- Title -->
        <div class="form-group {{{ $errors->has('title') ? 'has-error' : '' }}}">
            <div class="col-md-12">
                <label class="control-label" for="title">Title</label>
                <input class="form-control" type="text" name="title" id="title" value="{{{ Input::old('title', isset($posting) ? $posting->title : null) }}}" />
            </div>
        </div>
        <!-- ./ title -->
        
        <!-- Category -->
        <div class="form-group {{{ $errors->has('category') ? 'has-error' : '' }}}">
            <div class="col-md-12">
                <input type="hidden" name="_category" id="_category" value="{{{ Input::old('category', (isset($posting) && !is_null($posting->category)) ? $posting->category->id : null) }}}" />
                <label class="control-label" for="category">Category</label>
                <select class="form-control" name="category" id="category">
                    <option value="" selected="selected">-- Select a category --</option>
                </select>
            </div>
        </div>
        <!-- ./ category -->
        
        <!-- Tags -->
        <div class="form-group {{{ $errors->has('tags') ? 'has-error' : '' }}}">
            <div class="col-md-12">
                <label class="control-label" for="tags">Tags</label>
                <input class="form-control" type="text" name="tags" id="tags" data-role="tagsinput" placeholder="Add tags" 
                       @if(isset($posting) && count($posting->tags))  
                            value="@foreach($posting->tags as $tag){{ $tag->name }},@endforeach"
                       @endif/>
            </div>
        </div>
        <!-- ./ tags -->

        <!-- Content -->
        <div class="form-group">
            <div class="col-md-12">
                <label class="control-label" for="content">Content</label>
                <textarea class="form-control" name="content" id="content" rows="15">{{{ Input::old('content', isset($posting) ? $posting->content : null) }}}</textarea>
            </div>
        </div>
        <!-- ./ content -->
        
        <!-- Preview -->
        <div class="form-group">
            <div class="col-md-12">
                <label class="control-label" for="preview">Preview</label>
                <textarea class="form-control" name="preview" id="preview" rows="5">{{{ Input::old('preview', isset($posting) ? $posting->preview : null) }}}</textarea>
            </div>
        </div>
        <!-- ./ preview -->

        <!-- Form Actions -->
        <div class="form-group">
            <div class="col-md-12">
                <button type="submit" class="btn btn-success">Save changes</button>
                <button type="reset" class="btn btn-default">Reset</button>
                <button class="btn btn-cancel close_popup">Cancel</button>
            </div>
        </div>
        <!-- ./ form actions -->
    </form>
@stop

@section('scripts')
<script src="{{asset('assets/js/bootstrap/bootstrap-tagsinput.js')}}"></script>
<script src="{{asset('assets/js/tinymce/tinymce.min.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){
    var url = "";
    
    @if(isset($posting))
        var url = '../../../cms/categories/list';
    @else
        var url = '../../cms/categories/list';
    @endif
    
    // Set up tag input
    //$('#tags').tagsinput();
    
    // Get all categories
    $.get(url, function(categories) {
        // Find correct category
        var content = "";
        for(var i = 0; i < categories.length; i++)
        {
            content += "<option value='" + categories[i].id + "'";
            if (categories[i].id == ($('#_category').val()))
            {
                 content += " selected='selected'";
            }
            content += ">" + categories[i].name + "</option>";
        }
        // Insert options in the select structure
        $("#category").append(content);
    });
    
    tinymce.init({
        selector: "textarea",
        plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        style_formats: [
            {title: "Headers", items: [
                {title: "Header 1", format: "h1"},
                {title: "Header 2", format: "h2"},
                {title: "Header 3", format: "h3"},
                {title: "Header 4", format: "h4"},
                {title: "Header 5", format: "h5"},
                {title: "Header 6", format: "h6"}
            ]},
            {title: "Inline", items: [
                {title: "Bold", icon: "bold", format: "bold"},
                {title: "Italic", icon: "italic", format: "italic"},
                {title: "Underline", icon: "underline", format: "underline"},
                {title: "Strikethrough", icon: "strikethrough", format: "strikethrough"},
                {title: "Superscript", icon: "superscript", format: "superscript"},
                {title: "Subscript", icon: "subscript", format: "subscript"},
                {title: "Code", icon: "code", format: "code"}
            ]},
            {title: "Blocks", items: [
                {title: "Paragraph", format: "p"},
                {title: "Blockquote", format: "blockquote"},
                {title: "Div", format: "div"},
                {title: "Pre", format: "pre"}
            ]},
            {title: "Alignment", items: [
                {title: "Left", icon: "alignleft", format: "alignleft"},
                {title: "Center", icon: "aligncenter", format: "aligncenter"},
                {title: "Right", icon: "alignright", format: "alignright"},
                {title: "Justify", icon: "alignjustify", format: "alignjustify"}
            ]},
            {title: "Images", items: [
                {title: 'Image Left', selector: 'img', styles: {'float': 'left', 'padding': '5px 5px 5px 5px'}},
                {title: 'Image Right', selector: 'img', styles: {'float': 'right', 'padding': '5px 5px 5px 5px'}}
            ]}
        ]
    });
});
</script>
@stop