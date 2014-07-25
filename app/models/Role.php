<?php

class Role extends Eloquent {
    
    // No timestamps for this class
    public $timestamps = false;
    
    /**
     * Maps relation between roles and users.
     */
    public function users()
    {
        return $this->belongsToMany('User', 'users_roles');
    }
    
}
