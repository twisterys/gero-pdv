## 1-Rapport TVA

**Route:** `tva`

#### À QUOI SERT CE RAPPORT ?

Ce rapport calcule la TVA à payer ou à récupérer sur une période, en tenant compte des encaissements et décaissements effectifs.

#### MÉTHODE DE CALCUL

Ce rapport applique la méthode des encaissements :

- La TVA n'est comptabilisée que lors du paiement effectif
- Si une facture est payée partiellement, seule la TVA correspondant à la partie payée est prise en compte

#### INDICATEURS AFFICHÉS

#### TVA COLLECTÉE (TVA sur les ventes) :

- Pour chaque paiement client reçu sur une facture de vente
- **Calcul :** (Montant encaissé / Montant TTC de la facture) × TVA totale de la facture
- Seules les factures de vente (fa) sont prises en compte

#### TVA DÉDUCTIBLE (TVA sur les achats) :

- Pour chaque paiement fournisseur effectué sur une facture d'achat
- **Calcul :** (Montant décaissé / Montant TTC de la facture) × TVA totale de la facture
- Seules les factures d'achat (faa) sont prises en compte

#### RÉSULTAT :

**TVA à payer = TVA collectée - TVA déductible**

- Si positif : vous devez payer ce montant à l'administration fiscale
- Si négatif : vous avez un crédit de TVA à récupérer

#### FILTRES DISPONIBLES

- **Par période :** sélectionnez la période de déclaration (mensuelle, trimestrielle, etc.)

#### NOTES IMPORTANTES

- Ce rapport se base sur les dates de paiement, pas sur les dates de facturation
- Seules les factures validées sont prises en compte
- Les autres types de documents (bons, devis) ne génèrent pas de TVA à déclarer
- Vérifiez toujours que vos paiements sont correctement saisis

---

## 2-Rapport Annuel

**Route:** `annuel`

#### À QUOI SERT CE RAPPORT ?

Ce rapport donne une vision complète de votre exercice comptable : ventes, achats, dépenses, trésorerie et créances/dettes.

#### SECTION VENTES

#### Chiffre d'affaires de l'année :

- **CA TTC :** total des factures de vente validées sur l'exercice
- **CA HT :** montant hors taxes

#### Encaissements :

- Total des paiements clients effectivement reçus sur l'exercice
- Basé sur les dates de paiement, pas les dates de facturation

#### Créances clients :

- **Créances de l'année :** factures de l'année non encore encaissées
- **Créances cumulées au 1er jour de l'année :** reste dû des années précédentes
- **Total des créances :** somme des deux lignes précédentes

#### SECTION ACHATS

#### Achats de l'année :

- **Achats TTC :** total des factures fournisseurs validées sur l'exercice
- **Achats HT :** montant hors taxes

#### Décaissements :

- Total des paiements fournisseurs effectués sur l'exercice

#### Dettes fournisseurs :

- **Dettes de l'année :** factures de l'année non encore payées
- **Dettes cumulées au 1er jour de l'année :** reste dû des années précédentes
- **Total des dettes :** somme des deux lignes précédentes

#### SECTION DÉPENSES

- Total des dépenses enregistrées sur l'exercice
- Affichage HT ou TTC selon votre choix

#### DOCUMENTS PRIS EN COMPTE

- **Ventes :** uniquement les factures de vente (fa) validées
- **Achats :** uniquement les factures d'achat (faa) validées
- Les autres types de documents ne sont pas comptabilisés

#### NOTES IMPORTANTES

- Ce rapport couvre l'année d'exercice sélectionnée dans votre système
- Les créances et dettes cumulées vous donnent une vision complète de votre situation
- Comparez avec l'année précédente pour identifier les tendances

---

## 3-Mouvement de Stock

**Route:** `mouvement-stock`

#### À QUOI SERT CE RAPPORT ?

Ce rapport affiche l'historique complet de tous les mouvements de stock pour chaque article, classés par ordre chronologique.

