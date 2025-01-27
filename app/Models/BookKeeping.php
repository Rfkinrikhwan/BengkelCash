<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hidehalo\Nanoid\Client;

class BookKeeping extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'note',
        'debit',
        'credit',
        'saldo',
        'method_payment',
        'type',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
        'debit' => 'integer',
        'credit' => 'integer',
        'saldo' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $client = new Client();
                $model->id = $client->generateId($size = 21);
            }
        });
    }
}
