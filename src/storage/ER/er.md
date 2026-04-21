erDiagram
    users ||--o{ categories : "管理"
    users ||--o{ transactions : "記録"
    categories ||--o{ transactions : "分類"

    users {
        bigint id PK
        string name
        string email
        string password
    }

    categories {
        bigint id PK
        bigint user_id FK "一人用だが拡張性のために保持"
        string name "食費、給与、固定費など"
        string type "income(収入) / expense(支出)"
        string color_code "UIのバーの色用"
    }

    transactions {
        bigint id PK
        bigint user_id FK
        bigint category_id FK "どのカテゴリか"
        string description "内容（例：スーパー・食費）"
        integer amount "金額（円単位ならintegerでOK）"
        date date "発生日"
        datetime created_at
        datetime updated_at
    }
