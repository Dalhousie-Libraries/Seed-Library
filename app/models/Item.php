<?php

class Item extends Eloquent {
    
    // Provides soft deleting
    protected $softDelete = true; 
    
    /**
     * Maps relation between items and accessions.
     */
    public function accessions() {
        return $this->hasMany('Accession');
    }
    
    /**
     * Maps relation betwaeen items and images.
     */
    public function images() {
        return $this->hasMany('Image', 'relation_id');
    }
    
    /**
     * Returns item full qualified name.
     * 
     * @return String
     */
    public function getFullName() {
        return $this->family . ' - ' . $this->species . (!empty($this->variety) ? ' - ' .$this->variety : null);
    }
}
