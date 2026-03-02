from typing import Any

import pandas as pd


def input_test(df: pd.DataFrame) -> dict:
    """Run quick checks on an input dataframe and return a dict of results."""
    res = {}
    res["shape"] = df.shape
    res["columns"] = df.columns.tolist()
    res["missing"] = df.isnull().sum().to_dict()
    res["duplicates"] = int(df.duplicated().sum())
    res["dtypes"] = df.dtypes.apply(lambda x: str(x)).to_dict()
    # sample up to 5 rows as dict
    try:
        sample = df.sample(5, random_state=42)
    except Exception:
        sample = df.head(5)
    res["sample"] = sample.to_dict(orient="records")
    return res
