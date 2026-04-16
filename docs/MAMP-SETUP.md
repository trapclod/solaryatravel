# Configurazione MAMP per Solarya Travel

Questa guida spiega come configurare MAMP su macOS per il progetto Solarya Travel.

## Prerequisiti

- MAMP o MAMP PRO installato
- PHP 8.3+ disponibile in MAMP
- MySQL 8.0+ disponibile in MAMP

## 1. Configurazione Porte

Apri MAMP e vai in **Preferences > Ports**:
- Apache Port: `8890`
- MySQL Port: `8889`

## 2. Configura Virtual Host

### Opzione A: MAMP PRO

1. Vai in **Hosts**
2. Clicca **+** per aggiungere un nuovo host
3. Configura:
   - Name: `solaryatravel`
   - Document Root: `/Users/YOUR_USERNAME/DEVELOPMENT/solaryatravel/public`
   - PHP Version: 8.3.x

### Opzione B: MAMP Standard

Modifica il file `/Applications/MAMP/conf/apache/extra/httpd-vhosts.conf`:

```apache
<VirtualHost *:8890>
    ServerName solaryatravel
    DocumentRoot "/Users/YOUR_USERNAME/DEVELOPMENT/solaryatravel/public"
    
    <Directory "/Users/YOUR_USERNAME/DEVELOPMENT/solaryatravel/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "/Applications/MAMP/logs/solaryatravel-error.log"
    CustomLog "/Applications/MAMP/logs/solaryatravel-access.log" combined
</VirtualHost>
```

**Importante:** Sostituisci `YOUR_USERNAME` con il tuo nome utente macOS.

## 3. Abilita Virtual Hosts (solo MAMP Standard)

Modifica `/Applications/MAMP/conf/apache/httpd.conf` e decommenta:

```apache
Include /Applications/MAMP/conf/apache/extra/httpd-vhosts.conf
```

## 4. Modifica file hosts

Esegui nel terminale:

```bash
sudo nano /etc/hosts
```

Aggiungi la riga:

```
127.0.0.1   solaryatravel
```

Salva con `Ctrl+O`, poi `Ctrl+X` per uscire.

## 5. Configurazione PHP

Verifica che queste estensioni siano abilitate in `/Applications/MAMP/bin/php/php8.3.x/conf/php.ini`:

```ini
extension=pdo_mysql
extension=mbstring
extension=gd
extension=zip
extension=intl
extension=exif
extension=openssl

; Aumenta limiti per upload
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
memory_limit = 256M
```

## 6. Crea Database

In phpMyAdmin (http://localhost:8888/phpMyAdmin) o via CLI:

```sql
CREATE DATABASE solaryatravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'genericUsr'@'localhost' IDENTIFIED BY 'Password08$';
GRANT ALL PRIVILEGES ON solaryatravel.* TO 'genericUsr'@'localhost';
FLUSH PRIVILEGES;
```

## 7. Configura .env

Copia il file `.env.example` in `.env` e verifica questi valori:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=solaryatravel
DB_USERNAME=genericUsr
DB_PASSWORD=Password08$

APP_URL=https://solaryatravel:8890
```

## 8. Esegui migrazioni e seed

```bash
cd /Users/YOUR_USERNAME/DEVELOPMENT/solaryatravel

# Genera chiave applicazione
php artisan key:generate

# Esegui migrazioni
php artisan migrate

# Carica dati di esempio
php artisan db:seed

# Crea link storage
php artisan storage:link
```

## 9. Riavvia MAMP

Riavvia MAMP per applicare le modifiche.

## 10. Accedi all'applicazione

- **Frontend**: https://solaryatravel:8890
- **Admin**: https://solaryatravel:8890/admin

## Troubleshooting

### "This site can't be reached"
- Verifica che MAMP sia avviato
- Controlla che il file hosts sia configurato correttamente
- Verifica la porta Apache in MAMP

### "Database connection refused"
- Verifica che MySQL sia avviato in MAMP
- Controlla la porta MySQL (default MAMP: 8889)
- Verifica credenziali in .env

### "Permission denied" su storage
```bash
chmod -R 775 storage bootstrap/cache
```

### SSL Certificate Warning (HTTPS)
Per testing locale con HTTPS in MAMP PRO:
1. Vai in Hosts > tuo host > SSL
2. Abilita SSL
3. Usa certificato self-signed
4. Aggiungi il certificato al Keychain come "Always Trust"

Per MAMP Standard senza SSL, usa `http://solaryatravel:8890` invece di `https://`.

## Comandi Utili

```bash
# Pulisci cache
php artisan optimize:clear

# Visualizza route
php artisan route:list

# Avvia Vite per sviluppo frontend
npm run dev

# Build asset per produzione
npm run build
```
