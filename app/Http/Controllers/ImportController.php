<?php

namespace App\Http\Controllers;
use App\Imports\ArticlesImport;
use App\Imports\FournisseursImport;
use App\Imports\PaiementsImport;
use App\Imports\StocksImport;
use App\Imports\VentesImport;
use App\Models\Achat;
use App\Models\AchatLigne;
use App\Models\Compte;
use App\Models\Fournisseur;
use App\Models\Importation;
use App\Models\Magasin;
use App\Models\Paiement;
use App\Models\Stock;
use App\Models\TransactionStock;
use App\Services\FileService;
use App\Services\GlobalService;
use App\Services\LogService;
use App\Services\ModuleService;
use App\Services\PaiementService;
use App\Services\ReferenceService;
use App\Services\StockService;
use App\Traits\DocumentHelper;
use App\Traits\ImportationHelper;
use Exception;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Imports\ClientsImport;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Validation\Rule;
use App\Models\Vente;
use App\Models\VenteLigne;
use App\Models\Client;
use App\Models\Article;
use App\Models\Unite;
use App\Models\Famille;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use function Laravel\Prompts\error;
use Symfony\Component\HttpFoundation\Response;

class ImportController extends Controller
{
    use ImportationHelper;
    public function vente_page()
    {
        $this->guard_custom(['importer.*']);
        $o_magasins = Magasin::all();
        return view("importations.ventes",compact('o_magasins'));
    }
    public function achat_page()
    {
        $this->guard_custom(['importer.*']);

        $o_magasins = Magasin::all();
        return view("importations.achats",compact('o_magasins'));
    }
    public function load_import_file($file)
    {
        $this->guard_custom(['importer.*']);
        return $this->load($file);
    }

    public function afficher(Request $request, $id)
    {
        $this->guard_custom(['importer.*']);
        $importation = Importation::where('id', $id)->first();
        $date_importation = $importation->created_at->format('Y-m-d');
        $path = 'public/logs/imports-'.$date_importation.'.log';
        $log_file = Storage::disk('external_storage')->path($path);
        $todayDate = Carbon::now()->toDateString();
        $logContent= [];

        if (file_exists($log_file)) {
            $file = fopen($log_file, 'r');
            while (($line = fgets($file)) !== false) {
                if (strpos($line, $todayDate) !== false && strpos($line, $importation->reference) !== false) {
                    // Split the line by space to separate the date, hour, reference, and code
                    $parts = explode(' ', $line);
                    // Get the date, hour, reference, and code from the split parts
                    $date = $parts[0];
                    $hour = $parts[1];
                    $reference = $parts[2];
                    // Join the remaining parts to get the message
                    $message = implode(' ', array_slice($parts, 3));
                    // Store the parsed line as an array
                    $logContent[] = [
                        'date' => $date,
                        'hour' => $hour,
                        'reference' => $reference,
                        'message' => $message,
                    ];
                }
            }
            fclose($file);
        }
        if ($request->ajax()) {
            $logCollection = collect($logContent);
            $table = DataTables::of($logCollection);
            $table ->addColumn('date', function ($row) {
                return $row['date'] .'  '. $row['hour'];
            });$table ->addColumn('message', function ($row) {
                return $row['message'];
            });
            return $table->make();
        }

        return view('importations.afficher' ,['logContent' => $logContent], compact('importation'));
    }


    public function liste(Request $request)
    {
        $this->guard_custom(['importer.*']);
        if ($request->ajax()) {
            $o_importations = Importation::query()->with('magasin')->orderBy('created_at', 'desc');

            $table = DataTables::of($o_importations)
                ->addColumn('fichier_download',function ($row) {
                    $route = route("import.file.load", ["file" => $row->fichier_path]);
                    return '<a class="text-info text-decoration-underline" href="' . $route . '" >' . $row->fichier_path . '</a>';
                })
                ->rawColumns(['fichier_download'])
                ->addColumn('magasin',function ($row){
                    return $row->magasin ? $row->magasin->reference : '--';
                });

            $table->addColumn('actions', function ($row) {
                $crudRoutePart = 'importations';
                $show = 'afficher';
//                $delete = 'supprimer';
//                $edit = 'modifier';
                $id = $row->id;
                return view('partials.__datatable-action', compact('id', 'crudRoutePart', 'show'));
            });
            return $table->make();
        }
        return view("importations.liste");
    }

    public function produit_page()
    {
        $this->guard_custom(['importer.*']);
        $codeBarre = GlobalService::get_code_barre();
        return view("importations.produits",compact('codeBarre'));
    }

    public function client_page()
    {
        $this->guard_custom(['importer.*']);
        return view("importations.clients");
    }

    public function paiement_page()
    {
        $this->guard_custom(['importer.*']);
        $o_magasins = Magasin::all();
        return view("importations.paiements", compact('o_magasins'));
    }

    public function stock_page()
    {
        $this->guard_custom(['importer.*']);
        $o_magasins = Magasin::all();
        return view("importations.stocks", compact('o_magasins'));
    }

    public function fournisseur_page()
    {
        $this->guard_custom(['importer.*']);
        return view("importations.fournisseurs");
    }

