# analytics/knn_model.py
import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.neighbors import KNeighborsClassifier
from sklearn.metrics import accuracy_score, classification_report
from sklearn.preprocessing import StandardScaler


# =====================================================
# BUAT LABEL OTOMATIS (UNTUK 7 INDIKATOR)
# =====================================================
def create_label(df):
    numeric_cols = df.select_dtypes(include="number").columns
    df["score"] = df[numeric_cols].mean(axis=1)
    df["label"] = pd.qcut(
        df["score"],
        q=3,
        labels=["Rendah", "Sedang", "Tinggi"]
    )
    return df


# =====================================================
# TRAIN KNN (UNTUK 7 INDIKATOR)
# =====================================================
def train_knn(df, k=5):

    fitur_cols = ["GK", "PM", "TPT", "JP", "LPP", "KP", "RJK"]

    X = df[fitur_cols]
    y = df["label"]

    scaler = StandardScaler()
    X_scaled = scaler.fit_transform(X)

    X_train, X_test, y_train, y_test = train_test_split(
        X_scaled, y, test_size=0.2, random_state=42
    )

    model = KNeighborsClassifier(n_neighbors=k)
    model.fit(X_train, y_train)

    y_pred = model.predict(X_test)

    accuracy = accuracy_score(y_test, y_pred)
    report = classification_report(y_test, y_pred, output_dict=True)

    evaluasi = {
        "accuracy": round(accuracy * 100, 2),
        "report": report
    }

    return model, scaler, evaluasi


# =====================================================
# PREDIKSI DATA BARU
# =====================================================
def predict_new_data(model, scaler, data_baru):

    df_input = pd.DataFrame([data_baru])
    df_scaled = scaler.transform(df_input)

    prediction = model.predict(df_scaled)

    return prediction[0]


# =====================================================
# KEPUTUSAN KEBIJAKAN
# =====================================================
def keputusan_kebijakan(label):
    return label