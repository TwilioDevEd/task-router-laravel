<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * A missed call from a customer, lets call them back!
 */
class MissedCall extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'selectedProduct', 'phoneNumber',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $attributes = ['internationalPhoneNumber' => ''];

    /**
     * Phone number converted into the American International Standard
     * @return mixed
     */
    public function getInternationalPhoneNumberAttribute()
    {
        return $this->attributes['phoneNumber'];
    }
}
