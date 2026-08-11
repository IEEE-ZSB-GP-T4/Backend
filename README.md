# Planora Backend

## Back-End

### Prerequisites

Make sure the following software is installed:

- PHP 8.3+
- Composer 2.9+
- Laravel 12.x
- MySQL 8.0+

---

## Setup & Installation

### 1. Clone the Repository

```bash
git clone https://github.com/IEEE-ZSB-GP-T4/Backend.git
```

### 2. Navigate to the Backend Directory

```bash
cd Backend
```

### 3. Install Dependencies

```bash
composer install
```

### 4. Configure Environment Variables

Copy the example environment file:

```bash
cp .env.example .env
```

Update the database configuration in the `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_study_planner
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Make sure MySQL is running before continuing.

### 5. Generate the Application Key

```bash
php artisan key:generate
```

### 6. Install API Authentication

Laravel Sanctum is used for API authentication.

```bash
php artisan install:api
```

This command installs the API authentication setup and creates the required Sanctum configuration and migrations.

### 7. Run Database Migrations

```bash
php artisan migrate
```

### 8. Start the Development Server

```bash
php artisan serve
```

The application will be available at:

```text
http://127.0.0.1:8000
```

---

# Authentication

The API uses **Laravel Sanctum** for token-based authentication.

The authentication flow is:

```text
Register
   ↓
Login
   ↓
Receive Authentication Token
   ↓
Send Token with Protected Requests
   ↓
Logout
```

For protected endpoints, send the token using:

```http
Authorization: Bearer YOUR_TOKEN
```

---

## Authentication Endpoints

| Method | Endpoint        | Authentication | Description                         |
| ------ | --------------- | -------------- | ----------------------------------- |
| POST   | `/api/register` | No             | Create a new account                |
| POST   | `/api/login`    | No             | Login and receive a token           |
| POST   | `/api/logout`   | Yes            | Logout and revoke the current token |
| GET    | `/api/user`     | Yes            | Get the authenticated user          |

---

## Register

### Request

```http
POST /api/register
Content-Type: application/json
Accept: application/json
```

### Body

```json
{
    "name": "Mohamed",
    "email": "mohamed@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### Successful Response

```json
{
    "status": 201,
    "message": "Registration successful",
    "data": {
        "user": {
            "id": 1,
            "name": "Mohamed",
            "email": "mohamed@example.com"
        },
        "token": "1|xxxxxxxxxxxxxxxxxxxxxxxx"
    }
}
```

Save the returned `token`. It is required for protected API requests.

---

## Login

### Request

```http
POST /api/login
Content-Type: application/json
Accept: application/json
```

### Body

```json
{
    "email": "mohamed@example.com",
    "password": "password123"
}
```

### Successful Response

```json
{
    "status": 200,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "Mohamed",
            "email": "mohamed@example.com"
        },
        "token": "2|xxxxxxxxxxxxxxxxxxxxxxxx"
    }
}
```

---

## Authenticated User

This endpoint requires a valid Sanctum token.

### Request

```http
GET /api/user
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Successful Response

```json
{
    "id": 1,
    "name": "Mohamed",
    "email": "mohamed@example.com"
}
```

---

## Logout

Logout revokes the current authentication token.

### Request

```http
POST /api/logout
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Successful Response

```json
{
    "status": 200,
    "message": "Logged out successfully",
    "data": null
}
```

After logout, the revoked token can no longer be used to access protected endpoints.

---

# Courses

The Courses API allows authenticated users to manage their own courses.

Each course belongs to one user.

```text
User
  │
  │ 1
  │
  └──────────< Courses
```

All Courses endpoints require authentication using Laravel Sanctum.

Send the token using:

```http
Authorization: Bearer YOUR_TOKEN
```

---

## Course Endpoints

| Method | Endpoint           | Authentication | Description              |
| ------ | ------------------ | -------------- | ------------------------ |
| GET    | `/api/courses`     | Yes            | Get all user's courses   |
| POST   | `/api/courses`     | Yes            | Create a new course      |
| GET    | `/api/courses/{id}`| Yes            | Get a specific course    |
| PUT    | `/api/courses/{id}`| Yes            | Update a specific course |
| DELETE | `/api/courses/{id}`| Yes            | Delete a specific course |

---

## Get All Courses

Returns all courses belonging to the authenticated user.

### Request

```http
GET /api/courses
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Successful Response

```json
{
    "status": 200,
    "message": "Courses retrieved successfully",
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "name": "Intro to Algorithms",
            "instructor": "Dr. Ahmed",
            "code": "CS101",
            "created_at": "2026-08-09T10:00:00.000000Z",
            "updated_at": "2026-08-09T10:00:00.000000Z"
        },
        {
            "id": 2,
            "user_id": 1,
            "name": "Linear Algebra",
            "instructor": "Dr. Ali",
            "code": "MAT202",
            "created_at": "2026-08-09T10:10:00.000000Z",
            "updated_at": "2026-08-09T10:10:00.000000Z"
        }
    ]
}
```

---

## Create Course

Creates a new course for the authenticated user.

### Request

```http
POST /api/courses
Content-Type: application/json
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Body

