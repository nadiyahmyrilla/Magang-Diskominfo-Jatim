# 🏥 Dashboard Clustering Kesehatan Masyarakat Jawa Timur

> **Analisis komprehensif penyakit kesehatan di seluruh kabupaten/kota Jawa Timur menggunakan Machine Learning**

---

## 📋 Daftar Isi
- [Tujuan Project](#tujuan-project)
- [Manfaat](#manfaat)
- [Alur Project](#alur-project)
- [Cara Menjalankan](#cara-menjalankan)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Struktur Project](#struktur-project)

---

## 🎯 Tujuan Project

Proyek ini bertujuan untuk:

1. **Menganalisis Data Kesehatan**: Mengumpulkan dan menganalisis data penyakit kesehatan masyarakat (Diabetes, Hipertensi, dsb) di 38 kabupaten/kota Jawa Timur
2. **Mengelompokkan Wilayah**: Menggunakan KMeans Clustering untuk mengelompokkan wilayah berdasarkan pola kesehatan
3. **Mengidentifikasi Risiko**: Mengidentifikasi kabupaten/kota dengan risiko kesehatan Rendah, Sedang, dan Tinggi
4. **Memberikan Rekomendasi**: Menyediakan insights dan rekomendasi kebijakan berdasarkan pola clustering
5. **Visualisasi Interaktif**: Menyajikan data dalam dashboard yang mudah dipahami dengan peta, chart, dan tabel

---

## 💡 Manfaat

### Untuk Pemerintah / Dinas Kesehatan
- 📊 Memahami pola distribusi penyakit di berbagai wilayah
- 🎯 Mengalokasikan sumber daya kesehatan lebih efisien
- 📈 Mengidentifikasi daerah prioritas intervensi kesehatan
- 📋 Mendukung perencanaan strategi kesehatan berbasis data

### Untuk Peneliti
- 🔬 Analisis mendalam tentang kesehatan masyarakat di Jawa Timur
- 📚 Sumber data dan metodologi untuk penelitian lanjutan
- 🤖 Studi kasus implementasi Machine Learning untuk clustering

### Untuk Masyarakat
- 💭 Memahami status kesehatan wilayah mereka
- 🏥 Mendorong kesadaran kesehatan preventif
- 🔍 Transparansi data publik tentang kesehatan

---

## 🔄 Alur Project

### **Tahap 1: Data Loading & Preparation**
```
File Data Excel
    ↓
Baca Data per Tahun (multi-sheet Excel)
    ↓
Pandas DataFrame (dengan kolom: nama_kabupaten_kota, diabetes, hipertensi, dsb)
```

### **Tahap 2: Data Normalisasi**
```
Data Mentah (berbagai satuan/skala)
    ↓
StandardScaler (Z-score normalization)
    ↓
Data Ternormalisasi (mean=0, std=1)
```

**Formula Normalisasi:**
$$Z = \frac{X - \mu}{\sigma}$$

Dimana:
- $X$ = nilai asli
- $\mu$ = rata-rata kolom
- $\sigma$ = standar deviasi kolom
- $Z$ = nilai terstandarisasi

### **Tahap 3: KMeans Clustering**
```
Data Ternormalisasi
    ↓
KMeans (n_clusters=3, random_state=42)
    ↓
Cluster Assignment (0, 1, 2)
    ↓
Hitung Mean per Cluster
    ↓
Sorting Clusters by Mean (dari terendah ke tertinggi)
```

### **Tahap 4: Labeling Clusters**
```
Cluster Terurut (0→Cluster Terendah, 2→Cluster Tertinggi)
    ↓
Mapping:
  - Cluster Terendah → "Rendah"
  - Cluster Tengah → "Sedang"
  - Cluster Tertinggi → "Tinggi"
    ↓
Label Akhir per Kabupaten/Kota
```

**Contoh:**
- Jika KMeans menghasilkan cluster 0, 1, 2 dengan mean [50, 150, 300]:
  - Cluster 0 (mean=50) → **Rendah**
  - Cluster 1 (mean=150) → **Sedang**
  - Cluster 2 (mean=300) → **Tinggi**

### **Tahap 5: Visualisasi Dashboard**
```
Data Berlabel
    ↓
Generate Charts (Bar, Doughnut, Area)
    ↓
Generate Map (Leaflet.js)
    ↓
Generate Table (Filter, Search)
    ↓
Dashboard Web (Django Template)
```

---

## 🚀 Cara Menjalankan

### **Prasyarat**
- Python 3.8+
- pip atau conda
- Browser modern (Chrome, Firefox, Safari)

### **Instalasi**

1. **Clone/Masuk ke Direktori Project**
   ```bash
   cd kesehatan_masyarakat
   ```

2. **Buat Virtual Environment**
   ```bash
   python -m venv venv
   ```

3. **Aktivasi Virtual Environment**
   
   **Windows:**
   ```bash
   venv\Scripts\activate
   ```
   
   **Linux/Mac:**
   ```bash
   source venv/bin/activate
   ```

4. **Install Dependencies**
   ```bash
   pip install -r requirements.txt
   ```
   
   Atau manual:
   ```bash
   pip install django pandas numpy scikit-learn openpyxl leaflet matplotlib
   ```

5. **Setup Database**
   ```bash
   python manage.py migrate
   ```

6. **Jalankan Server**
   ```bash
   python manage.py runserver
   ```

7. **Akses Dashboard**
   - Buka browser: `http://localhost:8000/dashboard/` atau `http://127.0.0.1:8000/dashboard/`
   - Dashboard akan menampilkan data clustering kesehatan

### **Navigasi Dashboard**
- 📊 **Stat Cards**: Lihat ringkasan statistik total
- 🗺️ **Peta Interaktif**: Klik kabupaten/kota untuk melihat detail, filter berdasarkan cluster
- 📈 **Charts**: Visualisasi distribusi cluster dan trend per tahun
- 📋 **Tabel Data**: Lihat detail lengkap dengan fitur cari dan filter
- 📅 **Filter Tahun**: Pilih tahun untuk melihat data berbeda

---

## 🛠️ Teknologi yang Digunakan

### **Backend**
| Teknologi | Versi | Kegunaan |
|-----------|-------|----------|
| **Python** | 3.8+ | Bahasa pemrograman utama |
| **Django** | 4.x | Web framework |
| **Pandas** | 2.0+ | Manipulasi & analisis data |
| **NumPy** | 1.20+ | Komputasi numerik |
| **Scikit-learn** | 1.0+ | Machine Learning (KMeans, StandardScaler) |
| **OpenPyXL** | 3.x | Membaca file Excel |

### **Frontend**
| Teknologi | Kegunaan |
|-----------|----------|
| **HTML5** | Struktur halaman |
| **CSS3** | Styling & animasi (Glassmorphism design) |
| **JavaScript** | Interaksi dinamis |
| **Chart.js** | Visualisasi chart (Bar, Doughnut, Area) |
| **Leaflet.js** | Peta interaktif |
| **Leaflet GeoJSON** | Layer peta geografis |

### **Database**
| Teknologi | Kegunaan |
|-----------|----------|
| **SQLite** | Database development/testing |
| **PostgreSQL** (opsional) | Production database |

---

## 📁 Struktur Project

```
kesehatan_masyarakat/
│
├── manage.py                 # Django management script
├── db.sqlite3               # Database SQLite
├── requirements.txt         # Dependencies list
│
├── kesehatan_masyarakat/    # Main project folder
│   ├── settings.py          # Django settings
│   ├── urls.py              # URL routing
│   ├── asgi.py              # ASGI config
│   ├── wsgi.py              # WSGI config
│
├── dashboard/               # Dashboard app
│   ├── views.py             # View functions (rendering dashboard)
│   ├── urls.py              # URL patterns
│   ├── templates/
│   │   └── dashboard.html   # Dashboard template (UI)
│   └── admin.py
│
├── analysis/                # Analysis app (ML logic)
│   ├── baca_data.py         # Load data from Excel
│   ├── normalisasi.py       # StandardScaler normalization
│   ├── clustering.py        # KMeans clustering
│   ├── ambil_data.py        # Data extraction
│   └── models.py
│
├── data/                    # Data folder
│   └── *.xlsx               # Excel data files
│
├── static/                  # Static files
│   ├── css/                 # Custom CSS
│   ├── js/                  # Custom JavaScript
│   └── geo/                 # GeoJSON files
│
└── templates/               # Global templates
    └── base.html            # Base template
```

### **File Kunci:**

| File | Deskripsi |
|------|-----------|
| `dashboard/views.py` | Logika utama: load data → normalisasi → clustering → render dashboard |
| `analysis/clustering.py` | Implementasi KMeans clustering (n_clusters=3) |
| `analysis/normalisasi.py` | StandardScaler untuk normalisasi data |
| `analysis/baca_data.py` | Membaca semua sheet Excel dan organize by tahun |
| `dashboard/templates/dashboard.html` | UI dengan peta interaktif, chart, dan tabel |

---

## 📊 Data Processing Pipeline

```python
# Alur dalam kode:
1. baca_semua_tahun()              # Baca semua data Excel per tahun
2. normalisasi_data(df)            # StandardScaler normalisasi
3. proses_clustering(data_scaled)  # KMeans clustering
4. Mapping cluster → label         # Rendah/Sedang/Tinggi
5. render(dashboard.html, context) # Display di web
```

---

## 🔍 Contoh Output

### Dashboard menampilkan:
- ✅ **38 Kabupaten/Kota** tergolongkan dalam 3 cluster
- ✅ **Stat Cards**: Total diabetes, hipertensi, grand total, rata-rata
- ✅ **Peta Interaktif**: Warna berbeda per cluster (Hijau=Rendah, Merah=Sedang, Maroon=Tinggi)
- ✅ **Charts**: Distribusi cluster, trend diabetes/hipertensi per tahun
- ✅ **Tabel Lengkap**: Semua data dengan fitur filter & search

---

## 📝 Catatan Penting

1. **Data Normalisasi**: Menggunakan StandardScaler (Z-score), bukan Min-Max
2. **Clustering Method**: KMeans dengan random_state=42 untuk reproducibility
3. **Jumlah Cluster**: Fixed pada 3 (Rendah, Sedang, Tinggi)
4. **Label Assignment**: Berdasarkan mean nilai setiap cluster
5. **Multi-year Support**: Dashboard mendukung filter data per tahun

---

## 🤝 Kontribusi

Jika ada saran atau peningkatan:
1. Lakukan analisis terhadap kode
2. Buat perubahan
3. Dokumentasikan perubahan di README

---

## 📄 Lisensi

Data: BPS Jawa Timur & Open Data Jawa Timur  
Code: Internal Project 2026

---

## 📞 Kontak & Support

Untuk pertanyaan atau issue:
- Cek dokumentasi di project ini
- Lihat section "Alur Project" untuk pemahaman mendalam

---

**Last Updated**: Mei 2026  
**Status**: ✅ Aktif & Operational
