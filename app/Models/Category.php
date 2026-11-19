<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'spec_template'];

    public function specTemplateKey(): string
    {
        return \App\Support\ProductSpecs::templateKeyFor($this);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