#### COMMENT LIRE LES INFORMATIONS ?

- Chaque ligne représente un mouvement de stock (entrée ou sortie)
- Le mouvement peut provenir de : ventes(validé), achats(validé), inventaires ou importations
- Pour chaque mouvement, vous voyez :
    - La référence du document d'origine (cliquable pour voir le détail)
    - Le type de mouvement (achat, vente, inventaire, importation)
    - Le magasin concerné
    - La date du mouvement
    - La quantité entrée ou sortie

#### FILTRES DISPONIBLES

- **Par article :** recherchez par référence pour voir tous les mouvements d'un article spécifique
- **Par période :** sélectionnez une plage de dates

#### ASTUCE

Utilisez ce rapport pour comprendre pourquoi le stock d'un article a changé et tracer son historique complet.

---

## 4-Rapport Achat/Vente

**Route:** `achat_vente`

#### À QUOI SERT CE RAPPORT ?

Ce rapport vous donne une vue d'ensemble de votre activité sur une période : ventes, achats, retours et dépenses, avec un résultat net final.

#### SECTION VENTES

- **Ventes HT/TTC :** total des montants facturés (validés uniquement)
- **Encaissé :** somme des paiements effectivement reçus sur la période
- **Retours de vente :** total des retours émis aux clients

Les types de documents pris en compte dépendent de votre sélection (factures, bons de commande, etc.).

#### SECTION ACHATS

- **Achats HT/TTC :** total des montants facturés par vos fournisseurs
- **Crédit fournisseurs :** montant restant à payer à vos fournisseurs
- **Avoirs d'achat :** total des retours ou remises obtenus

Comme pour les ventes, seuls les types de documents sélectionnés sont inclus.

#### SECTION DÉPENSES

- Total des dépenses enregistrées sur la période (hors achats)

#### RÉCAPITULATIF NET

Le résultat net est calculé automatiquement :

**Ventes TTC - Retours de vente - Achats TTC + Avoirs d'achat - Dépenses**

#### FILTRES ET OPTIONS

- Sélectionnez les types de documents à inclure pour chaque catégorie (ventes et achats)
- Filtrez par balises (étiquettes) pour analyser une famille des documents ou tout autre critère que vous avez tagué
- Lorsque vous sélectionnez des balises, seuls les documents associés à ces balises sont comptabilisés

#### NOTES IMPORTANTES

- Tous les documents sont pris en compte (validé, non validé)
- Les encaissements et décaissements sont basés sur les paiements effectifs, pas sur les montants facturés
- Si vous filtrez par balises, assurez-vous que vos documents sont correctement tagués

---

## 5-Vente par Produit

**Route:** `vente-produit`

#### À QUOI SERT CE RAPPORT ?

Ce rapport détaille toutes les lignes de vente produit par produit, vous permettant d'analyser ce qui a été vendu, en quelle quantité, quand et où.

#### INFORMATIONS AFFICHÉES

Pour chaque ligne de vente, vous voyez :

- La référence du produit (SKU)
- La désignation du produit
- La quantité vendue
- Le montant TTC de la ligne
- Le type de document (facture, bon de commande, bon de retour, etc.)
- La référence du document de vente
- La date de la vente

#### FILTRES DISPONIBLES

- **Par période :** sélectionnez une plage de dates pour analyser une période précise
- **Par type de document :** choisissez les types de ventes à inclure (factures, bons, retours, etc.)
- **Par produit :** recherchez par référence ou désignation pour voir les ventes d'un article spécifique

#### UTILISATION PRATIQUE

- Identifiez les produits les plus vendus sur une période
- Analysez l'impact d'une promotion ou d'une campagne
- Suivez les retours de produits spécifiques

#### NOTE IMPORTANTE

Seules les ventes validées sont affichées dans ce rapport.

---

## 6-Achat par Produit

**Route:** `achat-produit`

