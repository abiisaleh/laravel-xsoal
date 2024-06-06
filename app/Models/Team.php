<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Team extends Model
{
    use HasFactory;

    protected function getKontakAttribute()
    {
        return 'Telp ' . $this->telp .
            'Fax ' . $this->fax .
            'Email ' . $this->email;
    }

    //relasi
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function soals(): HasManyThrough
    {
        return $this->hasManyThrough(Soal::class, Category::class);
    }
}
