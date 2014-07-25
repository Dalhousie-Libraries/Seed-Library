<?php

/**
 * Controller responsible for all front-end features related to the 'donations' table.
 *
 * Include CRUD for 'donations' table.
 */
class DonationController extends BaseController {

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
	* Displays donation's creation form.
        * 
        * @return Response
	*/
        public function getCreate()
        {
            // Title
            $title = 'Donate seed';

            return View::make('site/donations/create_edit')
                       ->with(compact('title'));
        }
        
        /**
	* Saves a newly created donation. Redirects to creation page and
        * displays the results.
        * 
        * @return Response
	*/
        public function postCreate()
        {
            // Declare the rules for the form validation
            $rules = array(
                'seed_name' => 'required|min:3',
                'amount'    => 'required|integer',
            );

            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Validates the item
                $itemData = explode(' - ', Input::get('seed_name'));
                if(isset($itemData[1])) 
                {
                    if (!isset($itemData[2])) // If seed has no variety in its name
                        $item = Item::where('family', '=', $itemData[0])
                                    ->where('species', '=', $itemData[1])
                                    ->first();
                    else
                        $item = Item::where('family', '=', $itemData[0])
                                    ->where('species', '=', $itemData[1])
                                    ->where('variety', '=', $itemData[2])
                                    ->first();
                } else
                    $error = true;
                
                // Throws an error if item is not valid and entered seed is not a new item
                if (isset($error) || is_null($item))
                    if (!Input::has('new_seed'))
                        // Redirect to create page
                        return Redirect::to('donations/donate')->withInput()->with('error', 'The entered item is not a valid one.')
                                                                            ->with('invalidItem', true);
                
                // Create the donation's data
                $this->donation->item_id          = Input::has('new_seed') ? 0 : $item->id; // 0 is the reserved id for new items
                $this->donation->user_id          = Auth::user()->id;
                $this->donation->type             = 'DONATION';
                $this->donation->amount           = Input::get('amount');
                $this->donation->description      = Input::get('description');
                $this->donation->requested_at     = Carbon::now(); // REQUEST ONLY!!! Checked In date is different...
                
                // Adds seed name to description if new item
                if (Input::has('new_seed'))
                    $this->donation->description = Input::get('seed_name') . '*###*' . $this->donation->description;

                // Was the donation created?
                if($this->donation->save())
                {
                    // Redirect to the new donation page
                    return Redirect::to('donations/donate')->with('success', 
                            'Donation registered successfully! Click <a href="'. URL::to('user/requests') .'">here</a> to see your requests.');
                }

                // Redirect to the donation create page
                return Redirect::to('donations/donate')->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('donations/donate')->withInput()->withErrors($validator);
        }
        
        /**
	* Renders a donation edit page.
        *         
        * @param int $id Donation id
        * @return Response
	*/
        public function getEdit($id)
        {
            // Find donation record in the database
            $donation = Donation::find($id);
            
            // Throw an error if not found
            if(is_null($donation))
                return App::abort(404, 'Donation record not found.');
            
            // Title
            $title = 'Edit donation request';

            return View::make('site/donations/create_edit')
                       ->with(compact('title'))
                       ->with(compact('donation'));
        }
        