#### À QUOI SERT CE RAPPORT ?

Ce rapport liste toutes les lignes d'achat produit par produit, vous permettant de suivre vos approvisionnements en détail.

#### INFORMATIONS AFFICHÉES

Pour chaque ligne d'achat, vous voyez :

- La référence du produit (SKU)
- La désignation du produit
- La quantité achetée
- Le montant TTC de la ligne
- Le type de document (facture, avoir, etc.)
- La référence du document d'achat
- La date de l'achat

#### FILTRES DISPONIBLES

- **Par période :** sélectionnez une plage de dates
- **Par type de document :** choisissez les types d'achats à inclure
- **Par produit :** recherchez par référence ou désignation

#### UTILISATION PRATIQUE

- Suivez vos approvisionnements par produit
- Identifiez les produits les plus achetés
- Analysez vos retours fournisseurs
- Comparez les achats entre différentes périodes

#### NOTE IMPORTANTE

Seuls les achats validés sont affichés dans ce rapport.

---

## 7-CA par Client

**Route:** `ca-client`

#### À QUOI SERT CE RAPPORT ?

Ce rapport classe vos clients par importance commerciale et vous aide à suivre leur situation de paiement.

#### INDICATEURS PAR CLIENT

- **Total TTC :** montant total des ventes facturées au client sur la période
- **Encaissé :** montant total des paiements effectivement reçus
- **Solde :** reste à payer par le client (Total TTC - Encaissé)

#### CALCUL DES MONTANTS

Les montants sont calculés en fonction :

- De la période sélectionnée
- Des types de documents choisis (factures, bons, etc.)
- Seules les ventes validées sont comptabilisées

#### FILTRES DISPONIBLES

- **Par période :** analysez le chiffre d'affaires sur une plage de dates précise
- **Par type de vente :** sélectionnez les types de documents à inclure
- **Recherche :** trouvez rapidement un client par nom ou référence

#### UTILISATION PRATIQUE

- Identifiez vos meilleurs clients
- Suivez les impayés client par client
- Relancez les clients ayant des soldes importants
- Analysez l'évolution du CA par client

#### ASTUCE

Un solde élevé peut indiquer un client important ou un problème de recouvrement. Analysez le contexte avant toute action.

---

## 8-Tendance Produit

**Route:** `tendance-produit`

#### À QUOI SERT CE RAPPORT ?

Ce rapport identifie vos produits stars et vos produits dormants en termes de volumes et de chiffre d'affaires.

#### INDICATEURS PAR PRODUIT

- Référence et désignation du produit
- **Quantités vendues :** nombre total d'unités vendues
- **Chiffre d'affaires :** montant TTC total généré par le produit

#### CALCUL DES INDICATEURS

Les données sont calculées selon :

- La période sélectionnée
- Les types de ventes choisis (factures, bons, etc.)
- Seules les ventes validées sont incluses

#### FILTRES DISPONIBLES

- **Par période :** comparez les performances sur différentes périodes
- **Par type de vente :** sélectionnez les types de documents
- **Recherche :** trouvez un produit par référence ou désignation

#### NOTE IMPORTANTE

Seuls les documents validés sont calculés dans ce rapport.

---

## 9-Stock Produit

**Route:** `stock-produit`

#### À QUOI SERT CE RAPPORT ?

Ce rapport vous donne une photographie complète de votre stock actuel avec sa valorisation financière.

#### INDICATEURS PAR PRODUIT

- Référence et désignation
- **Quantité :** stock actuel disponible
- Prix d'achat et prix de vente unitaires
- **Valeur d'achat :** Quantité × Prix d'achat (ce que le stock vous a coûté)
- **Valeur de vente :** Quantité × Prix de vente (valeur potentielle si tout est vendu)
- **Bénéfice potentiel :** Valeur de vente - Valeur d'achat

#### TOTAUX GLOBAUX

En bas du tableau, vous trouvez :

