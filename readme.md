
# Gero


### Requirements
- PHP 8.2+
- node 18.0+

### Instalation

1- Install all node modules:
```bash
npm install
```
2- Install all composer packages:
```bash
composer install
```
3- Init project:
- create your database and edit .env file:
```bash
cp .env.example .env
php artisan key:generate
```

4- Migrate database and seed:
```bash
php artisan migrate --seed
```
5- Build assets:
  ```bash
  npm run build
  ```
6- Serve:
```bash
php artisan serve
```



## Demo de setup de Saas


Suivez les étapes ci-dessous pour une démonstration de l'utilisation de Gero avec la couche saas:

1. Dans le fichier `.env`, modifier le nom de base de donnée :
    ```php
    DB_DATABASE=gero

2. Importer les deux base de donnée gero.sql et dev.sql



3. Ouvrez Laragon et cliquez avec le bouton droit de la souris. Sélectionnez "Quick app" puis "Blank".
Enter le nom de domaine : "dev.gero" (Changer l'extension par défaut dans Laragon à .ma).



4. Aller dans `config/filesystems.php` modifier le chemin root par votre repertoire de storage externe. 


6. Lancer la commande
    ```bash
    php artisan storage:link
    ```



7. Accédez  à l'espace Gero du tenant créé en utilisant le lien suivant :
    ```php
    http://dev.gero.ma:8000/

Bonne navigation ! 


03/02/2025 
commit : ajout de template cachet 
ajouter cette nouvelle template :

Template::create([
'nom'=>'Cachet',
'blade' => 'cachet',
'logo'=>null,
'logo_hauteur' => 0,
'logo_largeur' => 0,
'image_arriere_plan' => null,
'image' => 'images/documents-template2-cachet.png',
'image_en_tete' => '',
'image_en_tete_hauteur' => 130.0,
'image_en_tete_largeur' => 794.0,
'image_en_bas' => '',
'image_en_bas_hauteur' => 130.0,
'image_en_bas_largeur' => 794.0,
'couleur' => '#23b67f',
'cachet_hauteur'=>170,
'cachet_largeur'=>170,
'cachet'=>'',
'afficher_total_en_chiffre' => '0',
'elements' => 'image_en_bas,image_en_tete,cachet'
]);
