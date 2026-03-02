from pathlib import Path

import pandas as pd


DEFAULT_YEARS = [2021, 2022, 2023, 2024, 2025]


def load_data(file_name: str = "data load.xlsx") -> pd.DataFrame:
    base_dir = Path(__file__).resolve().parent
    file_path = base_dir.parent / "data" / file_name

    if not file_path.exists():
        raise FileNotFoundError(f"File tidak ditemukan: {file_path}")

    return pd.read_excel(file_path)


def data_summary(df: pd.DataFrame) -> dict:
    return {
        "head": df.head(),
        "describe": df.describe(include="all"),
        "missing_values": df.isnull().sum(),
    }


def get_year_columns(df: pd.DataFrame, years: list[int] | None = None) -> list[str | int]:
    years = years or DEFAULT_YEARS

    year_columns: list[str | int] = []
    missing_columns: list[int] = []

    for year in years:
        if year in df.columns:
            year_columns.append(year)
        elif str(year) in df.columns:
            year_columns.append(str(year))
        else:
            missing_columns.append(year)

    if missing_columns:
        raise ValueError(f"Kolom berikut wajib ada di data: {missing_columns}")

    return year_columns


def create_label(df: pd.DataFrame, reference_column: str | int) -> pd.DataFrame:
    labeled_df = df.copy()
    score = pd.to_numeric(labeled_df[reference_column], errors="coerce")

    bins = score.quantile([0.0, 1 / 3, 2 / 3, 1.0]).to_list()
    unique_bins = sorted(set(bins))

    if len(unique_bins) < 4:
        score = labeled_df.select_dtypes(include="number").mean(axis=1)
        bins = score.quantile([0.0, 1 / 3, 2 / 3, 1.0]).to_list()
        unique_bins = sorted(set(bins))

    if len(unique_bins) < 4:
        min_value = score.min()
        max_value = score.max()
        if pd.isna(min_value) or pd.isna(max_value):
            raise ValueError("Data numerik tidak valid untuk pembentukan label.")
        if min_value == max_value:
            labeled_df["label"] = "Sedang"
            return labeled_df
        step = (max_value - min_value) / 3
        bins = [min_value, min_value + step, min_value + 2 * step, max_value]

    bins[0] = bins[0] - 1e-9
    bins[-1] = bins[-1] + 1e-9
    labeled_df["label"] = pd.cut(
        score,
        bins=bins,
        labels=["Rendah", "Sedang", "Tinggi"],
        include_lowest=True,
    )
    labeled_df["label"] = labeled_df["label"].astype(str)
    return labeled_df


def prepare_knn_dataset(df: pd.DataFrame, years: list[int] | None = None) -> tuple[pd.DataFrame, pd.Series]:
    year_columns = get_year_columns(df, years)
    labeled_df = create_label(df, year_columns[-1])

    X = labeled_df[year_columns]
    y = labeled_df["label"]
    return X, y


def label_distribution(y: pd.Series) -> pd.Series:
    ordered_labels = ["Rendah", "Sedang", "Tinggi"]
    return y.value_counts().reindex(ordered_labels, fill_value=0)
