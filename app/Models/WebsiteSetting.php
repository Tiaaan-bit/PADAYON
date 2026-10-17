<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    protected $fillable = ['website_name','website_subname', 'logo_path', 'facebook_name', 'facebook_url', 'business_address', 'location_url', 'phone_number', 'opening_time', 'closing_time'];
}
