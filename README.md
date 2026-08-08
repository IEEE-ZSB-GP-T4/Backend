## Back-End

### Prerequisites

Make sure the following software is installed:

* PHP 8.3+
* Composer 2.9+
* Laravel 12.x
* MySQL 8.0+

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

# Branching Strategy

We follow a three-tier branching model:

```text
feature/<name> → dev → main
```

### Branches

* **`main`** — production-ready code only. Always stable and deployable.
* **`dev`** — integration branch. All finished features are merged here first for testing before going to `main`.
* **`feature/<name>`** — individual work branches. Each team member works in their own feature branch.

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

* Never commit directly to `main` or `dev`.
* Always pull the latest `dev` before creating a feature branch.
* Each feature should have its own branch.
* Open a Pull Request for every merge.
* Do not push directly to shared branches.
* Resolve merge conflicts locally before opening a Pull Request.
* Test your feature before creating the Pull Request.
* Keep commits clear and related to one feature or change.
