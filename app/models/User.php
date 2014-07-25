<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

    // Provides soft deleting
    protected $softDelete = true;
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password');

    /**
     * Get the roles a user has
     */
    public function roles() {
        return $this->belongsToMany('Role', 'users_roles');
    }

    /**
     * Find out if user has a specific role
     *
     * $return boolean
     */
    public function hasRole($check) {
        return in_array($check, array_fetch($this->roles->toArray(), 'name'));
    }
    
    /**
     * Assign a role to user.
     */
    public function assignRole($id, $byName = false) {
        
        $role = null;
        
        // Search for role by name        
        if ($byName)
            $role = Role::where('name', '=', $id)->first();
        else
            $role = Role::find($id);

        if (!is_null($role))
            $this->roles()->save($role);
    }
    
    /**
     * Remove a role from a user.
     */
    public function disallowRole($id, $byName = false) {
        
        $role = null;
        
        // Search for role by name        
        if ($byName)
            $role = Role::where('name', '=', $id)->first();
        else
            $role = Role::find($id);

        if (!is_null($role))
            $this->roles()->detach($role->id);
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier() {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword() {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken() {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value) {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName() {
        return 'remember_token';
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail() {
        return $this->email;
    }

}
