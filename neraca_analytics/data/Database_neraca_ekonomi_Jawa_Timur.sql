-- MEMBUAT DATABASE NERACA EKONOMI --
-- Dimana database ini berfungsi untuk memprediksikan perhitungan keputusan berdasrkan data akurat dari 5 tahun terakhir --
CREATE DATABASE neraca_ekonomi;

--                 Mmembuat Tabel Dimensi                --
-- Tabel dimensi waktu --
CREATE TABLE dim_waktu (
    id_waktu INT AUTO_INCREMENT PRIMARY KEY,
    tahun INT NOT NULL,
    triwulan VARCHAR(150) NOT NULL
);
-- Tabel dimensi luaran --
CREATE TABLE dim_sumber_data (
    id_sumber_data INT AUTO_INCREMENT PRIMARY KEY,
    nama_sumber_data VARCHAR(150) NOT NULL,
    satuan VARCHAR(50)
);
-- Tabel dimensi indikator --
CREATE TABLE dim_indikator (
    id_indikator INT AUTO_INCREMENT PRIMARY KEY,
    nama_indikator VARCHAR(150) NOT NULL
);
-- Tabel dimensi jenis analsiis --
CREATE TABLE dim_jenis_analisis (
    id_jenis_analisis INT AUTO_INCREMENT PRIMARY KEY,
    nama_jenis_analisis VARCHAR(100) NOT NULL
);
-- ------------------------------------------------------ --

--                 Mmembuat Tabel Fakta                   --
-- Tabel fakta neraca ekonomi --
CREATE TABLE fact_neraca_ekonomi (
    id_fakta BIGINT AUTO_INCREMENT PRIMARY KEY,
    id_waktu INT NOT NULL,
    id_sumber_data INT NOT NULL,
    id_indikator INT NOT NULL,
    id_jenis_analisis INT NOT NULL,
    nilai DECIMAL(15,4) NOT NULL,

    FOREIGN KEY (id_waktu) REFERENCES dim_waktu(id_waktu),
    FOREIGN KEY (id_sumber_data) REFERENCES dim_sumber_data(id_sumber_data),
    FOREIGN KEY (id_indikator) REFERENCES dim_indikator(id_indikator),
    FOREIGN KEY (id_jenis_analisis) REFERENCES dim_jenis_analisis(id_jenis_analisis)
);
-- Tbel string fakta neraca ekonomi --
CREATE TABLE fact_neraca_ekonomi_staging (
    tahun INT NOT NULL,
    triwulan VARCHAR(150) NOT NULL,
    nama_sumber_data VARCHAR(150) NOT NULL,
    nama_indikator VARCHAR(150) NOT NULL,
    jenis_analisis VARCHAR(100) NOT NULL,
    nilai DECIMAL(15,4) NOT NULL
);
-- ------------------------------------------------------ --

--               Mengisi data Tabel Fakta                 --
INSERT INTO fact_neraca_ekonomi (
    id_waktu,
    id_sumber_data,
    id_indikator,
    id_jenis_analisis,
    nilai
)
SELECT
    w.id_waktu,
    sd.id_sumber_data,
    i.id_indikator,
    ja.id_jenis_analisis,
    s.nilai
FROM fact_neraca_ekonomi_staging s
JOIN dim_waktu w
    ON s.tahun = w.tahun
   AND TRIM(UPPER(s.triwulan)) = TRIM(UPPER(w.triwulan))
JOIN dim_sumber_data sd
    ON TRIM(UPPER(s.nama_sumber_data)) = TRIM(UPPER(sd.nama_sumber_data))
JOIN dim_indikator i
    ON TRIM(UPPER(s.nama_indikator)) = TRIM(UPPER(i.nama_indikator))
JOIN dim_jenis_analisis ja
    ON TRIM(UPPER(s.jenis_analisis)) = TRIM(UPPER(ja.nama_jenis_analisis));

-- ------------------------------------------------------ --

--     cara cek data sudah benar atau belum ---
SELECT DISTINCT tahun, triwulan
FROM fact_neraca_ekonomi_staging;

SELECT tahun, triwulan
FROM dim_waktu;

SELECT DISTINCT nama_sumber_data
FROM fact_neraca_ekonomi_staging
WHERE nama_sumber_data NOT IN (
    SELECT nama_sumber_data FROM dim_sumber_data
);

SELECT DISTINCT jenis_analisis
FROM fact_neraca_ekonomi_staging
WHERE jenis_analisis NOT IN (
    SELECT nama_jenis_analisis FROM dim_jenis_analisis
);

SELECT DISTINCT triwulan FROM fact_neraca_ekonomi_staging;
SELECT DISTINCT triwulan FROM dim_waktu;

SELECT DISTINCT nama_indikator
FROM fact_neraca_ekonomi_staging
WHERE TRIM(UPPER(nama_indikator)) NOT IN (
    SELECT TRIM(UPPER(nama_indikator)) FROM dim_indikator
);