```json
{
    "name": "Intro to Algorithms",
    "instructor": "Dr. Ahmed",
    "code": "CS101"
}
```

### Successful Response

```json
{
    "status": 201,
    "message": "Course created successfully",
    "data": {
        "id": 1,
        "user_id": 1,
        "name": "Intro to Algorithms",
        "instructor": "Dr. Ahmed",
        "code": "CS101",
        "created_at": "2026-08-09T10:00:00.000000Z",
        "updated_at": "2026-08-09T10:00:00.000000Z"
    }
}
```

### Validation

| Field | Type | Required | Rules |
| ----- | ---- | -------- | ----- |
| `name` | string | Yes | Maximum 255 characters |
| `instructor` | string | No | Maximum 255 characters |
| `code` | string | Yes | Maximum 50 characters |

---

## Get Specific Course

Returns a specific course belonging to the authenticated user.

### Request

```http
GET /api/courses/1
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Successful Response

```json
{
    "status": 200,
    "message": "Course retrieved successfully",
    "data": {
        "id": 1,
        "user_id": 1,
        "name": "Intro to Algorithms",
        "instructor": "Dr. Ahmed",
        "code": "CS101",
        "created_at": "2026-08-09T10:00:00.000000Z",
        "updated_at": "2026-08-09T10:00:00.000000Z"
    }
}
```

---

## Update Course

Updates an existing course belonging to the authenticated user.

### Request

```http
PUT /api/courses/1
Content-Type: application/json
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Body

```json
{
    "name": "Advanced Algorithms",
    "instructor": "Dr. Ahmed",
    "code": "CS201"
}
```

### Successful Response

```json
{
    "status": 200,
    "message": "Course updated successfully",
    "data": {
        "id": 1,
        "user_id": 1,
        "name": "Advanced Algorithms",
        "instructor": "Dr. Ahmed",
        "code": "CS201",
        "created_at": "2026-08-09T10:00:00.000000Z",
        "updated_at": "2026-08-09T11:00:00.000000Z"
    }
}
```

---

## Delete Course

Deletes a specific course belonging to the authenticated user.

### Request

```http
DELETE /api/courses/1
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Successful Response

```json
{
    "status": 200,
    "message": "Course deleted successfully",
    "data": null
}
```

---

## Course Authorization

Users can only access, update, or delete their own courses.

For example, if the authenticated user has:

```text
user_id = 1
```

they can access:

```text
Course 1 → user_id = 1
Course 2 → user_id = 1
```

but they cannot access:

```text
Course 5 → user_id = 2
```

Unauthorized access should return:

```json
{
    "status": 403,
    "message": "Unauthorized access to this course",
    "data": null
}
```

---

# Tasks

The Tasks API allows authenticated users to create and manage tasks that belong to their courses.

Each task belongs to one course, and each course belongs to one user.

```text
User
  │
  └── Courses
        │
        └── Tasks
