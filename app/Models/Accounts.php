<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_id', 'balance', 'currency_id'];

    protected $hidden = ['currency_id', 'created_at', 'updated_at'];


    public function currencyName()
    {
        return $this->hasOne(Currencies::class, 'id', 'currency_id')->first()->currency;
    }
}
