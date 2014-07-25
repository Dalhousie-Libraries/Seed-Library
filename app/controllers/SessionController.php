<?php

class SessionController extends BaseController {

    /**
     * Show the form for creating a new resource.
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
     * Store a newly created resource in storage.
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
     * Remove specified resource from storage.
     * 
     * @param int $id
     * @return Response
     */
    public function destroy()
    {
        // Close session
        Auth::logout();
        
        return Redirect::to('/');
    }
    
}