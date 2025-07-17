<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WoocommerceSettings extends Model
{
    protected $fillable =[ 'consumer_key', 'consumer_secret', 'store_url', 'price_value', 'version', 'wp_api', 'verify_ssl', 'query_string_auth', 'timeout'];

}
