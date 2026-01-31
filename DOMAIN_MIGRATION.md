# Panduan Migrasi Domain: xipao.my.id

## Persiapan Sebelum Migrasi

### 1. Setup DNS Record

Di panel DNS domain `xipao.my.id`, tambahkan:

| Type | Name | Value         | TTL  |
| ---- | ---- | ------------- | ---- |
| A    | @    | 45.32.118.143 | 3600 |
| A    | www  | 45.32.118.143 | 3600 |

> Tunggu propagasi DNS (bisa 5 menit - 24 jam)

### 2. Cek DNS (setelah setup)

```bash
dig xipao.my.id +short
# harus menampilkan: 45.32.118.143
```

---

## Langkah Deploy di Server

### 1. SSH ke Server

```bash
ssh root@45.32.118.143
```

### 2. Pull Perubahan Terbaru

```bash
cd /var/www/xipao
git pull origin main
```

### 3. Update Nginx Config

```bash
# Backup config lama
sudo cp /etc/nginx/sites-available/xipao /etc/nginx/sites-available/xipao.backup

# Copy config baru
sudo cp nginx-xipao.conf /etc/nginx/sites-available/xipao

# Test config
sudo nginx -t

# Reload nginx
sudo systemctl reload nginx
```

### 4. Update .env Laravel

```bash
nano /var/www/xipao/.env
```

Ubah:

```env
APP_URL=https://xipao.my.id
```

### 5. Clear Cache Laravel

```bash
cd /var/www/xipao
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 6. Setup SSL dengan Certbot

```bash
sudo certbot --nginx -d xipao.my.id -d www.xipao.my.id
```

Certbot akan otomatis:

- Generate SSL certificate
- Update nginx config untuk HTTPS
- Setup auto-renewal

### 7. Verifikasi

- Buka https://xipao.my.id
- Pastikan redirect dari http ke https berjalan
- Cek semua fitur aplikasi

---

## Rollback (Jika Ada Masalah)

```bash
# Kembalikan nginx config lama
sudo cp /etc/nginx/sites-available/xipao.backup /etc/nginx/sites-available/xipao
sudo systemctl reload nginx

# Update .env kembali ke domain lama
nano /var/www/xipao/.env
# APP_URL=https://xipao.vinnnservices.my.id

php artisan config:clear
```

---

## Checklist Migrasi

- [ ] DNS A record untuk xipao.my.id sudah pointing ke 45.32.118.143
- [ ] DNS A record untuk www.xipao.my.id sudah pointing ke 45.32.118.143
- [ ] DNS sudah propagate (cek dengan `dig xipao.my.id`)
- [ ] Git pull di server
- [ ] Nginx config sudah diupdate
- [ ] .env APP_URL sudah diubah
- [ ] Cache Laravel sudah di-clear
- [ ] SSL sudah terinstall (certbot)
- [ ] Website bisa diakses via https://xipao.my.id
- [ ] Semua fitur berjalan normal
