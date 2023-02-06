<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadAmount extends Model
{
    use HasFactory; 
    protected $table = 'lead_amounts';
    protected $guarded = ['id'];
    protected $fillable = [];
}
