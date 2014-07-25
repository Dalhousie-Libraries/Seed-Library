@extends('site/layouts/default')

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
    <div class="page-header">
        <h1>
            {{{ $title }}}
        </h1>
    </div>

    {{-- Create User Form --}}
    <!-- Form beginning -->
    <form class="form-horizontal" method="post" autocomplete="off" 
          action="#" >
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <!-- ./ csrf token -->
        
        <!-- Tabs -->
        <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-general" data-toggle="tab">General</a></li>
                <li><a href="#tab-address" data-toggle="tab">Address</a></li>
                <li><a href="#tab-contact" data-toggle="tab">Contact</a></li>
                <li><a href="#tab-password" data-toggle="tab">Password</a></li>{{-- Add current password test --}}
        </ul>
	<!-- ./ tabs -->       
        
        <!-- Tabs General Info -->
        <div class="tab-content">
            <!-- General tab -->
            <div class="tab-pane active" id="tab-general">
                 <!-- User's email -->
                <div class="form-group {{{ $errors->has('email') ? 'has-error' : '' }}}">
                    <div class="col-md-12">
                        <label class="control-label" for="email">Email</label>
                        <input class="form-control" type="text" name="email" id="email" value="{{{ Input::old('email', isset($user) ? $user->email : null) }}}" readonly="readonly" />
                    </div>
                </div>
                <!-- ./ user's email -->
                
                <!-- User's name -->
                <div class="form-group {{{ $errors->has('name') ? 'has-error' : '' }}}">
                    <div class="col-md-12">
                        <label class="control-label" for="name">Name</label>
                        <input class="form-control" type="text" name="name" id="name" value="{{{ Input::old('name', isset($user) ? $user->name : null) }}}" />
                    </div>
                </div>
                <!-- ./ user's name -->
                
                <!-- User's gardening experience -->
                <div class="form-group">
                    <div class="col-md-12">
                        <input type="hidden" name="_gardening_exp" id="_gardening_exp" value="{{{ Input::old('gardening_exp', isset($user) ? $user->gardening_exp : null) }}}" />
                        <label class="control-label" for="gardening_exp">Gardening Experience</label>
                        <select class="form-control" name="gardening_exp" id="gardening_exp">
                        </select>
                    </div>
                </div>
                <!-- ./ user's gardening experience -->
                
                <!-- User's seed saving experience -->
                <div class="form-group">
                    <div class="col-md-12">
                        <input type="hidden" name="_seedsaving_exp" id="_seedsaving_exp" value="{{{ Input::old('seedsaving_exp', isset($user) ? $user->seedsaving_exp : null) }}}" />
                        <label class="control-label" for="seedsaving_exp">Seed Saving Experience</label>
                        <select class="form-control" name="seedsaving_exp" id="seedsaving_exp">
                        </select>
                    </div>
                </div>
                <!-- ./ user's seed saving experience -->
                
                <!-- Checkboxes -->
                <div class="form-group">
                    <div class="col-md-12">
                        <label class="control-label" for="volunteer">
                            <input type="checkbox" name="volunteer" id="volunteer" 
                                   @if (!empty(Input::old('volunteer'))) checked='checked'
                                   @elseif (isset($user)) {{{ $user->volunteer ? "checked='checked'" : null }}}
                                   @endif /> Volunteer&nbsp;
                        </label>
                        <label class="control-label" for="mentor">
                            <input type="checkbox" name="mentor" id="mentor"
                                   @if (!empty(Input::old('mentor'))) checked='checked'
                                   @elseif (isset($user)) {{{ $user->mentor ? "checked='checked'" : null }}}
                                   @endif /> Mentor&nbsp;
                        </label>
                        <label class="control-label" for="donor">
                            <input type="checkbox" name="donor" id="donor"
                                   @if (!empty(Input::old('donor'))) checked='checked'
                                   @elseif (isset($user)) {{{ $user->donor ? "checked='checked'" : null }}}
                                   @endif /> Donor&nbsp;
                        </label>
                    </div>
                </div>
                <!-- ./ checkboxes -->
                
            </div>
            <!-- ./ general tab -->
            
            <!-- Access info tab -->
            <div class="tab-pane" id="tab-password">
                <!-- User's password -->
                <div class="form-group {{{ $errors->has('password') ? 'has-error' : '' }}}">
                    <div class="col-md-12">
                        <label class="control-label" for="password">Password</label>
                        <input class="form-control" type="password" name="password" id="password" />
                    </div>
                </div>
                <!-- ./ user's password -->
                
                <!-- User's password confirmation -->
                <div class="form-group {{{ $errors->has('password_confirmation') ? 'has-error' : '' }}}">
                    <div class="col-md-12">
                        <label class="control-label" for="password_confirmation">Confirm password</label>
                        <input class="form-control" type="password" name="password_confirmation" id="password_confirmation" />
                    </div>
                </div>
                <!-- ./ user's password confirmation -->                
            </div>
            <!-- ./ access tab -->
            
            <!-- Address tab -->
            <div class="tab-pane" id="tab-address">
                <!-- User's address -->
                <div class="form-group {{{ $errors->has('address') ? 'has-error' : '' }}}">
                    <div class="col-md-12">
                        <label class="control-label" for="address">Address</label>
                        <input class="form-control" type="text" name="address" id="address" value="{{{ Input::old('address', isset($user) ? $user->address : null) }}}" />
                    </div>
                </div>
                <!-- ./ user's address -->
                
                <!-- User's city -->
                <div class="form-group {{{ $errors->has('city') ? 'has-error' : '' }}}">
                    <div class="col-md-12">
                        <label class="control-label" for="city">City</label>
                        <input class="form-control" type="text" name="city" id="city" value="{{{ Input::old('city', isset($user) ? $user->city : null) }}}" />
                    </div>
                </div>
                <!-- ./ user's city -->
                
                <!-- User's province -->
                <div class="form-group {{{ $errors->has('province') ? 'has-error' : '' }}}">
                    <div class="col-md-12">
                        <label class="control-label" for="province">Province</label>
                        <input type="hidden" name="_province" id="_province" value="{{{ Input::old('province', isset($user) ? $user->province : null) }}}" />
                        <select class="form-control" name="province" id="province">
                            <option value="NS" selected="selected">Nova Scotia</option>
                        </select>
                    </div>
                </div>
                <!-- ./ user's province -->
                
                <!-- User's postal code -->
                <div class="form-group {{{ $errors->has('postal_code') ? 'has-error' : '' }}}">
                    <div class="col-md-12">
                        <label class="control-label" for="postal_code">Postal Code</label>
                        <input class="form-control" type="text" name="postal_code" id="postal_code" value="{{{ Input::old('postal_code', isset($user) ? $user->postal_code : null) }}}" />
                    </div>
                </div>
                <!-- ./ user's postal code -->
                
            </div>
            <!-- ./ address tab -->
            
             <!-- Contact tab -->
            <div class="tab-pane" id="tab-contact">
                <!-- User's home phone -->
                <div class="form-group {{{ $errors->has('home_phone') ? 'has-error' : '' }}}">
                    <div class="col-md-12">
                        <label class="control-label" for="home_phone">Home Phone</label>
                        <input class="form-control" type="text" name="home_phone" id="home_phone" value="{{{ Input::old('home_phone', isset($user) ? $user->home_phone : null) }}}" />
                    </div>
                </div>
                <!-- ./ user's home phone -->
                
                <!-- User's work phone -->
                <div class="form-group {{{ $errors->has('work_phone') ? 'has-error' : '' }}}">
                    <div class="col-md-12">
                        <label class="control-label" for="work_phone">Work Phone</label>
                        <input class="form-control" type="text" name="work_phone" id="work_phone" value="{{{ Input::old('work_phone', isset($user) ? $user->work_phone : null) }}}" />
                    </div>
                </div>
                <!-- ./ user's work phone -->
                
                <!-- User's cell phone -->
                <div class="form-group {{{ $errors->has('cell_phone') ? 'has-error' : '' }}}">
                    <div class="col-md-12">
                        <label class="control-label" for="cell_phone">Cell Phone</label>
                        <input class="form-control" type="text" name="cell_phone" id="cell_phone" value="{{{ Input::old('cell_phone', isset($user) ? $user->cell_phone : null) }}}" />
                    </div>
                </div>
                <!-- ./ user's cell phone -->
                
            </div>
            <!-- ./ address tab -->
            
        </div>
        <!-- ./ tabs content -->

        <!-- Form Actions -->
        <div class="form-group">
            <div class="col-md-12">
                <button type="submit" class="btn btn-success">Update</button>
                <button type="reset" class="btn btn-default">Reset</button>
            </div>
        </div>
        <!-- ./ form actions -->
    </form>
