from analytics.preprocessing import prepare_dataset

def merge_all_indicators():
    indikator = {
        "Garis_Kemiskinan": "GK",
        "Persentase_Penduduk_Miskin": "PM",
        "TPT": "TPT",
        "Jumlah_Penduduk": "JP",
        "Laju_Pertumbuhan_Penduduk": "LPP",
        "Kepadatan_Penduduk": "KP",
        "Rasio_Jenis_Kelamin": "RJK"
    }

    dfs = []

    for sheet, kode in indikator.items():
        _, df = prepare_dataset(sheet)
        df = df.rename(columns={"Nilai_Normalisasi": kode})
        df = df[["Kabupaten/Kota", "Tahun", kode]]
        dfs.append(df)

    df_final = dfs[0]
    for df in dfs[1:]:
        df_final = df_final.merge(
            df,
            on=["Kabupaten/Kota", "Tahun"],
            how="inner"
        )

    return df_final
