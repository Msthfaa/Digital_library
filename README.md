# Digital Library Ultimate

**Nama Kelompok:** Kelompok 1 
Musthofa Agung Distyawan(3124500031)
Wina Rahmalia (3124500052)
Firas Rasendriya Athaillah(3124500042)

Aplikasi web manajemen perpustakaan yang tidak hanya menyediakan fungsionalitas CRUD yang lengkap, tetapi juga berfungsi sebagai alat pembelajaran interaktif untuk memahami cara kerja berbagai algoritma pengurutan dan pencarian secara visual.

## Fitur Utama

* **Manajemen Buku (CRUD):** Tambah, lihat, edit, dan hapus data buku dengan antarmuka yang intuitif.
* **Dashboard Analitik:** Menampilkan statistik kunci tentang koleksi perpustakaan, seperti total buku, jumlah penulis, dan distribusi kategori dalam bentuk diagram (Chart.js).
* **Visualisasi Algoritma Real-time:**

  * **Sorting:** Amati proses langkah-demi-langkah dari Insertion Sort dan Quick Sort.
  * **Searching:** Ikuti logika Binary Search saat mencari buku berdasarkan judul.
* **Panel Statistik Performa:** Setelah visualisasi selesai, panel akan menampilkan metrik penting seperti:

  * Waktu Eksekusi (ms)
  * Waktu Visualisasi (s)
  * Total Perbandingan
  * Total Penukaran (untuk sorting)
* **Tampilan Ganda:** Lihat koleksi buku dalam format Kartu (Cards) yang visual atau format Tabel yang dioptimalkan untuk visualisasi algoritma.
* **Desain Responsif:** Tampilan yang optimal di berbagai ukuran layar, dari desktop hingga perangkat mobile, berkat Tailwind CSS.
* **Inisialisasi Database Otomatis:** Sistem secara otomatis akan membuat tabel yang diperlukan saat aplikasi pertama kali dijalankan, membuat proses setup menjadi sangat mudah.

## Teknologi yang Digunakan

* **Backend:** PHP 7.4+ (dengan pendekatan Object-Oriented Programming), MySQL
* **Frontend:** HTML5, Tailwind CSS, JavaScript (ES6+), Chart.js
* **Lingkungan Pengembangan:** XAMPP / WAMP / MAMP

## Prasyarat

Pastikan Anda memiliki server web lokal yang terpasang di sistem Anda, seperti XAMPP atau WAMP, yang mencakup PHP dan MySQL.

## Instalasi dan Konfigurasi

Ikuti langkah-langkah berikut untuk menjalankan proyek ini secara lokal:

### Clone Repositori

```bash
git clone https://github.com/username/digital-library-ultimate.git
```

> Ganti `username` dengan nama pengguna GitHub Anda

### Pindahkan Folder Proyek

Pindahkan folder `digital-library-ultimate` yang sudah di-clone ke dalam direktori `htdocs` (untuk XAMPP) atau `www` (untuk WAMP) pada instalasi server lokal Anda.

### Buat Database

* Buka phpMyAdmin melalui browser ([http://localhost/phpmyadmin](http://localhost/phpmyadmin))
* Buat database baru dengan nama `digital_library`. Anda tidak perlu membuat tabel apa pun.

### Jalankan Aplikasi

Akses aplikasi melalui browser Anda dengan membuka URL:

```
http://localhost/digital-library-ultimate/
```

Aplikasi akan secara otomatis membuat tabel `books` saat pertama kali diakses. Anda siap untuk menggunakannya!

