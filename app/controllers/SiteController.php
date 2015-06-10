<?php

class SiteController extends BaseController {

    /**
     * Show homepage.
     * 
     * @return Response
     */
    public function index()
    {
        // Title
        $title = 'Welcome to the Seed Lending Library!';
        
        return View::make('site/home', compact('title'));
    }
    
    /**
    *  Show specified article.
    */
    public function show($id) 
    {
        // Find article in the database
        if (is_numeric($id))
            $posting = Posting::find($id);
        else {
            // Find by slug returns a collection, not a single object
            $posting = Posting::findBySlug($id);
            $posting = count($posting) ? $posting[0] : null;
        }

        // Raise error if posting not found
        if(is_null($posting))
            return App::abort(404, 'Article not found');

        // Show the page
        return View::make('site/postings/show', compact('posting'));
    }

    /**
     *  Search for an specific article.
     */
    public function search($keyword)
    {
        // keyword must be at least three caracters long
        if (strlen($keyword) < 3)
            return 'Error';

        // Search for article tags (left join will bring even articles that don't have tags)
        $results = Posting::leftJoin('tags', 'tags.posting_id', '=', 'postings.id')
                           ->orWhere('tags.name', 'LIKE', "%$keyword%")
                           ->orWhere('postings.title', 'LIKE', "%$keyword%")
                           ->orWhere('postings.content', 'LIKE', "%$keyword%")
                           ->get(array('postings.title', 'postings.slug', 'postings.preview'));
        
        // broadcast results to results template page
        return $results;
    }
}