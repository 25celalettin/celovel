<?php

namespace App\Models;

use Celovel\Database\Model;

class User extends Model
{
    protected $table = 'users';
    
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = password_hash($value, PASSWORD_DEFAULT);
    }

    public function verifyPassword($password): bool
    {
        return password_verify($password, $this->password);
    }
}
