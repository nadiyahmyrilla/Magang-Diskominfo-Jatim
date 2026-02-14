import os
import pandas as pd
from django.conf import settings

FILE_PATH = os.path.join(
    settings.BASE_DIR,
    "analytics",
    "data",
    "Dataset_load.xlsx"
)

# =========================
# LOAD DATA
# =========================
def load_raw_data(sheet_name):
    df = pd.read_excel(FILE_PATH, sheet_name=sheet_name)

    # Paksa semua nama kolom menjadi string
    df.columns = df.columns.map(str)

    # Hapus kolom Unnamed jika ada
    df = df.loc[:, ~df.columns.str.contains("^Unnamed")]

    return df


# =========================
# PREPROCESS TIME SERIES
# =========================
def preprocess_timeseries(df):
    """
    Mengubah data wide (2020–2024) menjadi long format
    """
    id_col = df.columns[0]  # Kabupaten/Kota
    year_cols = df.columns[1:]  # kolom tahun

    df_long = df.melt(
        id_vars=id_col,
        value_vars=year_cols,
        var_name="Tahun",
        value_name="Nilai"
    )

    # Bersihkan angka (koma -> titik)
    df_long["Nilai"] = (
        df_long["Nilai"]
        .astype(str)
        .str.replace(",", ".", regex=False)
        .astype(float)
    )

    df_long["Tahun"] = df_long["Tahun"].astype(int)

    return df_long.dropna()


# =========================
# NORMALISASI
# =========================
def min_max_normalization(df):
    min_val = df["Nilai"].min()
    max_val = df["Nilai"].max()

    df["Nilai_Normalisasi"] = (df["Nilai"] - min_val) / (max_val - min_val)
    return df


# =========================
# PIPELINE UTAMA
# =========================
def prepare_dataset(sheet_name):
    df_raw = load_raw_data(sheet_name)
    df_clean = preprocess_timeseries(df_raw)
    df_norm = min_max_normalization(df_clean)

    return df_clean, df_norm
