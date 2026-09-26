<p align="center">
    <a href="https://www.docotel.com/" target="_blank">
        <img src="https://www.docotel.com/img/docotel-logo.svg" height="100px">
    </a>
    <h1 align="center">SIMRS New Product</h1>
    <br>
</p>


PERSYARATAN SISTEM
------------------

1. Apache v 2.4.6 / Nginx v 1.3
1. PHP v 5.6.0 (stabil)
2. Postgre v 9.3 (stabil)
3. Node v 8.11
4. Redis v 3.2.12
5. Bower v 1.8.4 (global)
    - jika tidak memiliki [Bower](https://www.npmjs.com/package/bower-installer), silahkan mengikuti instruksi yang ada
6. Composer v 1.6.5
    - jika tidak memiliki [Composer](http://getcomposer.org/), kita dapat mengikuti instruksi pada [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix)


DOKUMENTASI BRANCH PROJECT
--------------------------

1. NEWSIMRS
  - master : branch master
  - staging : branch staging
  - development : branch development
2. RS BRIMOB
  - master : branch master_rsbrimob
  - staging : branch staging_rsbrimob
  - development : branch development_rsbrimob


INSTALASI
---------

Pastikan persyaratan sistem sudah terpenuhi

* Otomatis (Linux)
  1. Clone repo frontend
  2. Masuk ke directory hasil clone
  3. Jalankan "bash ./installation.sh"
  4. Ikuti petujuk instalasi, setup ini meliputi branching, install vendor, generate vhost, generate config, setting permission folder yang di butuhkan
  5. Server bisa di akses dengan port yg telah di setup
  6. Set chown user ke folder tujuan ketika deploy
  7. Setup backend aplikasi, ikuti petunjuk di link berikut : 


* Manual
  1. Clone repo 
  2. Masuk ke directory hasil clone
  3. Checkout ke branch yg akan di gunakan, contoh "git checkout development" untuk menggunakan branch development (optional)
  4. Jalankan "composer install"
  5. Masuk ke directory "web" dan jalankan "bower install" atau "bower install --allow-root" 
  6. Kembali ke root apps, masuk ek directory "nodejs" dan jalankan "npm install"
  7. Generate vhost (optional sesuaikan dengan web server yang di pake)
  8. Kembali ke root apps, buat folder "config/env"
  9. Buat file .env dan .api di folder "config/env"
  10. Konfigurasi aplikasi (lihat point konfigurasi)
  11. Set permission folder runtime di directory "~apps"/runtime
  12. Set permission folder runtime di directory "~apps"/web/assets
  13. Server bisa di akses dengan port yg telah di setup (jika sudah di setup)
  14. Set chown user ke folder tujuan ketika deploy
  15. Setup backend aplikasi, ikuti petunjuk di link berikut : 


KONFIGURASI
-----------

### Database

Edit file `config/env/.env` dengan data yang dibutuhkan, sebagai contoh:

```php
[db]
conn_str = "pgsql:host=localhost;port=5432;dbname=db_docohealth"
user = "postgres"
password = "******"
```

### ENDPOINT BACKEND

Edit file `config/env/.api` dengan data yang dibutuhkan, sebagai contoh:

```php
dcms = "http://newsimrs-backend.doc/dcms/web/v1/"
master = "http://newsimrs-backend.doc/master/web/v1/"
pendaftaran = "http://newsimrs-backend.doc/pendaftaran/web/v1/"
.
.
.
sesuaikan dengan backend yang di pake
```

### LISTENER REDIS DISPLAY ANTRIAN (untuk instalasi manual dan otomatis)

Edit file `web/json/config.json` dengan data yang dibutuhkan, sebagai contoh:

```php
{
    "ip" : "192.168.200.254",
    "port" : "8890"
}
```

LIST SERVER 
-----------


~~~
1. server staging newsimrs : http://stag-sirs.docotel.net/
2. server development newsimrs : http://dev-sirs.docotel.net/
~~~


