# analytics/knn_model.py
import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.neighbors import KNeighborsClassifier
from sklearn.metrics import accuracy_score, classification_report, confusion_matrix
from sklearn.preprocessing import StandardScaler


FEATURE_COLUMNS = ["GK", "PM", "TPT", "JP", "LPP", "KP", "RJK"]


def _safe_qcut_labels(series, q=3):
    ranked = series.rank(method="first")
    categories = pd.qcut(ranked, q=q, labels=["Rendah", "Sedang", "Tinggi"])
    return categories


def _get_feature_columns(df):
    return [column for column in FEATURE_COLUMNS if column in df.columns]


# =====================================================
# BUAT LABEL OTOMATIS (UNTUK 7 INDIKATOR)
# =====================================================
def create_label(df):
    data = df.copy()
    numeric_cols = _get_feature_columns(data)
    if not numeric_cols:
        numeric_cols = data.select_dtypes(include="number").columns.tolist()

    data["score"] = data[numeric_cols].mean(axis=1)
    data["label"] = _safe_qcut_labels(data["score"], q=3)
    data["label"] = data["label"].astype(str)
    return data


# =====================================================
# TRAIN KNN (UNTUK 7 INDIKATOR)
# =====================================================
def train_knn(df, k=5, random_state=42):

    data = df.copy()
    if "label" not in data.columns:
        data = create_label(data)

    fitur_cols = _get_feature_columns(data)
    if len(fitur_cols) != len(FEATURE_COLUMNS):
        raise ValueError("Kolom fitur KNN belum lengkap (butuh GK, PM, TPT, JP, LPP, KP, RJK).")

    data = data.dropna(subset=fitur_cols + ["label"]).copy()

    X = data[fitur_cols]
    y = data["label"].astype(str)

    if len(data) < 10:
        raise ValueError("Data training terlalu sedikit untuk evaluasi KNN (minimal 10 baris).")

    label_counts = y.value_counts()
    can_stratify = (label_counts.min() >= 2) and (label_counts.shape[0] >= 2)
    stratify_target = y if can_stratify else None

    X_train, X_test, y_train, y_test = train_test_split(
        X,
        y,
        test_size=0.2,
        random_state=random_state,
        stratify=stratify_target,
    )

    if X_train.empty or X_test.empty:
        raise ValueError("Gagal membagi data training/testing. Cek distribusi data.")

    effective_k = max(1, min(int(k), len(X_train)))

    scaler = StandardScaler()
    X_train_scaled = scaler.fit_transform(X_train)
    X_test_scaled = scaler.transform(X_test)

    model = KNeighborsClassifier(n_neighbors=effective_k)
    model.fit(X_train_scaled, y_train)

    y_pred = model.predict(X_test_scaled)

    accuracy = accuracy_score(y_test, y_pred)
    report = classification_report(y_test, y_pred, output_dict=True, zero_division=0)
    cm_labels = ["Rendah", "Sedang", "Tinggi"]
    cm = confusion_matrix(y_test, y_pred, labels=cm_labels)

    evaluasi = {
        "accuracy": round(accuracy * 100, 2),
        "report": report,
        "classification_report": report,
        "confusion_matrix": cm.tolist(),
        "train_count": int(len(X_train)),
        "test_count": int(len(X_test)),
        "total_count": int(len(data)),
        "k_used": effective_k,
    }

    return model, scaler, evaluasi


def compare_k(df, k_values=None):
    data = df.copy()
    if "label" not in data.columns:
        data = create_label(data)

    if k_values is None:
        max_k = min(15, max(3, len(data) - 1))
        k_values = [k for k in range(1, max_k + 1) if k % 2 == 1]

    results = []
    for k in k_values:
        try:
            _, _, evaluasi = train_knn(data, k=k)
            results.append({"k": int(k), "accuracy": float(evaluasi["accuracy"])})
        except ValueError:
            continue

    if not results:
        raise ValueError("Tidak ada nilai K yang valid untuk data saat ini.")

    result_df = pd.DataFrame(results)
    return result_df


# =====================================================
# PREDIKSI DATA BARU
# =====================================================
def predict_new_data(model, scaler, data_baru):

    df_input = pd.DataFrame([data_baru], columns=FEATURE_COLUMNS)
    df_scaled = scaler.transform(df_input)

    prediction = model.predict(df_scaled)

    return prediction[0]


# =====================================================
# KEPUTUSAN KEBIJAKAN
# =====================================================
def keputusan_kebijakan(label):
    return label