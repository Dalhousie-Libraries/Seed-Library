<?php

/**
 * Controller responsible for all front-end features related to the 'returns' table.
 *
 * Include CRUD for 'returns' table.
 */
class ReturnController extends BaseController {

	/**
        * Return Model
        * @var Return
        */
        protected $return;

        /**
         * Inject the models.
         * (the controller is always instatiated by the framework, so there's no 
         * need to call it in most situations)
         * 
         * @param Returning $return
         */
        public function __construct(Returning $return)
        {
            $this->return = $return;
        }
    
        /**
	* Displays return's creation form.
        * 
        * @return Response
	*/
        public function getCreate()
        {
            // Title
            $title = 'Return seed';

            return View::make('site/returns/create_edit')
                       ->with(compact('title'));
        }
        
        /**
	* Saves a newly created return. Redirects to creation page and
        * displays the results.
        * 
        * @return Response
	*/
        public function postCreate()
        {
            // Declare the rules for the form validation
            $rules = array(
                'parent_packet' => 'required|integer',
                'amount'        => 'required|integer',
            );

            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Validates the returner
                $returner = Auth::user();
                
                // Throws an error if returner is not valid
                if (is_null($returner))
                    // Redirect to create page
                    return Redirect::to('returns/return')->withInput()->with('error', 'Sorry, but it appears that you are not eligible for borrowing seed.');
                
                // Validates the parent packet
                $packet = Packet::where('packets.id', '=', Input::get('parent_packet'))
                                ->where('packets.borrower_id', '=', $returner->id)
                                ->first();
                
                // Throws an error if packet is not valid
                if (is_null($packet))
                    // Redirect to create page
                    return Redirect::to('returns/return')->withInput()->with('error', 'The entered packet is not a valid one.');
                
                // Create the return's data                
                if(!is_null($packet->accession) && !is_null($packet->accession->item))
                    $this->return->item_id      = $packet->accession->item->id;
                $this->return->user_id          = $returner->id;
                $this->return->parent_id        = $packet->id;
                $this->return->type             = 'RETURN';
                $this->return->amount           = Input::get('amount');
                $this->return->description      = Input::get('description');
                $this->return->requested_at     = Carbon::now(); // Does it have to be the current day? I guess not... CHANGE IT!

                // Was the return created?
                if($this->return->save())
                {
                    // Redirect to the new return page
                    return Redirect::to('returns/return')->with('success', 
                            'Return registered successfully! Click <a href="'. URL::to('user/requests') .'">here</a> to see your requests.');
                }

                // Redirect to the return create page
                return Redirect::to('returns/return')->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('returns/return')->withInput()->withErrors($validator);
        }
        
        /**
	* Renders a return edit page.
        *         
        * @param int $id Return id
        * @return Response
	*/
        public function getEdit($id)
        {
            // Find return record in the database
            $return = Returning::find($id);
            
            // Throw an error if not found
            if(is_null($return))
                return App::abort(404, 'Return record not found.');
            
            // Title
            $title = 'Edit return request';

            return View::make('site/returns/create_edit')
                       ->with(compact('title'))
                       ->with(compact('return'));
        }
        
        /**
	* Updates an existing return. Redirects to return edit page, showing
        * the results of the update.
        * 
        * @param int $id Return id.
        * @return Response
	*/
        public function postEdit($id)
        {
            // Declare the rules for the form validation
            $rules = array(
                'parent_packet' => 'required|integer',
                'amount'        => 'required|integer',
            );

            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Get return record in the database
                $return = Returning::find($id);
                
                // Throws an error if return record is not valid and redirects to edit page
                if(is_null($return))
                    return Redirect::to('returns/' . $id . '/edit')->withInput()->with('error', 'The record you are trying to edit does not exist.');
                
                // Validates the returner
                $returner = Auth::user();
                
                // Throws an error if returner is not valid
                if (is_null($returner))
                    // Redirect to edit page
                    return Redirect::to('returns/' . $id . '/edit')->withInput()->with('error', 'Sorry, but it appears that you are not eligible for borrowing seed.');
                
                // Validates the parent packet
                $packet = Packet::where('packets.id', '=', Input::get('parent_packet'))
                                ->where('packets.borrower_id', '=', $returner->id)
                                ->first();
                
                // Throws an error if packet is not valid
                if (is_null($packet))
                    // Redirect to create page
                    return Redirect::to('returns/' . $id . '/edit')->withInput()->with('error', 'The entered packet is not a valid one.');
                
                // Create the return's data                
                if(!is_null($packet->accession) && !is_null($packet->accession->item))
                    $return->item_id  = $packet->accession->item->id;
                $return->parent_id    = $packet->id;
                $return->amount       = Input::get('amount');
                $return->description  = Input::get('description');

                // Was the return created?
                if($return->save())
                {
                    // Redirect to the new return page
                    return Redirect::to('returns/' . $id . '/edit')->with('success', 
                            'Return updated successfully!');
                }

                // Redirect to the return create page
                return Redirect::to('returns/' . $id . '/edit')->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('returns/' . $id . '/edit')->withInput()->withErrors($validator);
        }
        
        /**
        * Remove the specified resource from storage.
        *
        * @param $id Return id.
        * @return JSON
        */
       public function delete($id)
       {
            // Response message
            $response = array();
            $response['success'] = true;
            $response['message'] = 'OK';
            
            // Find return in the database
            $return = Returning::find($id);
           
            // Check if return exists
            if (is_null($return)) {
                $response['success'] = false;
                $response['message'] = 'Request already canceled.';
            } else {
                // Verify logged user information
                if(Auth::user()->id !== $return->user_id)
                {
                    $response['success'] = false;
                    $response['message'] = 'You do not have permission to cancel this request.';
                }
                else {
                    // Delete it
                    if (!$return->delete()) 
                    {
                        $response['success'] = false;
                        $response['message'] = 'An error ocurred and the request could not be canceled.';
                    }
                }
            }
           
            return $response;
       }
        
        /**
         * Retrieves a specific return's data.
         * 
         * @param int $id Return id.
         * @return JSON
         */
        public function getReturn($id)
        {
            return Returning::find($id);
        }
        
        /**
        * Retrieves all returns.
        * 
        * @return JSON
        */
        public function getAll()
        {
            return Returning::all();
        }
        
        /**
        * Cancel many return requests (post data). Allows only creator and
        * administrators to delete a return request.
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
                $request = Returning::find($requestId);

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
