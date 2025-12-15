<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogicExpression extends Model
{
    protected $fillable = [
        'expression',
        'variables',
        'hash',
        'user_id',
        'variable_count'
    ];

    protected $casts = [
        'variables' => 'array'
    ];

    public function truthTable()
    {
        return $this->hasOne(TruthTable::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByUser($query, $userId = null)
    {
        if ($userId) {
            return $query->where('user_id', $userId);
        }
        return $query;
    }
}