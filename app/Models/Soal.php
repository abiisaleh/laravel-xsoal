<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Soal extends Model
{
    use HasFactory;

    protected $casts =  [
        'pilihan_ganda' => 'array',
        'essay' => 'array',
        'detail' => 'array',
    ];


    public function getJumlahSoalAttribute()
    {
        return collect($this->pilihan_ganda)->count() + collect($this->essay)->count();
    }

    public function getTanggalSoalAttribute()
    {
        Carbon::setLocale('id');
        return Carbon::createFromFormat('Y-m-d', $this->detail['tanggal'])
            ->translatedFormat('l / d F Y');
    }

    //relasi
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function team(): HasOneThrough
    {
        return $this->hasOneThrough(
            Team::class,
            Category::class,
            'id', # foreign key on intermediary -- categories
            'id', # foreign key on target -- team
            'category_id', # local key on this -- soal
            'team_id' # local key on intermediary -- categories
        );
    }
}
