#statistik & agregasi
import matplotlib.pyplot as plt

def count_satuan(df):
    return df['satuan'].value_counts()

def statistik_indikator(df):
    return df.groupby('indikator')['nilai'].describe()

def filter_pertumbuhan(df):
    return df[df['satuan'].str.contains('persen', case=False, na=False)]

def tren_tahunan(df_pertumbuhan):
    return (
        df_pertumbuhan
        .groupby(['tahun', 'indikator'])['nilai']
        .mean()
        .reset_index()
    )

def plot_tren(pertumbuhan_tahun):
    for indikator in pertumbuhan_tahun['indikator'].unique():
        subset = pertumbuhan_tahun[
            pertumbuhan_tahun['indikator'] == indikator
        ]
        plt.figure()
        plt.plot(subset['tahun'], subset['nilai'])
        plt.title(indikator)
        plt.xlabel('Tahun')
        plt.ylabel('Pertumbuhan (%)')
        plt.show()

