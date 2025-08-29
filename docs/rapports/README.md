# Documentation des Rapports — Guide de reconstruction technique

Ce document décrit, avec un niveau de détail technique suffisant pour une ré‑implémentation, tous les rapports fournis par:
- App\Http\Controllers\RapportController (Web)
- App\Http\Controllers\Api\pos\v1\RapportController (API POS v1)

Il complète les descriptions fonctionnelles par: routes, middlewares/permissions, contrôleurs/méthodes, tables et champs utilisés, pseudo‑SQL, paramètres attendus, formats de réponses (DataTables/JSON), vues Blade et notes de performance/edge cases.

Table des matières
- 1. Pré‑requis et conventions
- 2. Routes et points d’entrée
  - 2.1 Web (RapportController)
  - 2.2 API POS v1 (Api\\pos\\v1\\RapportController)
- 3. Rapports Web (détails par rapport)
  - Mouvement de stock
  - Achats & Ventes (Synthèse)
  - Ventes par produit
  - Achats par produit
  - CA par client
  - Tendance produit
  - Stock produit
  - Stock produit légal
  - Stock produit par magasin
  - Commerciaux
  - Détails d’une session POS
  - Sessions POS
  - TVA (prorata paiements)
  - Synthèse annuelle
- 4. Rapports API POS v1 (détails par rapport)
  - Stock (par session)
  - Matrice Client × Article
  - Matrice Fournisseur × Article
  - Créances (encaissements du jour)
  - Trésorerie (jour)


1) Pré‑requis et conventions
- Middlewares (Web): toutes les routes rapports Web sont chargées sous le groupe ['auth','exercice'] (voir routes/web.php). Chaque méthode applique dans le contrôleur guard_custom(['rapport.*']).
- Middlewares (API POS v1): ['auth:sanctum', InitializeTenancyByDomain, PreventAccessFromCentralDomains, ApiSessionMiddleware].
- Formats dates: dd/mm/YYYY pour les filtres i_date des vues Web; conversion en Y-m-d côté serveur via Carbon.
- DataTables: les requêtes AJAX portent order (index+dir) et columns[*].data; le back applique un orderByRaw sécurisé par la correspondance des clés data.
- Injection session_id (API POS): l’intercepteur Axios du POS ajoute automatiquement session_id (cf. resources/pos/services/api.ts). À défaut, fournir session_id manuellement.


2) Routes et points d’entrée
2.1 Web (RapportController) — préfixe /rapports
- GET /rapports -> RapportController@liste (name: rapports.liste)
- GET /rapports/mouvement_stock -> mouvement_stock (name: rapports.mouvement-stock)
- GET /rapports/achat-vente -> achat_vente (name: rapports.achat_vente)
- GET /rapports/vente-produit -> vente_produit (name: rapports.vente-produit)
- GET /rapports/achat-produit -> achat_produit (name: rapports.achat-produit)
- GET /rapports/ca-client -> ca_client (name: rapports.ca-client)
- GET /rapports/tendance-produit -> tendance_produit (name: rapports.tendance-produit)
- GET /rapports/stock-produit -> stock_produit (name: rapports.stock-produit)
- GET /rapports/stock-produit-legal -> stock_produit_legal (name: rapports.stock-produit-legal)
- GET /rapports/tva -> tva (name: rapports.tva)
- GET /rapports/annuel -> annuel (name: rapports.annuel)
- GET /rapports/stock-produit-magasin -> stock_produit_par_magasin (name: rapports.stock-produit-magasin)
- GET /rapports/commerciaux -> commerciaux (name: rapports.commerciaux)
- GET /rapports/sessions -> sessions (name: rapports.sessions)
- GET /rapports/sessions/ventes/{id} -> afficher_session (name: rapports.sessions.ventes)

2.2 API POS v1 — préfixe /api/pos/v1
- GET /api/pos/v1/articles-stock-rapport -> RapportController@stock
- GET /api/pos/v1/articles-clients-rapport -> RapportController@article_client_rapport
- GET /api/pos/v1/articles-fournisseurs-rapport -> RapportController@article_fournisseur_rapport
- GET /api/pos/v1/creance-rapport -> RapportController@creance_rapport
- GET /api/pos/v1/tresorie-rapport -> RapportController@tresorie_rapport

