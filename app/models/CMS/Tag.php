<?php

class Tag extends Eloquent {
    
    // Table name
    protected $table = 'tags';
    
    // No timestamps
    public $timestamps = false;
    
    // No auto-increment id
    public $incrementing = false;
    
    // Primary key is not called id
    public $primaryKey = 'name';
    
    /**
     * Maps relation between a tag an its posting.
     */
    public function posting() 
    {
        return $this->belongsTo('Posting');
    }
}
