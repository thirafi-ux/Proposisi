<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculationHistory extends Model
{
    use HasFactory;

    /**
     * Nama tabel (jika tidak mengikuti konvensi plural)
     */
    protected $table = 'calculation_histories';  // <-- Ini opsional, karena Laravel secara default akan mencari calculation_histories

    /**
     * Kolom yang dapat diisi secara massal
     */
    protected $fillable = [
        'user_id',
        'expression',
        'variables',
        'mode',
        'is_tautology',
        'is_contradiction'
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'variables' => 'array',
        'is_tautology' => 'boolean',
        'is_contradiction' => 'boolean'
    ];

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}