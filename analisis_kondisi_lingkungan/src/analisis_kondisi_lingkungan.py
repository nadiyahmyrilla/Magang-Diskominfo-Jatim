from analytics import run_pipeline


def main() -> None:
    result = run_pipeline("data load.xlsx")

    print("DATA BERHASIL DIMUAT:")
    print(result["summary"]["head"])

    print("\nRINGKASAN DATA:")
    print(result["summary"]["describe"])

    print("\nJUMLAH NILAI KOSONG:")
    print(result["summary"]["missing_values"])

    print("\nDISTRIBUSI LABEL:")
    print(result["label_distribution"])

    print("\nHASIL PENENTUAN KONDISI:")
    print("Akurasi:", result["evaluation"]["accuracy"])
    print("\nLaporan Klasifikasi:")
    print(result["evaluation"]["classification_report"])


if __name__ == "__main__":
    main()