```

All Task endpoints require authentication using Laravel Sanctum.

Send the token using:

```http
Authorization: Bearer YOUR_TOKEN
```

---

## Task Endpoints

| Method | Endpoint | Authentication | Description |
| ------ | -------- | -------------- | ----------- |
| GET | `/api/tasks` | Yes | Get all user's tasks |
| POST | `/api/tasks` | Yes | Create a new task |
| GET | `/api/tasks/{id}` | Yes | Get a specific task |
| PUT | `/api/tasks/{id}` | Yes | Update a specific task |
| PATCH | `/api/tasks/{id}/complete` | Yes | Mark a task as completed |
| DELETE | `/api/tasks/{id}` | Yes | Delete a specific task |
| GET | `/api/tasks/upcoming-deadlines` | Yes | Get upcoming incomplete tasks ordered by deadline |

---

## Create Task

Creates a new task for a course owned by the authenticated user.

### Request

```http
POST /api/tasks
Content-Type: application/json
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Body

```json
{
    "course_id": 1,
    "title": "Study SQL Joins",
    "description": "Study INNER JOIN, LEFT JOIN and RIGHT JOIN",
    "deadline": "2026-08-15 18:00:00",
    "estimated_hours": 2.5,
    "priority": "high"
}
```

### Validation

| Field | Type | Required | Rules |
| ----- | ---- | -------- | ----- |
| `course_id` | integer | Yes | Must exist in courses and belong to the authenticated user |
| `title` | string | Yes | Maximum 255 characters |
| `description` | string | No | Nullable |
| `deadline` | date | Yes | Valid date/time |
| `estimated_hours` | numeric | Yes | Minimum 0 |
| `priority` | string | Yes | `low`, `mid`, or `high` |

The backend automatically sets:

- `status` → `pending`
- `completed_at` → `null`

### Successful Response

```json
{
    "status": 201,
    "message": "Task created successfully",
    "data": {
        "id": 1,
        "course_id": 1,
        "title": "Study SQL Joins",
        "description": "Study INNER JOIN, LEFT JOIN and RIGHT JOIN",
        "deadline": "2026-08-15T18:00:00.000000Z",
        "estimated_hours": "2.50",
        "priority": "high",
        "status": "pending",
        "completed_at": null
    }
}
```

---

## Get All Tasks

Returns all tasks belonging to the authenticated user.

### Request

```http
GET /api/tasks
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Successful Response

```json
{
    "status": 200,
    "message": "Tasks retrieved successfully",
    "data": [
        {
            "id": 1,
            "course_id": 1,
            "title": "Study SQL Joins",
            "description": "Study INNER JOIN, LEFT JOIN and RIGHT JOIN",
            "deadline": "2026-08-15T18:00:00.000000Z",
            "estimated_hours": "2.50",
            "priority": "high",
            "status": "pending",
            "completed_at": null,
            "course": {
                "id": 1,
                "name": "Database Systems",
                "code": "CS301"
            }
        }
    ]
}
```

---

## Get Specific Task

Returns a specific task belonging to the authenticated user.

### Request

```http
GET /api/tasks/1
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Successful Response

```json
{
    "status": 200,
    "message": "Task retrieved successfully",
    "data": {
        "id": 1,
        "course_id": 1,
        "title": "Study SQL Joins",
        "description": "Study INNER JOIN, LEFT JOIN and RIGHT JOIN",
        "deadline": "2026-08-15T18:00:00.000000Z",
        "estimated_hours": "2.50",
        "priority": "high",
        "status": "pending",
        "completed_at": null
    }
}
```

---

## Update Task

Updates an existing task belonging to the authenticated user.

### Request

