from __future__ import annotations

from dataclasses import dataclass
from pathlib import Path
from typing import List, Optional

import pandas as pd


@dataclass
class ClassificationResult:
    sheet_name: str
    rows: List[List[str]]
    classes: List[str]
    summary: dict[str, int]
    good_count: int
    medium_count: int
    bad_count: int


def load_excel(file_path: Path, sheet_name: Optional[str] = None) -> pd.DataFrame:
    return pd.read_excel(file_path, sheet_name=sheet_name)


def get_excel_sheets(file_path: Path) -> List[str]:
    xls = pd.ExcelFile(file_path)
    return xls.sheet_names


def normalize_values(values: List[float]) -> List[float]:
    if not values:
        return []
    min_val = min(values)
    max_val = max(values)
    if max_val == min_val:
        return [0.0] * len(values)
    return [(v - min_val) / (max_val - min_val) for v in values]


def classify_by_year_columns(df: pd.DataFrame, id_col: str = "Kota/Kabupaten") -> ClassificationResult:
    """
    Process Excel where years are column names (2021, 2022, etc).
    Returns classification result with one row per year (average across all rows).
    """
    if id_col not in df.columns:
        id_col = df.columns[0]

    year_cols = []
    for col in df.columns:
        try:
            int(col)
            year_cols.append(col)
        except (ValueError, TypeError):
            pass

    if not year_cols:
        raise ValueError("No year columns found (expected numeric column names like 2021, 2022, etc).")

    year_cols_sorted = sorted(year_cols)
    year_means = []
    for year in year_cols_sorted:
        mean_val = pd.to_numeric(df[year], errors="coerce").mean()
        year_means.append(mean_val if pd.notna(mean_val) else 0.0)

    normalized = normalize_values(year_means)

    def label(score: float) -> str:
        if score >= 0.7:
            return "Baik"
        if score >= 0.4:
            return "Sedang"
        return "Buruk"

    rows = []
    summary = {"Baik": 0, "Sedang": 0, "Buruk": 0}
    good_count = medium_count = bad_count = 0

    for year_str, score, normalized_score in zip(year_cols_sorted, year_means, normalized):
        category = label(normalized_score)
        summary[category] += 1
        percent_value = int(normalized_score * 100)
        rows.append([str(year_str), f"{score:.2f}", category, str(percent_value)])
        if category == "Baik":
            good_count += 1
        elif category == "Sedang":
            medium_count += 1
        else:
            bad_count += 1

    return ClassificationResult(
        sheet_name="Sheet",
        rows=rows,
        classes=["Baik", "Sedang", "Buruk"],
        summary=summary,
        good_count=good_count,
        medium_count=medium_count,
        bad_count=bad_count,
    )


def classify_all_sheets_average(file_path: Path, sheets: List[str]) -> ClassificationResult:
    """
    Calculate average scores across all sheets for each year.
    """
    all_year_cols = set()
    sheet_data = {}

    for sheet in sheets:
        try:
            df = load_excel(file_path, sheet_name=sheet)
            year_cols = []
            for col in df.columns:
                try:
                    int(col)
                    year_cols.append(col)
                except (ValueError, TypeError):
                    pass
            
            if year_cols:
                year_cols_sorted = sorted(year_cols)
                year_means = []
                for year in year_cols_sorted:
                    mean_val = pd.to_numeric(df[year], errors="coerce").mean()
                    year_means.append(mean_val if pd.notna(mean_val) else 0.0)
                
                sheet_data[sheet] = dict(zip(year_cols_sorted, year_means))
                all_year_cols.update(year_cols_sorted)
        except Exception:
            pass

    all_year_cols = sorted(list(all_year_cols))

    if not all_year_cols:
        raise ValueError("No year columns found in any sheet.")

    combined_means = []
    for year in all_year_cols:
        values = [sheet_data[sheet].get(year, 0) for sheet in sheets if sheet in sheet_data]
        avg_value = sum(values) / len(values) if values else 0.0
        combined_means.append(avg_value)

    normalized = normalize_values(combined_means)

    def label(score: float) -> str:
        if score >= 0.7:
            return "Baik"
        if score >= 0.4:
            return "Sedang"
        return "Buruk"

    rows = []
    summary = {"Baik": 0, "Sedang": 0, "Buruk": 0}
    good_count = medium_count = bad_count = 0

    for year_str, score, normalized_score in zip(all_year_cols, combined_means, normalized):
        category = label(normalized_score)
        summary[category] += 1
        percent_value = int(normalized_score * 100)
        rows.append([str(year_str), f"{score:.2f}", category, str(percent_value)])
        if category == "Baik":
            good_count += 1
        elif category == "Sedang":
            medium_count += 1
        else:
            bad_count += 1

    return ClassificationResult(
        sheet_name="Rata-Rata Semua Data",
        rows=rows,
        classes=["Baik", "Sedang", "Buruk"],
        summary=summary,
        good_count=good_count,
        medium_count=medium_count,
        bad_count=bad_count,
    )