Notes:
- Les mêmes endpoints « rapports » existent aussi pour d’autres profils (v-parfums, v-commercial, v-classic, v-caisse) avec d’autres namespaces; cette section se concentre sur pos/v1.


3) Rapports Web (détails)

Mouvement de stock
- Route: GET /rapports/mouvement_stock (name rapports.mouvement-stock)
- Contrôleur: RapportController@mouvement_stock; Permissions: guard_custom(['rapport.*']).
- Vue: resources/views/rapports/mouvement_stock.blade.php (table AJAX DataTables).
- Tables et champs:
  - transaction_stocks (created_at, stockable_id, stockable_type, article_id, magasin_id, qte_entree, qte_sortir)
  - Polymorphes: achats/ventes/inventaires/importations (pour stockable)
  - articles.reference; magasins.reference
- Filtrage:
  - i_search: WHERE EXISTS article.reference LIKE %term%
  - i_date: WHERE created_at BETWEEN [start,end]
  - i_ref: stockable_type=Inventaire::class AND inventaires.reference = i_ref
  - i_imp: stockable_type=Importation::class AND importations.reference = i_imp
- Pseudo‑SQL:
  SELECT ts.*, a.reference, m.reference AS magasin
  FROM transaction_stocks ts
  LEFT JOIN articles a ON a.id=ts.article_id
  LEFT JOIN magasins m ON m.id=ts.magasin_id
  [filters]
  ORDER BY columns[order[i].column].data order[i].dir
- Réponse: DataTables JSON; colonne stockable_type rendue en lien vers la pièce source (achats/ventes) ou une ancre cliquable (inventaires/importations).
- Index conseillés: ts.created_at, ts.article_id, (stockable_type, stockable_id), ts.magasin_id.

Achats & Ventes (Synthèse)
- Route: GET /rapports/achat-vente (name rapports.achat_vente)
- Vue: rapports/achat_vente.blade.php avec 4 cartes (ventes, achats, dépense, récap).
- Paramètres: i_date, types_achat[], retours_achat[], types_vente[], retours_vente[], balises_achat[], balises_vente[].
- Données: agrégations SUM sur ventes/achats; filtre année=exercice et/ou i_date sur date_emission; dépenses sur date_operation.
- Pseudo‑SQL (extraits):
  ventes_total_ht = SUM(ventes.total_ht WHERE statut='validé' AND type_document IN (:types_vente) AND date_emission IN range)
  ventes_total_ttc = SUM(ventes.total_ttc ...)
  ventes_total_encaisser = SUM(ventes.encaisser ...)
  achats_total_ht/ttc/credit = SUM(achats.* ...)
  avoirs_vente = SUM(ventes.total_ttc WHERE type_document IN (:retours_vente) ...)
  avoirs_achat = SUM(achats.total_ttc WHERE type_document IN (:retours_achat) ...)
  depense_total = SUM(depenses.montant WHERE date_operation IN range)
  recap = ventes_ttc - avoirs_vente - achats_ttc + avoirs_achat - depense_total
- Notes: possibilité de filtrer par Tags via pivot taggables (taggable_type = Achat/Vente).

Ventes par produit
- Route: GET /rapports/vente-produit (name rapports.vente-produit)
- Tables: ventes (statut='validé'), vente_lignes, articles.
- Paramètres: i_types[] (obligatoire), i_date (sur ventes.date_document), i_search (articles.reference exact OU designation LIKE), order/columns.
- Pseudo‑SQL:
  SELECT v.id, v.reference, vl.total_ttc, a.designation, v.type_document, a.reference AS sku, vl.quantite, v.date_document
  FROM ventes v JOIN vente_lignes vl ON v.id=vl.vente_id
  JOIN articles a ON a.id=vl.article_id
  WHERE v.statut='validé' AND v.type_document IN (:i_types)
  [AND a.reference = :i_search OR a.designation LIKE %:i_search%]
  [AND v.date_document BETWEEN :start,:end]
  ORDER BY ...

