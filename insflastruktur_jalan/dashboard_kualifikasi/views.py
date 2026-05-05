from pathlib import Path

from django.conf import settings
from django.shortcuts import render

from .services.processing import (
    classify_all_sheets_average,
    classify_by_year_columns,
    get_excel_sheets,
    load_excel,
)


def dashboard(request):
    file_name = request.GET.get("file", "data.xlsx")
    sheet_name = request.GET.get("sheet", "jalan")
    view_mode = request.GET.get("mode", "sheet")
    data_dir = Path(settings.BASE_DIR) / "data_excel"
    file_path = data_dir / file_name

    context = {
        "file_name": file_name,
        "sheet_name": sheet_name,
        "view_mode": view_mode,
        "sheets": [],
        "row_count": 0,
        "rows": [],
        "summary": {"Baik": 0, "Sedang": 0, "Buruk": 0},
        "good_count": 0,
        "medium_count": 0,
        "bad_count": 0,
        "error": "",
    }

    if not file_path.exists():
        context["error"] = "File tidak ditemukan."
        return render(request, "dashboard_kualifikasi/result.html", context)

    try:
        sheets = get_excel_sheets(file_path)
        context["sheets"] = sheets

        if view_mode == "average":
            result = classify_all_sheets_average(file_path, sheets)
            context["sheet_name"] = "Rata-Rata Semua Data"
        else:
            if sheet_name not in sheets:
                sheet_name = sheets[0] if sheets else "jalan"
                context["sheet_name"] = sheet_name

            df = load_excel(file_path, sheet_name=sheet_name)
            result = classify_by_year_columns(df)

        context["rows"] = result.rows
        context["summary"] = result.summary
        context["good_count"] = result.good_count
        context["medium_count"] = result.medium_count
        context["bad_count"] = result.bad_count
        context["row_count"] = len(result.rows)
    except Exception as exc:
        context["error"] = f"Gagal memproses data: {exc}"

    return render(request, "dashboard_kualifikasi/result.html", context)
