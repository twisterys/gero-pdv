<?php

namespace App\Traits;

use App\Models\WoocommerceSettings;
use App\Services\LogService;
use Automattic\WooCommerce\Client;

trait WoocommerceTrait
{

    /**
     * @throws \Exception
     */
    protected function woocommerceClient(): Client
    {
        try {
            $woocommerce = WoocommerceSettings::first();
            return new Client(
                $woocommerce->store_url,
                $woocommerce->consumer_key,
                $woocommerce->consumer_secret,
                [
                    'version' => 'wc/'.$woocommerce->version,
                    'wp_api' => $woocommerce->wp_api,
                    'verify_ssl' => $woocommerce->verify_ssl,
                    'query_string_auth' => $woocommerce->query_string_auth,
                    'timeout' => $woocommerce->timeout,
                ]
            );
        }catch (\Exception $e) {
            LogService::logException($e);
            throw new \Exception('Woocommerce settings not found',$e->getCode());
        }
    }


    public function getAllProducts($options = [])
    {
        return $this->woocommerceClient()->get('products',$options);
    }

    public function getAllOrders($options = [])
    {
        return $this->woocommerceClient()->get('orders',$options);
    }

    public function getCustomer($id)
    {
        return $this->woocommerceClient()->get('customers/'.$id);
    }




}
