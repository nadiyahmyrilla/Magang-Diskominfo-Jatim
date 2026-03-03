import os
import pandas as pd
from django.shortcuts import render
from django.conf import settings

from analytics.knn_model import (
    create_label,
    train_knn,
    predict_new_data,
    keputusan_kebijakan,
    compare_k,
    FEATURE_COLUMNS,
)


# =====================================================
# LOAD DATASET
# =====================================================
def load_dataset():
    file_path = os.path.join(
        settings.BASE_DIR,
        "analytics",
        "data",
        "Dataset_load.xlsx"
    )

    if not os.path.exists(file_path):
        return pd.DataFrame()

    df = pd.read_excel(file_path)
    df.columns = df.columns.astype(str).str.strip()

    if "Kabupaten/Kota" in df.columns:
        df = df.rename(columns={"Kabupaten/Kota": "wilayah"})

    year_cols = [col for col in df.columns if col.isdigit()]
    year_cols.sort()

    for col in year_cols:
        df[col] = pd.to_numeric(df[col], errors="coerce")

    df = df.dropna(subset=["wilayah"]) if "wilayah" in df.columns else df

    return df


def _label_from_series(series):
    ranked = series.rank(method="first")
    labeled = pd.qcut(ranked, q=3, labels=["Rendah", "Sedang", "Tinggi"])
    return labeled.astype(str)


def _build_knn_training_data(df):
    if df.empty:
        return pd.DataFrame()

    data = df.copy()
    if "Kabupaten/Kota" in data.columns and "wilayah" not in data.columns:
        data = data.rename(columns={"Kabupaten/Kota": "wilayah"})

    if all(feature in data.columns for feature in FEATURE_COLUMNS):
        required = ["wilayah"] + FEATURE_COLUMNS if "wilayah" in data.columns else FEATURE_COLUMNS
        prepared = data[required].copy()
        for column in FEATURE_COLUMNS:
            prepared[column] = pd.to_numeric(prepared[column], errors="coerce")
        prepared = prepared.dropna(subset=FEATURE_COLUMNS)
        return prepared

    year_cols = [col for col in data.columns if str(col).isdigit()]
    year_cols = sorted(year_cols, reverse=True)
    if len(year_cols) < 5:
        return pd.DataFrame()

    latest, prev_1, prev_2, prev_3, prev_4 = year_cols[:5]

    prepared = pd.DataFrame()
    if "wilayah" in data.columns:
        prepared["wilayah"] = data["wilayah"]

    prepared["GK"] = pd.to_numeric(data[latest], errors="coerce")
    prepared["PM"] = pd.to_numeric(data[prev_1], errors="coerce")
    prepared["TPT"] = pd.to_numeric(data[prev_2], errors="coerce")
    prepared["JP"] = pd.to_numeric(data[latest], errors="coerce")
    prepared["LPP"] = (
        (pd.to_numeric(data[latest], errors="coerce") - pd.to_numeric(data[prev_1], errors="coerce"))
        / pd.to_numeric(data[prev_1], errors="coerce").replace(0, pd.NA)
    ) * 100
    prepared["KP"] = pd.to_numeric(data[prev_3], errors="coerce")
    prepared["RJK"] = (
        pd.to_numeric(data[latest], errors="coerce")
        / pd.to_numeric(data[prev_4], errors="coerce").replace(0, pd.NA)
    ) * 100

    prepared = prepared.dropna(subset=FEATURE_COLUMNS)
    return prepared


def _simulate_quick_input(input_value, feature_min, feature_max):
    normalized = max(0.0, min(100.0, input_value)) / 100.0
    simulated = {}
    for feature in FEATURE_COLUMNS:
        min_val = feature_min[feature]
        max_val = feature_max[feature]
        simulated[feature] = float(min_val + (max_val - min_val) * normalized)
    return simulated


def _to_float_dict(data_dict):
    return {feature: float(data_dict[feature]) for feature in FEATURE_COLUMNS}


