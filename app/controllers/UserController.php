<?php

class UserController extends BaseController {

	/**
        * User Model
        * @var User
        */
        protected $user;

        /**
         * Inject the models.
         * @param User $user
         */
        public function __construct(User $user)
        {
            $this->user = $user;
        }
    
        /**
	*   Display user's profile page.
	*/
        public function getIndex()
        {
            // Title
            $title = 'My profile';
            
            // Find the user in the database
            $user = Auth::user();
            
            return View::make('site/users/profile')
                       ->with(compact('title'))
                       ->with(compact('user'));
        }
        
        /**
	 * Show the form for registering a new user.
	 *
	 * @return Response
	 */
	public function getCreate()
	{
            // Logs out if user is logged in already
            Auth::logout();
            
            // Title
            $title = 'Sign up';

            // Show the page
            return View::make('site/users/create', compact('title'));
	}
        
        /**
	 * Store a newly registered user into the database.
	 *
	 * @return Response
	 */
	public function postCreate()
	{
            // Declare the rules for the form validation
            $rules = array(
                # General                
                'name'                  => 'required|min:3',
                # Address
                'address'               => 'required|min:3',
                'city'                  => 'required|min:3',
                'postal_code'           => 'min:6',
                # Contact
                'email'                 => 'email|required',
                'home_phone'            => 'regex:/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/',
                'work_phone'            => 'regex:/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/',
                'cell_phone'            => 'regex:/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/',
                # System                
                'password'              => 'required|min:6|Confirmed',
                'password_confirmation' => 'required|min:6',
            );

            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Create the user's data
                # General
                $this->user->name            = Input::get('name');
                $this->user->assumption_risk = Input::get('assumption_risk') ? true : false;
                $this->user->gardening_exp   = Input::get('gardening_exp');
                $this->user->seedsaving_exp  = Input::get('seedsaving_exp');
                $this->user->volunteer       = Input::get('volunteer') ? true : false;
                $this->user->mentor          = Input::get('mentor') ? true : false;
                $this->user->donor           = Input::get('donor') ? true : false;
                # Address
                $this->user->address         = Input::get('address');
                $this->user->city            = Input::get('city');
                $this->user->province        = Input::get('province');
                $this->user->postal_code     = Input::get('postal_code');
                # Contact
                $this->user->home_phone      = Input::get('home_phone');
                $this->user->work_phone      = Input::get('work_phone');
                $this->user->cell_phone      = Input::get('cell_phone');
                # System
                $this->user->email           = Input::get('email');
                $this->user->password        = Hash::make(Input::get('password'));
                
                // Was the user created?
                if($this->user->save())
                {
                    // Assign selected roles to user
                    $this->user->assignRole('borrower', true);                    
                    
                    if ($this->user->donor)
                        $this->user->assignRole('donor', true);
                    
                    
                    // Redirect to the new user page (or activation page maybe?)
                    return Redirect::to('login')->with('success', "You've been registered successfully! Enter your login information to sign in.");
                }

                // Redirect to the user create page
                return Redirect::to('signup')->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('signup')->withInput()->withErrors($validator);
	}
       
