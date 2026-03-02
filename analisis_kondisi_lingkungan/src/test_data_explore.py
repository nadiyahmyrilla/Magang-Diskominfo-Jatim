from pathlib import Path
import pandas as pd
import sys


def find_data_file(data_dir: Path):
    candidates = [
        "data load.xlsx",
        "Dataset_load.xlsx",
        "data_load.xlsx",
        "data load.xlsx",
    ]
    for name in candidates:
        p = data_dir / name
        if p.exists():
            return p
    # fallback: any xlsx in folder
    for p in data_dir.glob("*.xls*"):
        return p
    return None


def explore(path: Path):
    print(f"Menggunakan file: {path}\n")
    try:
        df = pd.read_excel(path)
    except Exception as e:
        print("Gagal membaca file:", e)
        sys.exit(1)

    print("--- head() ---")
    print(df.head().to_string(index=False))

    print("\n--- shape ---")
    print(df.shape)

    print("\n--- info() ---")
    df.info()

    print("\n--- isnull().sum() ---")
    print(df.isnull().sum())

    print("\n--- describe() ---")
    print(df.describe(include='all').to_string())

    print("\n--- duplicated().sum() ---")
    print(df.duplicated().sum())

    print("\n--- sample(5) ---")
    try:
        print(df.sample(5, random_state=42).to_string(index=False))
    except ValueError:
        print(df.sample(frac=1.0, random_state=42).to_string(index=False))

    print("\n--- min() ---")
    print(df.min(numeric_only=False))

    print("\n--- max() ---")
    print(df.max(numeric_only=False))


def main():
    base = Path(__file__).resolve().parent.parent
    data_dir = base / "data"

    if not data_dir.exists():
        print("Folder data tidak ditemukan di:", data_dir)
        sys.exit(1)

    data_file = find_data_file(data_dir)
    if data_file is None:
        print("Tidak menemukan file .xlsx di folder data")
        sys.exit(1)

    explore(data_file)


if __name__ == "__main__":
    main()
