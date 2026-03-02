from preprocessing import data_summary, label_distribution, load_data, prepare_knn_dataset
from knn_model import train_knn


def run_pipeline(file_name: str = "data load.xlsx") -> dict:
    df = load_data(file_name)
    summary = data_summary(df)
    X, y = prepare_knn_dataset(df)
    distribution = label_distribution(y)
    evaluation = train_knn(X, y)

    return {
        "summary": summary,
        "label_distribution": distribution,
        "evaluation": evaluation,
    }
