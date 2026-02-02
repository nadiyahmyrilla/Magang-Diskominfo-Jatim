#rule & rekomendasi

def deteksi_masalah(pertumbuhan_tahun):
    return pertumbuhan_tahun[pertumbuhan_tahun['nilai'] < 0]

def beri_rekomendasi(df):
    def rekomendasi(nilai):
        if nilai < 0:
            return 'Perlu Intervensi'
        elif nilai < 2:
            return 'Waspada'
        else:
            return 'Stabil'

    df['status'] = df['nilai'].apply(rekomendasi)
    return df
