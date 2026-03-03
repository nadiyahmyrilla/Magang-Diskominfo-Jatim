import os
import pandas as pd
from pathlib import Path

# Base directory project
BASE_DIR = Path(__file__).resolve().parent.parent

# Path file Excel (pastikan nama file sesuai)
DATA_PATH = BASE_DIR / "analytics" / "data" / "data.xlsx"


def prepare_dataset(sheet_name=0):
    """
    Membaca dataset Excel, membersihkan data,
    mengambil kolom numerik, dan melakukan normalisasi Min-Max.
    """

    print("📂 Path File:", DATA_PATH)

    # Cek apakah file ada
    if not os.path.exists(DATA_PATH):
        print("❌ FILE TIDAK DITEMUKAN!")
        return pd.DataFrame(), pd.DataFrame()

    # Baca file Excel
    df = pd.read_excel(DATA_PATH, sheet_name=sheet_name)

    print("✅ File berhasil dibaca")
    print("Kolom:", df.columns)
    print("Jumlah data:", len(df))

    # Bersihkan nama kolom (hapus spasi depan/belakang)
    df.columns = df.columns.str.strip()

    # Hapus baris kosong
    df = df.dropna()

    # Ambil kolom numerik saja (untuk KNN)
    df_numeric = df.select_dtypes(include="number")

    if df_numeric.empty:
        print("⚠️ Tidak ada kolom numerik ditemukan!")
        return df, pd.DataFrame()

    # Normalisasi Min-Max
    df_norm = (df_numeric - df_numeric.min()) / (
        df_numeric.max() - df_numeric.min()
    )

    print("✅ Normalisasi selesai")
    print("Kolom numerik:", df_numeric.columns)

    return df, df_norm