from pathlib import Path
from typing import Iterable

import pandas as pd
from sklearn.preprocessing import MinMaxScaler


DEFAULT_YEARS = [2021, 2022, 2023, 2024, 2025]


def load_raw_data(file_name: str = "data load.xlsx") -> pd.DataFrame:
    root = Path(__file__).resolve().parent.parent
    path = root / "data" / file_name
    if not path.exists():
        raise FileNotFoundError(f"File tidak ditemukan: {path}")
    return pd.read_excel(path)


def preprocess_timeseries(df: pd.DataFrame, years: Iterable[int] | None = None) -> pd.DataFrame:
    years = years or DEFAULT_YEARS
    df = df.copy()
    for y in years:
        if y in df.columns:
            df[y] = pd.to_numeric(df[y], errors="coerce").fillna(0)
        elif str(y) in df.columns:
            df[str(y)] = pd.to_numeric(df[str(y)], errors="coerce").fillna(0)
        else:
            raise ValueError(f"Kolom tahun {y} tidak ditemukan di data")
    return df


def min_max_normalization(X):
    scaler = MinMaxScaler()
    X_scaled = scaler.fit_transform(X)
    return scaler, X_scaled


def prepare_dataset(df: pd.DataFrame, years: Iterable[int] | None = None):
    years = years or DEFAULT_YEARS
    cols = []
    for y in years:
        cols.append(y if y in df.columns else str(y))
    from .knn_model import create_label

    labeled = create_label(df, cols)  # pass all year columns for aggregate scoring
    X = labeled[cols]
    y = labeled["label"]
    return X, y