Achats par produit
- Route: GET /rapports/achat-produit (name rapports.achat-produit)
- Tables: achats (statut='validé'), achat_lignes, articles.
- Paramètres: i_types[], i_date (achats.date_emission), i_search, order/columns.
- Pseudo‑SQL analogue à « ventes par produit ».

CA par client
- Route: GET /rapports/ca-client (name rapports.ca-client)
- Base: clients LEFT JOIN ventes (statut='validé', type IN i_types) avec agrégats SUM(total_ttc, encaisser, solde) groupés par client.
- Paramètres: i_types[], i_date (ventes.date_emission), i_search (clients.nom/reference), order/columns.

Tendance produit
- Route: GET /rapports/tendance-produit (name rapports.tendance-produit)
- Base: articles LEFT JOIN vente_lignes LEFT JOIN ventes (statut='validé', type IN i_types).
- Paramètres: i_types[], i_date (ventes.date_document), i_search.
- Agrégats: SUM(vente_lignes.quantite) AS nombre_des_ventes, SUM(vente_lignes.total_ttc) AS total_des_ventes.

Stock produit
- Route: GET /rapports/stock-produit (name rapports.stock-produit)
- Tables: articles LEFT JOIN stocks (quantite), affichage valeurs: quantite*prix_achat, quantite*prix_vente, bénéfice potentiel.
- KPI globaux (hors AJAX):
  stock_ventes = SUM(stocks.quantite * prix_vente)
  stock_achats = SUM(stocks.quantite * prix_achat)
  profit% = (stock_ventes - stock_achats) / stock_ventes

Stock produit légal
- Route: GET /rapports/stock-produit-legal (name rapports.stock-produit-legal)
- Calculs:
  stock_vente = SUM(ventes('fa').quantite) + SUM(achats('ava').quantite) [statut='validé']
  stock_achat = SUM(achats('faa').quantite) + SUM(ventes('av').quantite) [statut='validé']
  stock_legal = stock_achat - stock_vente

Stock produit par magasin
- Route: GET /rapports/stock-produit-magasin (name rapports.stock-produit-magasin)
- Paramètre requis: magasin_id (dans la requête AJAX).
- Quantité: SUM(qte_entree) - SUM(qte_sortir) sur transaction_stocks WHERE magasin_id.
- KPI + DataTables par article (valeur achats/ventes, bénéfice potentiel).

Commerciaux
- Route: GET /rapports/commerciaux (name rapports.commerciaux)
- Base: commercials LEFT JOIN ventes (statut='validé', type IN i_types).
- Agrégats: COUNT(ventes), SUM(ventes.total_ttc) AS total_ca, SUM(ventes.total_ttc * commission_par_defaut/100) AS total_commission.
- Paramètres: i_types[], i_date, i_search.

Détails d’une session POS
- Route: GET /rapports/sessions/ventes/{id} (name rapports.sessions.ventes)
- AJAX type=depense: liste des Depense de la session (catégorie join) — colonnes avec lien vers la dépense.
- AJAX sinon: liste des ventes de la session — colonnes avec lien vers la vente.
- Vue non‑AJAX: synthèse de la session (compteurs ventes/retours, totaux, dépenses); types extraits de PosSettings (type_vente/type_retour).

Sessions POS
- Route: GET /rapports/sessions (name rapports.sessions)
- whereYear(created_at) = session('exercice'); i_search permet filtrage exact sur magasin_id; total_ttc calculé via ventes() et PosService::getValue('type_vente').

TVA (au prorata des paiements)
- Route: GET /rapports/tva (name rapports.tva)
- Période: i_date sur paiements.date_paiement; LEFT JOIN ventes/achats par payable_type.
- Calculs par paiement (lignes groupées):
  tva_ventes = SUM((encaisser / ventes.total_ttc) * ventes.total_tva)
  tva_achats = SUM((decaisser / achats.total_ttc) * achats.total_tva)
  somme = tva_ventes - tva_achats
