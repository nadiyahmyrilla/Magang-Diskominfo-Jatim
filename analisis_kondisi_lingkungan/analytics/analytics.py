from functools import reduce
from typing import Iterable

import pandas as pd


def merge_all_indicators(dfs: Iterable[pd.DataFrame], on: str = "nama_kabupaten_kota") -> pd.DataFrame:
    """Merge multiple indicator dataframes on a common key (default: nama_kabupaten_kota).

    Performs outer merges so no row is lost.
    """
    dfs = list(dfs)
    if not dfs:
        return pd.DataFrame()

    def _merge(a, b):
        return pd.merge(a, b, how="outer", on=on)

    merged = reduce(_merge, dfs)
    return merged
