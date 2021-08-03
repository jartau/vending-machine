<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['price', 'stock'];

    public function hasStock(): bool
    {
        return $this->stock > 0;
    }

}