SELECT DISTINCT jenis_analisis
FROM fact_neraca_ekonomi_staging
WHERE TRIM(UPPER(jenis_analisis)) NOT IN (
    SELECT TRIM(UPPER(nama_jenis_analisis)) FROM dim_jenis_analisis
);

SHOW FULL COLUMNS FROM dim_sumber_data; -- cek conculation yang berbeda --
SHOW FULL COLUMNS FROM fact_neraca_ekonomi_staging;

-- mengatasi conculation yang berbeda --
ALTER TABLE dim_sumber_data
MODIFY nama_sumber_data VARCHAR(150)
COLLATE utf8mb4_general_ci;

ALTER TABLE fact_neraca_ekonomi_staging
MODIFY nama_sumber_data VARCHAR(150)
COLLATE utf8mb4_general_ci;


SELECT COUNT(*) AS total_baris
FROM (
    SELECT
        w.id_waktu,
        sd.id_sumber_data,
        i.id_indikator,
        ja.id_jenis_analisis,
        s.nilai
    FROM fact_neraca_ekonomi_staging s
    JOIN dim_waktu w
        ON s.tahun = w.tahun
       AND TRIM(UPPER(s.triwulan)) = TRIM(UPPER(w.triwulan))
    JOIN dim_sumber_data sd
        ON TRIM(UPPER(s.nama_sumber_data)) = TRIM(UPPER(sd.nama_sumber_data))
    JOIN dim_indikator i
        ON TRIM(UPPER(s.nama_indikator)) = TRIM(UPPER(i.nama_indikator))
    JOIN dim_jenis_analisis ja
        ON TRIM(UPPER(s.jenis_analisis)) = TRIM(UPPER(ja.nama_jenis_analisis))
) t;

SELECT
    SUM(w.id_waktu IS NULL) AS waktu_null,
    SUM(sd.id_sumber_data IS NULL) AS sumber_null,
    SUM(i.id_indikator IS NULL) AS indikator_null,
    SUM(ja.id_jenis_analisis IS NULL) AS jenis_null
FROM fact_neraca_ekonomi_staging s
LEFT JOIN dim_waktu w
    ON s.tahun = w.tahun
   AND TRIM(UPPER(s.triwulan)) = TRIM(UPPER(w.triwulan))
LEFT JOIN dim_sumber_data sd
    ON TRIM(UPPER(s.nama_sumber_data)) = TRIM(UPPER(sd.nama_sumber_data))
LEFT JOIN dim_indikator i
    ON TRIM(UPPER(s.nama_indikator)) = TRIM(UPPER(i.nama_indikator))
LEFT JOIN dim_jenis_analisis ja
    ON TRIM(UPPER(s.jenis_analisis)) = TRIM(UPPER(ja.nama_jenis_analisis));

SELECT @@sql_mode; -- cek apakah sql menolak insert secara diam - diam 

SELECT
    w.id_waktu,
    sd.id_sumber_data,
    i.id_indikator,
    ja.id_jenis_analisis,
    s.nilai
FROM fact_neraca_ekonomi_staging s
JOIN dim_waktu w
    ON s.tahun = w.tahun
   AND TRIM(UPPER(s.triwulan)) = TRIM(UPPER(w.triwulan))
JOIN dim_sumber_data sd
    ON TRIM(UPPER(s.nama_sumber_data)) = TRIM(UPPER(sd.nama_sumber_data))
JOIN dim_indikator i
    ON TRIM(UPPER(s.nama_indikator)) = TRIM(UPPER(i.nama_indikator))
JOIN dim_jenis_analisis ja
    ON TRIM(UPPER(s.jenis_analisis)) = TRIM(UPPER(ja.nama_jenis_analisis))
LIMIT 1;

UPDATE fact_neraca_ekonomi_staging
SET triwulan = REPLACE(triwulan, CHAR(160), '');

UPDATE fact_neraca_ekonomi_staging
SET triwulan = TRIM(triwulan);



-- ------------------------------------------------------ --


-- Menambah dan jenis analisis indikator yang kurang dan melakukan penghapusan indikator yg redundan --
INSERT INTO dim_indikator (nama_indikator)
SELECT DISTINCT nama_indikator
FROM fact_neraca_ekonomi_staging
WHERE TRIM(UPPER(nama_indikator)) NOT IN (
    SELECT TRIM(UPPER(nama_indikator)) FROM dim_indikator
);

INSERT INTO dim_jenis_analisis (nama_jenis_analisis)
SELECT DISTINCT jenis_analisis
FROM fact_neraca_ekonomi_staging
WHERE TRIM(UPPER(jenis_analisis)) NOT IN (
    SELECT TRIM(UPPER(nama_jenis_analisis)) FROM dim_jenis_analisis
);
-- ------------------------------------------------------ --


