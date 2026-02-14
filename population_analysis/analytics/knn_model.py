# analytics/knn_model.py

import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.neighbors import KNeighborsClassifier
from sklearn.metrics import accuracy_score, confusion_matrix, classification_report
from sklearn.preprocessing import MinMaxScaler

# =========================
# 1. PEMBENTUKAN LABEL (5 KATEGORI)
# =========================
def create_label(df):
    df = df.copy()

    indikator = ["GK", "PM", "TPT", "JP", "LPP", "KP", "RJK"]

    df["Skor"] = df[indikator].mean(axis=1)

    df["Kategori"] = pd.qcut(
        df["Skor"],
        q=5,
        labels=[0, 1, 2, 3, 4]
    ).astype(int)

    return df


# =========================
# 2. TRAINING + TESTING KNN
# =========================
def train_knn(df, k=5):
    fitur = ["GK", "PM", "TPT", "JP", "LPP", "KP", "RJK"]
    X = df[fitur]
    y = df["Kategori"]

    scaler = MinMaxScaler()
    X_scaled = scaler.fit_transform(X)

    X_train, X_test, y_train, y_test = train_test_split(
        X_scaled, y,
        test_size=0.2, # 20% data untuk testing
        random_state=42,
        stratify=y # memastikan distribusi kategori seimbang di train/test
    )

    model = KNeighborsClassifier(n_neighbors=k)
    model.fit(X_train, y_train)

    y_pred = model.predict(X_test)

    evaluasi = {
        "k": k,
        "accuracy": accuracy_score(y_test, y_pred),
        "confusion_matrix": confusion_matrix(y_test, y_pred),
        "classification_report": classification_report(y_test, y_pred)
    }

    return model, scaler, evaluasi


# =========================
# 3. PERBANDINGAN NILAI K
# =========================
def compare_k(df, k_list=[3, 5, 7, 9]):
    hasil = []

    for k in k_list:
        _, _, evals = train_knn(df, k)
        hasil.append({
            "k": k,
            "accuracy": evals["accuracy"]
        })

    return pd.DataFrame(hasil)


# =========================
# 4. PREDIKSI DATA BARU
# =========================
def predict_new_data(model, scaler, data_baru):
    df = pd.DataFrame([data_baru])
    df_scaled = scaler.transform(df)
    return model.predict(df_scaled)[0]


# =========================
# 5. INTERPRETASI KATEGORI
# =========================
def keputusan_kebijakan(label):
    mapping = {
        4: "Tinggi",
        3: "Agak Tinggi",
        2: "Sedang",
        1: "Agak Rendah",
        0: "Rendah"
    }
    return mapping[label]
