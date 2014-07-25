<?php

/**
 * Controller responsible for all front-end features related to the 'donations' table.
 *
 * Include CRUD for 'packets' table.
 */
class PacketController extends BaseController {

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
         * Retrieves all packets of a specific item (lists only available packets).
         * 
         * @param int $itemId Specific item id.
         * @return JSON
         */
        public function getItemPackets($itemId)
        {
            // Queries the database
            return 
            Packet::with(['accession', 'accession.item'])
                  ->join('accessions', 'accessions.id', '=', 'packets.accession_id')
                  ->join('items', 'accessions.item_id', '=', 'items.id')
                  ->where('accessions.item_id', '=', $itemId)
                  ->whereNull('borrower_id')
                  ->get(array('packets.id', 'packets.amount', 'packets.germination_ratio', 'packets.date_harvest', 'packets.grow_location', 'items.family', 'items.species', 'items.variety'));
        }
        
        /**
         * For a specific user, retrieves all requests of that user.
         * 
         * @param String $requestType Type of request.
         * @param int $id User id.
         * @return JSON
         */
        public function getRequests($requestType, $id) 
        {
            switch($requestType)
            {
                case 'lendings':
                    return 
                    Packet::with(['borrower'])
                          ->join('accessions', 'accessions.id', '=', 'packets.accession_id')
                          ->join('items', 'accessions.item_id', '=', 'items.id')
                          ->where('borrower_id', '=', $id)
                          ->orderBy('packets.requested_at', 'DESC')
                          ->get(array('packets.id', 'packets.amount', 'packets.requested_at', 'packets.reserved_until', 'items.family', 'items.species', 'items.variety',
                                      'packets.checked_out_date', 'packets.germination_ratio', 'packets.date_harvest', 'packets.grow_location'));
                    
                case 'donations':
                    return 
                    Donation::join('items', 'accessions.item_id', '=', 'items.id')
                            ->where('user_id', '=', $id)
                            ->where('type', '=', 'DONATION')
                            ->orderBy('accessions.requested_at', 'DESC')
                            ->orderBy('accessions.checked_in_date', 'DESC')
                            ->get(array('accessions.id', 'accessions.amount', 'accessions.requested_at', 'accessions.checked_in_date', 'items.family', 'items.species', 'items.variety'));
                    
                case 'returns':
                    return 
                    Donation::join('items', 'accessions.item_id', '=', 'items.id')
                            ->where('user_id', '=', $id)
                            ->where('type', '=', 'RETURN')
                            ->orderBy('accessions.requested_at', 'DESC')
                            ->orderBy('accessions.checked_in_date', 'DESC')
                            ->get(array('accessions.id', 'accessions.amount', 'accessions.requested_at', 'accessions.checked_in_date', 'items.family', 'items.species', 'items.variety'));

                default:
                    return array();
            }
        }
        
        /**
         * Returns the history of a packet.
         * 
         * @param int $id Packet id
         * @return JSON
         */
        public function getPacketHistory($id) {
            // Initializes and array
            $packets = array();
            
            // Finds the packet
            $packet = Packet::find($id);
            if(!is_null($packet)) 
            {
                do
                {
                    $packets[] = $packet;
                    $packet->borrower;
                    $packet->accession->item;
                    $packet->accession->user;
                    $packet = $packet->accession->parent;                    
                } while(!is_null($packet));
                
                // Return first record (cause it's going to contain all data)
                return $packets[0];
            }
            
            return null;
        }
        
        /**
         *  Renders packet history page.
         * 
         * @param int $id Packet id.
         * @return Response
         * 
         * @todo Finish implementation, create view (similar to admin version).
         */
        public function showPacketHistory($id)
        {
            // Title 
            $title = 'Packet history';
            
            return View::make('', compact('title'));
        }
        
        /**
         * Static function that is called every time a page is loaded.
         * (could be called once a day, for example) - should be a cronjob. 
         * 
         * @todo Create a command, move this method to the created command and 
         * create a cronjob to execute it once a day.
         */
        public static function releaseOverduePackets()
        {
            // Check whether reserved packets are overdue or not
            $overduePackets = Packet::whereNotNull('borrower_id')
                                    ->whereNull('checked_out_date')
                                    ->where('reserved_until', '<', Carbon::now()->format('Y-m-d'))
                                    ->get();

            // Release each packet
            foreach ($overduePackets as $packet) 
            {
                $packet->borrower_id = null;
                $packet->requested_at = null;
                $packet->reserved_until = null;

                try {
                    $packet->save();
                } catch(\Exception $e) {}
            }
        }
        
        /**
        * Cancel packet request.
        *
        * @param $id int Packet id.
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
                // Verify logged user information
                if(Auth::user()->id !== $packet->borrower_id)
                {
                    $response['success'] = false;
                    $response['message'] = 'You do not have permission to cancel this request.';
                }
                else {
                    // Reset packet
                    $packet->borrower_id = null;
                    $packet->requested_at = null;
                    $packet->reserved_until = null;
                    
                    // Save changes
                    if (!$packet->save()) 
                    {
                        $response['success'] = false;
                        $response['message'] = 'An error ocurred and the request could not be canceled.';
                    }
                }
            }
           
            return $response;
       }
       
       /**
        * Cancel many borrowing requests (using post data). Allows only creator 
        * and administrators to delete packets requests.
        * 
        * @return JSON
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
                    // Verify logged user information
                    if(!Auth::user()->hasRole('Admin') &&
                       !Auth::user()->hasRole('Super Admin') && Auth::user()->id !== $packet->borrower_id)
                    {
                        $response['success'] = false;
                        $response['message'] = 'You do not have permission to cancel this request.';
                    }
                    else {
                        // Reset packet
                        $packet->borrower_id = null;
                        $packet->requested_at = null;
                        $packet->reserved_until = null;

                        // Save changes
                        if (!$packet->save()) 
                        {
                            $response['success'] = false;
                            $response['message'] = 'An error ocurred and the request could not be canceled.';
                        }
                    }
                }
                
                // Add response to response array
                $responses[] = $response;
            }
            return $responses;
       }
}
