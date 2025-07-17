<?php

namespace App\Http\Controllers;


use App\Models\WoocommerceSettings;
use App\Traits\WoocommerceTrait;
use Automattic\WooCommerce\Client;
use Illuminate\Http\Request;

class WoocommerceController extends Controller
{

    use WoocommerceTrait;

    public function parametres()
    {
        $parametres = WoocommerceSettings::first();
        return view('parametres.woocommerce.liste', compact('parametres'));
    }

    public function mettre_a_jour(Request $request)
    {
        if ($request->ajax()) {
            \Validator::make($request->all(), [
                'consumer_key' => 'required',
                'consumer_secret' => 'required',
                'store_url' => 'required',
                'price_value' => 'required',
                'version' => 'nullable',
                'wp_api' => 'nullable',
                'verify_ssl' => 'nullable',
                'query_string_auth' => 'nullable',
                'timeout' => 'nullable',
            ],[],[
                'consumer_key' => 'Clé consommateur',
                'consumer_secret' =>'Clé secrète',
                'store_url' => 'Lien du magasin',
                'price_value' => 'Valeur du prix',
                'version' => 'Version',
                'wp_api' => 'API WP',
                'verify_ssl' => 'Vérifier SSL',
                'query_string_auth' => 'Authentification par chaîne de requête',
                'timeout' => 'Délai d\'attente',
            ])->validate();
            $parametres = WoocommerceSettings::first();
            if ($parametres) {
                $parametres->update($request->all());
            } else {
                WoocommerceSettings::create($request->all());
            }
            return response('Paramètres mis à jour avec succès');
        }

        abort(404);

    }

    public function products()
    {
        return $this->getAllProducts();
    }

    public function testConnection(Request $request)
    {
        if ($request->ajax()) {
            \Validator::make($request->all(), [
                'consumer_key' => 'required',
                'consumer_secret' => 'required',
                'store_url' => 'required',
                'version' => 'nullable',
            ],[],[
                'consumer_key' => 'Clé consommateur',
                'consumer_secret' =>'Clé secrète',
                'store_url' => 'Lien du magasin',
                'version' => 'Version',
            ])->validate();
            $woocommerce = new Client(
                $request->store_url,
                $request->consumer_key,
                $request->consumer_secret,
                [
                    'wp_api' => true,
                    'version' => 'wc/'.($request->version ?? 'v3'),
                    'verify_ssl' => false,
                    'query_string_auth' => false,
                    'timeout' => 15,
                ]
            );
            try {
                $woocommerce->get('coupons');
                return response('Connexion réussie');
            } catch (\Exception $e) {
                return response('Connexion échouée', 500);
            }
        }
        abort(404);
    }

}
