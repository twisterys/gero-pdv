# Documentation - Mécanisme de Sauvegarde

Le système de sauvegarde automatique permet de sauvegarder les bases de données de toutes les instances Gero de manière centralisée depuis Gero Control.

## Processus de Sauvegarde

### Étape 1 : Gero Control lance les sauvegardes

- Un cron job s'exécute quotidiennement
- Récupère les instances et tenants éligibles selon leurs paramètres de sauvegarde
- Envoie des requêtes API aux différents types d'instances actives

### Étape 2 : Gero traite les sauvegardes

- Reçoit la liste des tenants à sauvegarder
- Lance les jobs de sauvegarde dans la queue
- Exécute les sauvegardes et les envoie vers Google Drive

### Étape 3 : Gero notifie Gero Control

- Envoie un callback avec l'état de chaque sauvegarde
- Transmet l'emplacement des fichiers SQL sauvegardés

### Étape 4 : Gero Control enregistre les résultats

- Reçoit les callbacks de toutes les instances
- Enregistre les informations des sauvegardes en base

### Étape 5 : Nettoyage automatique (Gero Control)

- Un cron job nettoie les sauvegardes de plus de 30 jours
- Libère l'espace de stockage automatiquement

## Flux de données

1. **Gero Control → Gero** : Envoi de la liste des tenants à sauvegarder
2. **Gero → Google Drive** : Stockage des fichiers de sauvegarde
3. **Gero → Gero Control** : Callback avec le statut et l'emplacement des fichiers
