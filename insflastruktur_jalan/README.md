# 🛣️ Dashboard Klasifikasi Infrastruktur Jalan Jawa Timur

## 📋 Deskripsi Project

Sistem dashboard berbasis Django untuk klasifikasi dan analisis kondisi infrastruktur jalan di Jawa Timur. Dashboard ini mengolah data Excel yang berisi informasi tentang infrastruktur jalan per tahun (2021-2025) dan mengklasifikasikannya ke dalam tiga kategori:

- **Baik** (Skor ≥ 0.7) - Infrastruktur dalam kondisi excellent
- **Sedang** (Skor ≥ 0.4) - Infrastruktur dalam kondisi moderate
- **Buruk** (Skor < 0.4) - Infrastruktur memerlukan perbaikan

### Fitur Utama

✨ **Dashboard Modern**
- Interface yang elegan dengan tema grey dan baby blue
- Responsive design untuk desktop dan mobile
- Real-time data processing dan visualization

📊 **Visualisasi Data**
- Stat cards menampilkan ringkasan kategori
- Tabel data hasil klasifikasi per tahun
- Bar chart tren skor tahunan
- Dropdown untuk memilih mode tampilan

🔄 **Dual Mode Viewing**
- **Data Per Sheet**: Klasifikasi untuk setiap sheet Excel secara terpisah
- **Rata-Rata Semua Data**: Kombinasi dan rata-rata dari semua sheet

🔧 **Processing Otomatis**
- Min-max normalization untuk konsistensi data
- Multi-sheet averaging capability
- Automatic classification based on thresholds

---

## 🚀 Cara Setup dan Run Project

### Prerequisites
- Python 3.13 atau lebih baru
- pip (Python package manager)
- Windows/Mac/Linux

### Step 1: Clone/Akses Project


### Step 2: Setup Virtual Environment

**Windows PowerShell:**
```powershell
# Buat virtual environment (jika belum ada)
python -m venv knn_env

# Activate virtual environment
.\knn_env\Scripts\Activate.ps1

# Jika ada error permission, jalankan command ini terlebih dahulu:
# Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

**Mac/Linux:**
```bash
python3 -m venv knn_env
source knn_env/bin/activate
```

### Step 3: Install Dependencies

```bash
# Update pip
pip install --upgrade pip

# Install required packages
pip install django==6.0.4
pip install pandas
pip install openpyxl
```

Atau install dari requirements.txt jika ada:
```bash
pip install -r requirements.txt
```

### Step 4: Persiapkan Data Excel

1. Buat folder `data_excel` di root project (jika belum ada)
2. Letakkan file Excel dengan nama **`data.xlsx`** di folder `data_excel`

**Format Excel yang diharapkan:**
- Kolom A: Nama Kota/Kabupaten
- Kolom B-F: Tahun 2021-2025 (sebagai header kolom)
- Baris selanjutnya: Data numerik untuk setiap lokasi dan tahun
- Multiple sheets didukung (contoh: "jalan", "hujan", "kendaraan")

**Contoh struktur:**
```
| Kota/Kabupaten | 2021  | 2022  | 2023  | 2024  | 2025  |
|----------------|-------|-------|-------|-------|-------|
| Surabaya       | 75    | 78    | 81    | 85    | 88    |
| Malang         | 65    | 68    | 71    | 75    | 78    |
| Bandung        | 55    | 58    | 62    | 65    | 68    |
```

### Step 5: Database Migration

```bash
# Navigate ke project root
cd "c:\Users\Nadiyah Myrilla\OneDrive\Dokumen\cobaa\magang\insflastruktur_jalan"

# Run migrations
python manage.py migrate
```

### Step 6: Run Development Server

```bash
# Start Django development server
python manage.py runserver