    public function importer_client(Request $request)
    {
        $this->guard_custom(['importer.*']);
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
        $importation = new Importation();
        $importation->type = 'Clients' ;
        $importation->statut =  'Importation échoué';
        $importation->reference = 'CLI-'.Carbon::now()->format('YmdHis');
        if ($request->file('file')) {
            $file = $request->file('file');
            $fileName = $this->store_import_file($file);
            $importation->fichier_path= $fileName;
        }
        $importation->save();

        $headings = (new HeadingRowImport)->toArray($request->file('file'));
        $headerRow = $headings[0][0];
        $requiredHeaders = [
            "forme_juridique",
            "reference",
            "raison_sociale",
            "ice",
            "email",
            "telephone",
            "note",
            "adresse",
        ];

        // Check if all required header values are present
        $missingHeaders = array_diff($requiredHeaders, $headerRow);
        $headerLabels = [
            "forme_juridique" => "Forme Juridique",
            "reference" => "Référence",
            "raison_sociale" => "Dénomination",
            "ice" => "ICE",
            "email" => "Email",
            "telephone" => "Telephone",
            "note" => "Note",
            "adresse" => "Adresse",
        ];

        $missingHeadersString = implode(", ", array_map(function ($header) use ($headerLabels) {
            return $headerLabels[$header];
        }, $missingHeaders));

        if (!empty($missingHeadersString)) {
            LogService::logExceptionImports(new Exception("Les en-têtes suivants sont manquants dans le fichier Excel : $missingHeadersString."),$importation->reference );
            return redirect()->route('importations.afficher', $importation->id)->with('error', "Les en-têtes suivants sont manquants dans le fichier Excel : $missingHeadersString.");
        }

        $data = Excel::toArray(new ClientsImport, $request->file('file'));
        if (empty($data) || (count($data) === 1 && empty($data[0]))) {
            LogService::logExceptionImports(new Exception("Le fichier Excel est vide."),$importation->reference );
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Le fichier Excel est vide.');
        }
        $formeJuridiqueMapping = [
            'S.A.R.L' => 'sarl',
            'Personne Physique' => 'personne_physique',
            'Auto Entrepreneur' => 'auto_entrepreneur',
            'Société Anonyme' => 'sa',
            'Société Anonyme Simplifiée' => 'sas',
            'groupement d’intérêt économique' => 'gie',
            'Société en nom collectif' => 'snc',
            'Société en Commandite par Actions' => 'sca',
            'Société en Commandite Simple' => 'scs',
            'Particulier' => 'particulier'
        ];
        $errors = [];

        foreach ($data[0] as $index => $row) {
            $validator = Validator::make($row, [
                'forme_juridique' => 'required|in:' . implode(',', array_keys($formeJuridiqueMapping)),
                'reference' => 'nullable|max:20|unique:clients,reference',
                'raison_sociale' => 'required|max:255|unique:clients,nom',
                'ice' => 'nullable',
                'email' => 'nullable|email',
                'telephone' => 'nullable|max:255',
                'note' => 'nullable',
                'adresse' => 'nullable|string|max:255',
            ]);
            if ($validator->fails()) {
                $rowNumber = $index + 2;
                foreach ($validator->errors()->all() as $errorMessage) {
                    $errors[] = "Erreur de validation dans la ligne {$rowNumber}: " . $errorMessage;
                }
            }
        }
        if (!empty($errors)) {
            foreach ($errors as $error) {
                LogService::logExceptionImports(new Exception($error),$importation->reference );
            }
            return redirect()->route('importations.afficher', $importation->id )->with('error', 'Des erreurs de validation ont été trouvées. Veuillez consulter les logs pour plus de détails.');
        }
        DB::beginTransaction();
        try {

            Excel::import(new ClientsImport, $request->file('file'));

            $importation->statut= 'Importation réussie';
            $importation->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Une erreur inattendue s\'est produite : ' . $e->getMessage());
        }

