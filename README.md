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
