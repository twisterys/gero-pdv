### 1. **Installer Laravel Horizon**

```bash
composer require laravel/horizon
```

> Si on vous demande des extensions PHP manquantes, installez-les via apt, par exemple, php-redis.

---

### 2. **Publier la configuration de Horizon**

```bash
php artisan horizon:install
```

Cela publiera le fichier de configuration : 

`config/horizon.php`

### 3. **Installer et configurer Redis**

```bash
sudo apt install php-redis
sudo apt install redis-server
sudo systemctl start redis-server
sudo systemctl enable redis-server
```

> ✅ Vérifier le statut de Redis :

```bash
sudo systemctl status redis-server
```

---

### 4. **Tester Redis CLI (Optionnel)**

```bash
redis-cli
```

Essayez :

```bash
ping
# Résultat attendu : PONG
```

---

### 5. **Mettre à jour `.env`**

```
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
REDIS_DB=0
QUEUE_CONNECTION=redis

HORIZON_BASIC_AUTH_USERNAME=admin
HORIZON_BASIC_AUTH_PASSWORD=admin
```

> Assurez-vous que la connexion de file d'attente est définie sur redis et non sur database.

---

### 6. **Effacer et rafraîchir le cache Laravel**

```bash
php artisan cache:clear
php artisan config:clear
```

---

### 7. **Lancer Horizon**

```bash
php artisan horizon
```

Pour redémarrer Horizon en toute sécurité (par exemple, après un déploiement) :

```bash
php artisan horizon:terminate
```