@stop

@section('scripts')
<script type="text/javascript">
$(document).ready(function(){
    var content = ""; // DOM structure for the options
    //    
    // Pronvinces list
    var provinces = {
        "provinces": [
            { "abbr":"AB" , "fullname":"Alberta" }, 
            { "abbr":"BC" , "fullname":"British Columbia" }, 
            { "abbr":"MB" , "fullname":"Manitoba" },
            { "abbr":"NB" , "fullname":"New Brunswick" },
            { "abbr":"NL" , "fullname":"Newfoundland and Labrador" },
            { "abbr":"NT" , "fullname":"Northwest Territories" },
            { "abbr":"NU" , "fullname":"Nunavut" },
            { "abbr":"ON" , "fullname":"Ontario" },
            { "abbr":"PE" , "fullname":"Prince Edward Island" },
            { "abbr":"QC" , "fullname":"Quebec" },
            { "abbr":"SK" , "fullname":"Saskatchewan" },
            { "abbr":"YT" , "fullname":"Yukon" },            
        ]
    };
    //
    // Experience and Seed Saving levels
    var levels = {
        "levels": [
            "NONE", "SOME", "LOTS"
        ]
    };
    
    // Find correct province
    for(var i = 0; i < provinces.provinces.length; i++)
    {
        content += "<option value='" + provinces.provinces[i].abbr + "'";
        if (provinces.provinces[i].abbr == ($('#_province').val()))
        {
             content += " selected='selected'";
        }
        content += ">" + provinces.provinces[i].fullname + "</option>";
    }
    // Insert options in the select structure
    $("#province").append(content);
    
    // Find correct gardening level of experience
    content = "";
    for(var i = 0; i < levels.levels.length; i++)
    {
        content += "<option";
        if (levels.levels[i] == ($('#_gardening_exp').val()))
        {
             content += " selected='selected'";
        }
        content += ">" + levels.levels[i] + "</option>";
    }
    // Insert options in the select structure
    $("#gardening_exp").append(content);
    
    // Find correct seed saving level of experience
    content = "";
    for(var i = 0; i < levels.levels.length; i++)
    {
        content += "<option";
        if (levels.levels[i] == ($('#_seedsaving_exp').val()))
        {
             content += " selected='selected'";
        }
        content += ">" + levels.levels[i] + "</option>";
    }
    // Insert options in the select structure
    $("#seedsaving_exp").append(content);
});
</script>
@stop