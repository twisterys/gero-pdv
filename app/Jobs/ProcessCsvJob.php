<?php
//
//namespace App\Jobs;
//
//use App\Models\Client;
//use App\Models\Vente;
//use App\Models\VenteLigne;
//use App\Models\Article;
//use \Illuminate\Http\Request;
//use Illuminate\Bus\Queueable;
//use Illuminate\Contracts\Queue\ShouldQueue;
//use Illuminate\Foundation\Bus\Dispatchable;
//use Illuminate\Queue\InteractsWithQueue;
//use Illuminate\Queue\SerializesModels;
//use Illuminate\Support\Facades\Log;
//
//class ProcessCsvJob implements ShouldQueue
//{
//    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
//
//    protected $filePath;
//    protected $userId;
//    protected $tenantId;
//
//    public function __construct($filePath, $userId, $tenantId)
//    {
//        $this->filePath = $filePath;
//        $this->userId = $userId;
//        $this->tenantId = $tenantId;
//    }
//
//    public function handle(Request $request)
//    {
//        if ($this->tenantId) {
//            tenancy()->initialize($this->tenantId);
//        }
//
//        $file = fopen(storage_path('app/' . $this->filePath), 'r');
//
//        if ($file === false) {
//            Log::error("Impossible d'ouvrir le fichier : " . $this->filePath);
//            return;
//        }
//
//        fgetcsv($file);
//
//        $vente = null;
//        $position = 0;
//        $validData = true;
//
//        while (($row = fgetcsv($file)) !== false) {
//            if (!is_array($row) || count($row) < 13) {
//                Log::warning("Ligne mal formatée : " . json_encode($row));
//                $validData = false;
//                break;
//            }
//            $clientReference = $row[5];
//            $client = Client::where('reference', $clientReference)->first();
//
//            if (!$client) {
//                Log::warning("Client non trouvé : {$clientReference}");
//                $validData = false;
//                break;
//            }
//            $itemReference = trim($row[11]);
//            $article = Article::where('reference', $itemReference)->first();
//
//            if (!$article) {
//                Log::error("Article non trouvé : {$itemReference}");
//                $validData = false;
//                break;
//            }
//
//            // TODO : creation du magasin par defaut
//            if (!$vente) {
//                $vente = Vente::create([
//                    'created_by' => $this->userId,
//                    'client_id' => $client->id,
//                    'statut' => 'brouillon',
//                    'date_document' => $row[7],
//                    'date_emission' => $row[7],
//                    'date_expiration' => $row[6] ?: $row[7],
//                    'total_ht' => 0,
//                    'total_ttc' => 0,
//                    'statut_paiment' => 'non_paye',
//                    'solde' => 0,
//                    'type_document' => 'bc',
//                    'encaisser' => 0,
//                    'magasin_id' => 1, // Remplacer par 4 si nécessaire
//                    'avoir_sold' => 0,
//                ]);
//            }
//
//            // Process each sale line
//            $itemPrice = floatval($row[9]);
//            $itemQuantity = intval($row[10]);
//            $itemTotal = floatval($row[12]);
//
//            VenteLigne::create([
//                'vente_id' => $vente->id,
//                'article_id' => $article->id,
//                'unit_id' => 1,
//                'nom_article' => $article->designation,
//                'description' => $article->description,
//                'ht' => $itemPrice,
//                'quantite' => $itemQuantity,
//                'taxe' => 0,
//                'reduction' => 0,
//                'total_ttc' => $itemTotal,
//                'mode_reduction' => 'pourcentage',
//                'position' =>  $position,
//                'revient' => 0,
//                'magasin_id' => 1,
//            ]);
//            $position++;
//
//            // Update totals in the Vente
//            $vente->total_ht += $itemPrice * $itemQuantity;
//            $vente->total_ttc += $itemTotal;
//            $vente->solde += $itemTotal;
//        }
//
//        // If the data was valid, save the Vente
//        if ($validData && $vente) {
//            $vente->save();
//            Log::info("Vente enregistrée avec succès.");
//        } else {
//            Log::error("Annulation de la création de la vente : données invalides.");
//        }
//
//        fclose($file);
//
//        // Si les données étaient invalides, ne pas supprimer le fichier
//        if ($validData && file_exists(storage_path('app/' . $this->filePath))) {
//            unlink(storage_path('app/' . $this->filePath));
//            Log::info("Fichier CSV supprimé.");
//        }
//        Log::info("Traitement du fichier CSV terminé.");
//    }
//}
