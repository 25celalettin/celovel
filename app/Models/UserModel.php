<?php

namespace App\Models;

use Celovel\Database\Model;

class UserModel extends Model
{
    protected $table = 'user';
    protected $fillable = [];
    protected $guarded = ['id'];
    
    // Model methods here
}
