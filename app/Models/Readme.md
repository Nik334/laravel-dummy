Generic structure inherited by all the models
-------------------------------------------
| Column     | Data Type              |
|------------|------------------------|
| id         | Integer                |
| created_at | Timestamp              |
| updated_at | Timestamp              |
| status     | ENUM (active/inactive) |

Designation
-----------
| Column         | Data Type |
|----------------|-----------|
| id             | Integer   |
| name           | Text      |
| department_id  | Integer   |
| role_id        | Integer   |

Job
---
| Column         | Data Type |
|----------------|-----------|
| file           | Text      |
| version        | Text      |
| designation_id | Text      |

Department
----------
| Column   | Data Type |
|----------|-----------|
| name     | Text      |

User
----
| Column         | Data Type |
|----------------|-----------|
| user_id        | Character |
| first_name     | Text      |
| last_name      | Text      |
| email          | Text      |
| password       | Text      |
| designation_id | Integer   |
| joined_on      | Timestamp |
| mobile         | Text      |
