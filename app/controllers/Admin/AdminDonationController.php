<?php

/**
 * Controller responsible for all back-end features related to the 'donations' table.
 *
 * Include CRUD for 'donations' table, with greater permissions than the front-end.
 */
class AdminDonationController extends BaseController {

        /**
        * Donation Model
        * @var Donation
        */
        protected $donation;
    
	/**
         * Inject the models.
         * (the controller is always instatiated by the framework, so there's no 
         * need to call it in most situations)
         * 
         * @param Donation $donation
         */
        public function __construct(Donation $donation)
        {
            $this->donation = $donation;
        }
    
        /**
         * Display all the registered donations.
         * 
         * @return Response
	*/
        public function getIndex()
        {
            // Title
            $title = 'Donations';
            
            return View::make('admin/donations/index', compact('title'));
        }
        
        /**
         * Lists all requests that have been made by users.
         * 
         * @return Response
        */
       public function getRequests()
       {
           // Page title
           $title = 'Requested donations';
           
           // Return the response
           return View::make('admin/donations/requests', compact('title'));
       }
        
        /**
	 * Renders the form for creating a new donation.
	 *
	 * @return Response
	 */
	public function getCreate()
	{
            // Title
            $title = 'Create a new donation';

            // Show the page
            return View::make('admin/donations/create_edit', compact('title'));
	}
        
        /**
	 * Store a newly registered donation into the database.
	 *
	 * @return Response
	 */
	public function postCreate()
	{
            // Declare the rules for the form validation
            $rules = array(
                'seed_name' => 'required|min:3',
                'donor'     => 'required|min:3',
                'amount'    => 'required|integer',
            );

            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Validates the donor
                $donor = User::where('name', '=', Input::get('donor'))->first();
                
                // Throws an error if donor is not valid
                if (is_null($donor))
                    // Redirect to create page
                    return Redirect::to('admin/donations/create')->withInput()->with('error', 'The entered donor is not a valid one.');
                
                // Validates the item
                $itemData = explode(' - ', Input::get('seed_name'));
                if (is_null($itemData[2])) // If seed has no variety in its name
                    $item = Item::where('family', '=', $itemData[0])
                                ->where('species', '=', $itemData[1])
                                ->first();
                else
                    $item = Item::where('family', '=', $itemData[0])
                                ->where('species', '=', $itemData[1])
                                ->where('variety', '=', $itemData[2])
                                ->first();
                
                // Throws an error if item is not valid
                if (is_null($item))
                    // Redirect to create page
                    return Redirect::to('admin/donations/create')->withInput()->with('error', 'The entered item is not a valid one.');
                
                // Create the donation's data
                $this->donation->item_id          = $item->id;
                $this->donation->user_id          = $donor->id;
                $this->donation->type             = 'DONATION';
                $this->donation->amount           = Input::get('amount');
                $this->donation->description      = Input::get('description');
                $this->donation->accession_number = $this->donation->getAccessionNumber();
                $this->donation->checked_in_date  = Carbon::now(); // Does it have to be the current day? I guess not... CHANGE IT!

                // Was the donation created?
                if($this->donation->save())
                {
                    // Redirect to the new donation page
                    return Redirect::to('admin/donations/create')->with('success', 
                            'Donation registered successfully! Click <a href="'. URL::to('admin/donations/'.$this->donation->id.'/edit') .'">here</a> to add packets to it.');
                }

                // Redirect to the donation create page
                return Redirect::to('admin/donations/create')->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('admin/donations/create')->withInput()->withErrors($validator);
	}
        
        /**
         * Renders update form for a specific donation.
         * 
         * @param int $id Donation id.
         * @return Response
        */
       public function getEdit($id)
       {
           // Title
            $title = 'Edit donation record';

            // Find the donation in the database
            $donation = Donation::find($id);
            
            // Show the page
           return View::make('admin/donations/create_edit')
                   ->with(compact('donation'))
                   ->with(compact('title'));
       }
       
       /**
        * Updates donation record.
        * 
        * @param int $id Donation id.
        * @return Response
        */
       public function postEdit($id)
	{
           // Find donation's record in the database
            $donation = Donation::find($id);
           
            // Declare the rules for the form validation
            $rules = array(
                'seed_name' => 'required|min:3',
                'donor'     => 'required|min:3',
                'amount'    => 'required|integer',
            );

            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Validates the donor
                $donor = User::where('name', '=', Input::get('donor'))->first();
                
                // Throws an error if donor is not valid
                if (is_null($donor))
                    // Redirect to create page
                    return Redirect::to('admin/donations/' . $id . '/edit')->withInput()->with('error', 'The entered donor is not a valid one.');
                
                // Validates the item
                $itemData = explode(' - ', Input::get('seed_name'));
                if (is_null($itemData[2])) // If seed has no variety in its name
                    $item = Item::where('family', '=', $itemData[0])
                                ->where('species', '=', $itemData[1])
                                ->first();
                else
                    $item = Item::where('family', '=', $itemData[0])
                                ->where('species', '=', $itemData[1])
                                ->where('variety', '=', $itemData[2])
                                ->first();
                
                // Update the donation's data
                $donation->item_id     = $item->id;
                $donation->user_id     = $donor->id;
                $donation->amount      = Input::get('amount');
                $donation->description = Input::get('description');

                // Was the donation created?
                if($donation->save())
                {
                    // Redirect to the new donation page
                    return Redirect::to('admin/donations/' . $id . '/edit')->with('success', 'Donation updated successfully!');
                }

                // Redirect to the donation create page
                return Redirect::to('admin/donations/' . $id . '/edit')->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('admin/donations/' . $id . '/edit')->withInput()->withErrors($validator);
	}
        