        return redirect()->route('importer-liste')->with('success', 'Données importées avec succès.');

    }

    public function importer_fournisseur(Request $request)
    {
        $this->guard_custom(['importer.*']);
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $importation = new Importation();
        $importation->type = 'Fournisseurs' ;
        $importation->statut =  'Importation échoué';
        $importation->reference = 'FRS-'.Carbon::now()->format('YmdHis');
        if ($request->file('file')) {
            $file = $request->file('file');
            $fileName = $this->store_import_file($file);
            $importation->fichier_path= $fileName;
        }
        $importation->save();

        $headings = (new HeadingRowImport)->toArray($request->file('file'));
        $headerRow = $headings[0][0];
        $requiredHeaders = [
            "forme_juridique",
            "reference",
            "raison_sociale",
            "ice",
            "email",
            "telephone",
            "note",
            "adresse",
        ];

        // Check if all required header values are present
        $missingHeaders = array_diff($requiredHeaders, $headerRow);
        $headerLabels = [
            "forme_juridique" => "Forme Juridique",
            "reference" => "Référence",
            "raison_sociale" => "Dénomination",
            "ice" => "ICE",
            "email" => "Email",
            "telephone" => "Telephone",
            "note" => "Note",
            "adresse" => "Adresse",
        ];

        $missingHeadersString = implode(", ", array_map(function ($header) use ($headerLabels) {
            return $headerLabels[$header];
        }, $missingHeaders));

        if (!empty($missingHeadersString)) {
            LogService::logExceptionImports(new Exception("Les en-têtes suivants sont manquants dans le fichier Excel : $missingHeadersString."),$importation->reference );
            return redirect()->route('importations.afficher', $importation->id)->with('error', "Les en-têtes suivants sont manquants dans le fichier Excel : $missingHeadersString.");
        }
        $data = Excel::toArray(new FournisseursImport(), $request->file('file'));
        if (empty($data) || (count($data) === 1 && empty($data[0]))) {
            LogService::logExceptionImports(new Exception("Le fichier Excel est vide."),$importation->reference );
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Le fichier Excel est vide.');
        }
        $formeJuridiqueMapping = [
            'S.A.R.L' => 'sarl',
            'Personne Physique' => 'personne_physique',
            'Auto Entrepreneur' => 'auto_entrepreneur',
            'Société Anonyme' => 'sa',
            'Société Anonyme Simplifiée' => 'sas',
            'groupement d’intérêt économique' => 'gie',
            'Société en nom collectif' => 'snc',
            'Société en Commandite par Actions' => 'sca',
            'Société en Commandite Simple' => 'scs',
            'Particulier' => 'particulier'
        ];
        $errors= [];
        foreach ($data[0] as $index => $row) {
            $validator = Validator::make($row, [
                'forme_juridique' => 'required|in:' . implode(',', array_keys($formeJuridiqueMapping)),
                'reference' => 'nullable|max:20|unique:fournisseurs,reference',
                'raison_sociale' => 'required|max:255|unique:fournisseurs,nom',
                'ice' => 'nullable',
                'email' => 'nullable|email',
                'telephone' => 'nullable|max:255',
                'note' => 'nullable',
                'adresse' => 'nullable|string|max:255',
            ]);
            if ($validator->fails()) {
                $rowNumber = $index + 2;
                foreach ($validator->errors()->all() as $errorMessage) {
                    $errors[] = "Erreur de validation dans la ligne {$rowNumber}: " . $errorMessage;
                }
            }
        }
        if (!empty($errors)) {
            foreach ($errors as $error) {
                LogService::logExceptionImports(new Exception($error),$importation->reference );
            }
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Des erreurs de validation ont été trouvées. Veuillez consulter les logs pour plus de détails.');
        }

        DB::beginTransaction();
        try {
            Excel::import(new FournisseursImport(), $request->file('file'));
            $importation->statut= 'Importation réussie';
            $importation->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Une erreur inattendue s\'est produite : ' . $e->getMessage());
        }

        return redirect()->route('importer-liste')->with('success', 'Données importées avec succès.');

    }

    public function importer_stock(Request $request)
    {
        $this->guard_custom(['importer.*']);
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'i_date' => 'required',
            'magasin'=>'required|exists:magasins,id',
        ]);
        $magasin = Magasin::findOrFail($request->get('magasin'));
        //transform date to y-m-d
        $date = $request->get('i_date');
        $carbonDate = Carbon::createFromFormat('d/m/Y', $date);
        $date_effet = $carbonDate->format('Y-m-d');

        //creer une importation
        $importation = new Importation();
        $importation->type = 'Stocks' ;
        $importation->date_effet =  $date_effet;
        $importation->magasin_id =  $magasin->id;
        $importation->statut =  'Importation échoué';
        $importation->reference = 'STOCK-'.Carbon::now()->format('YmdHis');
        if ($request->file('file')) {
            $file = $request->file('file');
            $fileName = $this->store_import_file($file);
            $importation->fichier_path= $fileName;
        }
        $importation->save();


        $headings = (new HeadingRowImport)->toArray($request->file('file'));
        $headerRow = $headings[0][0];
        $requiredHeaders = [
            "reference_article",
            "designation",
            "quantite",
        ];

        // Check if all required header values are present
        $missingHeaders = array_diff($requiredHeaders, $headerRow);
        $headerLabels = [
            "reference_article" => "Référence Article",
            "designation" => "Designation",
            "quantite" => "Quantité",

        ];

        $missingHeadersString = implode(", ", array_map(function ($header) use ($headerLabels) {
            return $headerLabels[$header];
        }, $missingHeaders));

        if (!empty($missingHeadersString)) {
            LogService::logExceptionImports(new Exception("Les en-têtes suivants sont manquants dans le fichier Excel : $missingHeadersString."),$importation->reference );
            return redirect()->route('importations.afficher', $importation->id)->with('error', "Les en-têtes suivants sont manquants dans le fichier Excel : $missingHeadersString.");
        }

        $data = Excel::toArray(new StocksImport(), $request->file('file'));
        if (empty($data) || (count($data) === 1 && empty($data[0]))) {
            LogService::logExceptionImports(new Exception("Le fichier Excel est vide."),$importation->reference );
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Le fichier Excel est vide.');
        }
        $errors=[];
        foreach ($data[0] as $index => $row) {
            $validator = Validator::make($row, [
                'reference_article' => 'nullable|exists:articles,reference',
                'designation' => 'required_if:reference_article,null',
                'quantite' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                $rowNumber = $index + 2;
                foreach ($validator->errors()->all() as $errorMessage) {
                    $errors[] = "Erreur de validation dans la ligne {$rowNumber}: " . $errorMessage;
                }
            }
        }
        if (!empty($errors)) {
            foreach ($errors as $error) {
                LogService::logExceptionImports(new Exception($error),$importation->reference );
            }
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Des erreurs de validation ont été trouvées. Veuillez consulter les logs pour plus de détails.');
        }
        DB::beginTransaction();
        try {
            $transactions = TransactionStock::where('stockable_type', Importation::class)
                ->where('magasin_id', $magasin->id)
                ->get();
            foreach ($transactions as $transaction) {
                $transaction->delete();
            }
            foreach ($data[0] as $index => &$row) {
                if($row['reference_article']){
                    $article = Article::where('reference', $row['reference_article'])->first();
                }elseif($row['designation']){
                    $article = Article::where('designation', $row['designation'])->first();
                }

                if ($row['quantite'] > 0) {
                    StockService::stock_entre($article->id,  $row['quantite'], $date_effet, Importation::class, $importation->id,$magasin->id);
                } elseif ($row['quantite'] < 0) {
                    StockService::stock_sortir($article->id,  abs($row['quantite']), $date_effet, Importation::class, $importation->id,$magasin->id);
                }
            }
            $importation->statut =  'Importation réussie';
            $importation->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Une erreur inattendue s\'est produite : ' . $e->getMessage());
        }



        return redirect()->route('importer-liste')->with('success', 'Données importées avec succès.');

    }

    public function telecharger(String $path)
    {
        $this->guard_custom(['importer.*']);
        if(FileService::getStoragePath($path)){
            $file = storage_path('app/uploads/excel_imports/' . $path);
            return response()->download($file, $path, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'inline; filename="' . $path . '"'
            ]);
        }else {
            abort(404);
        }

    }

    public function importer_produit(Request $request)
    {
        $this->guard_custom(['importer.*']);
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
        $codeBarre = GlobalService::get_code_barre();
        $importation = new Importation();
        $importation->type = 'Produits' ;
        $importation->statut =  'Importation échoué';
        $importation->reference = 'ART-'.Carbon::now()->format('YmdHis');
        if ($request->file('file')) {
            $file = $request->file('file');
            $fileName = $this->store_import_file($file);
            $importation->fichier_path= $fileName;
        }
        $importation->save();


        $headings = (new HeadingRowImport)->toArray($request->file('file'));
        $headerRow = $headings[0][0];
        if ($codeBarre) {
            $requiredHeaders = [
                "reference",
                "code_barre",
                "designation",
                "nom_famille",
                "nom_unite",
                "prix_vente",
                "taxe",
                "stockable"
            ];
        }else {
            $requiredHeaders = [
                "reference",
                "designation",
                "nom_famille",
                "nom_unite",
                "prix_vente",
                "taxe",
                "stockable"
            ];
        }


        // Check if all required header values are present
        $missingHeaders = array_diff($requiredHeaders, $headerRow);
        if ($codeBarre){
            $headerLabels = [
                "reference" => "Référence",
                "code_barre"=>"Code barre",
                "designation" => "Designation",
                "nom_famille" => "Nom Famille",
                "nom_unite" => "Nom Unité",
                "prix_vente" => "Prix Vente",
                "taxe" => "Taxe",
                "stockable"=>"Stockable"
            ];
        }else {
            $headerLabels = [
                "reference" => "Référence",
                "designation" => "Designation",
                "nom_famille" => "Nom Famille",
                "nom_unite" => "Nom Unité",
                "prix_vente" => "Prix Vente",
                "taxe" => "Taxe",
                "stockable"=>"Stockable"
            ];
        }


        $missingHeadersString = implode(", ", array_map(function ($header) use ($headerLabels) {
            return $headerLabels[$header];
        }, $missingHeaders));

        if (!empty($missingHeadersString)) {
            LogService::logExceptionImports(new Exception("Les en-têtes suivants sont manquants dans le fichier Excel : $missingHeadersString."),$importation->reference );
            return redirect()->route('importations.afficher', $importation->id)->with('error', "Les en-têtes suivants sont manquants dans le fichier Excel : $missingHeadersString.");
        }
        $data = Excel::toArray(new ArticlesImport(), $request->file('file'));
        if (empty($data) || (count($data) === 1 && empty($data[0]))) {
            LogService::logExceptionImports(new Exception("Le fichier Excel est vide."),$importation->reference );
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Le fichier Excel est vide.');
        }

        foreach ($data[0] as $index => $row) {
            if ($codeBarre){
                $rules = [
                    'reference' => 'nullable|max:50|unique:articles,reference',
                    'code_barre' => 'nullable|max:200|unique:articles,code_barre',
                    'designation' => 'required|max:255',
                    'nom_famille' => 'nullable',
                    'nom_unite' => 'required|exists:unites,nom',
                    'prix_vente' => 'required|numeric',
                    'taxe' => 'required|numeric',
                    'stockable' => ['required', 'in:oui,non'],
                ];
            }else {
                $rules = [
                    'reference' => 'nullable|max:20|unique:articles,reference',
                    'designation' => 'required|max:255',
                    'nom_famille' => 'nullable',
                    'nom_unite' => 'required|exists:unites,nom',
                    'prix_vente' => 'required|numeric',
                    'taxe' => 'required|numeric',
                    'stockable' => ['required', 'in:oui,non'],
                ];
            }
            $validator = Validator::make($row, $rules);
            if ($validator->fails()) {
                $rowNumber = $index + 2;
                foreach ($validator->errors()->all() as $errorMessage) {
                    $errors[] = "Erreur de validation dans la ligne {$rowNumber}: " . $errorMessage;
                }
            }
        }
        if (!empty($errors)) {
            foreach ($errors as $error) {
                LogService::logExceptionImports(new Exception($error),$importation->reference );
            }
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Des erreurs de validation ont été trouvées. Veuillez consulter les logs pour plus de détails.');
        }


        DB::beginTransaction();
        try {
            foreach ($data[0] as $index => &$row) {
                if (!empty($row['nom_famille'])) {
                    $famille = Famille::where('nom', $row['nom_famille'])->first();
                    if ($famille) {
                        $row['famille_id'] = $famille->id;
                        unset($row['nom_famille']);
                    } else {
                        $famille = new Famille() ;
                        $famille->nom = $row['nom_famille'] ;
                        $famille->couleur = "#3b5461" ;
                        $famille->save() ;
                        $row['famille_id'] = $famille->id;
                        unset($row['nom_famille']);
                    }
                }

                if (!empty($row['nom_unite'])) {
                    $unite = Unite::where('nom', $row['nom_unite'])->first();
                    if ($unite) {
                        $row['unite_id'] = $unite->id;
                        unset($row['nom_unite']);
                    }
                }
                $row['prix_achat'] = 1;

                if (!$row['reference']) {
                    $row['reference'] = ReferenceService::generateReference('art');
                    ReferenceService::incrementCompteur('art');
                }
                $article = new Article();
                $article->designation = $row['designation'];
                if ($codeBarre){
                    $article->code_barre = $row['code_barre'];
                }
                $article->reference = $row['reference'];
                $article->prix_achat = 1;
                $article->prix_vente = $row['prix_vente'];
                $article->unite_id = $row['unite_id'];
                $article->famille_id = $row['famille_id'] ?? null;
                $article->taxe = $row['taxe'];
                $article->stockable = ($row['stockable'] === 'oui') ? '1' : '0';
                $article->save();
            }

            $importation->statut =  'Importation réussie';
            $importation->save();

            DB::commit();
        } catch (\Exception $e) {
            LogService::logException($e);
            DB::rollBack();
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Une erreur inattendue s\'est produite : ' . $e->getMessage());
        }

        return redirect()->route('importer-liste')->with('success', 'Données importées avec succès.');

    }

    public function importer_vente(Request $request)
    {
        $this->guard_custom(['importer.*']);
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'magasin'=>'nullable|exists:magasins,id',
        ]);
        if($request->get('magasin')){
            $magasin = Magasin::findOrFail($request->get('magasin'));
        }else{
            $magasin = Magasin::first();
        }
        $importation = new Importation();
        $importation->type = 'Ventes' ;
        $importation->magasin_id = $magasin->id ;
        $importation->statut =  'Importation échoué';
        $importation->reference = 'VENTE-'.Carbon::now()->format('YmdHis');
        if ($request->file('file')) {
            $file = $request->file('file');
            $fileName = $this->store_import_file($file);
            $importation->fichier_path= $fileName;
        }
        $importation->save();


        $headings = (new HeadingRowImport)->toArray($request->file('file'));
        $headerRow = $headings[0][0];
        $requiredHeaders = [
            "reference_vente",
            "reference_client",
            "date_document",
            "date_echeance",
            "reference_article",
            "nom_article",
            "prix_vente_ht",
            "tva",
            "nom_unite",
            "quantite",
            "type_reduction",
            "reduction",
        ];

        // Check if all required header values are present
        $missingHeaders = array_diff($requiredHeaders, $headerRow);
        $headerLabels = [
            "reference_vente" => "Référence Vente",
            "reference_client" => "Référence Client",
            "date_document" => "Date Document",
            "date_echeance" => "Date Echéance",
            "reference_article" => "Référence Article",
            "nom_article" => "Nom Article",
            "prix_vente_ht" => "Prix Vente HT",
            "tva" => "TVA",
            "nom_unite" => "Nom Unité",
            "quantite" => "Quantité",
            "type_reduction" => "Type Reduction",
            "reduction" => "Reduction",
        ];
        // Check for missing headers
        $missingHeadersString = implode(", ", array_map(function ($header) use ($headerLabels) {
            return $headerLabels[$header];
        }, $missingHeaders));

        if (!empty($missingHeadersString)) {
            LogService::logExceptionImports(new Exception("Les en-têtes suivants sont manquants dans le fichier Excel : $missingHeadersString."),$importation->reference );
            return redirect()->route('importations.afficher', $importation->id)->with('error', "Les en-têtes suivants sont manquants dans le fichier Excel : $missingHeadersString.");
        }
        $data = Excel::toArray(new VentesImport(), $request->file('file'));
        if (empty($data) || (count($data) === 1 && empty($data[0]))) {
            LogService::logExceptionImports(new Exception("Le fichier Excel est vide."),$importation->reference );
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Le fichier Excel est vide.');
        }

        foreach ($data[0] as $index => $row) {
            $validator = Validator::make($row, [
                'reference_vente' => 'required|max:20|unique:ventes,reference',
                'reference_client' => 'required|max:20|exists:clients,reference',
                'date_document' => 'required|date_format:d/m/Y',
                'date_echeance' => 'required|date_format:d/m/Y',
                'reference_article' => 'nullable|exists:articles,reference',
                'nom_article' => 'required_if:reference_article,null',
                'prix_vente_ht' => 'required|numeric',
                'nom_unite' => 'required|exists:unites,nom',
                'quantite' => 'required|numeric',
                'type_reduction' => 'nullable|in:fixe,pourcentage',
                'reduction' => 'nullable|numeric',
                'tva' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                $rowNumber = $index + 2;
                foreach ($validator->errors()->all() as $errorMessage) {
                    $errors[] = "Erreur de validation dans la ligne {$rowNumber}: " . $errorMessage;
                }
            }
        }
        if (!empty($errors)) {
            foreach ($errors as $error) {
                LogService::logExceptionImports(new Exception($error),$importation->reference );
            }
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Des erreurs de validation ont été trouvées. Veuillez consulter les logs pour plus de détails.');
        }

        foreach ($data[0] as $index => $row) {
            $date = Carbon::createFromFormat('d/m/Y', $row['date_document']);
            $data[0][$index]['date_document'] = $date->toDateString();

            $date = Carbon::createFromFormat('d/m/Y', $row['date_echeance']);
            $data[0][$index]['date_echeance'] = $date->toDateString();

        }


        $organizedData = [];
        $previousVente = null;

        // Iterate over the data
        foreach ($data[0] as $entry) {
            $venteReference = $entry['reference_vente'];

            // Check if vente reference is different from previous vente
            if ($venteReference !== $previousVente) {
                // Initialize vente details if it's a new vente
                $organizedData[$venteReference] = [
                    'reference_client' => $entry['reference_client'],
                    'date_document' => $entry['date_document'],
                    'date_echeance' => $entry['date_echeance'],
                    'vente_lignes' => [],
                ];
            }
            // Add vente ligne details to vente lignes array
            $organizedData[$venteReference]['vente_lignes'][] = [
                'reference_article' => $entry['reference_article'],
                'nom_article' => $entry['nom_article'],
                'prix_vente_ht' => $entry['prix_vente_ht'],
                'nom_unite' => $entry['nom_unite'],
                'quantite' => $entry['quantite'],
                'type_reduction' => $entry['type_reduction'] ?? 'fixe',
                'reduction' => $entry['reduction'] ?? 0,
                'tva' => $entry['tva'],
            ];

            $previousVente = $venteReference;
        }

        DB::beginTransaction();
        try {
            //create data
            foreach ($organizedData as $venteReference => $venteData) {
                $vente = Vente::where('reference', $venteReference)->first();
                if (!$vente) {
                    $client = Client::where('reference', $venteData['reference_client'])->firstOrFail();
                    $vente = Vente::create([
                        'created_by' => auth()->id(),
                        'reference' => $venteReference,
                        'client_id' => $client->id,
                        'magasin_id' => $magasin->id,
                        'date_document' => $venteData['date_document'],
                        'date_emission' => $venteData['date_document'],
                        'date_expiration' => $venteData['date_echeance'],
                        'statut' => 'validé',
                        'statut_paiement' => 'non_paye',
                        'type_document' => $request->input('fileType'),
                    ]);
                }
                // Iterate over vente lignes
                foreach ($venteData['vente_lignes'] as $venteLigneData) {
                    // Get article ID by searching with reference
                    if($venteLigneData['reference_article']){
                        $article = Article::where('reference', $venteLigneData['reference_article'])->firstOrFail();
                    }else{
                        $article = null;
                    }
                    // Get unit ID by searching with nom_unite
                    $unit = Unite::where('nom', $venteLigneData['nom_unite'])->firstOrFail();
                    // Create vente ligne record
                    $venteLigne = new VenteLigne();
                    $venteLigne->vente_id = $vente->id;
                    $venteLigne->article_id = $article->id ?? null;
                    $venteLigne->nom_article = $article->designation?? $venteLigneData['nom_article'];
                    $venteLigne->ht = $venteLigneData['prix_vente_ht'];
                    $venteLigne->unit_id = $unit->id;
                    $venteLigne->quantite = $venteLigneData['quantite'];
                    $venteLigne->mode_reduction = $venteLigneData['type_reduction'];
                    $venteLigne->reduction = $venteLigneData['reduction'] ?? 0;
                    $venteLigne->taxe = $venteLigneData['tva'];
                    $venteLigne->magasin_id = $magasin->id;


                    if ($venteLigneData['type_reduction'] === 'fixe') {
                        $reduction = $venteLigneData['reduction'];
                    } elseif ($venteLigneData['type_reduction'] === 'pourcentage') {
                        $reduction = round($venteLigneData['prix_vente_ht']  * (($venteLigneData['reduction'] ?? 0) / 100), 3);
                    }
                    $venteLigne->total_ttc = $this->calculate_ttc($venteLigneData['prix_vente_ht'], $reduction,$venteLigneData['tva'],$venteLigneData['quantite']);
                    $venteLigne->save();
                    $vente->total_ttc += $venteLigne->total_ttc;
                    $vente->total_reduction += $reduction *  $venteLigneData['quantite'];
                    $vente->total_ht += $venteLigneData['prix_vente_ht'] * $venteLigneData['quantite'];
                    $vente->total_tva += $this->calculate_tva_amount($venteLigneData['prix_vente_ht'], $reduction,$venteLigneData['tva'],$venteLigneData['quantite']);

                }
                $vente->solde = $vente->total_ttc;
                $vente->save();
            }

            $importation->statut= 'Importation réussie';
            $importation->save();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Une erreur est survenue lors de la création de la vente : ' . $e->getMessage());
        }

        return redirect()->route('importer-liste')->with('success', 'Données importées avec succès.');

    }
    public function importer_achat(Request $request)
    {
        $this->guard_custom(['importer.*']);
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'magasin'=>'nullable|exists:magasins,id',
        ]);
        if($request->get('magasin')){
            $magasin = Magasin::findOrFail($request->get('magasin'));
        }else{
            $magasin = Magasin::first();
        }
        $importation = new Importation();
        $importation->type = 'Achats' ;
        $importation->magasin_id = $magasin->id ;
        $importation->statut =  'Importation échoué';
        $importation->reference = 'ACHAT-'.Carbon::now()->format('YmdHis');
        if ($request->file('file')) {
            $file = $request->file('file');
            $fileName = $this->store_import_file($file);
            $importation->fichier_path= $fileName;
        }
        $importation->save();


        $headings = (new HeadingRowImport)->toArray($request->file('file'));
        $headerRow = $headings[0][0];
        $requiredHeaders = [
            "reference_achat_interne",
            "reference_achat_externe",
            "reference_fournisseur",
            "date_emission",
            "date_echeance",
            "reference_article",
            "nom_article",
            "prix_achat_ht",
            "tva",
            "nom_unite",
            "quantite",
            "type_reduction",
            "reduction",
        ];

        // Check if all required header values are present
        $missingHeaders = array_diff($requiredHeaders, $headerRow);
        $headerLabels = [
            "reference_achat_interne" => "Référence Achat Interne",
            "reference_achat_externe" => "Référence Achat Externe",
            "reference_fournisseur" => "Référence Fournisseur",
            "date_emission" => "Date Emission",
            "date_echeance" => "Date Echéance",
            "reference_article" => "Référence Article",
            "nom_article" => "Nom Article",
            "prix_achat_ht" => "Prix Achat HT",
            "tva" => "TVA",
            "nom_unite" => "Nom Unité",
            "quantite" => "Quantité",
            "type_reduction" => "Type Reduction",
            "reduction" => "Reduction",
        ];
        // Check for missing headers
        $missingHeadersString = implode(", ", array_map(function ($header) use ($headerLabels) {
            return $headerLabels[$header];
        }, $missingHeaders));

        if (!empty($missingHeadersString)) {
            LogService::logExceptionImports(new Exception("Les en-têtes suivants sont manquants dans le fichier Excel : $missingHeadersString."),$importation->reference );
            return redirect()->route('importations.afficher', $importation->id)->with('error', "Les en-têtes suivants sont manquants dans le fichier Excel : $missingHeadersString.");
        }

        $data = Excel::toArray(new VentesImport(), $request->file('file'));
        if (empty($data) || (count($data) === 1 && empty($data[0]))) {
            LogService::logExceptionImports(new Exception("Le fichier Excel est vide."),$importation->reference );
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Le fichier Excel est vide.');
        }

        foreach ($data[0] as $index => $row) {
            $validator = Validator::make($row, [
                'reference_achat_interne' => 'required|max:20|unique:achats,reference_interne',
                'reference_achat_externe' => 'required|max:20|unique:achats,reference',
                'reference_fournisseur' => 'required|max:20|exists:fournisseurs,reference',
                'date_emission' => 'required|date_format:d/m/Y',
                'date_echeance' => 'required|date_format:d/m/Y',
                'reference_article' => 'nullable|exists:articles,reference',
                'nom_article' => 'required_if:reference_article,null',
                'prix_achat_ht' => 'required|numeric',
                'nom_unite' => 'required|exists:unites,nom',
                'quantite' => 'required|numeric',
                'type_reduction' => 'nullable|in:fixe,pourcentage',
                'reduction' => 'nullable|numeric',
                'tva' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                $rowNumber = $index + 2;
                foreach ($validator->errors()->all() as $errorMessage) {
                    $errors[] = "Erreur de validation dans la ligne {$rowNumber}: " . $errorMessage;
                }
            }
        }
        if (!empty($errors)) {
            foreach ($errors as $error) {
                LogService::logExceptionImports(new Exception($error),$importation->reference );
            }
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Des erreurs de validation ont été trouvées. Veuillez consulter les logs pour plus de détails.');
        }

        foreach ($data[0] as $index => $row) {
            $date = Carbon::createFromFormat('d/m/Y', $row['date_emission']);
            $data[0][$index]['date_emission'] = $date->toDateString();

            $date = Carbon::createFromFormat('d/m/Y', $row['date_echeance']);
            $data[0][$index]['date_echeance'] = $date->toDateString();

        }


        $organizedData = [];
        $previousAchat = null;

        // Iterate over the data
        foreach ($data[0] as $entry) {
            $achatReference = $entry['reference_achat_interne'];

            // Check if vente reference is different from previous vente
            if ($achatReference !== $previousAchat) {
                // Initialize vente details if it's a new vente
                $organizedData[$achatReference] = [
                    'reference_fournisseur' => $entry['reference_fournisseur'],
                    'reference_achat_externe' => $entry['reference_achat_externe'],
                    'date_emission' => $entry['date_emission'],
                    'date_echeance' => $entry['date_echeance'],
                    'achat_lignes' => [],
                ];
            }
            // Add vente ligne details to vente lignes array
            $organizedData[$achatReference]['achat_lignes'][] = [
                'reference_article' => $entry['reference_article'],
                'nom_article' => $entry['nom_article'],
                'prix_achat_ht' => $entry['prix_achat_ht'],
                'nom_unite' => $entry['nom_unite'],
                'quantite' => $entry['quantite'],
                'type_reduction' => $entry['type_reduction'] ?? 'fixe',
                'reduction' => $entry['reduction'] ?? 0,
                'tva' => $entry['tva'],
            ];

            $previousAchat = $achatReference;
        }

        DB::beginTransaction();
        try {
            //create data
            foreach ($organizedData as $achatReference => $achatData) {
                $achat = Achat::where('reference_interne', $achatReference)->first();
                if (!$achat) {
                    $fournisseur = Fournisseur::where('reference', $achatData['reference_fournisseur'])->firstOrFail();
                    $achat = Achat::create([
                        'created_by' => auth()->id(),
                        'reference_interne' => $achatReference,
                        'reference' =>$achatData['reference_achat_externe'],
                        'fournisseur_id' => $fournisseur->id,
                        'magasin_id' => $magasin->id,
                        'date_emission' => $achatData['date_emission'],
                        'date_expiration' => $achatData['date_echeance'],
                        'statut' => 'validé',
                        'statut_paiement' => 'non_paye',
                        'type_document' => $request->input('fileType'),
                    ]);
                }

                // Iterate over vente lignes
                foreach ($achatData['achat_lignes'] as $index => $achatLigneData) {
                    // Get article ID by searching with reference
                    if($achatLigneData['reference_article']){
                        $article = Article::where('reference', $achatLigneData['reference_article'])->firstOrFail();
                    }else{
                        $article = null;
                    }
                    // Get unit ID by searching with nom_unite
                    $unit = Unite::where('nom', $achatLigneData['nom_unite'])->firstOrFail();
                    // Create vente ligne record
                    $achatLigne = new AchatLigne();
                    $achatLigne->achat_id = $achat->id;
                    $achatLigne->article_id = $article->id ?? null;
                    $achatLigne->nom_article = $article->designation?? $achatLigneData['nom_article'];
                    $achatLigne->ht = $achatLigneData['prix_achat_ht'];
                    $achatLigne->unite_id = $unit->id;
                    $achatLigne->quantite = $achatLigneData['quantite'];
                    $achatLigne->mode_reduction = $achatLigneData['type_reduction'];
                    $achatLigne->reduction = $achatLigneData['reduction'] ?? 0;
                    $achatLigne->taxe = $achatLigneData['tva'];
                    $achatLigne->position = $index;
                    $achatLigne->magasin_id = $magasin->id;


                    if ($achatLigneData['type_reduction'] === 'fixe') {
                        $reduction = $achatLigneData['reduction'];
                    } elseif ($achatLigneData['type_reduction'] === 'pourcentage') {
                        $reduction = round($achatLigneData['prix_achat_ht']  * (($achatLigneData['reduction'] ?? 0) / 100), 3);
                    }

                    $achatLigne->total_ttc = $this->calculate_ttc($achatLigneData['prix_achat_ht'], $reduction,$achatLigneData['tva'],$achatLigneData['quantite']);

                    $achatLigne->save();



                    $achat->total_ttc += $achatLigne->total_ttc;
                    $achat->total_reduction += $reduction *  $achatLigneData['quantite'];
                    $achat->total_ht += $achatLigneData['prix_achat_ht'] * $achatLigneData['quantite'];
                    $achat->total_tva += $this->calculate_tva_amount($achatLigneData['prix_achat_ht'], $reduction,$achatLigneData['tva'],$achatLigneData['quantite']);

                }
                $achat->debit = $achat->total_ttc;
                $achat->credit = 0;
                $achat->save();
            }

            $importation->statut= 'Importation réussie';
            $importation->save();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            LogService::logException($e);
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Une erreur est survenue lors de la création de la vente : ' . $e->getMessage());
        }

        return redirect()->route('importer-liste')->with('success', 'Données importées avec succès.');

    }


    function calculate_ttc(float $ht, float $reduction, float $tva, float $quantite): string
    {
        $ht = round($ht - $reduction, 3);
        $tva = (1 + $tva / 100);
        $ttc = round($ht * $tva, 3) * $quantite;
        return round($ttc, 3);

    }

    function calculate_tva_amount(float $ht, float $reduction, float $tva, float $quantite): float
    {
        return +number_format(round(($ht - $reduction) * ($tva / 100), 10) * $quantite, 3, '.', '');
    }

    public function importer_paiement(Request $request)
    {
        $this->guard_custom(['importer.*']);
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'payable_type' => 'required',
            'magasin' => 'nullable|exists:magasins,id',
        ]);
        if($request->get('magasin')){
            $magasin = Magasin::findOrFail($request->get('magasin'));
        }
        else{
            $magasin = Magasin::first();
        }
        $importation = new Importation();
        $importation->type = 'Paiements';
        $importation->magasin_id = $magasin->id;
        $importation->statut =  'Importation échoué';
        $importation->reference = 'PMT-'.Carbon::now()->format('YmdHis');
        if ($request->file('file')) {
            $file = $request->file('file');
            $fileName = $this->store_import_file($file);
            $importation->fichier_path= $fileName;
        }
        $importation->save();

        $headings = (new HeadingRowImport)->toArray($request->file('file'));
        $headerRow = $headings[0][0];
        $requiredHeaders = [
            "payable_reference",
            "methode_de_paiement",
            "montant_paye",
            "date_paiement",
            "compte",
            "chequelcn_reference",
            "chequelcn_date",
        ];

        // Check if all required header values are present
        $missingHeaders = array_diff($requiredHeaders, $headerRow);
        $headerLabels = [
            "payable_reference" => "Payable Référence",
            "methode_de_paiement"=> "Méthode de paiement",
            "montant_paye"=> "Montant Payé",
            "date_paiement"=> "Date Paiement",
            "compte" => "Compte",
            "chequelcn_reference"=> "Chèque/LCN Référence",
            "chequelcn_date" => "Chèque/LCN Date",
        ];

        $missingHeadersString = implode(", ", array_map(function ($header) use ($headerLabels) {
            return $headerLabels[$header];
        }, $missingHeaders));

        if (!empty($missingHeadersString)) {
            LogService::logExceptionImports(new Exception("Les en-têtes suivants sont manquants dans le fichier Excel : $missingHeadersString."),$importation->reference );
            return redirect()->route('importations.afficher', $importation->id)->with('error', "Les en-têtes suivants sont manquants dans le fichier Excel : $missingHeadersString.");
        }

        $data = Excel::toArray(new PaiementsImport(), $request->file('file'));
        if (empty($data) || (count($data) === 1 && empty($data[0]))) {
            LogService::logExceptionImports(new Exception("Le fichier Excel est vide."),$importation->reference );
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Le fichier Excel est vide.');
        }

        $methode_de_paiementMapping = [
            'Espèces' => 'especes',
            'Chèque' => 'cheque',
            'Carte bancaire' => 'carte',
            'TPE' => 'tpe',
            'Virement bancaire' => 'virement',
            'LCN' => 'lcn',
        ];
        $customMessages = [
            'date_format' => 'Le champ :attribute doit être une date valide au format : d/m/Y.',
        ];
        foreach ($data[0] as $index => $row) {
            $payable_type =$request->get('payable_type');
            $validator = Validator::make($row, [
                'methode_de_paiement' => 'required|in:' . implode(',', array_keys($methode_de_paiementMapping)),
                'payable_reference' => [
                    'required',
                    function ($attribute, $value, $fail) use ($payable_type, $headerLabels) {
                        if ($payable_type == 1) {
                            if (!\App\Models\Vente::where('reference', $value)->exists()) {
                                $fail('Le champ '.$headerLabels[$attribute].' doit exister dans les ventes.');
                            }
                        } elseif ($payable_type == 2) {
                            if (!\App\Models\Achat::where('reference_interne', $value)->exists()) {
                                $fail('le champ '.$headerLabels[$attribute].' doit exister dans les achats.');
                            }
                        } else {
                            $fail('Invalid payable type.');
                        }
                    },
                ],
                'montant_paye' => 'required|numeric|min:0',
                'date_paiement' => 'required|date_format:d/m/Y',
                'chequelcn_reference' => 'nullable',
                'chequelcn_date' => 'nullable|date_format:d/m/Y',
                'compte' => 'required|exists:comptes,nom',
            ], $customMessages, $headerLabels);
            if ($validator->fails()) {
                $rowNumber = $index + 2;
                foreach ($validator->errors()->all() as $errorMessage) {
                    $errors[] = "Erreur de validation dans la ligne {$rowNumber}: " . $errorMessage;
                }
            }
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                LogService::logExceptionImports(new Exception($error),$importation->reference );
            }
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Des erreurs de validation ont été trouvées. Veuillez consulter les logs pour plus de détails.');
        }

