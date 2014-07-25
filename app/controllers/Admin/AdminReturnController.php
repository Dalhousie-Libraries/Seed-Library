<?php

class AdminReturnController extends BaseController {

        /**
        * Return Model
        * @var Return
        */
        protected $return;
    
	/**
        * Inject the models.
        * @param Returning $return
        */
       public function __construct(Returning $return)
       {
           $this->return = $return;
       }
    
        /**
	*   Display all the registered returns.
	*/
        public function getIndex()
        {
            // Title
            $title = 'Returns';
            
            return View::make('admin/returns/index', compact('title'));
        }
        
        /**
	 * Show the form for registering a new return.
	 *
	 * @return Response
	 */
	public function getCreate()
	{
            // Title
            $title = 'Return seed';

            // Show the page
            return View::make('admin/returns/create_edit', compact('title'));
	}
        
        /**
        * Lists all requests that have been made.
        * 
        * @return Response
        */
       public function getRequests()
       {
           // Page title
           $title = 'Requested returns';
           
           // Return the response
           return View::make('admin/returns/requests', compact('title'));
       }
        
        /**
	 * Store a newly registered return into the database.
	 *
	 * @return Response
	 */
	public function postCreate()
	{
            // Declare the rules for the form validation
            $rules = array(
                'returner'      => 'required|min:3',
                'parent_packet' => 'required|integer',
                'amount'        => 'required|integer',
            );

            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Validates the returner
                $returner = User::where('name', '=', Input::get('returner'))->first();
                
                // Throws an error if returner is not valid
                if (is_null($returner))
                    // Redirect to create page
                    return Redirect::to('admin/returns/create')->withInput()->with('error', 'The entered returner is not a valid one.');
                
                // Validates the parent packet
                $packet = Packet::where('packets.id', '=', Input::get('parent_packet'))
                                ->where('packets.borrower_id', '=', $returner->id)
                                ->first();
                
                // Throws an error if packet is not valid
                if (is_null($packet))
                    // Redirect to create page
                    return Redirect::to('admin/returns/create')->withInput()->with('error', 'The entered packet is not a valid one.');
                
                // Create the return's data                
                if(!is_null($packet->accession) && !is_null($packet->accession->item))
                    $this->return->item_id      = $packet->accession->item->id;
                $this->return->user_id          = $returner->id;
                $this->return->parent_id        = $packet->id;
                $this->return->type             = 'RETURN';
                $this->return->amount           = Input::get('amount');
                $this->return->description      = Input::get('description');
                $this->return->accession_number = $this->return->getAccessionNumber();
                $this->return->checked_in_date  = Carbon::now(); // Does it have to be the current day? I guess not... CHANGE IT!

                // Was the return created?
                if($this->return->save())
                {
                    // Redirect to the new return page
                    return Redirect::to('admin/returns/create')->with('success', 
                            'Return registered successfully! Click <a href="'. URL::to('admin/returns/'.$this->return->id.'/edit') .'">here</a> to add packets to it.');
                }

                // Redirect to the return create page
                return Redirect::to('admin/returns/create')->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('admin/returns/create')->withInput()->withErrors($validator);
	}
        
        /**
        * Renders update form.
        * @param int $id
        */
       public function getEdit($id)
       {
           // Title
            $title = 'Edit return record';

            // Find the return in the database
            $return = Returning::find($id);
            
            // Show the page
           return View::make('admin/returns/create_edit')
                   ->with(compact('return'))
                   ->with(compact('title'));
       }
       
       /**
        * Updates user record.
        * @param int $id
        */
       public function postEdit($id)
	{
           // Find return's record in the database
            $return = Returning::find($id);
           
            // Declare the rules for the form validation
            $rules = array(
                'returner'      => 'required|min:3',
                'parent_packet' => 'required|integer',
                'amount'        => 'required|integer',
            );

            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Validates the returner
                $returner = User::where('name', '=', Input::get('returner'))->first();
                
                // Throws an error if returner is not valid
                if (is_null($returner))
                    // Redirect to create page
                    return Redirect::to('admin/returns/' . $id . '/edit')->withInput()->with('error', 'The entered returner is not a valid one.');
                
                // Validates the parent packet
                $packet = Packet::where('packets.id', '=', Input::get('parent_packet'))
                                ->where('packets.borrower_id', '=', $returner->id)
                                ->first();
                
                // Throws an error if packet is not valid
                if (is_null($packet))
                    // Redirect to create page
                    return Redirect::to('admin/returns/' . $id . '/edit')->withInput()->with('error', 'The entered packet is not a valid one.');
                
                // Update the return's data
                if(!is_null($packet->accession) && !is_null($packet->accession->item))
                    $return->item_id = $packet->accession->item->id;
                $return->user_id     = $returner->id;
                $return->parent_id   = $packet->id;
                $return->amount      = Input::get('amount');
                $return->description = Input::get('description');

                // Was the return created?
                if($return->save())
                {
                    // Redirect to the new return page
                    return Redirect::to('admin/returns/' . $id . '/edit')->with('success', 'Returning updated successfully!');
                }

                // Redirect to the return create page
                return Redirect::to('admin/returns/' . $id . '/edit')->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('admin/returns/' . $id . '/edit')->withInput()->withErrors($validator);
	}
        
