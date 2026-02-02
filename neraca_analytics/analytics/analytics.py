import pandas as pd
from sqlalchemy import create_engine
from django.conf import settings

#load_warehouse_data()
def load_warehouse_data():
    db = settings.DATABASE_WAREHOUSE

    engine = create_engine(
        f"mysql+pymysql://{db['USER']}@{db['HOST']}:{db['PORT']}/{db['NAME']}"
    )

    query = """
    SELECT
        w.tahun,
        w.triwulan,
        sd.nama_sumber_data AS sumber_data,
        sd.satuan,
        i.nama_indikator AS indikator,
        ja.nama_jenis_analisis AS jenis_analisis,
        f.nilai
    FROM fact_neraca_ekonomi f
    JOIN dim_waktu w ON f.id_waktu = w.id_waktu
    JOIN dim_sumber_data sd ON f.id_sumber_data = sd.id_sumber_data
    JOIN dim_indikator i ON f.id_indikator = i.id_indikator
    JOIN dim_jenis_analisis ja ON f.id_jenis_analisis = ja.id_jenis_analisis
    ORDER BY w.tahun, w.triwulan;
    """

    return pd.read_sql(query, engine)

