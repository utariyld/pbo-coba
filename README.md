# TestimoniApp - Aplikasi Testimoni Modern

TestimoniApp adalah aplikasi web modern untuk mengelola testimoni pengguna dengan fitur dashboard admin dan user yang menarik.

## 🚀 Fitur Utama

### Untuk User:
- ✅ **Tulis Testimoni**: Input testimoni dengan rating, kategori, dan deskripsi
- ✅ **Dashboard Personal**: Statistik testimoni pribadi dengan grafik menarik
- ✅ **Manajemen Testimoni**: Lihat, edit, dan hapus testimoni sendiri
- ✅ **Profile Management**: Edit profile dan ubah password
- ✅ **Achievement System**: Sistem ranking berdasarkan aktivitas

### Untuk Admin:
- ✅ **Dashboard Analytics**: Statistik lengkap dengan chart interaktif
- ✅ **Kelola Testimoni**: Lihat, moderasi, dan hapus semua testimoni
- ✅ **Kelola User**: Manajemen pengguna terdaftar
- ✅ **Export Data**: Export testimoni ke Excel/CSV
- ✅ **Real-time Statistics**: Data statistik yang update real-time

### UI/UX:
- 🎨 **Modern Design**: Tampilan modern dengan Bootstrap 5
- 📱 **Responsive**: Optimal di desktop, tablet, dan mobile
- ⚡ **Fast Loading**: AJAX untuk pengalaman yang smooth
- 🌈 **Beautiful Animations**: Animasi hover dan transition yang halus
- 🎯 **User-Friendly**: Interface yang intuitif dan mudah digunakan

## 📋 Prasyarat

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web server (Apache/Nginx)
- Browser modern (Chrome, Firefox, Safari, Edge)

## 🛠️ Instalasi

### 1. Clone atau Download
```bash
git clone [repository-url]
# atau download dan extract file ZIP
```

### 2. Setup Database
- Buat database MySQL baru dengan nama `testimoni_app`
- Aplikasi akan otomatis membuat tabel saat pertama kali dijalankan
- Konfigurasi database ada di `config/database.php`

### 3. Konfigurasi Database
Edit file `config/database.php` sesuai dengan setting database Anda:
```php
private $host = 'localhost';
private $db_name = 'testimoni_app'; 
private $username = 'root';
private $password = '';
```

### 4. Upload ke Web Server
- Upload semua file ke direktori web server (htdocs/www/public_html)
- Pastikan folder memiliki permission yang tepat

### 5. Akses Aplikasi
- Buka browser dan akses: `http://localhost/[nama-folder]`
- Aplikasi akan otomatis setup database dan data sample

## 👥 Akun Default

### Admin:
- **Username**: admin
- **Password**: admin123

### User Sample:
- **Username**: johndoe, **Password**: password123
- **Username**: janedoe, **Password**: password123  
- **Username**: bobsmith, **Password**: password123

## 📁 Struktur Folder

```
testimoni-app/
├── index.php                 # Halaman utama
├── logout.php               # Halaman logout
├── README.md                # Dokumentasi
│
├── admin/                   # Dashboard Admin
│   └── dashboard.php
│
├── user/                    # Dashboard User  
│   └── dashboard.php
│
├── api/                     # API Endpoints
│   ├── login.php           # API Login
│   ├── register.php        # API Register
│   ├── testimoni.php       # API Tambah Testimoni
│   ├── get_testimoni.php   # API Get Testimoni
│   ├── my_testimoni.php    # API Testimoni User
│   ├── admin_testimoni.php # API Admin Testimoni
│   ├── delete_testimoni.php # API Hapus Testimoni
│   └── dashboard_stats.php # API Statistik
│
├── assets/                  # Asset Statis
│   ├── css/
│   │   └── style.css       # Custom CSS
│   └── js/
│       └── main.js         # JavaScript Utama
│
├── config/                  # Konfigurasi
│   └── database.php        # Konfigurasi Database
│
└── PBOOOO/                 # Folder gambar existing
    └── src/main/resources/images/profiles/
```

## 🎯 Cara Penggunaan

### Untuk User Umum:
1. **Registrasi**: Klik "Register" dan isi form pendaftaran
2. **Login**: Masuk dengan username/email dan password
3. **Tulis Testimoni**: Klik "Tulis Testimoni" di halaman utama
4. **Dashboard**: Akses dashboard untuk melihat statistik pribadi
5. **Kelola Testimoni**: Edit atau hapus testimoni di dashboard

### Untuk Admin:
1. **Login Admin**: Login dengan akun admin
2. **Dashboard Analytics**: Lihat statistik lengkap aplikasi
3. **Kelola Testimoni**: Moderasi, approve, atau hapus testimoni
4. **Kelola User**: Lihat dan kelola data pengguna
5. **Export Data**: Download laporan dalam format Excel

## 🔧 Kustomisasi

### Mengubah Tema Warna:
Edit variabel CSS di `assets/css/style.css`:
```css
:root {
    --primary-color: #4f46e5;    /* Warna primer */
    --secondary-color: #7c3aed;  /* Warna sekunder */
    --success-color: #10b981;    /* Warna success */
    /* ... */
}
```

### Menambah Kategori Testimoni:
Edit pilihan kategori di file:
- `index.php` (form utama)
- `user/dashboard.php` (form dashboard user)

### Mengubah Validasi:
Edit validasi di:
- `api/testimoni.php` (validasi server)
- `assets/js/main.js` (validasi client)

## 🚨 Troubleshooting

### Database Connection Error:
- Pastikan MySQL service berjalan
- Cek konfigurasi di `config/database.php`
- Pastikan database `testimoni_app` sudah dibuat

### Permission Error:
```bash
# Set permission yang tepat (Linux/Mac)
chmod 755 -R .
chmod 644 *.php
```

### AJAX Error:
- Pastikan path API benar
- Cek Console Browser untuk error detail
- Pastikan session PHP aktif

### CSS/JS Not Loading:
- Cek path file di HTML
- Clear browser cache
- Pastikan file ada di folder assets/

## 📈 Fitur Mendatang

- [ ] **Email Notifications**: Notifikasi email untuk admin
- [ ] **Advanced Analytics**: Grafik lebih detail dan insights
- [ ] **Multi-language**: Support bahasa Indonesia dan Inggris
- [ ] **API Documentation**: Swagger/OpenAPI documentation
- [ ] **Mobile App**: React Native app untuk mobile
- [ ] **Social Login**: Login dengan Google/Facebook
- [ ] **Image Upload**: Upload gambar untuk testimoni
- [ ] **Comment System**: Komentar pada testimoni

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan:
1. Fork repository ini
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📝 Lisensi

Project ini menggunakan [MIT License](LICENSE).

## 📞 Support

Jika ada pertanyaan atau masalah:
- Buat Issue di repository
- Email: [your-email@example.com]
- Discord: [your-discord]

## 📊 Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript (ES6+), Bootstrap 5
- **Backend**: PHP 7.4+, MySQL
- **Libraries**: 
  - Chart.js (untuk grafik)
  - Font Awesome (icons)
  - Bootstrap 5 (UI framework)
- **Architecture**: MVC Pattern, RESTful API

---

⭐ **Jangan lupa berikan star jika project ini membantu!** ⭐