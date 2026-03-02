from sklearn.metrics import accuracy_score, classification_report
from sklearn.model_selection import train_test_split
from sklearn.neighbors import KNeighborsClassifier
from sklearn.preprocessing import MinMaxScaler


def train_knn(
    X,
    y,
    n_neighbors: int = 5,
    test_size: float = 0.3,
    random_state: int = 42,
) -> dict:
    scaler = MinMaxScaler()
    X_scaled = scaler.fit_transform(X)

    class_count = y.nunique()
    stratify_target = y if class_count > 1 else None

    X_train, X_test, y_train, y_test = train_test_split(
        X_scaled,
        y,
        test_size=test_size,
        random_state=random_state,
        stratify=stratify_target,
    )

    model = KNeighborsClassifier(n_neighbors=n_neighbors)
    model.fit(X_train, y_train)

    y_pred = model.predict(X_test)

    return {
        "model": model,
        "scaler": scaler,
        "accuracy": accuracy_score(y_test, y_pred),
        "classification_report": classification_report(y_test, y_pred, zero_division=0),
    }
