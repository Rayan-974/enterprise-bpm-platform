<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'region', 'is_active'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'country_code', 'code');
    }
}