- Somme totale des valeurs d'achat de tout le stock
- Somme totale des valeurs de vente potentielles
- Bénéfice potentiel total
- **Taux de marge global :** (Bénéfice / Valeur de vente) × 100

#### NOTES IMPORTANTES

- Les valeurs sont calculées avec les prix d'achat et de vente actuels
- Le stock affiché inclut tous les mouvements validés (achats, ventes, inventaires, importations)
- Le bénéfice est potentiel : il suppose que tout le stock sera vendu au prix de vente actuel

---

## 10-Stock Produit Légal

**Route:** `stock-produit-legal`

#### À QUOI SERT CE RAPPORT ?

Ce rapport reconstitue le stock théorique en se basant uniquement sur les documents légaux (factures et avoirs), permettant une comparaison avec le stock physique.

#### CALCUL DU STOCK LÉGAL

Le stock légal est calculé comme suit :

#### ENTRÉES LÉGALES (ce qui augmente le stock) :

- Factures d'achat validées (faa)
- Avoirs de vente validés (av) - retours clients

#### SORTIES LÉGALES (ce qui diminue le stock) :

- Factures de vente validées (fa)
- Avoirs d'achat validés (ava) - retours fournisseurs

**Stock légal = Entrées légales - Sorties légales**

#### INFORMATIONS AFFICHÉES

Pour chaque produit, vous voyez :

- Référence et désignation
- Stock physique actuel (selon vos mouvements de stock)
- Stock de vente (sorties légales)
- Stock d'achat (entrées légales)
- Stock légal calculé

#### NOTE IMPORTANTE

Ce rapport se base uniquement sur les documents légaux validés. Les bons de commande, bons de livraison ou autres documents non fiscaux ne sont pas pris en compte.

---

## 11-Stock Produit par Magasin

**Route:** `stock-produit-magasin`

#### À QUOI SERT CE RAPPORT ?

Ce rapport vous permet d'analyser le stock d'un magasin spécifique avec sa valorisation financière.

#### SÉLECTION DU MAGASIN

Vous devez d'abord sélectionner le magasin à analyser. Le rapport affichera uniquement les stocks de ce point de vente.

#### INDICATEURS PAR PRODUIT

- Référence et désignation
- **Stock :** quantité disponible dans le magasin sélectionné
- Prix d'achat et prix de vente unitaires
- **Valeur d'achat :** Stock × Prix d'achat
- **Valeur de vente :** Stock × Prix de vente
- **Bénéfice potentiel :** Valeur de vente - Valeur d'achat

#### CALCUL DU STOCK PAR MAGASIN

Le stock est calculé en analysant tous les mouvements :

- **Entrées :** achats, transferts entrants, ajustements d'inventaire positifs
- **Sorties :** ventes, transferts sortants, ajustements d'inventaire négatifs

**Stock magasin = Entrées - Sorties** (pour ce magasin uniquement)

#### TOTAUX GLOBAUX

En bas du rapport :

- Valeur totale d'achat du stock du magasin
- Valeur totale de vente potentielle
- Bénéfice potentiel total
- Taux de marge du magasin

#### FILTRES DISPONIBLES

- **Par magasin :** sélection obligatoire
- **Par référence produit :** recherche par code article
- **Par désignation :** recherche par nom de produit

---

## 12-Rapport Commerciaux

**Route:** `commerciaux`

#### À QUOI SERT CE RAPPORT ?

Ce rapport évalue la performance de chaque commercial en termes de nombre de ventes, de chiffre d'affaires généré et de commissions gagnées.

#### INDICATEURS PAR COMMERCIAL

- Nom du commercial
- **Nombre de ventes :** total des ventes attribuées au commercial
- **Chiffre d'affaires :** somme des montants TTC de toutes ses ventes
- **Commissions :** montant total des commissions gagnées

#### CALCUL DES COMMISSIONS

Pour chaque vente, la commission est calculée ainsi :

