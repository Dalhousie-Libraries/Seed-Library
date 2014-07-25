<?php

class Accession extends Eloquent {
    
    // We don't want to have timestamps in this model
    public $timestamps = false;
    
    // Table name accession
    protected $table = 'accessions';
    
    /**
     * Maps relation between an accession and its donor.
     */
    public function user()
    {
        return $this->belongsTo('User');
    }
    
    /**
     * Maps relation between an accession and its item.
     */
    public function item()
    {
        return $this->belongsTo('Item');
    }
    
    /**
     * Maps relation between an accession and its parent packet.
     */
    public function parent()
    {
        return $this->belongsTo('Packet', 'parent_id');
    }
    
    /**
     * Lists all packets related to an accession.
     */
    public function packets()
    {
        return $this->hasMany('Packet', 'accession_id');
    }
    
    /**
     * Generate an unique accession number.
     */
    public function getAccessionNumber()
    {
        // Assigns the current year - first part of the accession number (e.g. 2014)
        $currentYear = date('Y');
        
        // Gets the second part of the accession number
        $result = DB::select( DB::raw('SELECT accession_number FROM `accessions` WHERE YEAR(checked_in_date) = :year ORDER BY id DESC LIMIT 1'), array(
            'year' => $currentYear
        ));
        
        // Assigns the second part of the number
        return $currentYear . '.' . str_pad((explode('.', $result[0]->accession_number)[1]) + 1, 5, '0', STR_PAD_LEFT);
    }
}
