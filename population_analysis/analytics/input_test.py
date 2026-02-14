# analytics/input_test.py

from analytics.analytics import merge_all_indicators
from analytics.knn_model import (
    create_label,
    train_knn,
    predict_new_data,
    keputusan_kebijakan
)

def input_test():
    # 1. Load & label data
    df = merge_all_indicators()
    df = create_label(df)

    # 2. Training KNN (k=5)
    model, scaler, evaluasi = train_knn(df, k=5)

    print("Akurasi Model:", evaluasi["accuracy"])
    print("\nConfusion Matrix:\n", evaluasi["confusion_matrix"])

    # 3. INPUT MANUAL DARI CMD
    print("\n=== INPUT DATA WILAYAH BARU ===")

    data_baru = {
        "GK": float(input("Garis Kemiskinan (GK): ")),
        "PM": float(input("Persentase Penduduk Miskin (PM): ")),
        "TPT": float(input("Tingkat Pengangguran Terbuka (TPT): ")),
        "JP": float(input("Jumlah Penduduk (JP): ")),
        "LPP": float(input("Laju Pertumbuhan Penduduk (LPP): ")),
        "KP": float(input("Kepadatan Penduduk (KP): ")),
        "RJK": float(input("Rasio Jenis Kelamin (RJK): "))
    }

    # 4. Prediksi
    label = predict_new_data(model, scaler, data_baru)
    kategori = keputusan_kebijakan(label)

    print("\n=== HASIL KLASIFIKASI ===")
    print("Kategori Wilayah:", kategori)
