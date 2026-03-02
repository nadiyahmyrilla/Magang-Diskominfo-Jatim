from typing import Iterable

import numpy as np
import pandas as pd
from sklearn.metrics import accuracy_score
from sklearn.model_selection import train_test_split
from sklearn.neighbors import KNeighborsClassifier
from sklearn.preprocessing import MinMaxScaler


def create_label(df: pd.DataFrame, year_columns: list) -> pd.DataFrame:
    """Create Rendah/Sedang/Tinggi label based on aggregate score across all year columns.

    If a single reference column has zero variance, falls back to the sum/mean
    of all supplied year columns so labels are meaningful.
    """
    df = df.copy()

    # Primary: use the last year column
    score = pd.to_numeric(df[year_columns[-1]], errors="coerce")

    # Fallback: if the primary column has no variance, use sum across all years
    if score.nunique() <= 1:
        score = df[year_columns].apply(pd.to_numeric, errors="coerce").sum(axis=1)

    # If still no variance, assign a single label
    if score.nunique() <= 1:
        df["label"] = "Sedang"
        return df

    bins = score.quantile([0.0, 1 / 3, 2 / 3, 1.0]).tolist()
    unique_bins = sorted(set(bins))

    if len(unique_bins) < 4:
        min_value = score.min()
        max_value = score.max()
        step = (max_value - min_value) / 3
        bins = [min_value, min_value + step, min_value + 2 * step, max_value]

    bins[0] = bins[0] - 1e-9
    bins[-1] = bins[-1] + 1e-9
    df["label"] = pd.cut(score, bins=bins, labels=["Rendah", "Sedang", "Tinggi"], include_lowest=True)
    df["label"] = df["label"].astype(str)
    return df


def train_knn(X, y, n_neighbors: int = 5, test_size: float = 0.3, random_state: int = 42) -> dict:
    scaler = MinMaxScaler()
    X_scaled = scaler.fit_transform(X)

    class_count = pd.Series(y).nunique()
    stratify_target = y if class_count > 1 else None

    X_train, X_test, y_train, y_test = train_test_split(
        X_scaled, y, test_size=test_size, random_state=random_state, stratify=stratify_target
    )

    model = KNeighborsClassifier(n_neighbors=n_neighbors)
    model.fit(X_train, y_train)
    y_pred = model.predict(X_test)

    return {
        "model": model,
        "scaler": scaler,
        "accuracy": accuracy_score(y_test, y_pred),
    }


def compare_k(X, y, k_values: Iterable[int] = range(1, 11)) -> dict:
    results = {}
    for k in k_values:
        out = train_knn(X, y, n_neighbors=k)
        results[int(k)] = out["accuracy"]
    return results


def predict_new_data(model, scaler, X_new):
    arr = scaler.transform(X_new)
    return model.predict(arr)


def keputusan_kebijakan(label: str) -> str:
    mapping = {
        "Rendah": "Pantau rutin",
        "Sedang": "Intervensi ringan",
        "Tinggi": "Tindakan cepat dan evaluasi lapangan",
    }
    return mapping.get(label, "Tidak ada rekomendasi")
