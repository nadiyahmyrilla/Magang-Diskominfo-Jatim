import json

from django.shortcuts import render

from analytics.preprocessing import load_raw_data, preprocess_timeseries, prepare_dataset
from analytics.knn_model import train_knn, compare_k, keputusan_kebijakan


def dashboard_view(request):
    """Single-page dashboard for environmental condition analysis."""

    # --- Load & preprocess ---
    df = load_raw_data("data load.xlsx")
    df = preprocess_timeseries(df)

    years = [2021, 2022, 2023, 2024, 2025]
    year_cols = [c for c in df.columns if c in years or str(c) in [str(y) for y in years]]

    # --- Prepare KNN dataset ---
    X, y = prepare_dataset(df)
    eval_result = train_knn(X, y)
    k_comparison = compare_k(X, y, k_values=range(1, 11))

    # --- Stats cards ---
    total_rows = len(df)
    total_cols = len(df.columns)
    missing_total = int(df.isnull().sum().sum())
    duplicates = int(df.duplicated().sum())
    accuracy_pct = round(eval_result["accuracy"] * 100, 2)

    # --- Label distribution ---
    label_counts = y.value_counts().reindex(["Rendah", "Sedang", "Tinggi"], fill_value=0)

    # --- Per-year totals for bar/area chart ---
    year_totals = []
    year_labels = []
    for yc in year_cols:
        year_labels.append(str(yc))
        year_totals.append(int(df[yc].sum()))

    # --- Per-year mean ---
    year_means = [round(float(df[yc].mean()), 4) for yc in year_cols]

    # --- Per-kabupaten data for table ---
    table_data = []
    for _, row in df.iterrows():
        name = row.get("nama_kabupaten_kota", "")
        vals = [int(row[c]) for c in year_cols]
        label = y.iloc[_] if _ < len(y) else ""
        kebijakan = keputusan_kebijakan(str(label))
        table_data.append({
            "name": name,
            "values": vals,
            "label": str(label),
            "kebijakan": kebijakan,
        })

    # --- Min / Max per year ---
    year_min = [int(df[yc].min()) for yc in year_cols]
    year_max = [int(df[yc].max()) for yc in year_cols]

    ctx = {
        "total_rows": total_rows,
        "total_cols": total_cols,
        "missing_total": missing_total,
        "duplicates": duplicates,
        "accuracy_pct": accuracy_pct,
        "label_names_json": json.dumps(["Rendah", "Sedang", "Tinggi"]),
        "label_counts_json": json.dumps(label_counts.tolist()),
        "year_labels_json": json.dumps(year_labels),
        "year_totals_json": json.dumps(year_totals),
        "year_means_json": json.dumps(year_means),
        "year_min_json": json.dumps(year_min),
        "year_max_json": json.dumps(year_max),
        "k_labels_json": json.dumps(list(k_comparison.keys())),
        "k_acc_json": json.dumps([round(v * 100, 2) for v in k_comparison.values()]),
        "table_data": table_data,
        "year_labels": year_labels,
    }

    return render(request, "dashboard/index.html", ctx)
