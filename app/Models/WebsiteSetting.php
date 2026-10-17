<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    protected $fillable = ['booking_closed_date', 'website_name', 'website_subname', 'logo_path', 'facebook_name', 'facebook_url', 'business_address', 'location_url', 'phone_number', 'opening_time', 'closing_time'];

    protected function casts(): array
    {
        return [

            'booking_closed_date' => 'date',
        ];
    }
}
