<?php

use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;

class Posting extends Eloquent implements SluggableInterface {

    use SluggableTrait;

    // Slug configuration
    protected $sluggable = array(
        'build_from' => 'title',
        'save_to' => 'slug',
    );
    
    // Table name
    protected $table = 'postings';

    /**
     * Maps relation between a posting and its creator.
     */
    public function user() {
        return $this->belongsTo('User');
    }

    /**
     * Maps relation between a posting and its category.
     */
    public function category() {
        return $this->belongsTo('Category');
    }

    /**
     * Maps relation between a posting and its tags.
     */
    public function tags() {
        return $this->hasMany('Tag');
    }
    
    /**
     * Assign a tag to a posting.
     */
    public function assignTag($tagName) 
    {        
        // Create tag
        $tag = new Tag();
        $tag->posting_id = $this->id;
        $tag->name = $tagName;
        
        // Save new tag
        if ($this->tags()->save($tag))
            return true;
        
        return false;
    }
    
    public function deleteTags() 
    {
        // Delete each tag
        foreach($this->tags as $tag)
            $tag->delete();
    }

}