```http
PUT /api/tasks/1
Content-Type: application/json
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Body

```json
{
    "title": "Study Advanced SQL Joins",
    "description": "Study all SQL JOIN types",
    "deadline": "2026-08-16 20:00:00",
    "estimated_hours": 3,
    "priority": "mid"
}
```

### Successful Response

```json
{
    "status": 200,
    "message": "Task updated successfully",
    "data": {
        "id": 1,
        "course_id": 1,
        "title": "Study Advanced SQL Joins",
        "description": "Study all SQL JOIN types",
        "deadline": "2026-08-16T20:00:00.000000Z",
        "estimated_hours": "3.00",
        "priority": "mid",
        "status": "pending",
        "completed_at": null
    }
}
```

---

## Complete Task

Marks a task as completed and records the completion time.

### Request

```http
PATCH /api/tasks/1/complete
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

No request body is required.

### Successful Response

```json
{
    "status": 200,
    "message": "Task completed successfully",
    "data": {
        "id": 1,
        "course_id": 1,
        "title": "Study SQL Joins",
        "status": "completed",
        "completed_at": "2026-08-10T16:30:00.000000Z"
    }
}
```

---

## Get Upcoming Deadlines

Returns incomplete tasks whose deadlines are in the future, ordered from the nearest deadline to the latest deadline.

This endpoint is useful for the Dashboard's **Upcoming Deadlines** section.

### Request

```http
GET /api/tasks/upcoming-deadlines
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Successful Response

```json
{
    "status": 200,
    "message": "Upcoming deadlines retrieved successfully",
    "data": [
        {
            "id": 2,
            "course_id": 1,
            "title": "Database Assignment",
            "deadline": "2026-08-12T18:00:00.000000Z",
            "estimated_hours": "3.00",
            "priority": "high",
            "status": "pending",
            "completed_at": null,
            "course": {
                "id": 1,
                "name": "Database Systems",
                "code": "CS301"
            }
        },
        {
            "id": 3,
            "course_id": 2,
            "title": "Algorithms Assignment",
            "deadline": "2026-08-20T18:00:00.000000Z",
            "estimated_hours": "2.00",
            "priority": "mid",
            "status": "pending",
            "completed_at": null,
            "course": {
                "id": 2,
                "name": "Algorithms",
                "code": "CS201"
            }
        }
    ]
}
```

The endpoint:

- Returns only the authenticated user's tasks.
- Excludes tasks with a past deadline.
- Excludes completed tasks.
- Orders tasks by the nearest deadline first.

---

## Delete Task

Deletes a specific task belonging to the authenticated user.

### Request

```http
DELETE /api/tasks/1
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Successful Response

```json
{
    "status": 200,
    "message": "Task deleted successfully",
    "data": null
}
```

---

## Task Authorization

Users can only access, update, complete, or delete their own tasks.

The ownership chain is:

```text
Authenticated User
       │
       ↓
     Course
       │
       ↓
      Task
```

For example:

```text
User 1
 ├── Course 1
 │    └── Task 1
 └── Course 2
      └── Task 2
```

User 1 can access Task 1 and Task 2.

If another user owns the course:

```text
User 2
 └── Course 3
      └── Task 3
```

User 1 cannot access Task 3.

Unauthorized access should return:

```json
{
    "status": 403,
    "message": "Unauthorized access to this task",
    "data": null
}
```

---


# Data Export

The backend provides a data export system for the **Data Science team**.

The system exports available database tables as CSV files, packages them into a ZIP file, and provides them through a protected API endpoint.

## Data Export Flow

```text
MySQL Database
      ↓
CsvExportService
      ↓
CSV Files
      ↓
ZIP File
      ↓
GET /api/data-export/all
      ↓
Data Science Team
      ↓
Python + Pandas
```

## Automatic CSV Export

The database export is scheduled to run every **5 minutes**.

Laravel Scheduler:

```php
Schedule::command('database:export-csv')
    ->everyFiveMinutes();
```

Server cron:

```cron
* * * * * cd /var/www/html/Backend && php artisan schedule:run >> /dev/null 2>&1
```

Check scheduled tasks:

```bash
php artisan schedule:list
```