EAST_JAVA_COORDINATES = {
    "pacitan": (-8.1949, 111.1053),
    "ponorogo": (-7.8650, 111.4660),
    "trenggalek": (-8.0500, 111.7160),
    "tulungagung": (-8.0660, 111.9060),
    "blitar": (-8.0950, 112.1600),
    "kediri": (-7.8160, 112.0120),
    "malang": (-7.9800, 112.6300),
    "lumajang": (-8.1330, 113.2240),
    "jember": (-8.1730, 113.7000),
    "banyuwangi": (-8.2190, 114.3690),
    "bondowoso": (-7.9130, 113.8200),
    "situbondo": (-7.7060, 114.0090),
    "probolinggo": (-7.7540, 113.2150),
    "pasuruan": (-7.6450, 112.9070),
    "sidoarjo": (-7.4470, 112.7180),
    "mojokerto": (-7.5590, 112.4340),
    "jombang": (-7.5450, 112.2330),
    "nganjuk": (-7.6020, 111.9030),
    "madiun": (-7.6290, 111.5230),
    "magetan": (-7.6530, 111.3310),
    "ngawi": (-7.4020, 111.4450),
    "bojonegoro": (-7.1500, 111.8800),
    "tuban": (-6.8970, 112.0640),
    "lamongan": (-7.1160, 112.4170),
    "gresik": (-7.1530, 112.6560),
    "bangkalan": (-7.0450, 112.9330),
    "sampang": (-7.1870, 113.2500),
    "pamekasan": (-7.1580, 113.4740),
    "sumenep": (-7.0040, 113.8600),
    "kota kediri": (-7.8160, 112.0110),
    "kota blitar": (-8.0950, 112.1680),
    "kota malang": (-7.9830, 112.6210),
    "kota probolinggo": (-7.7540, 113.2170),
    "kota pasuruan": (-7.6450, 112.9070),
    "kota mojokerto": (-7.4720, 112.4330),
    "kota madiun": (-7.6290, 111.5230),
    "kota surabaya": (-7.2570, 112.7520),
    "kota batu": (-7.8710, 112.5280),
    "jawa timur": (-7.7500, 112.5000),
}


def _normalize_region_name(name, remove_prefix=False):
    if not isinstance(name, str):
        return ""
    normalized = " ".join(name.strip().lower().split())
    if remove_prefix:
        normalized = normalized.replace("kabupaten ", "")
        normalized = normalized.replace("kota ", "")
    return normalized


