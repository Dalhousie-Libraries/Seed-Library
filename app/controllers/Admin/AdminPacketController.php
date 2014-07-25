<?php

/**
 * Controller responsible for all back-end features related to the 'donations' table.
 *
 * Include CRUD for 'packets' table, with greater permissions than the front-end.
 */
class AdminPacketController extends BaseController {

        /**
        * Packet Model
        * @var Packet
        */
        protected $packet;
    
	/**
         * Inject the models.
         * (the controller is always instatiated by the framework, so there's no 
         * need to call it in most situations)
         * 
         * @param Packet $packet
         */
       public function __construct(Packet $packet)
       {
           $this->packet = $packet;
       }
       
       /**
        * Lists all lendings that have been made.
        * 
        * @return Response
        */
       public function getIndex()
       {
           // Page title
           $title = 'Checked out packets';
           
           // Return the response
           return View::make('admin/lendings/index', compact('title'));
       }
       
       /**
        * Lists all requests that have been made.
        * 
        * @return Response
        */
       public function getRequests()
       {
           // Page title
           $title = 'Requested packets';
           
           // Return the response
           return View::make('admin/lendings/requests', compact('title'));
       }
       
       /**
        * Lists all packets with filtering.
        * 
        * @return Response
        */
       public function getSeedsList()
       {
           // Page title
           $title = 'Seeds list';
           
           // Return the response
           return View::make('admin/lendings/seeds-list', compact('title'));
       }
        
        /**
	 * Store a newly registered packet into the database.
	 *
         * @param String $type Accession type.
	 * @return Response
	 */
	public function postCreate($type)
	{
            if($type == "return")
                $accessionType = "returns";
            else
                $accessionType = "donations";
            
            // Declare the rules for the form validation
            $rules = array(
                'accession_id_new'      => 'required|integer',
                'date_harvest_new'      =>  array('required', 'date_format:"Y-m-d"'),
                'grow_location_new'     => 'required|min:3',
                'physical_location_new' => 'required|min:3',
                'germination_ratio_new' => 'required|numeric',
                'pct_amount_new'        => 'required|integer',
            );

            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Create the packet's data
                $this->packet->accession_id      = Input::get('accession_id_new');
                $this->packet->date_harvest      = Input::get('date_harvest_new');
                $this->packet->grow_location     = Input::get('grow_location_new');
                $this->packet->physical_location = Input::get('physical_location_new');
                $this->packet->germination_ratio = Input::get('germination_ratio_new');
                $this->packet->amount            = Input::get('pct_amount_new');

                // Was the packet created?
                if($this->packet->save())
                {
                    return Redirect::to('admin/' . $accessionType . '/edit/' . Input::get('accession_id_new'))->with('success', 
                                'Packet registered successfully!');
                }

                // Redirect to donations page
                return Redirect::to('admin/' . $accessionType . '/edit/' . Input::get('accession_id_new'))->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('admin/' . $accessionType . '/edit/' . Input::get('accession_id_new'))->withInput()->withErrors($validator)->with('new_packet_error', true);
	}
        
        /**
         * Updates a specific packet record.
         * 
         * @param int $id Packet id.
         * @param String $type Accession type.
         * @return Response
        */
        public function postEdit($id, $type)
        {
            if($type == "return")
                $accessionType = "returns";
            else
                $accessionType = "donations";
            
            // Find packet's record in the database
            $packet = Packet::find($id);
            
            // Declare the rules for the form validation
            $rules = array(
                'date_harvest'      =>  array('required', 'date_format:"Y-m-d"'),
                'grow_location'     => 'required|min:3',
                'physical_location' => 'required|min:3',
                'germination_ratio' => 'required|numeric',
                'pct_amount'        => 'required|integer',
            );

            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Create the packet's data
                $packet->accession_id      = Input::get('accession_id');
                $packet->date_harvest      = Input::get('date_harvest');
                $packet->grow_location     = Input::get('grow_location');
                $packet->physical_location = Input::get('physical_location');
                $packet->germination_ratio = Input::get('germination_ratio');
                $packet->amount            = Input::get('pct_amount');

                // Was the packet created?
                if($packet->save())
                {
                    // Redirect to donations page
                    return Redirect::to('admin/' . $accessionType . '/edit/' . Input::get('accession_id'))->with('success', 'Packet updated successfully!');
                }

                // Redirect to donations page
                return Redirect::to('admin/' . $accessionType . '/edit/' . Input::get('accession_id'))->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('admin/' . $accessionType . '/edit/' . Input::get('accession_id'))->withInput()->withErrors($validator)->with('old_packet_error', $id);
	}
        
