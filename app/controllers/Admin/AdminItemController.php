<?php

class AdminItemController extends BaseController {
    
        /**
        * Item Model
        * @var Item
        */
        protected $item;

        /**
         * Inject the models.
         * @param Item $item
         */
        public function __construct(Item $item)
        {
            $this->item = $item;
        }

	/**
	*   Display all the registered items.
	*/
        public function getIndex()
        {
            // Title
            $title = 'Items';
            
            return View::make('admin/items/index', compact('title'));
        }
        
        /**
	 * Show the form for creating a new item.
	 *
	 * @return Response
	 */
	public function getCreate()
	{
            // Title
            $title = 'Create a new item';

            // Show the page
            return View::make('admin/items/create_edit', compact('title'));
	}

	/**
	 * Store a newly created item into the database.
	 *
	 * @return Response
	 */
	public function postCreate()
	{
            // Declare the rules for the form validation
            $rules = array(
                'family'            => 'required|min:3',
                'species'           => 'required|min:3',
                'variety'           => 'min:3',
            );

            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Create the item data
                $this->item->category          = Input::get('category');
                $this->item->family            = Input::get('family');
                $this->item->species           = Input::get('species');
                $this->item->variety           = Input::get('variety');
                $this->item->description       = Input::get('description');
                $this->item->seed_sav_level    = Input::get('seed_sav_level');

                // Was the item created?
                if($this->item->save())
                {
                    // Destination folder
                    $destinationPath = 'uploads/items';
                    
                    // Try to save images
                    $images = Input::file('files');
                    
                    if (!is_null($images[0]))
                    {
                        $uploadError = false;
                        $saveError   = false;
                        
                        // Create image objects for each image
                        foreach($images as $image)
                        {
                            $newImage = new Image();
                            $newImage->relation_id = $this->item->id;
                            $newImage->category    = 'ITEM';
                            $newImage->setFilename($this->item->id, $image->getClientOriginalExtension());

                            // Try to save it in the database
                            if ($newImage->save())
                            {
                                try {
                                    // Upload it
                                    if(is_null($image->move($destinationPath, $newImage->getFilename())))
                                        $uploadError = true;
                                } catch(\Exception $e) {
                                    $uploadError = true;
                                }
                            }
                            else
                                $saveError = true;                            
                        }
                        
                        if ($saveError)
                            return Redirect::to('admin/items/create')->withInput()->with('warning', 'Not all images were saved into the database');
                        
                        if ($uploadError)
                            return Redirect::to('admin/items/create')->withInput()->with('warning', 'Some of the images may not have been uploaded.');                        
                    }
                    
                    // Redirect to the new item page
                    return Redirect::to('admin/items/create')->with('success', 'Item registered successfully!');
                }

                // Redirect to the item create page
                return Redirect::to('admin/items/create')->withInput()->with('error', 'Something happened and we could not save the item into the database');
            }

            // Form validation failed
            return Redirect::to('admin/items/create')->withInput()->withErrors($validator);
	}
        
        /**
        * Renders update form.
        * @param int $id
        */
       public function getEdit($id)
       {
           // Title
            $title = 'Edit item';

            // Find the item in the database
            $item = Item::find($id);
            
            // Show the page
           return View::make('admin/items/create_edit')
                   ->with(compact('item'))
                   ->with(compact('title'));
       }

       /**
        * Updates item record.
        * @param int $id
        */
       public function postEdit($id)
       {
           // Find the item in the database
            $item = Item::find($id);
           
           // Declare the rules for the form validation
            $rules = array(
                'family'   => 'required|min:3',
                'species' => 'required|min:3',
                'variety' => 'min:3',
            );

            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Update the item data
                $item->category          = Input::get('category');
                $item->family            = Input::get('family');
                $item->species           = Input::get('species');
                $item->variety           = Input::get('variety');
                $item->description       = Input::get('description');
                $item->seed_sav_level    = Input::get('seed_sav_level');
                
                // Was the item updated?
                if($item->save())
                {
                    // Destination folder
                    $destinationPath = 'uploads/items';
                    
                    // Try to save images
                    $images = Input::file('files');
                    
                    if (!is_null($images[0]))
                    {
                        $uploadError = false;
                        $saveError   = false;
                        
                        // Create image objects for each image
                        foreach($images as $image)
                        {
                            $newImage = new Image();
                            $newImage->relation_id = $item->id;
                            $newImage->category    = 'ITEM';
                            $newImage->setFilename($item->id, $image->getClientOriginalExtension());

                            // Try to save it in the database
                            if ($newImage->save())
                            {
                                try {
                                    // Upload it
                                    if(is_null($image->move($destinationPath, $newImage->getFilename())))
                                        $uploadError = true;
                                } catch(\Exception $e) {
                                    $uploadError = true;
                                }
                            }
                            else
                                $saveError = true;                            
                        }
                        
                        if ($saveError)
                            return Redirect::to('admin/items/' . $id . '/edit')->withInput()->with('warning', 'Not all images were saved into the database');
                        
                        if ($uploadError)
                            return Redirect::to('admin/items/' . $id . '/edit')->withInput()->with('warning', 'Some of the images may not have been uploaded.');
                    }
                    
                    // Redirect to the new item page
                    return Redirect::to('admin/items/' . $id . '/edit')->with('success', 'Item updated successfully!');
                }

                // Redirect to the item create page
                return Redirect::to('admin/items/' . $id . '/edit')->withInput()->with('error', 'Something happened and we could not save the record into the database');
            }

            // Form validation failed
            return Redirect::to('admin/items/' . $id . '/edit')->withInput()->withErrors($validator);
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
           
           // Find item in the database
            $item = Item::find($id);
           
           // Check if item exists
            if (is_null($item)) {
                $response['success'] = false;
                $response['message'] = 'Item already deleted.';
            } else {
               // Delete it
                if (!$item->delete()) 
                {
                    $response['success'] = false;
                    $response['message'] = 'An error ocurred and the item could not be deleted.';
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
            $items = Input::get('items');
           
            // Try to delete each of the items
            foreach ($items as $itemId)
            {
                // Response message
                $response = array();
                $response['success'] = true;
                $response['message'] = 'OK';

                // Find item in the database
                $item = Item::find($itemId);

                // Check if item exists
                if (is_null($item)) {
                    $response['success'] = false;
                    $response['message'] = 'Item already deleted.';
                } else {
                    // Delete it
                    if (!$item->delete()) 
                    {
                        $response['success'] = false;
                        $response['message'] = 'An error ocurred and the item could not be deleted.';
                    }
                }
                
                // Add response to response array
                $responses[] = $response;
            } 
           
            return $responses;
       }
       
        /**
         *  Retrieve all items records formatted for DataTables.
         * 
         * @return Datatables JSON
         */
        public function getData()
        {
            $items = Item::select(array('items.id', 'items.family', 'items.species', 'items.variety', 'items.category'));

            return Datatables::of($items)
            ->add_column('actions', '<a href="{{{ URL::to(\'admin/items/\' . $id . \'/edit\' ) }}}" class="btn btn-default btn-xs iframe" >Edit</a> '
                                  . '<a href="#" class="btn btn-danger btn-xs delete" data-toggle="modal" data-target="#confirmDelete" data-link="{{{ URL::to(\'admin/items/\' . $id . \'/delete\') }}}" 
                                      data-title="Delete item" data-message="Are you sure you want to delete this item?">Delete</a>')
            ->edit_column('id', '<input type="checkbox" name="items" value="{{$id}}" />')

            ->make();
        }
}