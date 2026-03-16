import json
import traceback
from django.shortcuts import render

from analysis.baca_data import baca_semua_tahun
from analysis.normalisasi import normalisasi_data
from analysis.clustering import proses_clustering


def _proses_dataframe(df):
    """Proses satu DataFrame: normalisasi, clustering, labeling."""
    df.columns = df.columns.str.strip().str.replace(r'\s+', '_', regex=True)

    data_scaled = normalisasi_data(df)
    cluster = proses_clustering(data_scaled)
    df['cluster'] = cluster

    cluster_means = df.groupby('cluster')['grand_total'].mean()
    sorted_clusters = cluster_means.sort_values().index.tolist()
    label_map = {}
    labels = ['Rendah', 'Sedang', 'Tinggi']
    for i, c in enumerate(sorted_clusters):
        label_map[c] = labels[i]

    df['cluster_label'] = df['cluster'].map(label_map)

    # Rename kolom jaminan jika perlu
    if 'jaminan_perusahaan' not in df.columns:
        for col in df.columns:
            if 'jaminan' in col.lower():
                df = df.rename(columns={col: 'jaminan_perusahaan'})
                break

    return df


def dashboard(request):

    error_message = None
    data_records = []
    cluster_summary = []
    kabupaten_list = []
    chart_diabetes = {}
    chart_hipertensi = {}
    chart_asuransi = {}
    total_stats = {}
    cluster_distribution = {}
    peta_data_per_tahun = {}
    tahun_list = []
    selected_tahun = None

    try:
        semua_data = baca_semua_tahun()
        tahun_list = sorted(semua_data.keys())
        selected_tahun = request.GET.get('tahun', tahun_list[-1] if tahun_list else None)

        if selected_tahun not in semua_data:
            selected_tahun = tahun_list[-1]

        # Proses data per tahun untuk peta
        for tahun, df_tahun in semua_data.items():
            try:
                df_processed = _proses_dataframe(df_tahun.copy())
                peta_tahun = {}
                for _, row in df_processed.iterrows():
                    peta_tahun[row['nama_kabupaten_kota']] = {
                        'cluster': int(row['cluster']),
                        'cluster_label': row['cluster_label'],
                        'diabetes': int(row['diabetes']) if row['diabetes'] > 100 else round(float(row['diabetes']), 3),
                        'hipertensi': int(row['hipertensi']) if row['hipertensi'] > 100 else round(float(row['hipertensi']), 3),
                        'grand_total': int(row['grand_total']) if row['grand_total'] > 100 else round(float(row['grand_total']), 3),
                    }
                peta_data_per_tahun[tahun] = peta_tahun
            except Exception:
                peta_data_per_tahun[tahun] = {}

        # Proses data tahun terpilih untuk dashboard utama
        df = _proses_dataframe(semua_data[selected_tahun].copy())

        data_records = df.to_dict('records')
        kabupaten_list = sorted(df['nama_kabupaten_kota'].unique().tolist())

        # Statistik total
        total_stats = {
            'total_kabupaten': len(df),
            'total_diabetes': int(df['diabetes'].sum()) if df['diabetes'].mean() > 100 else round(float(df['diabetes'].sum()), 3),
            'total_hipertensi': int(df['hipertensi'].sum()) if df['hipertensi'].mean() > 100 else round(float(df['hipertensi'].sum()), 3),
            'total_grand': int(df['grand_total'].sum()) if df['grand_total'].mean() > 100 else round(float(df['grand_total'].sum()), 3),
            'avg_diabetes': int(df['diabetes'].mean()) if df['diabetes'].mean() > 100 else round(float(df['diabetes'].mean()), 3),
            'avg_hipertensi': int(df['hipertensi'].mean()) if df['hipertensi'].mean() > 100 else round(float(df['hipertensi'].mean()), 3),
        }

        # Distribusi cluster
        cluster_counts = df['cluster_label'].value_counts().to_dict()
        cluster_distribution = {
            'labels': list(cluster_counts.keys()),
            'values': list(cluster_counts.values()),
        }

        # Data untuk chart diabetes & hipertensi per kabupaten (top 15)
        df_sorted_diabetes = df.nlargest(15, 'diabetes')
        chart_diabetes = {
            'labels': df_sorted_diabetes['nama_kabupaten_kota'].tolist(),
            'values': df_sorted_diabetes['diabetes'].tolist(),
        }

        df_sorted_hipertensi = df.nlargest(15, 'hipertensi')
        chart_hipertensi = {
            'labels': df_sorted_hipertensi['nama_kabupaten_kota'].tolist(),
            'values': df_sorted_hipertensi['hipertensi'].tolist(),
        }

        # Data asuransi
        asuransi_cols = ['bpjs_pbi', 'bpjs_non_pbi', 'jamkesda', 'asuransi_swasta', 'jaminan_perusahaan']
        available_asuransi = [col for col in asuransi_cols if col in df.columns]

        chart_asuransi = {
            'labels': df['nama_kabupaten_kota'].tolist(),
            'datasets': {}
        }
        for col in available_asuransi:
            chart_asuransi['datasets'][col] = df[col].tolist()

        asuransi_avg = {}
        for col in available_asuransi:
            asuransi_avg[col] = round(df[col].mean(), 2)

        total_stats['asuransi_avg'] = asuransi_avg

        # Cluster summary
        for label in ['Rendah', 'Sedang', 'Tinggi']:
            cluster_df = df[df['cluster_label'] == label]
            if not cluster_df.empty:
                cluster_summary.append({
                    'label': label,
                    'count': len(cluster_df),
                    'avg_diabetes': int(cluster_df['diabetes'].mean()) if cluster_df['diabetes'].mean() > 100 else round(float(cluster_df['diabetes'].mean()), 3),
                    'avg_hipertensi': int(cluster_df['hipertensi'].mean()) if cluster_df['hipertensi'].mean() > 100 else round(float(cluster_df['hipertensi'].mean()), 3),
                    'avg_grand_total': int(cluster_df['grand_total'].mean()) if cluster_df['grand_total'].mean() > 100 else round(float(cluster_df['grand_total'].mean()), 3),
                    'kabupaten': sorted(cluster_df['nama_kabupaten_kota'].tolist()),
                })

    except FileNotFoundError as e:
        error_message = f"Data tidak ditemukan: {e}"
    except ValueError as e:
        error_message = f"Kesalahan data: {e}"
    except RuntimeError as e:
        error_message = f"Kesalahan proses: {e}"
    except Exception as e:
        error_message = f"Terjadi kesalahan: {e}\n{traceback.format_exc()}"

    context = {
        'error_message': error_message,
        'data': data_records,
        'total_stats': total_stats,
        'cluster_summary': cluster_summary,
        'cluster_distribution_json': json.dumps(cluster_distribution),
        'chart_diabetes_json': json.dumps(chart_diabetes),
        'chart_hipertensi_json': json.dumps(chart_hipertensi),
        'chart_asuransi_json': json.dumps(chart_asuransi),
        'peta_data_per_tahun_json': json.dumps(peta_data_per_tahun),
        'kabupaten_list': kabupaten_list,
        'tahun_list': tahun_list,
        'selected_tahun': selected_tahun,
    }

    return render(request, 'dashboard.html', context)
