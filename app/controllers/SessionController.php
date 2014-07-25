<?php

/**
 * Controller responsible for session management (login, logout).
 */
class SessionController extends BaseController {

    /**
     * Show the form for creating a new resource. Will render the page in its
     * modal version if inside iframe; otherwise, renders normal version (default
     * layout).
     * 
     * @param boolean $iframe Flag that indicates whether iframe version should
     * be rendered or not.
     * 
     * @return Response
     */
    public function create($iframe = false)
    {
        // Logs out if user is logged in already
        Auth::logout();

        // Login
        $title = 'Login';

        if ($iframe) {
            return View::make('site/login-modal', compact('title'));
        }
        
        return View::make('site/login', compact('title'));
    }
    
    /**
     * Store a newly created resource in storage. Likewise <i>create</i> method,
     * uses a flag to indicate iframe usage. In case of errors, goes back to
     * login page. Otherwise, goes to the intended page (that requires auth)
     * 
     * @param boolean $iframe Flag that indicates iframe usage.
     * 
     * @return  Response
     */
    public function store($iframe = false){
        // Declare the rules for the form validation
        $rules = array(
            'email' => 'email|required',
            'password' => 'required'
        );

        // Validate the inputs
        $validator = Validator::make(Input::all(), $rules);

        // Check if the form validates with success (redirects otherwise)
        if (!$validator->passes())
        {
            if ($iframe)
                return Redirect::to('login/true')->withInput()->withErrors($validator);
            
            return Redirect::to('login')->withInput()->withErrors($validator);
        }
            
            
        $input = Input::all();

        $attempt = Auth::attempt([
            'email' => $input['email'],
            'password' => $input['password'],
        ]);

        // Authentication is correct
        if ($attempt)
            return Redirect::intended();
        
        // If page is inside an iframe
        if ($iframe)
            return Redirect::to('login/true')->withInput()->with('error', 'Incorrect email address and/or password.');
        
        return Redirect::to('login')->withInput()->with('error', 'Incorrect email address and/or password.');
    }
    
    /**
     * Destroy the session (i.e. logs user out). Redirect to home page.
     * 
     * @return Response
     */
    public function destroy()
    {
        // Close session
        Auth::logout();
        
        return Redirect::to('/');
    }
    
}