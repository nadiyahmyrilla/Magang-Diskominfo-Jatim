from sklearn.preprocessing import StandardScaler

def normalisasi_data(df):

    kolom_non_numerik = ['nama_kabupaten_kota']
    kolom_drop = [col for col in kolom_non_numerik if col in df.columns]

    fitur = df.drop(columns=kolom_drop)

    kolom_numerik = fitur.select_dtypes(include='number').columns.tolist()
    if not kolom_numerik:
        raise ValueError("Tidak ada kolom numerik untuk dinormalisasi.")

    fitur = fitur[kolom_numerik]

    try:
        scaler = StandardScaler()
        data_scaled = scaler.fit_transform(fitur)
    except Exception as e:
        raise RuntimeError(f"Error saat normalisasi data: {e}")

    return data_scaled
