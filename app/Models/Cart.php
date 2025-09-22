<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'item_id', 'quantity'];

   // app/Models/Cart.php

public function item()
{
    return $this->belongsTo(Item::class);
}
}
