<?php

class TagController extends BaseController {

	/**
        * Posting Model
        * @var Posting
        */
        protected $posting;

        /**
         * Inject the models.
         * @param Posting $posting
         */
        public function __construct(Posting $posting)
        {
            $this->posting = $posting;
        }
    
        /**
	*   Display all the registered postings.
	*/
        public function getIndex()
        {
            // Title
            $title = 'Postings';
            
            return View::make('cms/postings/index', compact('title'));
        }
        
        /**
	 * Show the form for inserting a new posting.
	 *
	 * @return Response
	 */
	public function getCreate()
	{
            // Title
            $title = 'Create a new posting';

            // Show the page
            return View::make('cms/postings/create_edit', compact('title'));
	}
        
        /**
	 * Store a newly created posting into the database.
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
                // Create the posting's data
                # General
                $this->posting->name            = Input::get('name');
                $this->posting->assumption_risk = Input::get('assumption_risk') ? true : false;
                $this->posting->gardening_exp   = Input::get('gardening_exp');
                $this->posting->seedsaving_exp  = Input::get('seedsaving_exp');
                $this->posting->volunteer       = Input::get('volunteer') ? true : false;
                $this->posting->mentor          = Input::get('mentor') ? true : false;
                $this->posting->donor           = Input::get('donor') ? true : false;
                # Address
                $this->posting->address         = Input::get('address');
                $this->posting->city            = Input::get('city');
                $this->posting->province        = Input::get('province');
                $this->posting->postal_code     = Input::get('postal_code');
                # Contact
                $this->posting->home_phone      = Input::get('home_phone');
                $this->posting->work_phone      = Input::get('work_phone');
                $this->posting->cell_phone      = Input::get('cell_phone');
                # System
                $this->posting->email           = Input::get('email');
                $this->posting->password        = Hash::make(Input::get('password'));
                
                // Was the posting created?
                if($this->posting->save())
                {
                    // Assign selected roles to posting
                    if(count(Input::get('roles')))
                        foreach(Input::get('roles') as $role)
                            $this->posting->assignRole($role);
                    
                    // Redirect to the new posting page
                    return Redirect::to('admin/postings/create')->with('success', 'Posting registered successfully!');
                }

                // Redirect to the posting create page
                return Redirect::to('admin/postings/create')->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('admin/postings/create')->withInput()->withErrors($validator);
	}
}
