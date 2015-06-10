<?php

class Packet extends Eloquent {
    
    // We don't want to have timestamps in this model
    public $timestamps = false;
    
    /**
     * Maps relation between a borrower and a packet.
     */
    public function borrower()
    {
        return $this->belongsTo('User');
    }
    
    /**
     * Maps relation between an accession and a packet.
     */
    public function accession()
    {
        return $this->belongsTo('Accession');
    }
}