**Commission = Montant TTC de la vente × Taux de commission %**

Le taux de commission utilisé est :

- Le taux défini spécifiquement sur la vente, si renseigné
- Sinon, le taux de commission par défaut du commercial

#### FILTRES DISPONIBLES

- **Par période :** analysez la performance sur une plage de dates
- **Par type de vente :** sélectionnez les types de documents à inclure (factures, bons, etc.)
- **Recherche :** trouvez un commercial par nom ou référence

#### DOCUMENTS PRIS EN COMPTE

- Seules les ventes validées sont comptabilisées
- Seules les ventes ayant un commercial attribué apparaissent
- Les types de documents dépendent de votre sélection

#### NOTE IMPORTANTE

Seuls les documents validés sont calculés dans ce rapport.

---

## 13-Sessions de Caisse

**Route:** `sessions`

### À QUOI SERT CE RAPPORT ?

Ce rapport liste toutes les sessions de caisse de vos points de vente, permettant un suivi précis de l'activité de chaque utilisateur et magasin.

#### QU'EST-CE QU'UNE SESSION ?

Une session de caisse représente une période de travail :

- Elle commence à l'ouverture de la caisse par un utilisateur
- Elle se termine à la fermeture de la caisse
- Toutes les opérations effectuées pendant cette période y sont rattachées

#### INFORMATIONS AFFICHÉES

Pour chaque session, vous voyez :

- Le magasin concerné
- L'utilisateur qui a ouvert la session
- La date et l'heure de début
- La date et l'heure de fin (si fermée)
- Le statut (Ouverte ou Fermée)
- Le total TTC des ventes de la session

#### FILTRES DISPONIBLES

- **Par période :** sélectionnez une plage de dates
- **Par magasin :** isolez les sessions d'un point de vente

#### UTILISATION PRATIQUE

- Suivez l'activité par caissier et par magasin
- Identifiez les sessions non fermées
- Analysez les performances par période
- Cliquez sur l'œil pour voir le détail d'une session

---

## 14-Détail Session de Vente

**Route:** `sessions.ventes`

#### À QUOI SERT CE RAPPORT ?

Ce rapport affiche le détail complet d'une session de caisse spécifique : toutes les ventes, retours et dépenses effectués pendant cette session.

#### INFORMATIONS GÉNÉRALES DE LA SESSION

- Magasin concerné
- Utilisateur (caissier)
- Date et heure d'ouverture
- Date et heure de fermeture (si applicable)
- Statut de la session (Ouverte/Fermée)

#### RÉCAPITULATIF DES OPÉRATIONS

### Ventes :

- **Nombre de ventes :** total des transactions de vente
- Montant total des ventes TTC

#### Retours :

- **Nombre de retours :** total des avoirs émis
- Montant total des retours TTC

#### Dépenses :

- Montant total des dépenses enregistrées pendant la session

#### NOTES IMPORTANTES

- Seules les opérations effectuées pendant cette session précise sont affichées
- Les documents créés mais non validés n'apparaissent pas
- Une session ouverte peut toujours être consultée même si elle n'est pas encore fermée
- Les dépenses sont rattachées à la session active au moment de leur création

---

## 15-Catégorie de Dépense

**Route:** `categorie-depense`

#### À QUOI SERT CE RAPPORT ?

Ce rapport analyse vos dépenses par catégorie sur une période donnée, vous permettant d'identifier où va votre argent et d'optimiser vos coûts.

#### INFORMATIONS AFFICHÉES

Pour chaque catégorie de dépense, vous voyez :

- Le nom de la catégorie
- **Le montant total HT :** somme des dépenses hors taxes
- Le montant total des impôts/taxes
- **Le montant total TTC :** montant total incluant les taxes
- Le nombre de dépenses payées dans cette catégorie
- Le pourcentage par rapport au total des dépenses

#### VISUALISATION GRAPHIQUE