        /**
	 * Renders form for lending a packet.
	 *
         * @param int $id Packet id.
	 * @return Response
	 */
        public function getLending($id)
        {
            // Page title
            $title = 'Lend packet';
            
            // Find the packet in the database
            $packet = Packet::find($id);
            
            return View::make('admin/lendings/lend')
                       ->with(compact('title'))
                       ->with(compact('packet'));
        }
        
        /**
	 * Stores a newly registered lending into the database.
	 *
	 * @return Response
	 */
	public function postLending()
	{
            // Declare the rules for the form validation
            $rules = array(
                'borrower'  => 'required|min:3',
            );
            
            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Check if packet exists
                $packet = Packet::find(Input::get('id'));
                if (is_null($packet))
                    // Redirect to create page
                    return Redirect::to('admin/packets/' . $packet->id . '/lend')->with('error', 'The packet you requested does not exist.');
                
                // Validates the borrower
                $borrower = User::where('name', '=', Input::get('borrower'))->first();
                
                // Throws an error if donor is not valid
                if (is_null($borrower))
                    // Redirect to create page
                    return Redirect::to('admin/packets/' . $packet->id . '/lend')->withInput()->with('error', 'The entered borrower is not a valid one.');
                
                // Update packet data
                $packet->borrower_id      = $borrower->id;
                $packet->checked_out_date = Carbon::now();

                // Was the packet updated?
                if($packet->save())
                {
                    // Redirect to the packets records management page
                    return Redirect::to('admin/packets/' . $packet->id . '/lend')->with('success', 'Lending registered successfully.');
                }

                // Redirect to the lending page in case of error
                return Redirect::to('admin/packets/' . $packet->id . '/lend')->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('admin/packets/' . Input::get('id') . '/lend')->withInput()->withErrors($validator);
	}
        
