<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testing extends Model
{
    use HasFactory;

    protected $fillable = ['skenario_id', 'title', 'expected_result', 'actual_result', 'status'];

    public function skenario()
    {
        return $this->belongsTo(Skenario::class, 'skenario_id');
    }
}