        /**
	 * Renders form for checking in a donation.
	 *
         * @param int $id Donation id.
	 * @return Response
	 */
        public function getCheckIn($id)
        {
            // Page title
            $title = 'Check in donation';
            
            // Find the packet in the database
            $donation = Donation::find($id);
            
            return View::make('admin/donations/check-in')
                       ->with(compact('title'))
                       ->with(compact('donation'));
        }
        
        /**
	 * Store a newly checked in donation into the database.
	 *
	 * @return Response
	 */
	public function postCheckIn()
	{
            // Declare the rules for the form validation
            $rules = array(
                'donor'  => 'required|min:3',
            );
            
            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Check if donation exists
                $donation = Donation::find(Input::get('id'));
                if (is_null($donation))
                    // Redirect to create page
                    return Redirect::to('admin/donations/' . $donation->id . '/check_in')->with('error', 'The donation you requested does not exist.');
                
                // Update donation data
                $donation->checked_in_date  = Carbon::now();
                $donation->accession_number = $donation->getAccessionNumber();

                // Was the packet updated?
                if($donation->save())
                {
                    // Redirect to the packets records management page
                    return Redirect::to('admin/donations/' . $donation->id . '/check_in')->with('success', 'Check in registered successfully. Click ' . 
                            '<a href="' . URL::to('admin/donations/' . $donation->id . '/edit') . '">here</a> to add packets to this accession.');
                }

                // Redirect to the lending page in case of error
                return Redirect::to('admin/donations/' . $donation->id . '/check_in')->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('admin/donations/' . Input::get('id') . '/check_in')->withInput()->withErrors($validator);
	}
        
        /**
         * Retrieves all completed donations records formatted for DataTables.
         * 
         * @return JSON\Datatables
         */
        public function getDonatedPackets()
        {
            $donations = Donation::select(array('accessions.id', 'items.family', 'users.name', 'amount', 'checked_in_date', 'items.species', 'items.variety'))
                                 ->join('users', 'users.id', '=', 'accessions.user_id')
                                 ->join('items', 'items.id', '=', 'accessions.item_id')
                                 ->whereNotNull('checked_in_date')
                                 ->where('type', '=', 'donation');

            return Datatables::of($donations)

            ->edit_column('family', '{{ $family }} > {{ $species }} > {{ $variety }}')
             
            ->add_column('actions', '<a href="{{{ URL::to(\'admin/donations/\' . $id . \'/edit\' ) }}}" class="btn btn-default btn-xs iframe" >Edit</a>
                    
                ')

            ->remove_column('id')
            ->remove_column('species')
            ->remove_column('variety')

            ->make();
        }
        
        /**
         * Retrieve all requested donations records formatted for DataTables.
         * 
         * @return JSON\Datatables
         */
        public function getRequestedPackets()
        {
            $donations = Donation::select(array('accessions.id', 'items.family', 'users.name', 'amount', 'requested_at', 'items.species', 'items.variety'))
                                 ->join('users', 'users.id', '=', 'accessions.user_id')
                                 ->join('items', 'items.id', '=', 'accessions.item_id')
                                 ->whereNull('checked_in_date')
                                 ->where('type', '=', 'donation');

            return Datatables::of($donations)

            ->edit_column('family', '{{ $family }} > {{ $species }} > {{ $variety }}')
             
            ->add_column('actions', '<a href="{{{ URL::to(\'admin/donations/\' . $id . \'/check_in\' ) }}}" class="btn btn-default btn-xs iframe" >Check in</a> '
                                  . '<a href="{{{ URL::to(\'admin/donations/\' . $id . \'/edit\' ) }}}" class="btn btn-default btn-xs iframe" >Edit</a> '
                                  . '<a href="#" class="btn btn-danger btn-xs delete" data-toggle="modal" data-target="#confirmDelete" data-link="{{{ URL::to(\'donations/\' . $id . \'/delete\') }}}" 
                                      data-title="Delete request" data-message="Are you sure you want to delete this request?">Delete</a>')

            ->edit_column('id', '<input type="checkbox" name="items" value="{{$id}}" /> {{$id}}')
            ->remove_column('species')
            ->remove_column('variety')

            ->make();
        }
}