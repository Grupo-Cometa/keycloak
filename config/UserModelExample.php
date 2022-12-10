<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AccessAuthorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract ;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AccessAuthorizable
{
    use Authenticatable, Authorizable;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'public_id',
        'preferred_username',
        'email',
        'created_at',
        'updated_at'
    ];
}