# Server akan berjalan di http://127.0.0.1:8000/
```

Output yang diharapkan:
```
Starting development server at http://127.0.0.1:8000/
Quit the server with CTRL-BREAK.
```

### Step 7: Akses Dashboard

Buka browser dan navigasi ke:
```
http://127.0.0.1:8000/
```

---

## 📁 Struktur Project

```
insflastruktur_jalan/
├── manage.py                          # Django management script
├── db.sqlite3                         # Database SQLite
├── README.md                          # File ini
├── knn_env/                           # Virtual environment
├── data_excel/                        # Folder untuk data.xlsx
│   └── data.xlsx                      # File data Excel (input)
├── dashboard_kualifikasi/             # Main Django app
│   ├── migrations/                    # Database migrations
│   ├── templates/
│   │   └── dashboard_kualifikasi/
│   │       └── result.html            # Template dashboard
│   ├── static/
│   │   └── dashboard_kualifikasi/
│   │       └── styles.css             # Styling dashboard
│   ├── services/
│   │   └── processing.py              # Data processing logic
│   ├── views.py                       # Django views/controllers
│   ├── urls.py                        # URL routing
│   ├── models.py                      # Database models
│   ├── apps.py                        # App configuration
│   └── admin.py                       # Admin configuration
└── insflasutuktur_jalan/              # Django project settings
    ├── settings.py                    # Project settings
    ├── urls.py                        # Project URL routing
    ├── asgi.py                        # ASGI config
    └── wsgi.py                        # WSGI config
```

---

## 🔧 Troubleshooting

### Error: "Kolom tahun tidak ditemukan"
**Solusi:** Pastikan file Excel di `data_excel/data.xlsx` memiliki struktur yang benar dengan tahun (2021-2025) sebagai header kolom.

### Error: Module not found (Django, pandas, openpyxl)
**Solusi:** Pastikan virtual environment sudah di-activate dan semua dependencies sudah terinstall:
```bash
pip install django==6.0.4 pandas openpyxl
```

### Port 8000 sudah terpakai
**Solusi:** Gunakan port lain:
```bash
python manage.py runserver 8080
# Akses di http://127.0.0.1:8080/
```

### Database error
**Solusi:** Reset database dan jalankan migrations lagi:
```bash
del db.sqlite3
python manage.py migrate
```

---

## 📊 Fitur Klasifikasi

### Proses Normalisasi
Data diproses menggunakan **Min-Max Normalization**:
```
Nilai Normalisasi = (Nilai - Min) / (Max - Min)
Range Output: 0.0 - 1.0
```

### Threshold Klasifikasi
- **Baik**: Skor ≥ 0.7 (70%)
- **Sedang**: Skor ≥ 0.4 dan < 0.7 (40-70%)
- **Buruk**: Skor < 0.4 (< 40%)

### Mode Tampilan

**1. Data Per Sheet**
- Menampilkan klasifikasi untuk satu sheet Excel tertentu
- Menghitung rata-rata untuk setiap tahun dari semua lokasi
- Normalisasi dan klasifikasi dilakukan terpisah per sheet

**2. Rata-Rata Semua Data**
- Menggabungkan data dari semua sheet
- Menghitung rata-rata keseluruhan per tahun
- Normalisasi dilakukan pada data gabungan
- Memberikan perspektif holistik infrastruktur

---

## 💡 Tips Penggunaan

1. **Update Data**: Ganti isi `data_excel/data.xlsx` dengan data terbaru, kemudian refresh browser
2. **Multiple Sheets**: Dashboard otomatis mendeteksi semua sheet dalam file Excel
3. **Dropdown Menu**: Gunakan dropdown di header untuk beralih antara "Data Per Sheet" dan "Rata-Rata Semua Data"
4. **Sheet Tabs**: Klik tab sheet untuk melihat klasifikasi per sheet tertentu

---

## 🎨 Customization

### Mengubah Warna Theme
Edit file `dashboard_kualifikasi/static/dashboard_kualifikasi/styles.css`:

```css
:root {
  --accent-blue: #5ba3ff;      /* Warna biru utama */
  --accent-purple: #7c5cff;    /* Warna ungu aksen */
  --good: #3da9ff;             /* Warna untuk kategori Baik */
  --medium: #ffb347;           /* Warna untuk kategori Sedang */
  --bad: #ff6b6b;              /* Warna untuk kategori Buruk */
}
```

### Mengubah Threshold Klasifikasi
Edit file `dashboard_kualifikasi/services/processing.py`:

```python
# Di fungsi classify_by_year_columns() atau classify_all_sheets_average()
# Ubah nilai threshold:
if normalized_score >= 0.7:
    category = "Baik"
elif normalized_score >= 0.4:
    category = "Sedang"
else:
    category = "Buruk"
```

---

## 📞 Support & Kontribusi

Jika menemukan bug atau ingin menambah fitur, silakan:
1. Periksa file `processing.py` untuk logic klasifikasi
2. Periksa file `result.html` untuk tampilan dashboard
3. Periksa file `styles.css` untuk styling

---

## 📝 License

Project ini dibuat untuk keperluan magang/training.

---

**Last Updated:** May 5, 2026
**Version:** 1.0.0
