<?php

namespace App\Services;

use App\Models\Article;
use App\Models\TransactionStock;
use Carbon\Carbon;

class StockService
{
    /**
     * @param int $article_id
     * @param float $quantite
     * @param string $date // format: Y-m-d
     * @param string $stockable_type
     * @param int $stockable_id
     * @return void
     */
    public static function stock_entre(int $article_id, float $quantite, string $date, string $stockable_type, int $stockable_id, int $magasin_id = 1): void
    {
        try {
            if (Article::find($article_id)->stockable) {
                TransactionStock::create([
                    'article_id' => $article_id,
                    'declencheur' => $stockable_type::DECLENCHEUR,
                    'qte_entree' => $quantite,
                    'qte_sortir' => 0,
                    'stockable_type' => $stockable_type,
                    'stockable_id' => $stockable_id,
                    'date' => Carbon::createFromFormat('Y-m-d', $date)->toDateString(),
                    'magasin_id' => $magasin_id,

                ]);
            }
        } catch (\Exception $exception) {
            LogService::logException($exception);
        }
    }

    /**
     * @param int $article_id
     * @param float $quantite
     * @param string $date // format: Y-m-d
     * @param string $stockable_type
     * @param int $stockable_id
     * @return void
     */
    public static function stock_sortir(int $article_id, float $quantite, string $date, string $stockable_type, int $stockable_id, int $magasin_id = 1): void
    {
        try {
            if (Article::find($article_id)->stockable) {
                TransactionStock::create([
                    'article_id' => $article_id,
                    'declencheur' => $stockable_type::DECLENCHEUR,
                    'qte_entree' => 0,
                    'qte_sortir' => $quantite,
                    'stockable_type' => $stockable_type,
                    'stockable_id' => $stockable_id,
                    'date' => Carbon::createFromFormat('Y-m-d', $date)->toDateString(),
                    'magasin_id' => $magasin_id,

                ]);
            }
        } catch (\Exception $exception) {
            LogService::logException($exception);
        }
    }

    public static function stock_revert(string $stockable_type, int $stockable_id): void
    {
        try {
            $o_stockable = $stockable_type::find($stockable_id);
            $transactions = $o_stockable->stock_transaction;
            foreach ($transactions as $transaction) {
                $transaction->delete();
            }
        } catch (\Exception $exception) {
            LogService::logException($exception);
        }
    }

    public static function getMagasinStock(int $magasin_id, int $article_id)
    {
        if (Article::find($article_id)->stockable) {
            return TransactionStock::where('magasin_id', $magasin_id)->where('article_id', $article_id)->selectRaw('(SUM(qte_entree) - SUM(qte_sortir)) as quantite')->first()?->quantite ?? 0.00;
        }
        return 0.00;
    }
}
