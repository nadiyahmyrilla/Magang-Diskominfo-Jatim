#mapping, sorting, cleaning
def add_triwulan_numeric(df):
    # Mapping triwulan numerik
    triwulan_map = {
        'I': 1,
        'II': 2,
        'III': 3,
        'IV': 4,
        'Tahunan': 0
    }

    df['triwulan_num'] = df['triwulan'].map(triwulan_map)
    return df

# sorting time series
def sort_time_series(df):
    return df.sort_values(['tahun', 'triwulan_num'])

def clean_satuan(df):
    df['satuan'] = df['satuan'].str.strip()
    return df