## Currently Exported Tables

```text
users.csv
courses.csv
tasks.csv
```

> `notifications.csv` is not included yet because the Notifications backend is still under development.

## Data Export API

| Method | Endpoint | Authentication | Description |
| ------ | -------- | -------------- | ----------- |
| GET | `/api/data-export/all` | Yes | Download all available datasets as a ZIP file |

### Request

```http
GET /api/data-export/all
Accept: application/zip
Authorization: Bearer YOUR_TOKEN
```

The endpoint requires a valid **Laravel Sanctum Bearer Token**.

### Response

The API returns:

```text
planora_dataset.zip
```

Containing:

```text
planora_dataset.zip
│
├── users.csv
├── courses.csv
└── tasks.csv
```

---

# Data Science Team

The Data Science team can download the complete dataset using a single request.

A Python script is provided:

```text
test.py
```

## Install Python Dependencies

Create a virtual environment:

```bash
python3 -m venv .venv
```

Activate it:

```bash
source .venv/bin/activate
```

Install dependencies:

```bash
pip install requests pandas
```

## Run the Export Script

```bash
python test.py
```

The script:

1. Sends a request to the Data Export API.
2. Authenticates using a Sanctum Bearer Token.
3. Downloads the ZIP file.
4. Extracts the CSV files.
5. Saves the files locally inside the `data/` directory.

Result:

```text
data/
├── users.csv
├── courses.csv
└── tasks.csv
```

## Data Export Architecture

```text
                    MySQL
                      │
                      ▼
               Laravel Backend
                      │
                      ▼
              CsvExportService
                      │
                      ▼
                 CSV Files
                      │
                Every 5 Minutes
                      │
                      ▼
              Data Export API
                      │
                      ▼
          GET /api/data-export/all
                      │
                      ▼
                 ZIP File
                      │
                      ▼
           Data Science Team
                      │
                      ▼
                 Python
                      │
                      ▼
                  Pandas
                      │
                      ▼
                ML / Analysis
```

## Updating the Dataset

Run:

```bash
python test.py
```

Process:

```text
Run test.py
    ↓
GET /api/data-export/all
    ↓
Download latest ZIP
    ↓
Extract CSV files
    ↓
Replace local dataset
    ↓
Ready for Data Science / ML
```

---

# Security

The Data Export endpoint is protected using Laravel Sanctum:

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get(
        '/data-export/all',
        [DataExportController::class, 'downloadAll']
    );
});
```

Do not commit sensitive files or generated datasets to GitHub.

Add to `.gitignore`:

```gitignore
.env
.venv/
/data/
*.zip
```

The exported CSV files may contain real database data and must not be uploaded to GitHub.


---

# Branching Strategy

We follow a three-tier branching model:

```text
feature/<name> → dev → main
```

### Branches

- **`main`** — production-ready code only. Always stable and deployable.
- **`dev`** — integration branch. All finished features are merged here first for testing before going to `main`.
- **`feature/<name>`** — individual work branches. Each team member works in their own feature branch.

### Workflow

1. **Create your feature branch**

```bash
git checkout dev
git pull origin dev
git checkout -b feature/<name>
```

2. **Work and commit on your feature branch**

```bash
git add .
git commit -m "Add <feature>"
```

3. **Push your feature branch**

```bash
git push origin feature/<name>
```

4. **Open a Pull Request into `dev`**

Do **not** open the Pull Request directly into `main`.

5. **After review and approval**, merge the feature into `dev`.

6. **After `dev` is stable and fully tested**, the team lead opens a Pull Request:

```text
dev → main
```

---

## Branching Rules

- Never commit directly to `main` or `dev`.
- Always pull the latest `dev` before creating a feature branch.
- Each feature should have its own branch.
- Open a Pull Request for every merge.
- Do not push directly to shared branches.
- Resolve merge conflicts locally before opening a Pull Request.
- Test your feature before creating the Pull Request.
- Keep commits clear and related to one feature or change.
