<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;
    protected $table = 'leads';
    protected $guarded = ['id'];
    protected $fillable = [];

    public function activities() 
    {
        return $this->hasMany(LeadActivity::class, 'lead_id', 'id');
    }

}
