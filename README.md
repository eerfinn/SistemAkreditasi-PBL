<h3 align="center">بِسْــــــــــــــمِ اللهِ الرَّحْمَنِ الرَّحِيْــــــــــــــمِ</h3>



### Clone Repository

```bash
git clone https://github.com/eerfinn/SistemAkreditasi-PBL.git
```

### Masuk ke Direktori Proyek

```bash
cd SistemAkreditasi-PBL
```

### Install Dependensi

```bash
composer install
```

### Konfigurasi File `.env`

```bash
cp .env.example .env
```
- Buat Database baru dengan nama SistemAkreditasi
  ![image](https://github.com/user-attachments/assets/f81642c1-9951-424c-b11d-bcf08fc9c8ce)

- Buka file `.env` dan sesuaikan konfigurasi database:
  ```env
  DB_DATABASE=SistemAkreditasi
  DB_USERNAME=root
  DB_PASSWORD=
  ```

### Generate Application Key

```bash
php artisan key:generate
```

### Jalankan Server Laravel

```bash
php artisan serve
```

### Akses Aplikasi di Browser

Buka browser dan akses:

```bash
http://127.0.0.1:8000
```
