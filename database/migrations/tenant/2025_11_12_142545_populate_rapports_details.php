<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/*
 * Insérer details pour les rapports de Gero PDV
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $detailsByRoute = [
            'tva' =>
                "À QUOI SERT CE RAPPORT ?\n\n" .
                "Ce rapport calcule la TVA à payer ou à récupérer sur une période, en tenant compte des encaissements et décaissements effectifs.\n\n" .
                "MÉTHODE DE CALCUL\n\n" .
                "Ce rapport applique la méthode des encaissements :\n" .
                "• La TVA n'est comptabilisée que lors du paiement effectif\n" .
                "• Si une facture est payée partiellement, seule la TVA correspondant à la partie payée est prise en compte\n\n" .
                "INDICATEURS AFFICHÉS\n\n" .
                "TVA COLLECTÉE (TVA sur les ventes) :\n" .
                "• Pour chaque paiement client reçu sur une facture de vente\n" .
                "• Calcul : (Montant encaissé / Montant TTC de la facture) × TVA totale de la facture\n" .
                "• Seules les factures de vente (fa) sont prises en compte\n\n" .
                "TVA DÉDUCTIBLE (TVA sur les achats) :\n" .
                "• Pour chaque paiement fournisseur effectué sur une facture d'achat\n" .
                "• Calcul : (Montant décaissé / Montant TTC de la facture) × TVA totale de la facture\n" .
                "• Seules les factures d'achat (faa) sont prises en compte\n\n" .
                "RÉSULTAT :\n" .
                "TVA à payer = TVA collectée - TVA déductible\n\n" .
                "Si positif : vous devez payer ce montant à l'administration fiscale\n" .
                "Si négatif : vous avez un crédit de TVA à récupérer\n\n" .
                "FILTRES DISPONIBLES\n\n" .
                "• Par période : sélectionnez la période de déclaration (mensuelle, trimestrielle, etc.)\n\n" .
                "NOTES IMPORTANTES\n\n" .
                "• Ce rapport se base sur les dates de paiement, pas sur les dates de facturation\n" .
                "• Seules les factures validées sont prises en compte\n" .
                "• Les autres types de documents (bons, devis) ne génèrent pas de TVA à déclarer\n" .
                "• Vérifiez toujours que vos paiements sont correctement saisis\n\n",

            'annuel' =>
                "À QUOI SERT CE RAPPORT ?\n\n" .
                "Ce rapport donne une vision complète de votre exercice comptable : ventes, achats, dépenses, trésorerie et créances/dettes.\n\n" .
                "SECTION VENTES\n\n" .
                "Chiffre d'affaires de l'année :\n" .
                "• CA TTC : total des factures de vente validées sur l'exercice\n" .
                "• CA HT : montant hors taxes\n\n" .
                "Encaissements :\n" .
                "• Total des paiements clients effectivement reçus sur l'exercice\n" .
                "• Basé sur les dates de paiement, pas les dates de facturation\n\n" .
                "Créances clients :\n" .
                "• Créances de l'année : factures de l'année non encore encaissées\n" .
                "• Créances cumulées au 1er jour de l'année : reste dû des années précédentes\n" .
                "• Total des créances : somme des deux lignes précédentes\n\n" .
                "SECTION ACHATS\n\n" .
                "Achats de l'année :\n" .
                "• Achats TTC : total des factures fournisseurs validées sur l'exercice\n" .
                "• Achats HT : montant hors taxes\n\n" .
                "Décaissements :\n" .
                "• Total des paiements fournisseurs effectués sur l'exercice\n\n" .
                "Dettes fournisseurs :\n" .
                "• Dettes de l'année : factures de l'année non encore payées\n" .
                "• Dettes cumulées au 1er jour de l'année : reste dû des années précédentes\n" .
                "• Total des dettes : somme des deux lignes précédentes\n\n" .
                "SECTION DÉPENSES\n\n" .
                "• Total des dépenses enregistrées sur l'exercice\n" .
                "• Affichage HT ou TTC selon votre choix\n\n" .
                "DOCUMENTS PRIS EN COMPTE\n\n" .
                "• Ventes : uniquement les factures de vente (fa) validées\n" .
                "• Achats : uniquement les factures d'achat (faa) validées\n" .
                "• Les autres types de documents ne sont pas comptabilisés\n\n" .
                "NOTES IMPORTANTES\n\n" .
                "• Ce rapport couvre l'année d'exercice sélectionnée dans votre système\n" .
                "• Les créances et dettes cumulées vous donnent une vision complète de votre situation\n" .
                "• Comparez avec l'année précédente pour identifier les tendances\n\n",

            'mouvement-stock' =>
                "À QUOI SERT CE RAPPORT ?\n\n" .
                "Ce rapport affiche l'historique complet de tous les mouvements de stock pour chaque article, classés par ordre chronologique.\n\n" .
                "COMMENT LIRE LES INFORMATIONS ?\n\n" .
                "• Chaque ligne représente un mouvement de stock (entrée ou sortie)\n" .
                "• Le mouvement peut provenir de : ventes(validé), achats(validé), inventaires ou importations\n" .
                "• Pour chaque mouvement, vous voyez :\n" .
                "  - La référence du document d'origine (cliquable pour voir le détail)\n" .
                "  - Le type de mouvement (achat, vente, inventaire, importation)\n" .
                "  - Le magasin concerné\n" .
                "  - La date du mouvement\n" .
                "  - La quantité entrée ou sortie\n\n" .
                "FILTRES DISPONIBLES\n\n" .
                "• Par article : recherchez par référence pour voir tous les mouvements d'un article spécifique\n" .
                "• Par période : sélectionnez une plage de dates\n\n" .
                "ASTUCE\n\n" .
                "Utilisez ce rapport pour comprendre pourquoi le stock d'un article a changé et tracer son historique complet.",

            'achat_vente' =>
                "À QUOI SERT CE RAPPORT ?\n\n" .
                "Ce rapport vous donne une vue d'ensemble de votre activité sur une période : ventes, achats, retours et dépenses, avec un résultat net final.\n\n" .
                "SECTION VENTES\n\n" .
                "• Ventes HT/TTC : total des montants facturés (validés uniquement)\n" .
                "• Encaissé : somme des paiements effectivement reçus sur la période\n" .
                "• Retours de vente : total des retours émis aux clients\n\n" .
                "Les types de documents pris en compte dépendent de votre sélection (factures, bons de commande, etc.).\n\n" .
                "SECTION ACHATS\n\n" .
                "• Achats HT/TTC : total des montants facturés par vos fournisseurs\n" .
                "• Crédit fournisseurs : montant restant à payer à vos fournisseurs\n" .
                "• Avoirs d'achat : total des retours ou remises obtenus\n\n" .
                "Comme pour les ventes, seuls les types de documents sélectionnés sont inclus.\n\n" .
                "SECTION DÉPENSES\n\n" .
                "• Total des dépenses enregistrées sur la période (hors achats)\n\n" .
                "RÉCAPITULATIF NET\n\n" .
                "Le résultat net est calculé automatiquement :\n" .
                "Ventes TTC - Retours de vente - Achats TTC + Avoirs d'achat - Dépenses\n\n" .
                "FILTRES ET OPTIONS\n\n" .
                "• Sélectionnez les types de documents à inclure pour chaque catégorie (ventes et achats)\n" .
                "• Filtrez par balises (étiquettes) pour analyser une famille des documents ou tout autre critère que vous avez tagué\n" .
                "• Lorsque vous sélectionnez des balises, seuls les documents associés à ces balises sont comptabilisés\n\n" .
                "NOTES IMPORTANTES\n\n" .
                "• Tous les documents sont pris en compte (validé, non validé)\n" .
                "• Les encaissements et décaissements sont basés sur les paiements effectifs, pas sur les montants facturés\n" .
                "• Si vous filtrez par balises, assurez-vous que vos documents sont correctement tagués",

            'vente-produit' =>
                "À QUOI SERT CE RAPPORT ?\n\n" .
                "Ce rapport détaille toutes les lignes de vente produit par produit, vous permettant d'analyser ce qui a été vendu, en quelle quantité, quand et où.\n\n" .
                "INFORMATIONS AFFICHÉES\n\n" .
                "Pour chaque ligne de vente, vous voyez :\n" .
                "• La référence du produit (SKU)\n" .
                "• La désignation du produit\n" .
                "• La quantité vendue\n" .
                "• Le montant TTC de la ligne\n" .
                "• Le type de document (facture, bon de commande, bon de retour, etc.)\n" .
                "• La référence du document de vente\n" .
                "• La date de la vente\n\n" .
                "FILTRES DISPONIBLES\n\n" .
                "• Par période : sélectionnez une plage de dates pour analyser une période précise\n" .
                "• Par type de document : choisissez les types de ventes à inclure (factures, bons, retours, etc.)\n" .
                "• Par produit : recherchez par référence ou désignation pour voir les ventes d'un article spécifique\n\n" .
                "UTILISATION PRATIQUE\n\n" .
                "• Identifiez les produits les plus vendus sur une période\n" .
                "• Analysez l'impact d'une promotion ou d'une campagne\n" .
                "• Suivez les retours de produits spécifiques\n\n" .
                "NOTE IMPORTANTE\n\n" .
                "Seules les ventes validées sont affichées dans ce rapport.",

            'achat-produit' =>
                "À QUOI SERT CE RAPPORT ?\n\n" .
                "Ce rapport liste toutes les lignes d'achat produit par produit, vous permettant de suivre vos approvisionnements en détail.\n\n" .
                "INFORMATIONS AFFICHÉES\n\n" .
                "Pour chaque ligne d'achat, vous voyez :\n" .
                "• La référence du produit (SKU)\n" .
                "• La désignation du produit\n" .
                "• La quantité achetée\n" .
                "• Le montant TTC de la ligne\n" .
                "• Le type de document (facture, avoir, etc.)\n" .
                "• La référence du document d'achat\n" .
                "• La date de l'achat\n\n" .
                "FILTRES DISPONIBLES\n\n" .
                "• Par période : sélectionnez une plage de dates\n" .
                "• Par type de document : choisissez les types d'achats à inclure\n" .
                "• Par produit : recherchez par référence ou désignation\n\n" .
                "UTILISATION PRATIQUE\n\n" .
                "• Suivez vos approvisionnements par produit\n" .
                "• Identifiez les produits les plus achetés\n" .
                "• Analysez vos retours fournisseurs\n" .
                "• Comparez les achats entre différentes périodes\n\n" .
                "NOTE IMPORTANTE\n\n" .
                "Seuls les achats validés sont affichés dans ce rapport.",

            'ca-client' =>
                "À QUOI SERT CE RAPPORT ?\n\n" .
                "Ce rapport classe vos clients par importance commerciale et vous aide à suivre leur situation de paiement.\n\n" .
                "INDICATEURS PAR CLIENT\n\n" .
                "• Total TTC : montant total des ventes facturées au client sur la période\n" .
                "• Encaissé : montant total des paiements effectivement reçus\n" .
                "• Solde : reste à payer par le client (Total TTC - Encaissé)\n\n" .
                "CALCUL DES MONTANTS\n\n" .
                "Les montants sont calculés en fonction :\n" .
                "• De la période sélectionnée\n" .
                "• Des types de documents choisis (factures, bons, etc.)\n" .
                "• Seules les ventes validées sont comptabilisées\n\n" .
                "FILTRES DISPONIBLES\n\n" .
                "• Par période : analysez le chiffre d'affaires sur une plage de dates précise\n" .
                "• Par type de vente : sélectionnez les types de documents à inclure\n" .
                "• Recherche : trouvez rapidement un client par nom ou référence\n\n" .
                "UTILISATION PRATIQUE\n\n" .
                "• Identifiez vos meilleurs clients\n" .
                "• Suivez les impayés client par client\n" .
                "• Relancez les clients ayant des soldes importants\n" .
                "• Analysez l'évolution du CA par client\n\n" .
                "ASTUCE\n\n" .
                "Un solde élevé peut indiquer un client important ou un problème de recouvrement. Analysez le contexte avant toute action.",

            'tendance-produit' =>
                "À QUOI SERT CE RAPPORT ?\n\n" .
                "Ce rapport identifie vos produits stars et vos produits dormants en termes de volumes et de chiffre d'affaires.\n\n" .
                "INDICATEURS PAR PRODUIT\n\n" .
                "• Référence et désignation du produit\n" .
                "• Quantités vendues : nombre total d'unités vendues\n" .
                "• Chiffre d'affaires : montant TTC total généré par le produit\n\n" .
                "CALCUL DES INDICATEURS\n\n" .
                "Les données sont calculées selon :\n" .
                "• La période sélectionnée\n" .
                "• Les types de ventes choisis (factures, bons, etc.)\n" .
                "• Seules les ventes validées sont incluses\n\n" .
                "FILTRES DISPONIBLES\n\n" .
                "• Par période : comparez les performances sur différentes périodes\n" .
                "• Par type de vente : sélectionnez les types de documents\n" .
                "• Recherche : trouvez un produit par référence ou désignation\n\n" .
                "NOTE IMPORTANTE\n\n" .
                "Seuls les documents validés sont calculés dans ce rapport.",

            'stock-produit' =>
                "À QUOI SERT CE RAPPORT ?\n\n" .
                "Ce rapport vous donne une photographie complète de votre stock actuel avec sa valorisation financière.\n\n" .
                "INDICATEURS PAR PRODUIT\n\n" .
                "• Référence et désignation\n" .
                "• Quantité : stock actuel disponible\n" .
                "• Prix d'achat et prix de vente unitaires\n" .
                "• Valeur d'achat : Quantité × Prix d'achat (ce que le stock vous a coûté)\n" .
                "• Valeur de vente : Quantité × Prix de vente (valeur potentielle si tout est vendu)\n" .
                "• Bénéfice potentiel : Valeur de vente - Valeur d'achat\n\n" .
                "TOTAUX GLOBAUX\n\n" .
                "En bas du tableau, vous trouvez :\n" .
                "• Somme totale des valeurs d'achat de tout le stock\n" .
                "• Somme totale des valeurs de vente potentielles\n" .
                "• Bénéfice potentiel total\n" .
                "• Taux de marge global : (Bénéfice / Valeur de vente) × 100\n\n" .
                "NOTES IMPORTANTES\n\n" .
                "• Les valeurs sont calculées avec les prix d'achat et de vente actuels\n" .
                "• Le stock affiché inclut tous les mouvements validés (achats, ventes, inventaires, importations)\n" .
                "• Le bénéfice est potentiel : il suppose que tout le stock sera vendu au prix de vente actuel",

            'stock-produit-legal' =>
                "À QUOI SERT CE RAPPORT ?\n\n" .
                "Ce rapport reconstitue le stock théorique en se basant uniquement sur les documents légaux (factures et avoirs), permettant une comparaison avec le stock physique.\n\n" .
                "CALCUL DU STOCK LÉGAL\n\n" .
                "Le stock légal est calculé comme suit :\n\n" .
                "ENTRÉES LÉGALES (ce qui augmente le stock) :\n" .
                "• Factures d'achat validées (faa)\n" .
                "• Avoirs de vente validés (av) - retours clients\n\n" .
                "SORTIES LÉGALES (ce qui diminue le stock) :\n" .
                "• Factures de vente validées (fa)\n" .
                "• Avoirs d'achat validés (ava) - retours fournisseurs\n\n" .
                "Stock légal = Entrées légales - Sorties légales\n\n" .
                "INFORMATIONS AFFICHÉES\n\n" .
                "Pour chaque produit, vous voyez :\n" .
                "• Référence et désignation\n" .
                "• Stock physique actuel (selon vos mouvements de stock)\n" .
                "• Stock de vente (sorties légales)\n" .
                "• Stock d'achat (entrées légales)\n" .
                "• Stock légal calculé\n\n" .
                "NOTE IMPORTANTE\n\n" .
                "Ce rapport se base uniquement sur les documents légaux validés. Les bons de commande, bons de livraison ou autres documents non fiscaux ne sont pas pris en compte.",

            'stock-produit-magasin' =>
                "À QUOI SERT CE RAPPORT ?\n\n" .
                "Ce rapport vous permet d'analyser le stock d'un magasin spécifique avec sa valorisation financière.\n\n" .
                "SÉLECTION DU MAGASIN\n\n" .
                "Vous devez d'abord sélectionner le magasin à analyser. Le rapport affichera uniquement les stocks de ce point de vente.\n\n" .
                "INDICATEURS PAR PRODUIT\n\n" .
                "• Référence et désignation\n" .
                "• Stock : quantité disponible dans le magasin sélectionné\n" .
                "• Prix d'achat et prix de vente unitaires\n" .
                "• Valeur d'achat : Stock × Prix d'achat\n" .
                "• Valeur de vente : Stock × Prix de vente\n" .
                "• Bénéfice potentiel : Valeur de vente - Valeur d'achat\n\n" .
                "CALCUL DU STOCK PAR MAGASIN\n\n" .
                "Le stock est calculé en analysant tous les mouvements :\n" .
                "• Entrées : achats, transferts entrants, ajustements d'inventaire positifs\n" .
                "• Sorties : ventes, transferts sortants, ajustements d'inventaire négatifs\n\n" .
                "Stock magasin = Entrées - Sorties (pour ce magasin uniquement)\n\n" .
                "TOTAUX GLOBAUX\n\n" .
                "En bas du rapport :\n" .
                "• Valeur totale d'achat du stock du magasin\n" .
                "• Valeur totale de vente potentielle\n" .
                "• Bénéfice potentiel total\n" .
                "• Taux de marge du magasin\n\n" .
                "FILTRES DISPONIBLES\n\n" .
                "• Par magasin : sélection obligatoire\n" .
                "• Par référence produit : recherche par code article\n" .
                "• Par désignation : recherche par nom de produit\n\n",

            'commerciaux' =>
                "À QUOI SERT CE RAPPORT ?\n\n" .
                "Ce rapport évalue la performance de chaque commercial en termes de nombre de ventes, de chiffre d'affaires généré et de commissions gagnées.\n\n" .
                "INDICATEURS PAR COMMERCIAL\n\n" .
                "• Nom du commercial\n" .
                "• Nombre de ventes : total des ventes attribuées au commercial\n" .
                "• Chiffre d'affaires : somme des montants TTC de toutes ses ventes\n" .
                "• Commissions : montant total des commissions gagnées\n\n" .
                "CALCUL DES COMMISSIONS\n\n" .
                "Pour chaque vente, la commission est calculée ainsi :\n" .
                "Commission = Montant TTC de la vente × Taux de commission %\n\n" .
                "Le taux de commission utilisé est :\n" .
                "• Le taux défini spécifiquement sur la vente, si renseigné\n" .
                "• Sinon, le taux de commission par défaut du commercial\n\n" .
                "FILTRES DISPONIBLES\n\n" .
                "• Par période : analysez la performance sur une plage de dates\n" .
                "• Par type de vente : sélectionnez les types de documents à inclure (factures, bons, etc.)\n" .
                "• Recherche : trouvez un commercial par nom ou référence\n\n" .
                "DOCUMENTS PRIS EN COMPTE\n\n" .
                "• Seules les ventes validées sont comptabilisées\n" .
                "• Seules les ventes ayant un commercial attribué apparaissent\n" .
                "• Les types de documents dépendent de votre sélection\n\n" .
                "NOTE IMPORTANTE\n\n" .
                "Seuls les documents validés sont calculés dans ce rapport.",

            'sessions' =>
                "À QUOI SERT CE RAPPORT ?\n\n" .
                "Ce rapport liste toutes les sessions de caisse de vos points de vente, permettant un suivi précis de l'activité de chaque utilisateur et magasin.\n\n" .
                "QU'EST-CE QU'UNE SESSION ?\n\n" .
                "Une session de caisse représente une période de travail :\n" .
                "• Elle commence à l'ouverture de la caisse par un utilisateur\n" .
                "• Elle se termine à la fermeture de la caisse\n" .
                "• Toutes les opérations effectuées pendant cette période y sont rattachées\n\n" .
                "INFORMATIONS AFFICHÉES\n\n" .
                "Pour chaque session, vous voyez :\n" .
                "• Le magasin concerné\n" .
                "• L'utilisateur qui a ouvert la session\n" .
                "• La date et l'heure de début\n" .
                "• La date et l'heure de fin (si fermée)\n" .
                "• Le statut (Ouverte ou Fermée)\n" .
                "• Le total TTC des ventes de la session\n\n" .
                "FILTRES DISPONIBLES\n\n" .
                "• Par période : sélectionnez une plage de dates\n" .
                "• Par magasin : isolez les sessions d'un point de vente\n\n" .
                "UTILISATION PRATIQUE\n\n" .
                "• Suivez l'activité par caissier et par magasin\n" .
                "• Identifiez les sessions non fermées\n" .
                "• Analysez les performances par période\n" .
                "• Cliquez sur l'œil pour voir le détail d'une session\n\n",

            'sessions.ventes' =>
                "À QUOI SERT CE RAPPORT ?\n\n" .
                "Ce rapport affiche le détail complet d'une session de caisse spécifique : toutes les ventes, retours et dépenses effectués pendant cette session.\n\n" .
                "INFORMATIONS GÉNÉRALES DE LA SESSION\n\n" .
                "• Magasin concerné\n" .
                "• Utilisateur (caissier)\n" .
                "• Date et heure d'ouverture\n" .
                "• Date et heure de fermeture (si applicable)\n" .
                "• Statut de la session (Ouverte/Fermée)\n\n" .
                "RÉCAPITULATIF DES OPÉRATIONS\n\n" .
                "Ventes :\n" .
                "• Nombre de ventes : total des transactions de vente\n" .
                "• Montant total des ventes TTC\n\n" .
                "Retours :\n" .
                "• Nombre de retours : total des avoirs émis\n" .
                "• Montant total des retours TTC\n\n" .
                "Dépenses :\n" .
                "• Montant total des dépenses enregistrées pendant la session\n\n" .
                "NOTES IMPORTANTES\n\n" .
                "• Seules les opérations effectuées pendant cette session précise sont affichées\n" .
                "• Les documents créés mais non validés n'apparaissent pas\n" .
                "• Une session ouverte peut toujours être consultée même si elle n'est pas encore fermée\n" .
                "• Les dépenses sont rattachées à la session active au moment de leur création\n\n",

            'categorie-depense' =>
                "À QUOI SERT CE RAPPORT ?\n\n" .
                "Ce rapport analyse vos dépenses par catégorie sur une période donnée, vous permettant d'identifier où va votre argent et d'optimiser vos coûts.\n\n" .
                "INFORMATIONS AFFICHÉES\n\n" .
                "Pour chaque catégorie de dépense, vous voyez :\n" .
                "• Le nom de la catégorie\n" .
                "• Le montant total HT : somme des dépenses hors taxes\n" .
                "• Le montant total des impôts/taxes\n" .
                "• Le montant total TTC : montant total incluant les taxes\n" .
                "• Le nombre de dépenses payées dans cette catégorie\n" .
                "• Le pourcentage par rapport au total des dépenses\n\n" .
                "VISUALISATION GRAPHIQUE\n\n" .
                "Un graphique circulaire (camembert) vous permet de visualiser rapidement :\n" .
                "• La répartition des dépenses par catégorie\n" .
                "• Les catégories qui pèsent le plus dans votre budget\n" .
                "• La proportion de chaque type de dépense\n\n" .
                "CALCUL DES MONTANTS\n\n" .
                "• Montant HT : montant de base de la dépense\n" .
                "• Impôt : Montant HT × (Taux de taxe / 100)\n" .
                "• Montant TTC : Montant HT + Impôt\n\n" .
                "FILTRES DISPONIBLES\n\n" .
                "• Par période : sélectionnez une plage de dates pour analyser vos dépenses\n" .
                "• Par défaut : l'année d'exercice en cours est affichée\n\n" .
                "NOTES IMPORTANTES\n\n" .
                "• Seules les dépenses de la période sélectionnée sont incluses\n" .
                "• Les dépenses sans catégorie sont regroupées sous 'Sans catégorie'\n" .
                "• Le tri est effectué par montant décroissant (les plus grosses dépenses en premier)\n\n",

            'journalier' =>
                "À QUOI SERT CE RAPPORT ?\n\n" .
                "Ce rapport journalier vous donne une vue complète de l'activité d'un magasin sur une journée précise : ventes, achats, paiements, créances et trésorerie.\n\n" .
                "SECTION 1 : VENTES CLIENT / ARTICLE\n\n" .
                "Cette section présente un tableau croisé dynamique :\n" .
                "SECTION 2 : ARTICLES / FOURNISSEURS\n\n" .
                "Tableau croisé des achats du jour :\n" .
                "SECTION 3 : PAIEMENTS / CRÉDITS\n\n" .
                "Liste détaillée des paiements reçus sur d'anciennes créances :\n" .
                "Cette section permet de suivre les encaissements différés et les clients qui règlent leurs anciennes factures.\n\n" .
                "SECTION 4 : TRÉSORERIE DU JOUR\n\n" .
                "Tableau récapitulatif des flux financiers :\n\n" .
                "VENTES :\n" .
                "• Ventes du jour : montant total TTC des ventes de la journée\n" .
                "• Ventes créances encaissées : montant des anciennes créances payées\n" .
                "• Total ventes : somme des deux lignes précédentes\n\n" .
                "ENCAISSEMENTS PAR MÉTHODE :\n" .
                "• Espèces du jour : paiements cash des ventes du jour\n" .
                "• Espèces créances : paiements cash des anciennes créances\n" .
                "• Total espèces : total des encaissements en liquide\n\n" .
                "• Chèques du jour : paiements par chèque des ventes du jour\n" .
                "• Chèques créances : chèques reçus pour anciennes créances\n" .
                "• Total chèques : total des paiements par chèque\n\n" .
                "• LCN du jour : lettres de change normalisées du jour\n" .
                "• LCN créances : LCN reçues pour anciennes créances\n" .
                "• Total LCN : total des paiements par LCN\n\n" .
                "DÉPENSES :\n" .
                "• Total dépenses : somme de toutes les dépenses enregistrées le jour\n\n" .
                "SOLDE :\n" .
                "• Reste en caisse : Total espèces - Total dépenses\n" .
                "  (Ce montant représente le liquide qui devrait être physiquement en caisse)\n\n" .
                "SECTION 5 : DÉPENSES PAR CATÉGORIE\n\n" .
                "Répartition des dépenses du jour par catégorie :\n" .
                "FILTRES DISPONIBLES\n\n" .
                "• Date : sélectionnez le jour à analyser\n" .
                "• Magasin : choisissez le point de vente concerné\n\n" .
                "NOTES IMPORTANTES\n\n" .
                "• Ce rapport se base uniquement sur les ventes liées à des sessions POS (ventes en caisse)\n" .
                "• Les ventes du jour sont celles dont la date_document correspond au jour sélectionné\n" .
                "• Les créances encaissées sont les paiements du jour sur des ventes antérieures\n" .
                "• Le montant 'Reste en caisse' doit correspondre au comptage physique des espèces\n" .
                "• Seules les ventes et achats validés sont pris en compte\n\n"];

        $now = now();
        foreach ($detailsByRoute as $route => $details) {
            DB::table('rapports')
                ->where('route', $route)
                ->update([
                    'details' => $details,
                    'updated_at' => $now,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $routes = [
            'mouvement-stock', 'achat_vente', 'vente-produit', 'achat-produit',
            'ca-client', 'tendance-produit', 'stock-produit', 'stock-produit-legal',
            'stock-produit-magasin', 'commerciaux', 'sessions', 'sessions.ventes',
            'tva', 'annuel', 'categorie-depense', 'journalier'
        ];

        DB::table('rapports')
            ->whereIn('route', $routes)
            ->update(['details' => null]);
    }
};