def _get_region_coordinate(region_name, index):
    region_exact = _normalize_region_name(region_name, remove_prefix=False)
    region_no_prefix = _normalize_region_name(region_name, remove_prefix=True)

    if region_exact in EAST_JAVA_COORDINATES:
        return EAST_JAVA_COORDINATES[region_exact]

    if region_no_prefix in EAST_JAVA_COORDINATES:
        return EAST_JAVA_COORDINATES[region_no_prefix]

    return (-8.2 + ((index % 8) * 0.22), 111.0 + ((index // 8) * 0.35))



# =====================================================
# DASHBOARD PAGE
# =====================================================
def dashboard(request):
    df = load_dataset()

    if df.empty:
        return render(request, "dashboard.html", {
            "data": [],
            "years": [],
            "selected_year": None,
            "latest_year": None,
            "rata2": 0,
            "tertinggi": 0,
            "terendah": 0,
            "yearly_avg": [],
            "map_data": [],
        })

    year_cols = [col for col in df.columns if col.isdigit()]
    year_cols.sort()

    latest_year = year_cols[-1]
    selected_year = request.GET.get("year", latest_year)

    if selected_year not in year_cols:
        selected_year = latest_year

    selected_values = pd.to_numeric(df[selected_year], errors="coerce")
    safe_values = selected_values.fillna(selected_values.mean())

    df_display = df.copy()
    df_display["kategori"] = _label_from_series(safe_values)
    df_display["selected_value"] = selected_values

    yearly_avg = [round(float(pd.to_numeric(df[col], errors="coerce").mean()), 2) for col in year_cols]

    map_data = []
    map_lookup = {}
    for index, row in df_display.iterrows():
        wilayah_name = row.get("wilayah", "-")
        row_value = round(float(row.get("selected_value", 0) or 0), 2)
        row_kategori = row.get("kategori", "Sedang")
        lat, lng = _get_region_coordinate(wilayah_name, index)

        map_data.append({
            "wilayah": wilayah_name,
            "value": row_value,
            "kategori": row_kategori,
            "lat": lat,
            "lng": lng,
        })

        region_exact = _normalize_region_name(wilayah_name, remove_prefix=False)
        region_no_prefix = _normalize_region_name(wilayah_name, remove_prefix=True)

        map_lookup[region_exact] = {
            "wilayah": wilayah_name,
            "value": row_value,
            "kategori": row_kategori,
        }

        if region_no_prefix not in map_lookup:
            map_lookup[region_no_prefix] = {
                "wilayah": wilayah_name,
                "value": row_value,
                "kategori": row_kategori,
            }

    rata2 = round(float(safe_values.mean()), 2)
    tertinggi = round(float(safe_values.max()), 2)
    terendah = round(float(safe_values.min()), 2)

    context = {
        "data": df_display.to_dict("records"),
        "years": year_cols,
        "selected_year": selected_year,
        "latest_year": latest_year,
        "rata2": rata2,
        "tertinggi": tertinggi,
        "terendah": terendah,
        "yearly_avg": yearly_avg,
        "map_data": map_data,
        "map_lookup": map_lookup,
    }

    return render(request, "dashboard.html", context)


# =====================================================
# EVALUATION PAGE
# =====================================================
def evaluation(request):
    raw_df = load_dataset()

    if raw_df.empty:
        return render(request, "evaluation.html", {
            "accuracy": 0,
            "precision": 0,
            "recall": 0,
            "k_best": 0,
            "k_values": [],
            "k_accuracies": [],
            "train_count": 0,
            "test_count": 0,
            "total_count": 0,
            "prediction": None,
            "prediction_7": None,
            "error": "Dataset tidak ditemukan atau kosong.",
        })

    training_df = _build_knn_training_data(raw_df)
    if training_df.empty:
        return render(request, "evaluation.html", {
            "accuracy": 0,
            "precision": 0,
            "recall": 0,
            "k_best": 0,
            "k_values": [],
            "k_accuracies": [],
            "train_count": 0,
            "test_count": 0,
            "total_count": 0,
            "prediction": None,
            "prediction_7": None,
            "error": "Data training belum cukup untuk 7 indikator.",
        })

    labeled_df = create_label(training_df)

    k_result_df = compare_k(labeled_df)
    k_best = int(k_result_df.sort_values("accuracy", ascending=False).iloc[0]["k"])

    model, scaler, evaluasi = train_knn(labeled_df, k=k_best)

    accuracy = evaluasi["accuracy"]
    report = evaluasi["classification_report"]

    precision = round(float(report["weighted avg"]["precision"] * 100), 2)
    recall = round(float(report["weighted avg"]["recall"] * 100), 2)

    feature_min = labeled_df[FEATURE_COLUMNS].min()
    feature_max = labeled_df[FEATURE_COLUMNS].max()

    prediction = None
    prediction_7 = None

    if request.method == "POST":

        # ===============================
        # FORM 1 (1 INPUT)
        # ===============================
        if "submit_single" in request.POST:
            try:
                value = float(request.POST.get("total_penduduk", 0))
                sample = _simulate_quick_input(value, feature_min, feature_max)

                label = predict_new_data(model, scaler, sample)
                prediction = keputusan_kebijakan(label)

            except Exception:
                prediction = "Input tidak valid"

        # ===============================
        # FORM 7 INDIKATOR
        # ===============================
        if "submit_7" in request.POST:
            try:
                data_baru = {
                    "GK": float(request.POST.get("gk")),
                    "PM": float(request.POST.get("pm")),
                    "TPT": float(request.POST.get("tpt")),
                    "JP": float(request.POST.get("jp")),
                    "LPP": float(request.POST.get("lpp")),
                    "KP": float(request.POST.get("kp")),
                    "RJK": float(request.POST.get("rjk")),
                }

                label = predict_new_data(model, scaler, _to_float_dict(data_baru))
                prediction_7 = keputusan_kebijakan(label)

            except Exception:
                prediction_7 = "Semua indikator harus diisi dengan benar"

    context = {
        "accuracy": accuracy,
        "precision": precision,
        "recall": recall,
        "k_best": k_best,
        "k_values": k_result_df["k"].astype(int).tolist(),
        "k_accuracies": k_result_df["accuracy"].round(2).tolist(),
        "train_count": evaluasi["train_count"],
        "test_count": evaluasi["test_count"],
        "total_count": evaluasi["total_count"],
        "prediction": prediction,
        "prediction_7": prediction_7,
        "error": None,
    }

    return render(request, "evaluation.html", context)