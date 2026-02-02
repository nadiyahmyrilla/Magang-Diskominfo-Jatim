
# Neraca Ekonomi – Data Warehouse & Analisis Time Series

Proyek ini merupakan aplikasi analisis **neraca ekonomi** berbasis Python yang menerapkan konsep **data warehouse** dan **analisis time series** untuk mendukung pengambilan keputusan berbasis data.  
Aplikasi ini melakukan proses pemuatan data, pembersihan, analisis tren, visualisasi, serta pemberian rekomendasi otomatis terhadap indikator ekonomi.

---

## Fitur
- Load data dari database MySQL (data warehouse)
- Pembersihan dan standarisasi satuan data
- Transformasi data triwulan menjadi numerik
- Analisis statistik indikator ekonomi
- Analisis pertumbuhan dan tren tahunan
- Visualisasi tren time series
- Deteksi masalah dan rekomendasi keputusan

---

## Teknologi
- Python 3.x
- Pandas
- SQLAlchemy
- MySQL Connector
- Matplotlib

---

## Struktur Modul
```

analytics/
├── analytics.py        # Load data dari data warehouse
├── preprocessing.py   # Pembersihan dan transformasi data
├── analysis.py        # Analisis statistik dan tren
└── decision.py        # Deteksi masalah dan rekomendasi

````

---

## Cara Menjalankan Aplikasi

### 1. Membuat Database
Buat database MySQL dengan nama berikut:
```sql
CREATE DATABASE neraca_ekonomi;
````

> Script SQL dapat dijalankan dari file database yang tersedia di dalam proyek.

---

### 2. Install Dependency

Jalankan perintah berikut pada terminal:

```bash
pip install pandas sqlalchemy mysql-connector-python matplotlib
```

---

### 3. Menjalankan Aplikasi (First Run)

Masuk ke Python shell:

```bash
python manage.py shell
```

Kemudian jalankan perintah berikut secara berurutan:

```python
from analytics.analytics import load_warehouse_data
from analytics.preprocessing import add_triwulan_numeric, sort_time_series, clean_satuan
from analytics.analysis import count_satuan, statistik_indikator, filter_pertumbuhan, tren_tahunan, plot_tren
from analytics.decision import deteksi_masalah, beri_rekomendasi

# Load data
df = load_warehouse_data()

# Cleaning dan preprocessing
df = clean_satuan(df)
count_satuan(df)
df = add_triwulan_numeric(df)
df = sort_time_series(df)

# Analisis statistik
statistik_indikator(df)

# Analisis pertumbuhan
df_pertumbuhan = filter_pertumbuhan(df)
pertumbuhan_tahun = tren_tahunan(df_pertumbuhan)

# Filter data triwulan valid
df_pertumbuhan_q = df_pertumbuhan[df_pertumbuhan['triwulan_num'] != 0]
pertumbuhan_tahun = tren_tahunan(df_pertumbuhan_q)

# Visualisasi tren
plot_tren(pertumbuhan_tahun)

# Deteksi masalah dan rekomendasi
alert = deteksi_masalah(pertumbuhan_tahun)
pertumbuhan_tahun = beri_rekomendasi(pertumbuhan_tahun)
```

---

## Cek Hasil Analisis

### Status Keputusan per Indikator

```python
pertumbuhan_tahun[['tahun', 'indikator', 'nilai', 'status']]
```

### Indikator Bermasalah pada Tahun Tertentu

```python
pertumbuhan_tahun[pertumbuhan_tahun['tahun'] == 2025]
```

### Seluruh Indikator Bermasalah

```python
pertumbuhan_tahun[pertumbuhan_tahun['status'] == 'Perlu Intervensi']
```

### Rekap Keputusan per Tahun

```python
pertumbuhan_tahun.groupby(['tahun', 'status']).size().reset_index(name='jumlah')
```

### Rekap Keputusan per Indikator

```python
pertumbuhan_tahun.groupby(['indikator', 'status']).size().reset_index(name='jumlah')
```

---

## Aturan Penentuan Status

| Nilai Pertumbuhan | Status           |
| ----------------- | ---------------- |
| nilai < 0         | Perlu Intervensi |
| 0 ≤ nilai < 2     | Waspada          |
| nilai ≥ 2         | Stabil           |

---

## Catatan

* Pastikan konfigurasi koneksi database MySQL sudah sesuai.
* Grafik tren akan ditampilkan menggunakan Matplotlib.
* Proyek ini dapat digunakan untuk kebutuhan akademik, konversi magang, maupun analisis kebijakan berbasis data.

---

## Author

**Nadiyah Myrilla**
Data Analyst | Data Warehouse | Time Series Analysis

```

---

Kalau mau, aku bisa:
- 🔹 Buat **versi Inggris (professional journal / GitHub recruiter)**
- 🔹 Tambahkan **badge GitHub**, **requirements.txt**, atau **contoh output grafik**
- 🔹 Sesuaikan README agar **selaras dengan laporan konversi magang Diskominfo**

Tinggal bilang ya.
```