- Réponse: DataTables JSON enrichi avec ventes_tva, achats_tva, somme (totaux de bas de page).

Synthèse annuelle
- Route: GET /rapports/annuel (name rapports.annuel)
- Ventes (fa, validé): CA TTC, CA HT, créance période (TTC − encaisser période), créance cumulée antérieure (< 1er jour), recettes (encaisser période).
- Achats (faa, validé): CA TTC/HT, dettes période (TTC − decaisser période), dettes cumulées antérieures, décaissements période.
- Dépenses: SUM(montant) et SUM(montant + montant * taxe/100) sur date_operation.
- Vue: rendus partiels pour vente/achat/dépense + page récap.


4) Rapports API POS v1 (détails)

Stock (par session)
- Route: GET /api/pos/v1/articles-stock-rapport
- Params requis: session_id (session POS ouverte obligatoire).
- Logique: stock par article du magasin de la session = SUM(ts.qte_entree) - SUM(ts.qte_sortir).
- Réponse: [{ id, designation, reference, stock }]. 500 si session fermée.
- Index: transaction_stocks(article_id, magasin_id), qte_entree/qte_sortir cumulables.

Matrice Client × Article (ventes du jour)
- Route: GET /api/pos/v1/articles-clients-rapport
- Params: session_id.
- Filtre: magasin_id = session.magasin_id; ventes.date_document = today; ventes.type_document = PosService::getValue('type_vente') (défaut 'bc'); ventes.pos_session_id NOT NULL.
- Agrégats: par (client, article) — SUM(quantite), SUM(total_ttc); totaux par client (total_ttc, total_paye via paiements).
- Réponse: { clients: string[], articles: string[], data: Record<client, Record<article, {quantite,total_ttc}>>, client_totals: Record<client,{total_ttc,total_paye}> }.

Matrice Fournisseur × Article (achats du jour)
- Route: GET /api/pos/v1/articles-fournisseurs-rapport
- Params: session_id.
- Filtre: achats.magasin_id = session.magasin_id; achats.date_emission = today; jointures achat_lignes, articles, fournisseurs; paiements LEFT JOIN.
- Réponse: { fournisseurs: string[], articles: string[], data: Record<fournisseur, Record<article,{quantite,total_ttc}>> }.

Créances (encaissements du jour)
- Route: GET /api/pos/v1/creance-rapport
- Params: session_id.
- Filtre: paiements.date_paiement = today; paiements.magasin_id = session.magasin_id; payable_type = Vente; ventes.date_emission <= today; ventes.pos_session_id NOT NULL.
- Réponse: tableau d’objets { reference, client_name, last_payment_method, last_payment_date, cheque_lcn_number, sale_date, is_controled, total_ttc, statut_paiement(libellé), creance_amount }.

Trésorerie (jour)
- Route: GET /api/pos/v1/tresorie-rapport
- Params: session_id.
- Totaux: ventes (SUM total_ttc today), encaissements espèces/chèque/LCN (SUM encaisser today par méthode), dépenses (SUM montant today, pos_session_id NOT NULL), reste_en_caisse = espèces − dépenses.
- Réponse: { total_vente, total_espece, total_cheque, total_lcn, total_depenses, reste_en_caisse }.


Annexes — Conseils perf & edge cases
- Toujours limiter les périodes (i_date) pour éviter des scans complets.
- Indices recommandés: ventes(date_emission, type_document, statut), achats(date_emission, type_document, statut), paiements(date_paiement, payable_type), transaction_stocks(magasin_id, article_id, created_at), stocks(article_id), articles(reference).
- DataTables: valider côté client les colonnes utilisées dans order/columns pour éviter des orderBy arbitraires; côté serveur, seules les clés attendues sont concaténées.
- Zéro division TVA: le calcul tva_ventes/tva_achats protège les divisions par zéro via CASE WHEN total_ttc != 0 THEN ... END.
- Formats monétaires: arrondir à 2 décimales et afficher ' MAD' selon besoin UI (cf. code existant).
- Autorisations: toutes les pages Web requièrent le droit 'rapport.*'; s’assurer que les seeds/roles le contiennent.
