import re
import pandas as pd
from .ambil_data import ambil_path_data


def baca_dataset(sheet_name=0):

    try:
        path = ambil_path_data()
        df = pd.read_excel(path, sheet_name=sheet_name)
    except FileNotFoundError as e:
        raise FileNotFoundError(f"Gagal membaca dataset: {e}")
    except Exception as e:
        raise RuntimeError(f"Error saat membaca file Excel: {e}")

    if isinstance(df, pd.DataFrame) and df.empty:
        raise ValueError("Dataset kosong, tidak ada data untuk diproses.")

    return df


def baca_semua_tahun():

    try:
        path = ambil_path_data()
        all_sheets = pd.read_excel(path, sheet_name=None)
    except FileNotFoundError as e:
        raise FileNotFoundError(f"Gagal membaca dataset: {e}")
    except Exception as e:
        raise RuntimeError(f"Error saat membaca file Excel: {e}")

    data_per_tahun = {}
    for sheet_name, df in all_sheets.items():
        match = re.search(r'(\d{4})', sheet_name)
        if match:
            tahun = match.group(1)
        else:
            tahun = sheet_name

        if df.empty:
            continue

        data_per_tahun[tahun] = df

    if not data_per_tahun:
        raise ValueError("Semua sheet kosong, tidak ada data untuk diproses.")

    return data_per_tahun