//        foreach ($data[0] as $index => $row) {
//            $date = Carbon::createFromFormat('d/m/Y', $row['date_paiement']);
//            $data[0][$index]['date_paiement'] = $date->toDateString();
//
//            if($row['chequelcn_date']){
//                $date = Carbon::createFromFormat('d/m/Y', $row['chequelcn_date']);
//                $data[0][$index]['chequelcn_date'] = $date->toDateString();
//            }
//        }

        DB::beginTransaction();
        try {
            foreach ($data[0] as $index => $row){

                //Paiement ventes
                if($request->get('payable_type') ==1) {
                    $vente = Vente::where('reference', $row['payable_reference'])->first();
                    $compte = Compte::where('nom',$row['compte'])->first();
                    $data = [
                        'i_date_paiement' => $row['date_paiement'],
                        'i_compte_id' => $compte->id,
                        'i_method_key' => $methode_de_paiementMapping[$row['methode_de_paiement']],
                        'i_montant' => $row['montant_paye'],
                        'i_comptable' => 1,
                    ];

                    if (!empty($row['chequelcn_reference'])) {
                        $data['i_reference'] = $row['chequelcn_reference'];
                    }

                    if (!empty($row['chequelcn_date'])) {
                        $data['i_date'] = $row['chequelcn_date'];
                    }
                    PaiementService::add_paiement(Vente::class, $vente->id, $data, $magasin->id);

                }elseif ($request->get('payable_type') ==2){
                    $achat = Achat::where('reference_interne', $row['payable_reference'])->first();
                    $compte = Compte::where('nom',$row['compte'])->first();
                    $data = [
                        'i_date_paiement' => $row['date_paiement'],
                        'i_compte_id' => $compte->id,
                        'i_method_key' => $methode_de_paiementMapping[$row['methode_de_paiement']],
                        'i_montant' => $row['montant_paye'],
                        'i_reference'=> $row['chequelcn_reference'],
                        'i_date' => $row['chequelcn_date'],
                        'i_comptable'=> 1,
                    ];
                    PaiementService::add_paiement(Achat::class, $achat->id, $data, $magasin->id);
                }
            }

            $importation->statut= 'Importation réussie';
            $importation->save();
            DB::commit();
        } catch (\Exception $e) {
            LogService::logException($e);
            DB::rollBack();
            return redirect()->route('importations.afficher', $importation->id)->with('error', 'Une erreur inattendue s\'est produite : ' . $e->getMessage(). $index+2 );
        }
        return redirect()->route('importer-liste')->with('success', 'Données importées avec succès.');

    }
}

