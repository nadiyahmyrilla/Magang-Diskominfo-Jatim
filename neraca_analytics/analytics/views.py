import json
import mysql.connector
from django.shortcuts import render
from django.conf import settings


def index(request):

    conn = mysql.connector.connect(
        host=settings.DATABASE_WAREHOUSE['HOST'],
        user=settings.DATABASE_WAREHOUSE['USER'],
        password=settings.DATABASE_WAREHOUSE['PASSWORD'],
        database=settings.DATABASE_WAREHOUSE['NAME'],
        port=settings.DATABASE_WAREHOUSE['PORT']
    )

    cursor = conn.cursor(dictionary=True)

    # ==============================
    # 1️⃣ CUSTOMER HABITS (BAR)
    # ==============================

    cursor.execute("""
        SELECT
            i.nama_indikator,
            w.tahun,
            SUM(f.nilai) AS total
        FROM fact_neraca_ekonomi f
        JOIN dim_indikator i ON f.id_indikator = i.id_indikator
        JOIN dim_waktu w ON f.id_waktu = w.id_waktu
        GROUP BY i.nama_indikator, w.tahun
        ORDER BY w.tahun
    """)

    rows = cursor.fetchall()

    labels = sorted(list(set(r['nama_indikator'] for r in rows)))
    tahun_list = sorted(list(set(r['tahun'] for r in rows)))

    colors = ["#6366f1", "#22c55e", "#f59e0b", "#ef4444", "#06b6d4"]

    datasets = []

    for idx, tahun in enumerate(tahun_list):
        data_per_tahun = []

        for indikator in labels:
            total = next(
                (r['total'] for r in rows
                 if r['tahun'] == tahun
                 and r['nama_indikator'] == indikator),
                0
            )
            data_per_tahun.append(float(total))

        datasets.append({
            "label": str(tahun),
            "data": data_per_tahun,
            "backgroundColor": colors[idx % len(colors)]
        })

    # ==============================
    # 2️⃣ RADAR STABILITAS
    # ==============================

    cursor.execute("""
        SELECT
            i.nama_indikator,
            ROUND(STDDEV(f.nilai),2) AS stabilitas
        FROM fact_neraca_ekonomi f
        JOIN dim_indikator i ON f.id_indikator = i.id_indikator
        GROUP BY i.nama_indikator
        ORDER BY stabilitas ASC
        LIMIT 6
    """)

    radar_rows = cursor.fetchall()

    radar_labels = [r['nama_indikator'] for r in radar_rows]
    radar_data = [float(r['stabilitas']) for r in radar_rows]

    # ==============================
    # 3️⃣ LINE TAHUNAN
    # ==============================

    cursor.execute("""
        SELECT
            w.tahun,
            SUM(f.nilai) AS total
        FROM fact_neraca_ekonomi f
        JOIN dim_waktu w ON f.id_waktu = w.id_waktu
        GROUP BY w.tahun
        ORDER BY w.tahun
    """)

    line_rows = cursor.fetchall()

    line_labels = [r['tahun'] for r in line_rows]
    line_data = [float(r['total']) for r in line_rows]

    # ==============================
    # 4️⃣ STATUS INDIKATOR PER TAHUN
    # ==============================

    cursor.execute("""
        SELECT
            indikator,
            tahun,
            nilai,

            ROUND(pertumbuhan,2) AS stabilitas,

            CASE
                WHEN pertumbuhan IS NULL THEN 'Stabil'
                WHEN pertumbuhan BETWEEN -2 AND 2 THEN 'Stabil'
                WHEN pertumbuhan BETWEEN -5 AND 5 THEN 'Waspada'
                ELSE 'Perlu Intervensi'
            END AS status

        FROM (

            SELECT
                indikator,
                tahun,
                nilai,

                (
                    nilai
                    - LAG(nilai)
                    OVER (PARTITION BY indikator ORDER BY tahun)
                )
                /
                LAG(nilai)
                OVER (PARTITION BY indikator ORDER BY tahun)
                * 100 AS pertumbuhan

            FROM (

                SELECT
                    i.nama_indikator AS indikator,
                    w.tahun,
                    SUM(f.nilai) AS nilai
                FROM fact_neraca_ekonomi f
                JOIN dim_indikator i
                    ON f.id_indikator = i.id_indikator
                JOIN dim_waktu w
                    ON f.id_waktu = w.id_waktu
                GROUP BY i.nama_indikator, w.tahun

            ) base

        ) hasil

        ORDER BY tahun, indikator
    """)

    indikator_stabil = cursor.fetchall()

    # ==============================
    # 5️⃣ KPI — 4 INDIKATOR UTAMA
    # ==============================

    cursor.execute("""
        SELECT
            i.nama_indikator,
            SUM(f.nilai) AS total_nilai
        FROM fact_neraca_ekonomi f
        JOIN dim_indikator i ON f.id_indikator = i.id_indikator
        GROUP BY i.nama_indikator
        ORDER BY total_nilai DESC
        LIMIT 4
    """)

    kpi_rows = cursor.fetchall()

    kpi_data = []

    for row in kpi_rows:
        kpi_data.append({
            "jenis": "Indikator Utama",
            "nilai": row["nama_indikator"],
            "keterangan": f"Total Nilai: {row['total_nilai']:,.0f}"
        })

    cursor.close()
    conn.close()

    context = {
        "customer_labels": json.dumps(labels),
        "customer_datasets": json.dumps(datasets),

        "radar_labels": json.dumps(radar_labels),
        "radar_data": json.dumps(radar_data),

        "line_labels": json.dumps(line_labels),
        "line_data": json.dumps(line_data),

        "indikator_stabil": indikator_stabil,
        "kpi_data": kpi_data
    }

    return render(request, "analytics/index.html", context)
