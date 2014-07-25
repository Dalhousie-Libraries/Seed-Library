<?php

class AdminUserController extends BaseController {

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
	*   Display all the registered users.
	*/
        public function getIndex()
        {
            // Title
            $title = 'Users';
            
            return View::make('admin/users/index', compact('title'));
        }
        
        /**
	 * Show the form for inserting a new user.
	 *
	 * @return Response
	 */
	public function getCreate()
	{
            // Title
            $title = 'Register a new user';

            // Show the page
            return View::make('admin/users/create_edit', compact('title'));
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
                    if(count(Input::get('roles')))
                        foreach(Input::get('roles') as $role)
                            $this->user->assignRole($role);
                    
                    // Redirect to the new user page
                    return Redirect::to('admin/users/create')->with('success', 'User registered successfully!');
                }

                // Redirect to the user create page
                return Redirect::to('admin/users/create')->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('admin/users/create')->withInput()->withErrors($validator);
	}
        
        /**
        * Renders update form.
        * @param int $id
        */
       public function getEdit($id)
       {
           // Title
            $title = 'Edit user record';

            // Find the user in the database
            $user = User::find($id);
            
            // Show the page
           return View::make('admin/users/create_edit')
                   ->with(compact('user'))
                   ->with(compact('title'));
       }
       
       /**
        * Updates user record.
        * @param int $id
        */
       public function postEdit($id)
       {
            // Find user's record in the database
            $user = User::find($id);
           
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
                    // Deletes roles relations, once they may be changed
                    $user->roles()->detach();
                    
                    // Assign selected roles to user
                    if(count(Input::get('roles')))
                        foreach(Input::get('roles') as $role)
                            $user->assignRole($role);
                    
                    // Redirect to the user page
                    return Redirect::to('admin/users/' . $id . '/edit')->with('success', 'User record updated successfully!');
                }

                // Redirect to the user create page
                return Redirect::to('admin/users/' . $id . '/edit')->withInput()->with('error', 'Something happened and we could not save the record into the database');
            }

            // Form validation failed
            return Redirect::to('admin/users/' . $id . '/edit')->withInput()->withErrors($validator);
       }
       
       /**
        * Remove the specified resource from storage.
        *
        * @param $id
        * @return Response
        */
       public function delete($id)
       {
           // Response message
            $response = array();
            $response['success'] = true;
            $response['message'] = 'OK';
           
           // Find image in the database
            $user = User::find($id);
           
           // Check if user exists
            if (is_null($user)) {
                $response['success'] = false;
                $response['message'] = 'User already deleted.';
            } else {
               // Delete it
                if (!$user->delete()) 
                {
                    $response['success'] = false;
                    $response['message'] = 'An error ocurred and the user record could not be deleted.';
                }
            }
           
            return $response;
       }
       
       /**
        * Remove all specified resources from storage.
        * 
        * @return Response
        */
       public function deleteAll()
       {
            // Responses array
            $responses = array();
           
            // Get post data
            $users = Input::get('users');
           
            // Try to delete each of the items
            foreach ($users as $userId)
            {
                // Response message
                $response = array();
                $response['success'] = true;
                $response['message'] = 'OK';

                // Find item in the database
                $user = User::find($userId);

                // Check if item exists
                if (is_null($user)) {
                    $response['success'] = false;
                    $response['message'] = 'User already deleted.';
                } else {
                    // Delete it
                    if (!$user->delete()) 
                    {
                        $response['success'] = false;
                        $response['message'] = 'An error ocurred and the user could not be deleted.';
                    }
                }
                
                // Add response to response array
                $responses[] = $response;
            } 
           
            return $responses;
       }
        
        /**
         *  Retrieve all users records formatted for DataTables.
         * 
         * @return Datatables JSON
         */
        public function getData()
        {
            $users = User::select(array('users.id', 'users.name', 'users.email', 'users.gardening_exp', 'users.seedsaving_exp'));

            return Datatables::of($users)

            ->add_column('actions', '<a href="{{{ URL::to(\'admin/users/\' . $id . \'/edit\' ) }}}" class="btn btn-default btn-xs iframe" >Edit</a> '
                                  . '<a href="#" class="btn btn-danger btn-xs delete" data-toggle="modal" data-target="#confirmDelete" data-link="{{{ URL::to(\'admin/users/\' . $id . \'/delete\') }}}" 
                                      data-title="Delete user" data-message="Are you sure you want to delete this user?">Delete</a>')

            ->edit_column('id', '<input type="checkbox" name="items" value="{{$id}}" />')

            ->make();
        }
        
        /**
         *  Retrieve all eligible borrowers name formatted for auto-completion.
         * 
         * @param String $name Specified name.
         * @return JSON
         */
        public function getBorrowers($name)
        {
            // Get all records from the database
            $names = DB::table('users')->where('name', 'like', '%' . htmlentities($name) . '%')
                                       ->lists('name');
            
            // Return result set in JSON format
            return json_encode($names);
        }
        
        /**
         *  Retrieve all donors's name formatted for auto-completion.
         * 
         * @param String $name Specified name.
         * @return JSON
         */
        public function getDonors($name)
        {
            // Get all records from the database
            $names = DB::table('users')->where('name', 'like', '%' . htmlentities($name) . '%')
                                       ->where('donor', '=', 1)
                                       ->lists('name');
            
            // Return result set in JSON format
            return json_encode($names);
        }
}
