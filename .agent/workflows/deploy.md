---
description: Deploy Xipao ke server Ubuntu baru
---

# Xipao Deployment

// turbo-all

## 1. Install Dependencies
```bash
ssh root@[SERVER_IP] "add-apt-repository -y ppa:ondrej/php && apt update && apt install -y nginx mysql-server php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl unzip git nodejs npm"
```

## 2. Install Composer
```bash
ssh root@[SERVER_IP] "curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer"
```

## 3. Clone Project
```bash
ssh root@[SERVER_IP] "cd /var/www && git clone https://github.com/Avzls/xipao.git xipao"
```

## 4. Install Dependencies
```bash
ssh root@[SERVER_IP] "cd /var/www/xipao && composer install --no-dev --ignore-platform-reqs && npm install && npm run build"
```

## 5. Setup MySQL User & Database
```bash
ssh root@[SERVER_IP] "cat > /tmp/mysql_setup.sql << 'SQLEOF'
CREATE DATABASE xipao;
CREATE USER 'xipao_app'@'localhost' IDENTIFIED WITH mysql_native_password BY 'xipao2026';
GRANT ALL PRIVILEGES ON xipao.* TO 'xipao_app'@'localhost';
FLUSH PRIVILEGES;
SQLEOF
mysql < /tmp/mysql_setup.sql"
```

## 6. Setup Environment
```bash
ssh root@[SERVER_IP] "cd /var/www/xipao && cp .env.example .env && php artisan key:generate"
```

Update .env:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://[DOMAIN]

DB_DATABASE=xipao
DB_USERNAME=xipao_app
DB_PASSWORD=xipao2026
DB_SOCKET=/var/run/mysqld/mysqld.sock
```

## 7. Run Migration & Seeder
```bash
ssh root@[SERVER_IP] "cd /var/www/xipao && php artisan migrate --force --seed --seeder=UserSeeder"
```

## 8. Set Permissions
```bash
ssh root@[SERVER_IP] "chown -R www-data:www-data /var/www/xipao && chmod -R 755 /var/www/xipao && chmod -R 775 /var/www/xipao/storage /var/www/xipao/bootstrap/cache"
```

## 9. Create Nginx Config
Create file `/etc/nginx/sites-available/xipao`:
```nginx
server {
    listen 80;
    server_name [DOMAIN];
    root /var/www/xipao/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 10. Enable Site
```bash
ssh root@[SERVER_IP] "ln -sf /etc/nginx/sites-available/xipao /etc/nginx/sites-enabled/ && nginx -t && systemctl reload nginx"
```

## 11. Install SSL
```bash
ssh root@[SERVER_IP] "apt install -y certbot python3-certbot-nginx && certbot --nginx -d [DOMAIN] --non-interactive --agree-tos -m admin@[DOMAIN]"
```

## 12. Optimize Laravel
```bash
ssh root@[SERVER_IP] "cd /var/www/xipao && php artisan config:cache && php artisan route:cache && php artisan view:cache"
```

---

## Login Credentials
- **Email:** admin@xipao.com
- **Password:** password

## Variables to Replace
- `[SERVER_IP]` - IP server tujuan
- `[DOMAIN]` - Domain yang akan dipakai (contoh: xipao.vinnnservices.my.id)
