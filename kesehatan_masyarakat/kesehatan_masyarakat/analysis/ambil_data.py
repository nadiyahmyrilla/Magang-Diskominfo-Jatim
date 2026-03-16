import os

def ambil_path_data():

    BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

    path = os.path.join(BASE_DIR, 'data', 'data transform.xlsx')

    if not os.path.exists(path):
        raise FileNotFoundError(f"File data tidak ditemukan: {path}")

    return path