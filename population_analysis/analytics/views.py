import os
import pandas as pd
from django.shortcuts import render
from django.conf import settings

from analytics.knn_model import create_label, train_knn, predict_new_data, keputusan_kebijakan


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
        df[col] = (
            df[col]
            .astype(str)
            .str.replace(",", "", regex=False)
            .astype(float)
        )

    return df


# =====================================================
# DASHBOARD PAGE
# =====================================================
def dashboard(request):
    df = load_dataset()

    if df.empty:
        return render(request, "dashboard.html", {})

    year_cols = [col for col in df.columns if col.isdigit()]
    year_cols.sort()

    latest_year = year_cols[-1]
    selected_year = request.GET.get("year", latest_year)

    if selected_year not in year_cols:
        selected_year = latest_year

    df["score"] = df[year_cols].mean(axis=1)

    def create_label_dashboard(score):
        if score >= 100:
            return "Tinggi"
        elif score >= 98:
            return "Sedang"
        else:
            return "Rendah"

    df["kategori"] = df["score"].apply(create_label_dashboard)

    context = {
        "data": df.to_dict("records"),
        "years": year_cols,
        "selected_year": selected_year,
        "latest_year": latest_year,
    }

    return render(request, "dashboard.html", context)


# =====================================================
# EVALUATION PAGE
# =====================================================
def evaluation(request):
    df = load_dataset()

    if df.empty:
        return render(request, "evaluation.html", {})

    numeric_cols = df.select_dtypes(include="number").columns.tolist()

    if len(numeric_cols) < 7:
        return render(request, "evaluation.html", {
            "error": "Dataset tidak memiliki minimal 7 kolom numerik."
        })

    fitur_cols = numeric_cols[:7]
    df = df[["wilayah"] + fitur_cols]

    df.columns = ["wilayah", "GK", "PM", "TPT", "JP", "LPP", "KP", "RJK"]

    df = create_label(df)
    model, scaler, evaluasi = train_knn(df, k=5)

    accuracy = evaluasi["accuracy"]
    report = evaluasi["report"]

    precision = round(report["weighted avg"]["precision"] * 100, 2)
    recall = round(report["weighted avg"]["recall"] * 100, 2)

    prediction = None
    prediction_7 = None

    if request.method == "POST":

        # ===============================
        # FORM 1 (1 INPUT)
        # ===============================
        if "submit_single" in request.POST:
            try:
                value = float(request.POST.get("total_penduduk"))

                sample = {
                    "GK": value,
                    "PM": value,
                    "TPT": value,
                    "JP": value,
                    "LPP": value,
                    "KP": value,
                    "RJK": value,
                }

                label = predict_new_data(model, scaler, sample)
                prediction = keputusan_kebijakan(label)

            except:
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

                label = predict_new_data(model, scaler, data_baru)
                prediction_7 = keputusan_kebijakan(label)

            except:
                prediction_7 = "Semua indikator harus diisi dengan benar"

    context = {
        "accuracy": accuracy,
        "precision": precision,
        "recall": recall,
        "k_best": 5,
        "k_values": [5],
        "k_accuracies": [accuracy],
        "train_count": len(df),
        "test_count": 0,
        "total_count": len(df),
        "prediction": prediction,
        "prediction_7": prediction_7,
    }

    return render(request, "evaluation.html", context)