Un graphique circulaire (camembert) vous permet de visualiser rapidement :

- La répartition des dépenses par catégorie
- Les catégories qui pèsent le plus dans votre budget
- La proportion de chaque type de dépense

#### CALCUL DES MONTANTS

- **Montant HT :** montant de base de la dépense
- **Impôt :** Montant HT × (Taux de taxe / 100)
- **Montant TTC :** Montant HT + Impôt

#### FILTRES DISPONIBLES

- **Par période :** sélectionnez une plage de dates pour analyser vos dépenses
- **Par défaut :** l'année d'exercice en cours est affichée

#### NOTES IMPORTANTES

- Seules les dépenses de la période sélectionnée sont incluses
- Les dépenses sans catégorie sont regroupées sous 'Sans catégorie'
- Le tri est effectué par montant décroissant (les plus grosses dépenses en premier)

---

## 16-Rapport Journalier

**Route:** `journalier`

#### À QUOI SERT CE RAPPORT ?

Ce rapport journalier vous donne une vue complète de l'activité d'un magasin sur une journée précise : ventes, achats, paiements, créances et trésorerie.

#### SECTION 1 : VENTES CLIENT / ARTICLE

Cette section présente un tableau croisé dynamique des ventes du jour par client et par article.

#### SECTION 2 : ARTICLES / FOURNISSEURS

Tableau croisé des achats du jour par article et par fournisseur.

#### SECTION 3 : PAIEMENTS / CRÉDITS

Liste détaillée des paiements reçus sur d'anciennes créances. Cette section permet de suivre les encaissements différés et les clients qui règlent leurs anciennes factures.

#### SECTION 4 : TRÉSORERIE DU JOUR

Tableau récapitulatif des flux financiers :

#### VENTES :

- **Ventes du jour :** montant total TTC des ventes de la journée
- **Ventes créances encaissées :** montant des anciennes créances payées
- **Total ventes :** somme des deux lignes précédentes

#### ENCAISSEMENTS PAR MÉTHODE :

**Espèces :**

- Espèces du jour : paiements cash des ventes du jour
- Espèces créances : paiements cash des anciennes créances
- **Total espèces :** total des encaissements en liquide

**Chèques :**

- Chèques du jour : paiements par chèque des ventes du jour
- Chèques créances : chèques reçus pour anciennes créances
- **Total chèques :** total des paiements par chèque

**LCN :**

- LCN du jour : lettres de change normalisées du jour
- LCN créances : LCN reçues pour anciennes créances
- **Total LCN :** total des paiements par LCN

#### DÉPENSES :

- **Total dépenses :** somme de toutes les dépenses enregistrées le jour

#### SOLDE :

- **Reste en caisse :** Total espèces - Total dépenses
  (Ce montant représente le liquide qui devrait être physiquement en caisse)

#### SECTION 5 : DÉPENSES PAR CATÉGORIE

Répartition des dépenses du jour par catégorie avec montants et pourcentages.

#### FILTRES DISPONIBLES

- **Date :** sélectionnez le jour à analyser
- **Magasin :** choisissez le point de vente concerné

#### NOTES IMPORTANTES

- Ce rapport se base uniquement sur les ventes liées à des sessions POS (ventes en caisse)
- Les ventes du jour sont celles dont la date_document correspond au jour sélectionné
- Les créances encaissées sont les paiements du jour sur des ventes antérieures
- Le montant 'Reste en caisse' doit correspondre au comptage physique des espèces
- Seules les ventes et achats validés sont pris en compte

---

## Rapport Créances Clients

#### À QUOI SERT CE RAPPORT ?

Ce rapport permet de suivre vos impayés clients, d’analyser leur ancienneté (aging) et de préparer les actions de relance.

#### MÉTHODE DE CALCUL

