
Sentry est une plateforme de surveillance des erreurs et des performances qui aide les équipes de développement à identifier, prioriser et résoudre les problèmes en temps réel. Ses principaux avantages sont :
## 1. Journal d'erreurs et surveillance

- **Capture automatiquement les exceptions non gérées** dans votre application
- Affiche :
    - Traces d'exécution
    - Noms de fichiers et numéros de ligne
    - Données de requête/réponse
    - Informations utilisateur (si activé)
- Vous aide à **trouver et corriger les bugs rapidement**

## 2. Traçage (Surveillance des performances)

- Suit les **requêtes HTTP lentes**, les **tâches**, les **requêtes BDD**
- Les décompose en **transactions** et **portions** (spans)
- Vous aide à comprendre :
    - Quelle partie de la requête était lente

## 3. Profilage (Disponible uniquement sous Linux)

- Capture le **temps CPU** utilisé dans les fonctions pendant les requêtes
- Affiche des **graphiques en flamme** pour les parties lentes de votre application
- Aide à l'**optimisation approfondie des performances**

_Actuellement, le profilage PHP nécessite Linux + Excimer_

## 4. Alertes et notifications

Envoi d'alertes vers :

- Email
- Slack
- Microsoft Teams
- Discord, etc.


Essai de 14 jours puis 26$ par mois avec le forfait équipe.
## 5. Guide d'installation

### Pour Laravel

```php
# Installation via Composer
composer require sentry/sentry-laravel
```

Pour publier la configuration :

```php
php artisan vendor:publish --provider="Sentry\\Laravel\\ServiceProvider"
```

Connectez-vous à Sentry avec votre email pour générer un DSN et ajoutez-le au fichier .env :

```
SENTRY_LARAVEL_DSN=VOTRE_DSN
SENTRY_TRACES_SAMPLE_RATE=1.0
```

Accédez au tableau de bord depuis votre compte Sentry.