        /**
	* Updates an existing donation. Redirects to donation edit page, showing
        * the results of the update.
        * 
        * @param int $id Donation id.
        * @return Response
	*/
        public function postEdit($id)
        {
            // Declare the rules for the form validation
            $rules = array(
                'seed_name' => 'required|min:3',
                'amount'    => 'required|integer',
            );

            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Get donation record from database
                $donation = Donation::find($id);
                
                // Throw an error if donation not found, redirect to previous page
                if(is_null($donation))
                    return Redirect::to('donations/' . $id . '/edit')->withInput()->with('error', 'The record you are trying to edit does not exist.');
                
                // Validates the item
                $itemData = explode(' - ', Input::get('seed_name'));
                if(isset($itemData[1])) 
                {
                    if (!isset($itemData[2])) // If seed has no variety in its name
                        $item = Item::where('family', '=', $itemData[0])
                                    ->where('species', '=', $itemData[1])
                                    ->first();
                    else
                        $item = Item::where('family', '=', $itemData[0])
                                    ->where('species', '=', $itemData[1])
                                    ->where('variety', '=', $itemData[2])
                                    ->first();
                } else
                    $error = true;
                
                // Throws an error if item is not valid and entered seed is not a new item
                if (isset($error) || is_null($item))
                    if (!Input::has('new_seed'))
                        // Redirect to create page
                        return Redirect::to('donations/' . $id . '/edit')->withInput()->with('error', 'The entered item is not a valid one.')
                                                                                      ->with('invalidItem', true);
                
                // Create the donation's data
                $donation->item_id          = Input::has('new_seed') ? 0 : $item->id; // 0 is the reserved id for new items
                $donation->amount           = Input::get('amount');
                $donation->description      = Input::get('description');
                
                // Adds seed name to description if new item
                if (Input::has('new_seed'))
                    // Worst workaround ever, but will work most of the times (arbitrary delimiter *###*)
                    $donation->description = Input::get('seed_name') . '*###*' . $donation->description;

                // Was the donation created?
                if($donation->save())
                {
                    // Redirect to the donation page
                    return Redirect::to('donations/' . $id . '/edit')->with('success', 
                            'Donation updated successfully!');
                }

                // Redirect to the donation edit page
                return Redirect::to('donations/' . $id . '/edit')->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('donations/' . $id . '/edit')->withInput()->withErrors($validator);
        }
        
        /**
        * Remove the specified resource from storage.
        *
        * @param $id Donation id.
        * @return JSON
        */
       public function delete($id)
       {
            // Response message
            $response = array();
            $response['success'] = true;
            $response['message'] = 'OK';
            
            // Find donation in the database
            $donation = Donation::find($id);
           
            // Check if donation exists
            if (is_null($donation)) {
                $response['success'] = false;
                $response['message'] = 'Request already canceled.';
            } else {
                // Verify logged user information
                if(!Auth::user()->hasRole('Admin') &&
                   !Auth::user()->hasRole('Super Admin') && Auth::user()->id !== $donation->user_id)
                {
                    $response['success'] = false;
                    $response['message'] = 'You do not have permission to cancel this request.';
                }
                else {
                    // Delete it
                    if (!$donation->delete()) 
                    {
                        $response['success'] = false;
                        $response['message'] = 'An error ocurred and the request could not be canceled.';
                    }
                }
            }
           
            return $response;
       }
        
        /**
         * Retrieves a specific donation's data.
         * 
         * @param int $id Donation id.
         * @return JSON
         */
        public function getDonation($id)
        {
            return Donation::find($id);
        }
        
        /**
        * Retrieves all donations.
        * 
        * @return JSON
        */
        public function getAll()
        {
            return Donation::all();
        }
        
        /**
        * Cancel many donation requests (post data). Allows only creator and
        * administrators to delete a donation request.
        * 
        * @return JSON
        */
       public function deleteAll()
       {
            // Responses array
            $responses = array();
           
            // Get post data
            $requests= Input::get('requests');
           
            // Try to delete each of the requests
            foreach ($requests as $requestId)
            {
                // Response message
                $response = array();
                $response['success'] = true;
                $response['message'] = 'OK';

                // Find request in the database
                $request = Donation::find($requestId);

                // Check if request exists
                if (is_null($request)) {
                    $response['success'] = false;
                    $response['message'] = 'Request already deleted.';
                } else {
                    // Verify logged user information
                    if(!Auth::user()->hasRole('Admin') &&
                       !Auth::user()->hasRole('Super Admin') && Auth::user()->id !== $request->user_id)
                    {
                        $response['success'] = false;
                        $response['message'] = 'You do not have permission to cancel this request.';
                    }
                    else {
                        // Save changes
                        if (!$request->delete()) 
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