       /**
        * Updates user record.
        */
       public function postEdit()
       {
            // Find user's record in the database
            $user = Auth::user();
           
            // Declare the rules for the form validation
            $rules = array(
                # General                
                'name'                  => 'required|min:3',
                # Address
                'address'               => 'required|min:3',
                'city'                  => 'required|min:3',
                'postal_code'           => 'min:6',
                # Contact
                'email'                 => 'email|required',
                'home_phone'            => 'regex:/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/',
                'work_phone'            => 'regex:/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/',
                'cell_phone'            => 'regex:/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/',
                # System                
                'password'              => 'min:6|Confirmed',
                'password_confirmation' => 'min:6',
            );

            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Create the user's data
                # General
                $user->name            = Input::get('name');
                if (!empty(Input::get('password')))
                    $user->password        = Hash::make(Input::get('password'));
                $user->assumption_risk = Input::get('assumption_risk') ? true : false;
                $user->gardening_exp   = Input::get('gardening_exp');
                $user->seedsaving_exp  = Input::get('seedsaving_exp');
                $user->volunteer       = Input::get('volunteer') ? true : false;
                $user->mentor          = Input::get('mentor') ? true : false;
                $user->donor           = Input::get('donor') ? true : false;
                # Address
                $user->address         = Input::get('address');
                $user->city            = Input::get('city');
                $user->province        = Input::get('province');
                $user->postal_code     = Input::get('postal_code');
                # Contact
                $user->email           = Input::get('email');
                $user->home_phone      = Input::get('home_phone');
                $user->work_phone      = Input::get('work_phone');
                $user->cell_phone      = Input::get('cell_phone');

                // Was the user created?
                if($user->save())
                {
                    // Assign selected roles to user (check this...)
                    $user->disallowRole('donor', true);
                    if ($user->donor)
                        $user->assignRole('donor', true);
                        
                    
                    // Redirect to the user page
                    return Redirect::to('user/profile')->with('success', 'Profile updated successfully!');
                }

                // Redirect to the user create page
                return Redirect::to('user/profile')->withInput()->with('error', 'Something happened and we could not save your profile into the database');
            }

            // Form validation failed
            return Redirect::to('user/profile')->withInput()->withErrors($validator);
       }
        
        /**
         *  Show all user's requests.
         */
        public function getRequests()
        {
            // Page title
            $title = 'My requests';
            
            // Return result set in JSON format
            return View::make('site/users/requests', compact('title'));
        }
        
        /**
         *  Show the retrieve password form.
         */
        public function getPassword()
        {
            // Page title
            $title = 'Retrieve password';
            
            return View::make('site/users/password', compact('title'));
        }
        
        /**
         *  Create a new password and send it to user's email.
         */
        public function postPassword()
        {
            // Declare the rules for the form validation
            $rules = array(
                'email' => 'email|required',
            );

            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Try to find user in the database
                $user = User::where('email', '=', Input::get('email'))->first();

                // Check if user exists
                if(is_null($user))
                    return Redirect::to('getpass')->with('error', 'It appears that this user does not exist.');
                
                if(!empty($user->password))
                    return Redirect::to('getpass')->with('error', 'Your account is already active. Click <a href="' . URL::to('user/profile') . '">here</a> to access your profile page');
                
                // Generate new password
                $activationLink = str_random(60);
                $user->remember_token = $activationLink;
                
                // Try to update user 
                if (!$user->save())
                    return Redirect::to('getpass')->with('error', 'Something wrong occurred and your activation link could not be generated.');
                
                // Send email
                $data = array(
                    'activationLink' => $activationLink, 
                    'name' => $user->name, 
                    'email' => $user->email
                );
                $success = Mail::send('emails.password', $data, function($message) use ($user)
                {
                    $message->to($user->email, $user->name)->subject('Account activation link');
                });
                
                // Error while sending email
                if (!$success)
                    return Redirect::to('getpass')->with('error', 'We\'re sorry, but we could not send an email with the activation link.');
                
                // Finally, success!
                return Redirect::to('login')->with('success', 'Your account activation link has been sent to your email address.');
            }
            
            // Form validation failed
            return Redirect::to('getpass')->withInput()->withErrors($validator);
        }
        
        /**
         * Activates a user account; renders password form.
         * @param type $token
         */
        public function activateAccount($token)
        {
			// Declare the rules for the form validation
            $rules = array(
            # System                
                'password'              => 'required|min:6|Confirmed',
                'password_confirmation' => 'required|min:6',
            );

            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Create the user's data
                # System
                $this->user->email           = Input::get('email');
                $this->user->password        = Hash::make(Input::get('password'));
                
                // Was the user created?
                if($this->user->save())
                {                  
                    // Redirect to the new user page (or activation page maybe?)
                    return Redirect::to('login')->with('success', "Your password has been updated! Enter your login information to sign in.");
                }

                // Redirect to the user create page
                return Redirect::to('signup')->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('signup')->withInput()->withErrors($validator);
        }
}
