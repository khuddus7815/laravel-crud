<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
   // Make sure to add the fillable property
protected $fillable = ['name', 'user_id'];

public function user()
{
    return $this->belongsTo(User::class);
}

public function items()
{
    return $this->hasMany(Item::class);
}
}
