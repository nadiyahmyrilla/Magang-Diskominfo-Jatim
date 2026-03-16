from sklearn.cluster import KMeans

def proses_clustering(data_scaled):

    if data_scaled is None or len(data_scaled) == 0:
        raise ValueError("Data scaled kosong, tidak bisa melakukan clustering.")

    if len(data_scaled) < 3:
        raise ValueError("Data terlalu sedikit untuk clustering (minimal 3 baris).")

    try:
        model = KMeans(n_clusters=3, random_state=42)
        cluster = model.fit_predict(data_scaled)
    except Exception as e:
        raise RuntimeError(f"Error saat proses clustering: {e}")

    return cluster
