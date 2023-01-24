<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTargets extends Model
{
    use HasFactory;
    protected $table = 'user_targets';
    protected $guarded = ['id'];
    protected $fillable = [];
}
