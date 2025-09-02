<?php

namespace App\Http\Controllers\Api\parfums;

use App\Http\Controllers\Controller;
use App\Models\PosSession;
use App\Models\Rebut;
use App\Services\ReferenceService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RebutController extends Controller
{
    public function sauvegarder(Request $request)
    {
        // L'intercepteur Axios ajoute session_id automatiquement
        Validator::make($request->all(), [
            'lignes' => 'required|array|min:1',
            'lignes.*.i_article_id' => 'required|exists:articles,id',
            'lignes.*.quantite_rebut' => 'required|numeric|min:0.0001',
        ], [], [
            'lignes' => 'Lignes de rebut',
            'lignes.*.i_article_id' => 'article',
            'lignes.*.quantite_rebut' => 'quantité de rebut',
        ])->validate();

        $sessionId = $request->get('session_id');
        $o_pos_session = PosSession::findOrFail($sessionId);
        $reference = "RBT" . \Carbon\Carbon::now()->format('YmdHis');

        DB::beginTransaction();
        try {
            $rebut = Rebut::create([
                'date_operation' => Carbon::now()->toDateString(),
                'reference' => $reference,
                'magasin_id' => $o_pos_session->magasin_id,
                'pos_session_id' => $o_pos_session->id,
            ]);

            foreach ($request->input('lignes', []) as $row) {
                $articleId = $row['i_article_id'];
                $qty = (float) $row['quantite_rebut'];
                if ($qty > 0) {
                    StockService::stock_sortir(
                        $articleId,
                        $qty,
                        Carbon::now()->format('Y-m-d'),
                        Rebut::class,
                        $rebut->id,
                        $o_pos_session->magasin_id
                    );
                }
            }

            ReferenceService::incrementCompteur('rbt');
            DB::commit();

            return response()->json(['message' => 'Rebut ajouté avec succès !', 'id' => $rebut->id]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erreur lors de la création du rebut', 'error' => $e->getMessage()], 422);
        }
    }

    public function liste(Request $request)
    {
        $sessionId = $request->get('session_id');
        if (!$sessionId) {
            return response()->json([]);
        }

        // On récupère les entêtes Rebut de la session
        $rebuts = Rebut::where('pos_session_id', $sessionId)
            ->orderByDesc('id')
            ->get();

        // Pour chaque Rebut, on extrait les transactions de stock liées (articles + quantités)
        $payload = $rebuts->map(function (Rebut $rebut) {
            $lines = DB::table('transaction_stocks as ts')
                ->join('articles as a', 'a.id', '=', 'ts.article_id')
                ->select(
                    'a.id as article_id',
                    DB::raw("COALESCE(a.designation, a.reference) as article"),
                    'ts.qte_sortir as quantity'
                )
                ->where('ts.stockable_type', Rebut::class)
                ->where('ts.stockable_id', $rebut->id)
                ->get()
                ->map(function ($row) {
                    return [
                        'article_id' => (int) $row->article_id,
                        'article' => $row->article ?? ('#' . $row->article_id),
                        'quantity' => (float) $row->quantity,
                    ];
                });

            return [
                'id' => $rebut->id,
                'reference' => $rebut->reference,
                'date_operation' => $rebut->date_operation,
                'lignes' => $lines,
            ];
        });

        return response()->json($payload);
    }
}
