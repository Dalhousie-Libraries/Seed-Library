<?php

/**
 * Controller responsible for all front-end features related to the 'items' table.
 * 
 * Generates front-end content, including 'View details', 'search', 'basket' and
 * 'checkout'. Also includes methods that retrieve data in JSON format.
 */
class ItemController extends BaseController {

	/**
        * Item Model
        * @var Item
        */
        protected $item;

        /**
         * Inject the models.
         * (the controller is always instatiated by the framework, so there's no 
         * need to call it in most situations)
         * 
         * @param Item $item
         */
        public function __construct(Item $item)
        {
            $this->item = $item;
        }
    
        /**
	 * Renders a specific item's page.
         *
         * @param int $id Item id.
         * @return Response
	*/
        public function show($id)
        {
            // Find item in the database
            $item = Item::find($id);
            
            // Throws an error if item not found
            if (is_null($item))
                return App::abort(404, 'Page not found.');
            
            // Title
            $title = $item->getFullName();

            return View::make('site/items/show')
                       ->with(compact('title'))
                       ->with(compact('item'));
        }
        
        /**
         * Renders item search page.
         * 
         * @return Response
         */
        public function search()
        {
            // Page title
            $title = 'Seed search';
            
            return View::make('site/items/search', compact('title'));
        }
        
        /**
         * Retrieves a specific item's data.
         * 
         * @return JSON
         */
        public function getItem($id)
        {
            return Item::find($id);
        }
        
        /**
         * Retrieves all items.
         * 
         * @return JSON\Datatables
         */
        public function getAll()
        {
            $items = Item::select(array('items.id', 'items.family','items.species', 'items.variety', 'items.category', 'items.seed_sav_level'));
            
            return Datatables::of($items)
                    
            ->edit_column('family', '{{ $family }} > {{ $species }} > {{ $variety }}')                    
            ->add_column('items.id', '<a href="{{{ URL::to(\'item/show/\' . $id) }}}" class="btn btn-default btn-xs iframe"><span class="glyphicon glyphicon-search"></span> View details</a>')            
            ->remove_column('id')
            ->remove_column('species')
            ->remove_column('variety')
            ->make();
        }
        
        /**
         * Retrieve all available items (i.e. items that have packets).
         * 
         * @return JSON\Datatables
         */
        public function getAllAvail()
        {
            // Query the database
            $packets = Packet::select(array('items.family', 'items.species', 'items.variety', 'items.category', 'items.seed_sav_level', 'items.id'))
                             ->join('accessions', 'accessions.id', '=', 'packets.accession_id')
                             ->join('items', 'items.id', '=', 'accessions.item_id')
                             ->whereNull('borrower_id')
                             ->groupBy('items.id');

            return Datatables::of($packets)

            ->edit_column('family', '{{ $family }} > {{ $species }} > {{ $variety }}')                    
            ->edit_column('items.id', '<a href="{{{ URL::to(\'item/show/\' . $id) }}}" class="btn btn-default btn-xs iframe"><span class="glyphicon glyphicon-search"></span> View details</a>')
            
            ->remove_column('id')
            ->remove_column('species')
            ->remove_column('variety')

            ->make();
        }
        
        /**
	* Renders user's basket page.
        * 
        * @return Response
	*/
        public function basket()
        {
            // Title
            $title = 'My basket';

            return View::make('site/items/basket')
                       ->with(compact('title'));
        }
        
        /**
	* Renders checkout confirmation page.
        * 
        * @return Response
	*/
        public function confirmCheckout()
        {
            // Title
            $title = 'Checkout';

            return View::make('site/items/checkout')
                       ->with(compact('title'));
        }
        
        /**
	* Saves borrowing request. Returns operation status in JSON format.
        * Gets input via POST data.
        * 
        * @return JSON
	*/
        public function doCheckout()
        {
            // Get post data
            $packets    = Input::get('packets');
            $borrowerId = Input::get('borrowerId');

            // Check if post data is an array (otherwise returns an empty array)
            $newPackets = array();
            if(is_array($packets) && isset($borrowerId)) 
            {
                foreach($packets as $packet)
                {
                    $databasePacket = Packet::find($packet['id']);
                    
                    // Checks if packet is registered in the database
                    if (!is_null($databasePacket)) 
                    {
                        // It exists, but may have already been reserved to another person
                        if (isset($databasePacket->borrower_id)) 
                        {
                            $packet['ok']     = false;
                            $packet['response'] = 'RESERVED';
                        } else
                        {
                            // Reserve packet for user
                            $databasePacket->borrower_id    = $borrowerId;
                            $databasePacket->requested_at   = Carbon::now();
                            $databasePacket->reserved_until = Carbon::now()->addWeek();
                            
                            if ($databasePacket->save())
                                $packet['ok'] = true;
                            else {
                                $packet['ok'] = false; // Error while saving
                                $packet['response'] = 'FAILURE';
                            }
                        }
                    } else // Packet was not found
                    {
                        $packet['ok']     = false;
                        $packet['response'] = 'NULL';
                    }
                    
                    // Add packet to array
                    $newPackets[] = $packet;
                }
            }
            
            return $newPackets;
        }
        
        /**
         * Search an item by its full name (i.e. family, species and variety).
         * Returns search results in JSON, formatted specifically to TYPEAHEAD
         * plugin.
         * 
         * @param String $itemName Search terms.
         * @return JSON
         */
        public function getItemByName($itemName) 
        {
            $items = DB::select(DB::raw("SELECT CONCAT(family, ' - ', species, ' - ', variety) as name FROM items "
                                    . "WHERE family LIKE CONCAT('%', :family, '%') OR species LIKE CONCAT('%', :species, '%') OR variety LIKE CONCAT('%', :variety, '%')"), 
                    
                array(
                    'family' => $itemName,
                    'species' => $itemName,
                    'variety' => $itemName
                )
            );
            
            // Converts it to TYPEAHEAD format
            foreach($items as $key => $item)
                $items[$key] = $item->name;
            
            return $items;
        }
}