- Documents pris en compte : factures de vente (fa) validées
- Solde d’une facture : Montant TTC − Paiements encaissés − Avoirs de vente imputés
- Paiements partiels : pris en compte au prorata
- Échéance : déterminée par les conditions de paiement (date facture + délai)
- Ancienneté : calculée à partir d’une date de référence (fin de période ou aujourd’hui)

#### INDICATEURS AFFICHÉS

- Solde total des créances (toutes factures non soldées)
- Répartition par tranches d’âge : 0–30 j, 31–60 j, 61–90 j, >90 j
- Nombre de factures échues et en retard
- Top clients par solde dû
- Date du dernier encaissement par client

#### DÉTAIL PAR FACTURE

Chaque ligne affiche :
- Référence de la facture, date, date d’échéance
- Montant TTC, total encaissé, solde restant
- Jours de retard (si échéance dépassée)
- Magasin / commercial (si disponibles)
- Balises (tags) associées

#### FILTRES DISPONIBLES

- Date de référence pour l’aging (par défaut : aujourd’hui)
- Période de facturation (date de facture)
- Client(s), magasin, commercial
- Statut : À échéance, Échue, En retard, Soldée
- Balises (tags) documentaires
- Inclure/Exclure les avoirs imputés

#### UTILISATION PRATIQUE

- Prioriser les relances en ciblant les factures les plus anciennes
- Identifier les clients à risque (>90 j)
- Suivre l’efficacité des encaissements (date du dernier paiement)
- Exporter un état pour votre comptable

#### NOTES IMPORTANTES

- Les devis, bons et documents non fiscaux ne sont pas inclus
- Les avoirs de vente (av) réduisent le solde s’ils sont imputés sur la facture
- En multi-devises, les soldes sont présentés dans la devise du document

---

## Rapport Historique Client

#### À QUOI SERT CE RAPPORT ?

Ce rapport constitue le relevé chronologique d’un client (état de compte) : toutes les opérations qui impactent son solde, avec un cumul en cours.

#### CONTENU ET MÉTHODE

- Opérations listées (validées uniquement) :
    - Factures de vente (fa) : augmentent le solde dû
    - Avoirs de vente (av) : diminuent le solde dû
    - Paiements reçus : diminuent le solde dû
- Solde initial (optionnel) : créance cumulée avant la période sélectionnée
- Solde courant : calculé après chaque opération (running balance)

#### INFORMATIONS PAR OPÉRATION

- Date, type d’opération (facture/avoir/paiement)
- Référence du document et lien de consultation
- Montant TTC (facture/avoir) ou montant du paiement
- Méthode de paiement (si disponible)
- Solde après opération
- Magasin / commercial / balises (si disponibles)

#### FILTRES DISPONIBLES

- Client (obligatoire)
- Période (dates de documents)
- Types d’opérations à inclure (factures, avoirs, paiements)
- Magasin, commercial, balises
- Inclure le solde initial (oui/non)

#### UTILISATION PRATIQUE

- Émettre un relevé de compte pour un client donné
- Comprendre l’historique des impayés et des régularisations
- Justifier un solde lors d’une relance ou d’un litige
- Vérifier l’imputation d’avoirs et de paiements

#### NOTES IMPORTANTES

- Seuls les documents validés sont pris en compte
- Les paiements non affectés à une facture apparaissent comme mouvements généraux de solde
- Les montants et soldes respectent la devise du client/document


---

## Notes Générales

#### Conventions utilisées dans tous les rapports :

- **HT :** Hors Taxes
- **TTC :** Toutes Taxes Comprises
- **fa :** Facture de vente
- **faa :** Facture d'achat
- **av :** Avoir de vente
- **ava :** Avoir d'achat
- **LCN :** Lettre de Change Normalisée

#### Principes généraux :

- Tous les rapports se basent sur des documents **validés** sauf mention contraire
- Les montants incluent toujours la TVA sauf indication "HT"
- Les périodes sont toujours personnalisables via les filtres
- Les données sont calculées en temps réel à partir de la base de données