        /**
         * Retrieves all packets records formatted for DataTables.
         * 
         * @param String $keyword Search terms.
         * @return JSON\Datatables
         */        
        public function getListByName($keyword = false)
        {            
            // Query the database - LISTING ONLY AVAILABLE PACKETS!!!
            $packets = Packet::select(array('packets.id', 'items.family', 'packets.amount', 'packets.germination_ratio', 'packets.date_harvest', 'packets.grow_location', 'items.species', 'items.variety'))
                             ->join('accessions', 'accessions.id', '=', 'packets.accession_id')
                             ->join('items', 'items.id', '=', 'accessions.item_id')
                             ->whereNull('borrower_id')
                             ->where(function($query) use ($keyword)
                             {
                                // Change query according to entered search
                                $params = explode('-', $keyword);
                                
                                // Remove white spaces
                                foreach($params as &$param)
                                    $param = rtrim(ltrim($param));
                                
                                // Assemble query
                                if(count($params) == 2) 
                                {   // Search was a complete seed name (without variety)
                                    $query->where('items.family', 'LIKE', "%".$params[0]."%")
                                          ->where('items.species', 'LIKE', "%".$params[1]."%");
                                } elseif(count($params) == 3) 
                                {   // Search was a complete seed name (including variety)
                                    $query->where('items.family', 'LIKE', "%".$params[0]."%")
                                          ->where('items.species', 'LIKE', "%".$params[1]."%")
                                          ->where('items.variety', 'LIKE', "%".$params[2]."%");
                                } else 
                                {   // Search was only a keyword                                 
                                    $query->where('items.family', 'LIKE', "%".$keyword."%")
                                          ->orWhere('items.species', 'LIKE', "%".$keyword."%")
                                          ->orWhere('items.variety', 'LIKE', "%".$keyword."%");
                                }
                             });

            return Datatables::of($packets)

            ->edit_column('family', '{{ $family }} > {{ $species }} > {{ $variety }}')                    
            ->edit_column('germination_ratio', '{{number_format((float)$germination_ratio, 1, \'.\', \'\')}}')             
            ->add_column('actions', '<a href="{{{ URL::to(\'admin/packets/\' . $id . \'/lend\' ) }}}" class="btn btn-default btn-xs iframe" >Lend</a> '. 
                                    '<a href="#" class="btn btn-danger btn-xs delete" data-toggle="modal" data-target="#confirmDelete" data-link="{{{ URL::to(\'admin/packets/\' . $id . \'/delete\') }}}" 
                                                 data-title="Delete packet" data-message="Are you sure you want to delete this packet? (You cannot undo this action)">Delete</a>')
            ->edit_column('id', '<input type="checkbox" name="items" value="{{$id}}" /> {{$id}}')
                    
            ->remove_column('species')
            ->remove_column('variety')

            ->make();
        }
        
         /**
         * Retrieves all lending records formatted for DataTables.
         * 
         * @return JSON\Datatables
         */
        public function getLentPackets()
        {
            // Queries the database
            $packets = Packet::select(array('packets.id', 'items.family', 'users.name', 'packets.checked_out_date', 'items.species', 'items.variety', 'packets.accession_id'))
                             ->join('accessions', 'accessions.id', '=', 'packets.accession_id')
                             ->join('items', 'items.id', '=', 'accessions.item_id')
                             ->join('users', 'users.id', '=', 'packets.borrower_id')
                             ->whereNotNull('checked_out_date')
                             ->whereNotNull('borrower_id');

            return Datatables::of($packets)

            ->edit_column('family', '{{ $family }} > {{ $species }} > {{ $variety }}')
                    
            //->add_column('packets.id', '<a href="{{{ URL::to(\'admin/packets/\' . $id . \'/lend\' ) }}}" class="btn btn-default btn-xs iframe" >Lend</a>')

            ->remove_column('species')
            ->remove_column('variety')
            ->remove_column('accession_id')

            ->make();
        }
        
        /**
         * Retrieve all requests records formatted for DataTables.
         * 
         * @return JSON\Datatables
         */
        public function getRequestedPackets()
        {
            // Queries the database
            $packets = Packet::select(array('packets.id', 'items.family', 'users.name', 'packets.requested_at', 'items.species', 'items.variety', 'packets.accession_id'))
                             ->join('accessions', 'accessions.id', '=', 'packets.accession_id')
                             ->join('items', 'items.id', '=', 'accessions.item_id')
                             ->join('users', 'users.id', '=', 'packets.borrower_id')
                             ->whereNull('checked_out_date')
                             ->whereNotNull('borrower_id');

            return Datatables::of($packets)

            ->edit_column('family', '{{ $family }} > {{ $species }} > {{ $variety }}')
                    
            ->add_column('packets.id', '<a href="{{{ URL::to(\'admin/packets/\' . $id . \'/lend\' ) }}}" class="btn btn-default btn-xs iframe" >Check out</a> '
                    .                  '<a href="#" class="btn btn-danger btn-xs delete" data-toggle="modal" data-target="#confirmDelete" data-link="{{ URL::to(\'packet/\' . $id . \'/delete\')}}"'
                    .                               'data-title="Delete request" data-message="Are you sure you want to delete this request?">Delete</a>')

            ->edit_column('id', '<input type="checkbox" name="items" value="{{$id}}" /> {{$id}}')
            
            ->remove_column('species')
            ->remove_column('variety')
            ->remove_column('accession_id')

            ->make();
        }
        
        /**
         * Displays the history of a specific packet.
         * 
         * @param int $id Packet id.
         * @return Response
         */
        public function showPacketHistory($id)
        {
            // Title 
            $title = 'Packet history';
            
            // Returns 404 error if packet not found
            if (is_null(Packet::find($id)))
                return App::abort(404, 'Packet not found');
            
            return View::make('admin/packets/history')
                        ->with(compact('id'))
                        ->with(compact('title'));
        }
        
        /**
        * Cancel a packet request.
        *
        * @param int $id Packet id.
        * @return Response
        */
       public function delete($id)
       {
            // Response message
            $response = array();
            $response['success'] = true;
            $response['message'] = 'OK';
            
            // Find packet in the database
            $packet = Packet::find($id);
           
            // Check if donation exists
            if (is_null($packet)) {
                $response['success'] = false;
                $response['message'] = 'Request already canceled.';
            } else {
                // Save changes
                if (!$packet->delete()) 
                {
                    $response['success'] = false;
                    $response['message'] = 'An error ocurred and the request could not be canceled.';
                }
            }
           
            return $response;
       }
       
       /**
        * Removes all specified resources from storage.
        * 
        * @return Response
        */
       public function deleteAll()
       {
            // Responses array
            $responses = array();
           
            // Get post data
            $packets = Input::get('packets');
           
            // Try to delete each of the packets
            foreach ($packets as $packetId)
            {
                // Response message
                $response = array();
                $response['success'] = true;
                $response['message'] = 'OK';

                // Find packet in the database
                $packet = Packet::find($packetId);

                // Check if packet exists
                if (is_null($packet)) {
                    $response['success'] = false;
                    $response['message'] = 'Packet already deleted.';
                } else {
                    // Delete it
                    if (!$packet->delete()) 
                    {
                        $response['success'] = false;
                        $response['message'] = 'An error ocurred and the packet could not be deleted.';
                    }
                }
                
                // Add response to response array
                $responses[] = $response;
            }           
            return $responses;
       }
}