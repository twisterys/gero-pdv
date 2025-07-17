<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Client;
use App\Models\Famille;
use App\Models\Magasin;
use App\Models\Taxe;
use App\Models\Unite;
use App\Models\Vente;
use App\Models\VenteLigne;
use App\Models\WoocommerceImport;
use App\Models\WoocommerceSettings;
use App\Services\LogService;
use App\Services\ReferenceService;
use App\Traits\WoocommerceTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WoocommerceImportController extends Controller
{

    use WoocommerceTrait;

    public function liste(Request $request)
    {
        if ($request->ajax()) {
            $query = WoocommerceImport::query();
            $table = datatables($query)
                ->addColumn('action', function ($row) {
                    return '';
                })->addColumn('selectable_td', function ($row) {
                    $id = $row['id'];
                    return '<input type="checkbox" class="row-select form-check-input" value="' . $id . '">';
                })->editColumn('statut',function ($row){
                    $color = $row->statut === 'success' ? 'success' : 'danger';
                    $text = $row->statut === 'success' ? 'Importation réussie' :  'Importation échouée';
                    return '<span class="badge bg-'.$color.'">'.$text.'</span>';
                })->editColumn('magasin_id',function ($row){
                    return $row?->magasin->nom ?? '---';
                })->editColumn('type',function ($row){
                    $types =['orders' => 'Ventes', 'products' => 'Produits'];
                    return $types[$row->type];
                })
                ->rawColumns(['action', 'selectable_td','statut']);
            return $table->make(true);

        }
        $magasins = \request()->user()->magasins()->get();
        return view('importations.woocommerce', compact('magasins'));

    }

    public function importProducts(Request $request)
    {
        $woooCommerceSettings = WoocommerceSettings::first();
        if (!$woooCommerceSettings) {
            return response()->json(['message' => 'Woocommerce settings not found'], 404);
        }
        $wooCommerceLastImport = WoocommerceImport::where('type', 'products')->where('statut', 'success')->latest()->first();
        $options = [];

        if ($wooCommerceLastImport) {
            $options = ['after' => Carbon::createFromTimestamp($wooCommerceLastImport->last_imported_object)->toIso8601String()];
        }
        $products = $this->getAllProducts($options);
        if (count($products) == 0) {
            return response('Aucun produit à importer', 200);
        }
        $unite = Unite::where('defaut', 1)->first();
        \DB::beginTransaction();
        $latest_created_article = null;
        try {
            $price_to_take = $woooCommerceSettings->price_value;
            $existingArticles = Article::pluck('reference')->toArray();
            foreach ($products as $product) {
                if (in_array($product->sku, $existingArticles)) {
                    continue;
                }
                $o_article = new Article();
                $o_article->designation = $product->name;
                $o_article->reference = $product->sku;
                $o_article->description = $product->description;
                $o_article->prix_achat = 0;
                $o_article->prix_vente = $product->$price_to_take ?? 0;
                $o_article->stockable = $product->manage_stock;
                $o_article->unite_id = $unite->id;
                $o_article->taxe = 0;

                if (count($product->categories) > 0) {
                    foreach ($product->categories as $category) {
                        $famille_exists = Famille::where('nom', $category->name)->first();
                        if ($famille_exists) {
                            $o_article->famille_id = $famille_exists->id;
                            break;
                        }
                        $o_famille = new Famille();
                        $o_famille->nom = $category->name;
                        $o_famille->couleur = $this->random_color();
                        $o_famille->actif = 1;
                        $o_famille->save();
                        $o_article->famille_id = $o_famille->id;
                        break;
                    }
                }
                $o_article->save();
                $latest_created_article = $product->date_created;
            }
            WoocommerceImport::create([
                'reference' => 'WOOIMPR' . time(),
                'type' => 'products',
                'last_imported_object' => $latest_created_article,
                'statut' => 'success'
            ]);
            \DB::commit();
            return response('Articles importer avec succès', 200);
        } catch (\Exception $exception) {
            \DB::rollBack();
            LogService::logException($exception);
            WoocommerceImport::create([
                'reference' => 'WOOIMPR' . time(),
                'type' => 'products',
                'last_imported_object' => null,
                'statut' => 'failed'
            ]);
            return response("Une erreur s'est produite, les produits n'ont pas pu être importés", 500);
        }
    }

    public function importOrders(Request $request)
    {
        \Validator::make($request->all(), [
            'magasin_id' => 'nullable|exists:magasins,id',
            'type' => ['required', Rule::in(Vente::TYPES)]
        ], [], [
            'magasin_id' => 'Magasin',
            'type' => 'Type de vente'
        ])->validate();
        $woooCommerceSettings = WoocommerceSettings::first();
        if (!$woooCommerceSettings) {
            return response()->json(['message' => 'Woocommerce settings not found'], 404);
        }
        $wooCommerceLastImport = WoocommerceImport::where('type', 'orders')->where('statut', 'success')->latest()->first();
        $options = [];

        if ($wooCommerceLastImport) {
            $options = ['after' => Carbon::createFromTimestamp($wooCommerceLastImport->last_imported_object)->toIso8601String()];
        }
        $orders = $this->getAllOrders($options);
        if (count($orders) == 0) {
            return response('Aucun vente à importer', 200);
        }
        $magasin_id = $request->get('magasin_id') ?? Magasin::first()->id;
        $price_to_take = $woooCommerceSettings->price_value;
        $unite = Unite::where('defaut', 1)->first();
        $latest_created_article= null;
        \DB::beginTransaction();
        try {
            foreach ($orders as $order) {
                $data = [
                    'created_by' => auth()->id(),
                    'commercial_id' => null,
                    'commission_par_defaut' => null,
                    'reference' => null,
                    "statut" => "brouillon",
                    "objet" => null,
                    'date_document' => now()->toDateString(),
                    'date_emission' => $order->date_created,
                    'type_document' => $request->get('type'),
                    'statut_paiement' => 'non_paye',
                    'note' => null,
                    'magasin_id' => $magasin_id,

                ];
                if ($order->customer_id !== 0) {
                    $customer = $this->getCustomer($order->customer_id);
                    $o_client = Client::where('email', $customer->email)->first();
                    if ($o_client) {
                        $data['client_id'] = $o_client->id;
                    } else {
                        $o_client = new Client();
                        $o_client->nom = $customer->first_name . ' ' . $customer->last_name;
                        $o_client->reference = ReferenceService::generateReference('clt');
                        $o_client->email = $customer->email;
                        $o_client->telephone = $customer->phone;
                        $o_client->adresse = $customer->billing->address_1;
                        $o_client->ville = $customer->billing->city;
                        $o_client->forme_juridique_id = null;
                        $o_client->save();
                        $data['client_id'] = $o_client->id;
                    }
                } else {
                    $o_client = Client::where('email', $order->billing->email)->first();
                    if ($o_client) {
                        $data['client_id'] = $o_client->id;
                    } else {
                        $o_client = new Client();
                        $o_client->nom = $order->billing->first_name . ' ' . $order->billing->last_name;
                        $o_client->reference = ReferenceService::generateReference('clt');
                        $o_client->email = $order->billing->email;
                        $o_client->telephone = $order->billing->phone;
                        $o_client->adresse = $order->billing->address_1;
                        $o_client->ville = $order->billing->city;
                        $o_client->forme_juridique_id = null;
                        $o_client->save();
                        $data['client_id'] = $o_client->id;
                    }
                }
                $o_vente = Vente::create($data);
                $lignes = $order->line_items;
                if (count($lignes) > 0) {
                    foreach ($lignes as $key => $ligne) {
                        $reduction = 0;
                        $o_ligne = new VenteLigne();
                        $o_ligne->vente_id = $o_vente->id;
                        $o_ligne->unit_id = $unite->id;
                        $o_ligne->mode_reduction = 'fixe';
                        $o_ligne->nom_article = $ligne->name;
                        $o_ligne->description = null;
                        $o_ligne->ht = $ligne->total/ $ligne->quantity;
                        $o_ligne->revient = null;
                        $o_ligne->quantite = $ligne->quantity;
                        $o_ligne->taxe = 0;
                        $o_ligne->reduction =  0;
                        $o_ligne->total_ttc = $this->calculate_ttc($o_ligne->ht ?? 0.00, $reduction ?? 0.00, $o_ligne->taxe ?? 0, $o_ligne->quantite ?? 0.00);
                        $o_ligne->position = $key;
                        $o_ligne->magasin_id =  $magasin_id;
                        if ($ligne->sku) {
                            $article = Article::where('reference', $ligne->sku)->first();
                            if ($article) {
                                $o_ligne->article_id = $article->id;
                            }else {
                                $product = $this->woocommerceClient()->get('products/'.$ligne->product_id);
                                $o_article = new Article();
                                $o_article->designation = $product->name;
                                $o_article->reference = $product->sku;
                                $o_article->description = $product->description;
                                $o_article->prix_achat = 0;
                                $o_article->prix_vente = $product->$price_to_take ?? 0;
                                $o_article->stockable = $product->manage_stock;
                                $o_article->unite_id = $unite->id;
                                $o_article->taxe = 0;
                                if (count($product->categories) > 0) {
                                    foreach ($product->categories as $category) {
                                        $famille_exists = Famille::where('nom', $category->name)->first();
                                        if ($famille_exists) {
                                            $o_article->famille_id = $famille_exists->id;
                                            break;
                                        }
                                        $o_famille = new Famille();
                                        $o_famille->nom = $category->name;
                                        $o_famille->couleur = $this->random_color();
                                        $o_famille->actif = 1;
                                        $o_famille->save();
                                        $o_article->famille_id = $o_famille->id;
                                        break;
                                    }
                                }
                                $o_article->save();
                                $o_ligne->article_id = $o_article->id;
                            }
                        }
                        $o_ligne->save();
                    }
                    $o_vente->update([
                        'total_ht' => $order->total - $order->total_tax,
                        'total_tva' => $order->total_tax,
                        'total_reduction' => $order->discount_total,
                        'total_ttc' => $order->total,
                        'solde' => $order->total,
                    ]);
                }
                $latest_created_article = $order->date_created;
                ReferenceService::incrementCompteur('clt');
            }
            WoocommerceImport::create([
                'reference' => 'WOOIMPR' . time(),
                'type' => 'orders',
                'last_imported_object' => $latest_created_article,
                'statut' => 'success',
                'magasin_id' => $magasin_id
            ]);
            \DB::commit();
            return response('Ventes importer avec succès', 200);

        } catch (\Exception $exception) {
            \DB::rollBack();
            dd($exception->getMessage());
            LogService::logException($exception);
            WoocommerceImport::create([
                'reference' => 'WOOIMPR' . time(),
                'type' => 'orders',
                'last_imported_object' => null,
                'statut' => 'failed',
                'magasin_id' => $magasin_id
            ]);
            return response("Une erreur s'est produite, les ventes n'ont pas pu être importés", 500);
        }
    }

    function random_color_part()
    {
        return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    }

    function random_color()
    {
        return '#' . $this->random_color_part() . $this->random_color_part() . $this->random_color_part();
    }
    function calculate_ttc(float $ht, float $reduction, float $tva, float $quantite): string
    {
        $ht = round($ht - $reduction, 2);
        $tva = (1 + $tva / 100);
        $ttc = round($ht * $tva, 2) * $quantite;
        return round($ttc, 2);
    }
}
