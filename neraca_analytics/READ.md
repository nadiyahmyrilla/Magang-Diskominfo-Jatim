Neraca Ekonomi – Data Warehouse & Analisis Time Series

Proyek ini merupakan aplikasi analisis neraca ekonomi berbasis Python yang menerapkan konsep data warehouse dan analisis time series untuk mendukung pengambilan keputusan berbasis data.
Aplikasi ini melakukan proses pemuatan data, pembersihan, analisis tren, visualisasi, serta pemberian rekomendasi otomatis terhadap indikator ekonomi.

Fitur

Load data dari database MySQL (data warehouse)

Pembersihan dan standarisasi satuan data

Transformasi data triwulan menjadi numerik

Analisis statistik indikator ekonomi

Analisis pertumbuhan dan tren tahunan

Visualisasi tren time series

Deteksi masalah dan rekomendasi keputusan

Dashboard visual interaktif berbasis Django

Teknologi

Python 3.x

Pandas

SQLAlchemy

MySQL Connector

Matplotlib

Django

Chart.js

Struktur Modul
analytics/
├── analytics.py        # Load data dari data warehouse
├── preprocessing.py   # Pembersihan dan transformasi data
├── analysis.py        # Analisis statistik dan tren
└── decision.py        # Deteksi masalah dan rekomendasi
Cara Menjalankan Aplikasi
1. Membuat Database

Buat database MySQL dengan nama berikut:

CREATE DATABASE neraca_ekonomi;

Script SQL dapat dijalankan dari file database yang tersedia di dalam proyek.

2. Install Dependency

Jalankan perintah berikut pada terminal:

pip install pandas sqlalchemy mysql-connector-python matplotlib
pip install sqlalchemy pymysql pandas
3. Menjalankan Aplikasi (First Run)

Masuk ke Python shell:

python manage.py shell
python manage.py runserver

Kemudian jalankan perintah berikut secara berurutan:

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
Cek Hasil Analisis
Status Keputusan per Indikator
pertumbuhan_tahun[['tahun', 'indikator', 'nilai', 'status']]
Indikator Bermasalah pada Tahun Tertentu
pertumbuhan_tahun[pertumbuhan_tahun['tahun'] == 2025]
Seluruh Indikator Bermasalah
pertumbuhan_tahun[pertumbuhan_tahun['status'] == 'Perlu Intervensi']
Rekap Keputusan per Tahun
pertumbuhan_tahun.groupby(['tahun', 'status']).size().reset_index(name='jumlah')
Rekap Keputusan per Indikator
pertumbuhan_tahun.groupby(['indikator', 'status']).size().reset_index(name='jumlah')
Aturan Penentuan Status (Analisis Python)
Nilai Pertumbuhan	Status
nilai < 0	Perlu Intervensi
0 ≤ nilai < 2	Waspada
nilai ≥ 2	Stabil
📊 Integrasi Dashboard & Business Intelligence

Selain analisis berbasis Python shell, proyek ini juga terintegrasi dengan dashboard web berbasis Django untuk menampilkan hasil analisis secara visual dan interaktif.

Dashboard berfungsi sebagai lapisan Business Intelligence (BI) yang menampilkan ringkasan indikator ekonomi dalam bentuk grafik, KPI, dan tabel status.

Komponen Dashboard

Dashboard menampilkan beberapa komponen utama:

1. KPI – 4 Indikator Utama

Menampilkan 4 indikator dengan total nilai terbesar sebagai indikator ekonomi paling dominan.

2. Bar Chart – Perbandingan Indikator per Tahun

Memvisualisasikan distribusi nilai indikator ekonomi antar tahun.

3. Radar Chart – Stabilitas Indikator

Mengukur stabilitas indikator menggunakan standar deviasi (STDDEV).
Semakin kecil nilai → semakin stabil.

4. Line Chart – Tren Ekonomi Tahunan

Menampilkan total agregat neraca ekonomi per tahun untuk melihat arah pertumbuhan makro.

5. Tabel Status Indikator per Tahun

Menampilkan:

Tahun

Nama indikator

Nilai

Stabilitas (% pertumbuhan)

Status keputusan

🧮 Metode Perhitungan Stabilitas (Growth)

Stabilitas dihitung menggunakan pertumbuhan tahunan (Year‑on‑Year Growth):

Growth (%) =
(nilai_tahun_ini − nilai_tahun_sebelumnya)
-----------------------------------------
nilai_tahun_sebelumnya × 100

Query SQL menggunakan fungsi window:

LAG(nilai)
OVER (PARTITION BY indikator ORDER BY tahun)

Fungsi ini mengambil nilai tahun sebelumnya untuk indikator yang sama.

❗ Kenapa Tahun Pertama Bernilai NULL?

Pada tahun pertama setiap indikator, nilai pertumbuhan akan NULL.

Alasannya:

Tidak ada data pembanding dari tahun sebelumnya

Growth membutuhkan minimal 2 titik waktu

Secara matematis tidak bisa dihitung

Ilustrasi:

Tahun	Nilai	Growth
2021	8.7 jt	NULL
2022	9.4 jt	8.81%

Karena 2021 tidak punya data 2020, maka growth = NULL.

Penanganan di Dashboard

Nilai NULL tidak dianggap error, tetapi ditangani sebagai:

Ditampilkan “—” pada kolom stabilitas

Status otomatis menjadi Stabil sebagai baseline awal

Logika SQL:

WHEN pertumbuhan IS NULL THEN 'Stabil'

Pendekatan ini umum digunakan dalam analisis time series sebagai baseline year.

🚦 Aturan Status pada Dashboard

Berbasis growth tahunan:

Growth (%)	Status
-2 s/d 2	Stabil
-5 s/d 5	Waspada
< -5 atau > 5	Perlu Intervensi

Tujuan:

Mengukur fluktuasi

Mengidentifikasi indikator volatil

Memberi sinyal intervensi kebijakan

🏗️ Arsitektur Data

Alur integrasi sistem:

Data Source
   ↓
ETL / Warehouse (MySQL)
   ↓
Python Analytics Module
   ↓
Django Views (SQL Query)
   ↓
Dashboard Visualisasi
Struktur Warehouse

Fact Table

fact_neraca_ekonomi

Dimension Tables

dim_waktu

dim_indikator

dim_sumber_data

dim_jenis_analisis

Skema ini memungkinkan analisis multi dimensi dan agregasi cepat.

📈 Interpretasi Hasil Dashboard

Insight yang dapat diperoleh:

Indikator paling dominan → KPI

Indikator paling stabil → Radar chart

Tren makro ekonomi → Line chart

Indikator perlu intervensi → Tabel status

⚠️ Catatan Analisis Data

Tahun terakhir bisa turun ekstrem jika data belum full 1 tahun.

Growth negatif besar tidak selalu krisis.

Perlu analisis lanjutan forecasting.

🔮 Potensi Pengembangan

Forecasting (ARIMA / Prophet)

Filter interaktif dashboard

Heatmap stabilitas

Alert otomatis indikator drop

Export laporan PDF

Author

Nadiyah Myrilla
Data Analyst | Data Warehouse | Time Series Analysis