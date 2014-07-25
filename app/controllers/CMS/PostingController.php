<?php

class PostingController extends BaseController {

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
                'title'    => 'required|min:3',
                'content'  => 'required|min:3',
                'category' => 'required'
            );

            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Validates the category
                $category = Category::find(Input::get('category'));
                
                // Redirect to the posting create page
                if (is_null($category))
                    return Redirect::to('cms/postings/create')->with('error', 'Invalid category.');
                
                // Create the posting's data
                $this->posting->title       = Input::get('title');
                $this->posting->content     = Input::get('content');
                $this->posting->preview     = Input::get('preview');
                $this->posting->category_id = Input::get('category');
                $this->posting->author_id   = Auth::user()->id;

                // Was the posting created?
                if($this->posting->save())
                {
                    // Attach tags to posting
                    if(!empty(Input::get('tags')))
                    {
                        $tags = explode(',', Input::get('tags'));                        
                        foreach($tags as $tag)
                            $this->posting->assignTag($tag);
                    }
                    
                    // Redirect to the new posting page
                    return Redirect::to('cms/postings/create')->with('success', 'Posting registered successfully!');
                }

                // Redirect to the posting create page
                return Redirect::to('cms/postings/create')->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('cms/postings/create')->withInput()->withErrors($validator);
	}
        
        /**
	 * Show the form for editing a posting.
	 *
	 * @return Response
	 */
	public function getEdit($id)
	{
            // Title
            $title = 'Edit posting';
            
            // Get posting in the database
            $posting = Posting::find($id);
            
            // Show the page
            return View::make('cms/postings/create_edit')
                       ->with(compact('title'))
                       ->with(compact('posting'));
	}
        
        /**
	 * Update existing posting record into the database.
	 *
	 * @return Response
	 */
	public function postEdit($id)
	{
            // Find posting in the database
            $posting = Posting::find($id);
            
            // Return error if not found
            if (is_null($posting))
                return Redirect::to('cms/postings/' . $id . '/edit')->with('error', 'The posting you\'re editing does not exist.');
            
            // Declare the rules for the form validation
            $rules = array(
                'title'    => 'required|min:3',
                'content'  => 'required|min:3',
                'category' => 'required'
            );

            // Validate the inputs
            $validator = Validator::make(Input::all(), $rules);

            // Check if the form validates with success
            if ($validator->passes())
            {
                // Validates the category
                $category = Category::find(Input::get('category'));
                
                // Redirect to the posting create page
                if (is_null($category))
                    return Redirect::to('cms/postings/' . $id . '/edit')->with('error', 'Invalid category.');
                
                // Create the posting's data
                $posting->title       = Input::get('title');
                $posting->content     = Input::get('content');
                $posting->preview     = Input::get('preview');
                $posting->category_id = Input::get('category');

                // Was the posting created?
                if($posting->save())
                {
                    // Delete all tags attached to posting
                    $posting->deleteTags();
                    
                    // Attach tags to posting                    
                    if(!empty(Input::get('tags')))
                    {
                        $tags = explode(',', Input::get('tags'));                        
                        foreach($tags as $tag)
                            $posting->assignTag($tag);
                    }
                    
                    // Redirect to the new posting page
                    return Redirect::to('cms/postings/' . $id . '/edit')->with('success', 'Posting registered successfully!');
                }

                // Redirect to the posting create page
                return Redirect::to('cms/postings/' . $id . '/edit')->withInput()->with('error', 'Something happened and we could not save data into the database');
            }

            // Form validation failed
            return Redirect::to('cms/postings/' . $id . '/edit')->withInput()->withErrors($validator);
	}
        
        /**
         *  Delete a posting from the database.
         * 
         * @return JSON
         */
        public function delete($id) 
        {
            // Response message
            $response = array();
            $response['success'] = true;
            $response['message'] = 'OK';
           
           // Find image in the database
            $posting = Posting::find($id);
           
           // Check if image exists
            if (is_null($posting)) {
                $response['success'] = false;
                $response['message'] = 'Posting already deleted.';
            } else {
               // Delete it
                if (!$posting->delete()) 
                {
                    $response['success'] = false;
                    $response['message'] = 'An error ocurred and the posting could not be deleted.';
                }
            }           
           
            return $response;
        }
        
        /**
         *  Change a posting status and save it.
         * 
         * @return JSON
         */
        public function changePublishStatus($id) 
        {
            // Response message
            $response = array();
            $response['success'] = true;
            $response['message'] = 'OK';
           
           // Find image in the database
            $posting = Posting::find($id);
           
           // Check if image exists
            if (is_null($posting)) {
                $response['success'] = false;
                $response['message'] = 'Posting doesn\'t exist.';
            } else {
                // Change posting status
                $posting->published = !$posting->published;
                
               // Saves it
                if (!$posting->save()) 
                {
                    $response['success'] = false;
                    $response['message'] = 'An error ocurred and the posting could not be (un)published.';
                }
            }           
           
            return $response;
        }
        
        /**
         * List all published postings.
         * 
         * @return JSON
         */
        public function getData()
        {
            $postings = Posting::select(array('postings.id', 'postings.title', 'postings.author_id', 'users.name', 'postings.created_at', 'postings.updated_at', 'postings.published'))
                                 ->join('users', 'users.id', '=', 'postings.author_id');

            return Datatables::of($postings)

            ->edit_column('name', '<a href="{{URL::to(\'admin/users/\' . $author_id . \'/edit\')}}" title="User page" class="iframe">{{ $name }}</a>')             
                    
            ->edit_column('published', '<a href="#" title="{{{ $published ? \'Click to withdraw it\' : \'Click to publish it\'}}}" class="publish_btn" data-id="{{$id}}">'
                                        . '{{{ $published ? \'Published\' : \'Not published\'}}}'
                    . '                 </a>')
            
            ->add_column('actions', '<a href="{{{ URL::to(\'cms/postings/\' . $id . \'/edit\' ) }}}" class="btn btn-default btn-xs iframe" >Edit</a> '
                                  . '<a href="#" class="btn btn-danger btn-xs delete" data-toggle="modal" data-target="#confirmDelete" data-link="{{{ URL::to(\'cms/postings/\' . $id . \'/delete\' ) }}}" 
                                      data-title="Delete posting" data-message="Are you sure you want to delete this posting?">Delete</a>')

            ->remove_column('id')
            ->remove_column('author_id')

            ->make();
        }
}
