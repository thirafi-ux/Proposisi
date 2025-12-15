<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TruthTable extends Model
{
    protected $fillable = [
        'expression_id',
        'table_data',
        'simplified_form',
        'is_tautology',
        'is_contradiction',
        'is_contingent',
        'true_count',
        'false_count',
        'truth_percentage'
    ];

    protected $casts = [
        'table_data' => 'array'
    ];

    public function logicExpression()
    {
        return $this->belongsTo(LogicExpression::class);
    }
}