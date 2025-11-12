<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('rapports')) {
            return; // Table inexistante, rien à faire
        }

        $rapports = [
            [
                'route' => 'mouvement-stock',
                'nom' => 'Mouvement de stock',
                'description' => "Historique détaillé des entrées, sorties et ajustements de stock pour tracer chaque mouvement.",
                'type' => 'stock',
            ],
            [
                'route' => 'achat_vente',
                'nom' => 'Synthèse achats & ventes',
                'description' => "Vue consolidée des achats, ventes et retours avec indicateurs financiers clés.",
                'type' => 'achat-vente',
            ],
            [
                'route' => 'vente-produit',
                'nom' => 'Ventes par produit',
                'description' => "Performances des produits vendus : quantités, montants et répartition par type de document.",
                'type' => 'achat-vente',
            ],
            [
                'route' => 'achat-produit',
                'nom' => 'Achats par produit',
                'description' => "Analyse des achats par article : quantités, coûts et retours associés.",
                'type' => 'achat-vente',
            ],
            [
                'route' => 'ca-client',
                'nom' => 'Chiffre d\'affaires client',
                'description' => "Répartition du CA par client pour identifier les comptes stratégiques et leur évolution.",
                'type' => 'statistiques',
            ],
            [
                'route' => 'tendance-produit',
                'nom' => 'Tendance produits',
                'description' => "Évolution des ventes par produit sur la période afin de détecter les produits en hausse ou baisse.",
                'type' => 'statistiques',
            ],
            [
                'route' => 'stock-produit',
                'nom' => 'Stock par produit',
                'description' => "Niveau actuel des stocks par article avec quantités disponibles.",
                'type' => 'stock',
            ],
            [
                'route' => 'stock-produit-legal',
                'nom' => 'Stock légal produit',
                'description' => "Comparaison des stocks réels vs seuils légaux / minimums fixés pour contrôle de conformité.",
                'type' => 'stock',
            ],
            [
                'route' => 'stock-produit-magasin',
                'nom' => 'Stock produit par magasin',
                'description' => "Répartition des stocks par magasin pour optimiser la logistique interne.",
                'type' => 'stock',
            ],
            [
                'route' => 'commerciaux',
                'nom' => 'Performance commerciaux',
                'description' => "Indicateurs de performance par commercial : ventes, montants et efficacité.",
                'type' => 'statistiques',
            ],
            // Attention : la route 'sessions.ventes' nécessite un paramètre {id} dans la définition de route.
            // L'appel route('rapports.sessions.ventes') sans ID échouera. Insérée ici pour répondre à la demande.
            [
                'route' => 'sessions.ventes',
                'nom' => 'Détail ventes session',
                'description' => "Ventes détaillées pour une session POS spécifique (sélection d\'une session requise).",
                'type' => 'pos.ventes',
            ],
        ];

        foreach ($rapports as $rapport) {
            if (!DB::table('rapports')->where('route', $rapport['route'])->exists()) {
                DB::table('rapports')->insert($rapport);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // On ne supprime pas les rapports insérés pour éviter la perte de configuration.
        // Si nécessaire, ajouter ici des suppressions ciblées.
    }
};

