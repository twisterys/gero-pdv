# Documentation Auto-Hébergement Sentry

## Accès à Sentry

L'interface Sentry est accessible via l'URL : https://errors.tarmiz.ma

## Configuration de Sentry

### 1. Prérequis

- Docker
- Docker Compose
- Git

### 2. Installation

Le projet Sentry est cloné depuis GitHub et installé dans le répertoire suivant :

```bash
/opt/sentry
```

### 3. Gestion du service

Un service Linux est configuré pour gérer Sentry avec les commandes suivantes :

```bash
systemctl status sentry
systemctl stop sentry
systemctl start sentry
systemctl restart sentry
```

Pour consulter les logs du service Sentry en temps réel :

```bash
journalctl -u sentry -f
```

### 4. Configuration Nginx et SSL

Le serveur Nginx est configuré pour servir Sentry via le nom de domaine `errors.tarmiz.ma`, sécurisé par un certificat SSL (Let's Encrypt).

### 5. Configuration SMTP

Sentry gère la configuration SMTP via ses fichiers de configuration plutôt que par l'interface web. Pour mettre à jour la configuration SMTP :

1. Ouvrir le fichier de configuration :
    
    ```bash
    /opt/sentry/sentry/config.yml
    ```
    
2. Modifier les paramètres SMTP :
    
    ```yaml
    mail.host: ''
    mail.port: 123
    mail.username: ''
    mail.password: '*********'
    mail.use-tls: false
    mail.use-ssl: true
    mail.from: ''
    ```
    
3. Redémarrer le serveur :
    
    ```bash
    systemctl restart sentry
    ```
    

### 6. Configuration du préfixe DSN

Pour que la génération des DSN soit correcte, ajoutez votre nom de domaine dans le fichier `/opt/sentry/sentry/config.yml` :

```yaml
system.url-prefix: https://errors.tarmiz.ma
```

### 7. Résolution du problème CSRF

Lors de l'accès à l'interface web de Sentry via son nom de domaine personnalisé, une erreur liée à la vérification CSRF (Cross-Site Request Forgery) peut survenir, empêchant l'accès.

Pour corriger ce problème, ajoutez explicitement les domaines de confiance dans la configuration Django de Sentry en modifiant le fichier `/opt/sentry/sentry/sentry.conf.py` :

```python
CSRF_TRUSTED_ORIGINS = ["https://errors.tarmiz.ma", "http://145.239.66.27:9000"]
```

**Note importante :** Ces valeurs doivent être modifiées en cas de changement de nom de domaine.

## Intégration d'un projet Laravel à Sentry

Pour ajouter un projet Laravel à Sentry, suivez ces étapes :

1. Installez le package Sentry dans votre projet Laravel :
    
    ```bash
    composer require sentry/sentry-laravel
    php artisan vendor:publish --provider="Sentry\\Laravel\\ServiceProvider"
    ```
    
2. Modifiez le fichier `config/sentry.php` avec les valeurs suivantes :
    
    ```php
    'dsn' => env('SENTRY_LARAVEL_DSN', ''),
    'traces_sample_rate' => (float) env('SENTRY_TRACES_SAMPLE_RATE', 1.0),
    'profiles_sample_rate' => (float) env('SENTRY_PROFILES_SAMPLE_RATE', 1.0),
    ```
    
3. Dans `config/logging.php`, ajoutez le channel Sentry :
    
    ```php
    'sentry' => [
        'driver' => 'sentry',
    ],
    ```
    
4. Dans le même fichier, ajoutez le channel Sentry au driver stack :
    
    ```php
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'sentry'],
        'ignore_exceptions' => false,
    ],
    ```
    
5. Dans l'interface Sentry, créez un nouveau projet et sélectionnez Laravel comme type de projet.
    
6. Une fois le projet créé, récupérez le DSN et ajoutez-le à votre fichier `.env` de Laravel.
   ```php
    SENTRY_LARAVEL_DSN=https://**************************@errors.tarmiz.ma/6
    ```