        /**
	 * Show the form for checking in a return.
	 *
	 * @return Response
	 */
        public function getCheckIn($id)
        {
            // Page title
            $title = 'Check in return';
            
            // Find the packet in the database
            $return = Donation::find($id);
            
            return View::make('admin/returns/check-in')
                       ->with(compact('title'))
                       ->with(compact('return'));
        }
        
        /**
	 * Store a newly checked in return into the database.
	 *
	 * @return Response
	 */
	public function postCheckIn()
	{
            // Declare the rules for the form validation
            $rules = array(
                'returner'  => 'required|min:3',
            );
            
            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Check if return exists
                $return = Donation::find(Input::get('id'));
                if (is_null($return))
                    // Redirect to create page
                    return Redirect::to('admin/returns/' . $return->id . '/check_in')->with('error', 'The return you requested does not exist.');
                
                // Update return data
                $return->checked_in_date  = Carbon::now();
                $return->accession_number = $return->getAccessionNumber();

                // Was the packet updated?
                if($return->save())
                {
                    // Redirect to the packets records management page
                    return Redirect::to('admin/returns/' . $return->id . '/check_in')->with('success', 'Check in registered successfully. Click ' . 
                            '<a href="' . URL::to('admin/returns/' . $return->id . '/edit') . '">here</a> to add packets to this accession.');
                }

                // Redirect to the lending page in case of error
                return Redirect::to('admin/returns/' . $return->id . '/check_in')->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('admin/returns/' . Input::get('id') . '/check_in')->withInput()->withErrors($validator);
	}
        
        /**
         *  Retrieve all returns records formatted for DataTables.
         * 
         * @return Datatables JSON
         */
        public function getUserPackets($name)
        {
            // Queries the database
            $returns = Packet::select(array('packets.id', 'items.family', 'items.species', 'items.variety', 'packets.amount', 'packets.germination_ratio', 'packets.date_harvest', 'packets.grow_location'))
                             ->join('accessions', 'accessions.id', '=', 'packets.accession_id')
                             ->join('users', 'users.id', '=', 'packets.borrower_id')
                             ->join('items', 'items.id', '=', 'accessions.item_id')
                             ->where('users.name', '=', $name)
                             ->whereNotNull('borrower_id')
                             ->whereNotNull('checked_out_date');

            return Datatables::of($returns)

            ->edit_column('family', '{{ $family }} > {{ $species }} > {{ $variety }}')
                    
            ->edit_column('germination_ratio', '{{number_format((float)$germination_ratio, 1, \'.\', \'\')}}')

            ->remove_column('species')
            ->remove_column('variety')

            ->make();
        }
        
        /**
         *  Retrieve all returns records formatted for DataTables.
         * 
         * @return Datatables JSON
         */
        public function getReturnedPackets()
        {
            $returns = Returning::select(array('accessions.id', 'items.family', 'users.name', 'amount', 'checked_in_date', 'items.species', 'items.variety'))
                                ->join('users', 'users.id', '=', 'accessions.user_id')
                                ->join('items', 'items.id', '=', 'accessions.item_id')
                                ->whereNotNull('checked_in_date')
                                ->where('type', '=', 'return');

            return Datatables::of($returns)

            ->edit_column('family', '{{ $family }} > {{ $species }} > {{ $variety }}')
             
            ->add_column('actions', '<a href="{{{ URL::to(\'admin/returns/\' . $id . \'/edit\' ) }}}" class="btn btn-default btn-xs iframe" >Edit</a>
                    
                ')

            ->remove_column('id')
            ->remove_column('species')
            ->remove_column('variety')

            ->make();
        }
        
        /**
         *  Retrieve all return requests records formatted for DataTables.
         * 
         * @return Datatables JSON
         */
        public function getRequestedPackets()
        {
            $returns = Returning::select(array('accessions.id', 'items.family', 'users.name', 'amount', 'requested_at', 'items.species', 'items.variety'))
                                ->join('users', 'users.id', '=', 'accessions.user_id')
                                ->join('items', 'items.id', '=', 'accessions.item_id')
                                ->whereNull('checked_in_date')
                                ->where('type', '=', 'return');

            return Datatables::of($returns)

            ->edit_column('family', '{{ $family }} > {{ $species }} > {{ $variety }}')
             
            ->add_column('actions', '<a href="{{{ URL::to(\'admin/returns/\' . $id . \'/check_in\' ) }}}" class="btn btn-default btn-xs iframe" >Check in</a> '
                                  . '<a href="{{{ URL::to(\'admin/returns/\' . $id . \'/edit\' ) }}}" class="btn btn-default btn-xs iframe" >Edit</a> '
                                  . '<a href="#" class="btn btn-danger btn-xs delete" data-toggle="modal" data-target="#confirmDelete" data-link="{{{ URL::to(\'returns/\' . $id . \'/delete\') }}}" 
                                                 data-title="Delete request" data-message="Are you sure you want to delete this request?">Delete</a>')

            ->edit_column('id', '<input type="checkbox" name="items" value="{{$id}}" /> {{$id}}')
            ->remove_column('species')
            ->remove_column('variety')

            ->make();
        }
}