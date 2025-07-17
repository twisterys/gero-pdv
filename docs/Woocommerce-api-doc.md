# Module d'Importation WooCommerce

## Aperçu
Le module d'importation WooCommerce est responsable de l'importation des produits et des commandes depuis une boutique WooCommerce vers l'application. Ce module garantit la synchronisation des données entre la boutique WooCommerce et le système interne.

## Fonctionnalités
- Importer les produits WooCommerce dans le catalogue de produits de l'application.
- Importer les commandes WooCommerce dans le système de vente.
- Maintenir l'intégrité des données en empêchant les importations en double.
- Conserver l'historique des importations avec suivi du statut.
- Gérer les erreurs de manière élégante et enregistrer les exceptions.

## Dépendances
- Laravel 10
- codexshaper/laravel-woocommerce v3.0.4
- Woocommerce Api v2 / v3

## Points de terminaison API WooCommerce utilisés

### Récupération des produits
**Point de terminaison :** `GET /wp-json/wc/v3/products`

### Récupération d'un produit
**Point de terminaison :** `GET /wp-json/wc/v3/products/{id}`

### Récupération des commandes
**Point de terminaison :** `GET /wp-json/wc/v3/orders`

### Récupération d'un client
**Point de terminaison :** `GET /wp-json/wc/v3/customers/{id}`

## Importation des produits
**Méthode :** `importProducts(Request $request)`

### Étapes :
1. Récupérer les paramètres WooCommerce depuis la base de données.
2. Obtenir l'horodatage de la dernière importation réussie des produits.
3. Récupérer les nouveaux produits depuis l'API WooCommerce via `getAllProducts($options)`.
4. Parcourir les produits et vérifier s'ils existent déjà.
5. Si un produit n'existe pas :
    - Créer un nouvel enregistrement `Article`.
    - Assigner la catégorie à `Famille`.
    - Sauvegarder les détails du produit dans la base de données.
6. Sauvegarder l'historique d'importation dans `WoocommerceImport`.
7. Gérer les erreurs en utilisant un bloc try-catch et enregistrer les exceptions.

## Importation des commandes
**Méthode :** `importOrders(Request $request)`

### Étapes :
1. Valider les paramètres de la requête (`magasin_id`, `type`).
2. Récupérer les paramètres WooCommerce depuis la base de données.
3. Obtenir l'horodatage de la dernière importation réussie des commandes.
4. Récupérer les nouvelles commandes depuis l'API WooCommerce via `getAllOrders($options)`.
5. Parcourir les commandes et traiter chacune :
    - Si `customer_id` existe, vérifier si le client existe dans la base de données.
    - Si le client n'existe pas, créer un nouvel enregistrement `Client`.
    - Créer un nouvel enregistrement `Vente`.
    - Parcourir les articles de la commande et :
        - Vérifier si le produit existe dans `Article`.
        - Si non, récupérer les détails du produit via l'API WooCommerce et créer un nouvel enregistrement `Article`.
        - Créer des enregistrements `VenteLigne` pour chaque article de la commande.
    - Mettre à jour les totaux de la `Vente`.
6. Sauvegarder l'historique d'importation dans `WoocommerceImport`.
7. Gérer les erreurs en utilisant un bloc try-catch et enregistrer les exceptions.

## Gestion des erreurs et journalisation
- Les transactions sont encapsulées dans `DB::beginTransaction()` et `DB::commit()` pour garantir l'intégrité des données.
- En cas d'exception, `DB::rollBack()` annule les modifications.
- Les erreurs sont enregistrées en utilisant `LogService::logException($exception)`.
- Le statut de l'importation est enregistré dans `WoocommerceImport` avec le statut (`succès` ou `échec`).

## Mesures d'intégrité des données
- Empêcher les importations en double de produits en vérifiant le SKU avant de créer de nouveaux produits.
- Empêcher les importations en double de commandes en suivant la dernière commande importée.
- S'assurer que les enregistrements `Client` ne sont créés que si l'email n'existe pas déjà dans le système.

## Conclusion
Ce module permet une synchronisation fluide des données WooCommerce en récupérant, traitant et stockant efficacement les produits et les commandes tout en maintenant l'intégrité des données et en gérant les erreurs de manière appropriée.
