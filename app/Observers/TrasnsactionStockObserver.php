<?php

namespace App\Observers;

use App\Models\Stock;
use App\Models\TransactionStock;

class TrasnsactionStockObserver
{
    /**
     * Handle the TransactionStock "created" event.
     */
    public function created(TransactionStock $transactionStock): void
    {
        $stock = Stock::where('article_id', $transactionStock->article_id)->first();
        $quantite = TransactionStock::where('article_id', $transactionStock->article_id)->sum('qte_entree') - TransactionStock::where('article_id', $transactionStock->article_id)->sum('qte_sortir');
        if ($stock) {
            $stock->update(
                [
                    'quantite' => $quantite
                ]
            );
        } else {
            Stock::create([
                'article_id' => $transactionStock->article_id,
                'quantite' => $quantite
            ]);
        }
    }

    /**
     * Handle the TransactionStock "updated" event.
     */
    public function updated(TransactionStock $transactionStock): void
    {
        //
    }

    /**
     * Handle the TransactionStock "deleted" event.
     */
    public function deleted(TransactionStock $transactionStock): void
    {
        $stock = Stock::where('article_id', $transactionStock->article_id)->first();
        $quantite = TransactionStock::where('article_id', $transactionStock->article_id)->sum('qte_entree') - TransactionStock::where('article_id', $transactionStock->article_id)->sum('qte_sortir');
        $stock->update(
            [
                'quantite' => $quantite
            ]
        );

    }

    /**
     * Handle the TransactionStock "restored" event.
     */
    public function restored(TransactionStock $transactionStock): void
    {
        //
    }

    /**
     * Handle the TransactionStock "force deleted" event.
     */
    public function forceDeleted(TransactionStock $transactionStock): void
    {
        //
    }
}
