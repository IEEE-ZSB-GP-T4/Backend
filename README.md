# Planora Backend

## Table of Contents

- [Prerequisites](#prerequisites)
- [Setup & Installation](#setup--installation)
  - [Run Without Docker](#run-without-docker)
  - [Run With Docker](#run-with-docker)
- [Useful Docker Commands](#useful-docker-commands)
- [Authentication](#authentication)
- [Courses](#courses)
- [Tasks](#tasks)
- [Notifications](#notifications)
- [Study Plan](#study-plan)
- [Ai Integration](#Ai-integration-using-URL)
- [Data Export](#data-export)
- [Data Science Integration](#data-science-integration)
- [Security](#security)
- [Branching Strategy](#branching-strategy)

---

# Prerequisites

## Without Docker

- PHP 8.3+
- Composer 2.9+
- Laravel 12.x
- MySQL 8.0+

## With Docker

- Docker
- Docker Compose

---

# Setup & Installation

## Run Without Docker

### 1. Clone the Repository

```bash
git clone https://github.com/IEEE-ZSB-GP-T4/Backend.git
cd Backend
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Configure Environment Variables

```bash
cp .env.example .env
```

Configure MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_study_planner
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Generate the Application Key

```bash
php artisan key:generate
```

### 5. Install API Authentication

```bash
php artisan install:api
```

> Skip this step if Sanctum is already installed and configured.

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Start the Server

```bash
php artisan serve
```

```text
http://127.0.0.1:8000
```

---

## Run With Docker

### 1. Clone the Repository

```bash
git clone https://github.com/IEEE-ZSB-GP-T4/Backend.git
cd Backend
```

### 2. Create the Environment File

```bash
cp .env.example .env
```

For Docker, the database host should be the MySQL service name:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=smart_study_planner
DB_USERNAME=laravel
DB_PASSWORD=laravel
```

> Make sure the database credentials match your `docker-compose.yml`.

### 3. Build and Start Containers

```bash
docker compose up -d --build
```

### 4. Install Dependencies

```bash
docker compose exec app composer install
```

### 5. Generate Application Key

```bash
docker compose exec app php artisan key:generate
```

### 6. Run Migrations

```bash
docker compose exec app php artisan migrate
```

### 7. Clear Cache

```bash
docker compose exec app php artisan optimize:clear
```

Access the application using the port configured in `docker-compose.yml`, for example:

```text
http://localhost
```

or:

```text
http://127.0.0.1:8000
```

---

# Useful Docker Commands

### Start

```bash
docker compose up -d
```

### Stop

```bash
docker compose down
```

### Rebuild

```bash
docker compose up -d --build
```

### Check Containers

```bash
docker compose ps
```

### View Logs

```bash
docker compose logs app
```

### Access Application Container

```bash
docker compose exec app bash
```

### Run Artisan Commands

```bash
docker compose exec app php artisan <command>
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
# Notifications

The Notifications API allows authenticated users to view, read, and manage system-generated notifications.

Notifications are **not created directly by users**. They are generated automatically by the system in two ways:

1. **Scheduled deadline reminders** — a background job checks tasks nearing their deadline and notifies the task's owner.
2. **Next-task notifications** — triggered automatically when a user completes a task.

Every notification is also sent as an email to the user, in addition to appearing in-app.

```text
User
  │
  │ 1
  │
  └──────────< Notifications
```

All Notifications endpoints require authentication using Laravel Sanctum.

Send the token using:

```http
Authorization: Bearer YOUR_TOKEN
```

---

## Notification Endpoints

| Method | Endpoint                       | Authentication | Description                          |
| ------ | ------------------------------- | --------------- | ------------------------------------- |
| GET    | `/api/notifications`            | Yes              | Get all of the user's notifications   |
| GET    | `/api/notifications/{id}`       | Yes              | Get a specific notification           |
| PATCH  | `/api/notifications/{id}/read`  | Yes              | Mark a notification as read           |
| DELETE | `/api/notifications/{id}`       | Yes              | Delete a specific notification        |

---

## Get All Notifications

Returns all notifications belonging to the authenticated user, most recent first.

### Request

```http
GET /api/notifications
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Successful Response

```json
{
    "status": 200,
    "message": "Notifications retrieved successfully",
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "title": "Reminder: Task deadline approaching",
            "body": "Your task \"Study SQL Joins\" is due tomorrow (2026-08-15 18:00)",
            "is_read": false,
            "created_at": "2026-08-14T18:00:00.000000Z"
        }
    ]
}
```

---

## Get Specific Notification

Returns a specific notification belonging to the authenticated user.

### Request

```http
GET /api/notifications/1
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Successful Response

```json
{
    "status": 200,
    "message": "Notification retrieved successfully",
    "data": {
        "id": 1,
        "user_id": 1,
        "title": "Reminder: Task deadline approaching",
        "body": "Your task \"Study SQL Joins\" is due tomorrow (2026-08-15 18:00)",
        "is_read": false,
        "created_at": "2026-08-14T18:00:00.000000Z"
    }
}
```

---

## Mark Notification as Read

Marks a specific notification as read.

### Request

```http
PATCH /api/notifications/1/read
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

No request body is required.

### Successful Response

```json
{
    "status": 200,
    "message": "Notification marked as read",
    "data": {
        "id": 1,
        "user_id": 1,
        "title": "Reminder: Task deadline approaching",
        "body": "Your task \"Study SQL Joins\" is due tomorrow (2026-08-15 18:00)",
        "is_read": true,
        "created_at": "2026-08-14T18:00:00.000000Z"
    }
}
```

---

## Delete Notification

Deletes a specific notification belonging to the authenticated user.

### Request

```http
DELETE /api/notifications/1
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Successful Response

```json
{
    "status": 200,
    "message": "Notification deleted",
    "data": null
}
```

---

## Notification Authorization

Users can only view, mark as read, or delete their own notifications, enforced via a `NotificationPolicy`.

Unauthorized access returns:

```json
{
    "status": 403,
    "message": "This action is unauthorized",
    "data": null
}
```

---

## Automatic Deadline Reminders

Notifications for approaching deadlines are generated automatically by a scheduled command — they are **not** triggered through the API.

| Reminder | Trigger window | Sent once per task per reminder type |
| -------- | --------------- | -------------------------------------- |
| First reminder | Task deadline is within the next 24 hours | Yes (tracked via `reminder_sent_at`) |
| Second reminder | Task deadline is within the next 2 hours | Yes (tracked via `second_reminder_sent_at`) |

Completed tasks (`status = completed`) are excluded from reminders.

### Command

```bash
php artisan notifications:send-deadline-reminders
```

### Schedule

Registered in `routes/console.php` to run automatically every hour:

```php
Schedule::command('notifications:send-deadline-reminders')->hourly();
```

Running the command hourly (instead of once daily) is what allows the 2-hour-before reminder to be caught accurately, since it depends on the exact time remaining before the deadline, not just the day.

---

## Automatic "Next Task" Notification

When a task is marked as completed via `PATCH /api/tasks/{id}/complete`, the system automatically looks up the user's next incomplete task and sends a notification about it.

**Next task selection logic:**
1. Nearest upcoming `deadline` first.
2. If multiple tasks share the same deadline, the one with the higher `priority` (`high` > `mid` > `low`) is chosen.
3. Completed tasks are excluded.
4. If the user has no other incomplete tasks, no notification is sent.

This logic lives in `TaskController@notifyNextTask` and reuses the same `NotificationService` used by the deadline reminders, so every notification — regardless of source — is recorded in the database and emailed consistently.

---

## Email Delivery

Every notification sent via `NotificationService::send()` is both:
1. Saved to the `notifications` table (visible via the endpoints above / the in-app bell icon).
2. Emailed to the user using the `NotificationMail` mailable (`resources/views/emails/notification.blade.php`).

During development, `MAIL_MAILER` is set to `log` in `.env`, so emails are written to `storage/logs/laravel.log` instead of being sent to a real inbox. Before deployment, this should be switched to a real mail driver (e.g. Mailtrap for staging, or a production SMTP provider).

---

## Implementation Details

This section lists every file created or modified while building the Notifications feature, for reference during code review or future maintenance.

### New Files Created

| File | Purpose |
| ---- | ------- |
| `database/migrations/..._create_notifications_table.php` | Creates the `notifications` table. |
| `database/migrations/..._add_reminder_sent_at_to_tasks_table.php` | Adds `reminder_sent_at` (nullable timestamp) to `tasks`, used to prevent duplicate 24-hour reminders. |
| `database/migrations/..._add_second_reminder_sent_at_to_tasks_table.php` | Adds `second_reminder_sent_at` (nullable timestamp) to `tasks`, used to prevent duplicate 2-hour reminders. |
| `app/Models/Notification.php` | Eloquent model for notifications. `$timestamps = false` (only `created_at` exists, no `updated_at`), `is_read` cast to boolean. |
| `app/Policies/NotificationPolicy.php` | Authorization rules (`view`, `update`, `delete`) — a notification can only be accessed by the user it belongs to. |
| `app/Http/Controllers/NotificationController.php` | Handles `index`, `show`, `markAsRead`, `destroy` for the notification endpoints. Uses `$this->authorize()` with `NotificationPolicy`. |
| `app/Services/NotificationService.php` | Central place all notifications are created from. `NotificationService::send($user, $title, $body)` creates the DB record **and** sends the email in one call, so every part of the codebase that needs to notify a user goes through the same logic. |
| `app/Mail/NotificationMail.php` | Mailable class that defines the email's subject and view. |
| `resources/views/emails/notification.blade.php` | HTML template used for every notification email. |
| `app/Console/Commands/SendTaskDeadlineReminders.php` | Artisan command (`notifications:send-deadline-reminders`). Checks all tasks for two conditions and sends reminders accordingly: <br>• Deadline within 24 hours → first reminder (marks `reminder_sent_at`)<br>• Deadline within 2 hours → second reminder (marks `second_reminder_sent_at`)<br>Excludes completed tasks and tasks that already received each reminder type. |

### Existing Files Modified

| File | What changed |
| ---- | ------------- |
| `app/Models/User.php` | Added `notifications(): HasMany` relationship. |
| `app/Models/Task.php` | Added `reminder_sent_at` and `second_reminder_sent_at` to `$fillable` and `$casts`. |
| `app/Http/Controllers/TaskController.php` | `complete()` now calls a new private method `notifyNextTask()` after marking a task complete. This method finds the user's next incomplete task (nearest deadline first, then highest priority) and sends a notification via `NotificationService`. |
| `routes/api.php` | Registered the four Notification routes (`GET /notifications`, `GET /notifications/{id}`, `PATCH /notifications/{id}/read`, `DELETE /notifications/{id}`) inside the existing `auth:sanctum` middleware group. |
| `routes/console.php` | Registered the scheduler: `Schedule::command('notifications:send-deadline-reminders')->hourly()`. Runs hourly (not daily) so the 2-hour-before reminder is caught accurately. |
| `bootstrap/app.php` | Added centralized exception handling (`withExceptions`) so `AuthenticationException`, `AuthorizationException` (used by the Notification policy), `ModelNotFoundException`, `NotFoundHttpException`, `ValidationException`, and any other `Throwable` all return a consistent `ApiResponse` JSON shape instead of Laravel's default error pages. This affects error responses project-wide, not just Notifications. |
| `.env` | `MAIL_MAILER` left as `log` for local development. **To be changed before deployment** — see "Email Delivery" above. |

### Database Schema: ERD vs. Actual Implementation

The original ERD (shared at the start of the project) specified the `Notifications` table like this:

```text
Notifications (ERD)
├── id            int
├── user_id       int
├── title         varchar
├── body          varchar
├── is_read       enum('yes','no')
└── created_at    timestamp
```

**What we actually implemented is not identical** — two deliberate changes were made, plus two columns were added to `tasks` that don't appear in the ERD at all:

| # | Difference | ERD said | We implemented | Why |
| - | ---------- | -------- | ---------------- | --- |
| 1 | `is_read` type | `enum('yes','no')` | `boolean`, default `false` | Booleans are smaller, faster to query (`where('is_read', false)` vs `where('is_read', 'no')`), and Eloquent casts them automatically — the API still returns `true`/`false` in JSON either way, so this is invisible to the frontend. |
| 2 | `updated_at` | Not present in ERD | Still not present — `Notification` model sets `$timestamps = false` | Matches the ERD's intent: notifications are never edited after creation, only read or deleted, so there's nothing to "update." |
| 3 | `tasks.reminder_sent_at` | **Not in the ERD** | Added (nullable timestamp) | Needed to track whether the "1 day before deadline" reminder was already sent for a task, so the scheduled command doesn't send it twice. |
| 4 | `tasks.second_reminder_sent_at` | **Not in the ERD** | Added (nullable timestamp) | Same reasoning as #3, but for the "2 hours before deadline" reminder. |

**Actual `notifications` table:**

```text
notifications
├── id            bigint, primary key
├── user_id       bigint, FK → users.id, cascade on delete
├── title         varchar
├── body          varchar
├── is_read       boolean, default false
└── created_at    timestamp   (no updated_at — see difference #2 above)
```

**Actual changes to the existing `tasks` table:**

```text
tasks
├── ... (all original columns unchanged)
├── reminder_sent_at         timestamp, nullable   ← new (difference #3)
└── second_reminder_sent_at  timestamp, nullable   ← new (difference #4)
```

If anyone updates the master ERD diagram, these four differences are what need to be reflected in it.

### Design Decisions Worth Knowing

- **No `POST /api/notifications` endpoint exists on purpose.** Notifications are only ever created by the system (scheduled reminders, task completion), never directly by a user request. This keeps notification creation centralized and predictable.
- **`is_read` is a boolean**, not the `enum('yes','no')` shown in the original ERD, for cleaner querying (`where('is_read', false)`) and smaller storage. Functionally equivalent.
- **The two reminder "sent" columns live on `tasks`, not `notifications`**, because they represent task state ("has this task's reminder already gone out"), not notification state.
- **The hourly schedule is a deliberate trade-off.** A daily schedule would be simpler but could miss the exact 2-hour window before a deadline. Hourly checking balances accuracy with simplicity; a more precise (e.g. every-minute) schedule was considered unnecessary for this project's scope.
---

# Study Plan

The Study Plan API lets an authenticated user select a set of their pending tasks, specify how many hours they have available to study per day, and get back an AI-generated day-by-day study schedule — built by distributing the selected tasks' estimated hours across the days leading up to each task's deadline, prioritizing earlier deadlines and higher priority.

Every time a plan is requested, it's saved as a **new history entry** rather than overwriting the previous one — so a user can look back at plans they generated in the past (useful for the Performance Analytics feature). Editing or deleting a task afterwards does not retroactively change a plan already generated, since each plan stores a snapshot of the tasks at the moment it was created.

```text
User
  │
  │ 1
  │
  └──────────< Study_plans
```

All Study Plan endpoints require authentication using Laravel Sanctum.

Send the token using:

```http
Authorization: Bearer YOUR_TOKEN
```

---

## Study Plan Endpoints

| Method | Endpoint                  | Authentication | Description                                              |
| ------ | -------------------------- | --------------- | ---------------------------------------------------------- |
| GET    | `/api/study-plan/tasks`    | Yes              | Get the user's courses with their pending tasks, for building a selection checklist |
| POST   | `/api/study-plan`          | Yes              | Generate a new study plan from selected tasks              |
| GET    | `/api/study-plan`          | Yes              | Get the user's most recently generated plan                |
| GET    | `/api/study-plan/history`  | Yes              | Get all of the user's previously generated plans           |

---

## Get Tasks for Checklist

Returns the authenticated user's courses, each with its non-completed tasks nested inside — intended for rendering a task-selection checklist (checkboxes) on the frontend before generating a plan.

### Request

```http
GET /api/study-plan/tasks
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
            "id": 2,
            "name": "Database Systems",
            "code": "CS301",
            "tasks": [
                {
                    "id": 5,
                    "title": "Quick Quiz Review",
                    "deadline": "2026-08-14T10:00:00.000000Z",
                    "estimated_hours": "6.00",
                    "priority": "high",
                    "status": "pending"
                }
            ]
        }
    ]
}
```

Completed tasks are excluded automatically.

---

## Generate Study Plan

Generates a new AI-assisted study plan from a chosen set of tasks and a daily available-hours budget, and saves it as a new history entry.

### Request

```http
POST /api/study-plan
Content-Type: application/json
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Body

```json
{
    "available_hours": 2,
    "task_ids": [4, 5, 6]
}
```

### Validation

| Field | Type | Required | Rules |
| ----- | ---- | -------- | ----- |
| `available_hours` | numeric | Yes | Between 0.5 and 16 |
| `task_ids` | array | Yes | At least 1 item |
| `task_ids.*` | integer | Yes | Must exist in `tasks` and belong to a course owned by the authenticated user |

### How the schedule is built

- Tasks are sorted by nearest deadline first, then by higher priority (`high` > `mid` > `low`) as a tiebreaker.
- The plan covers every day from today until the **latest** deadline among the selected tasks — so all selected tasks fit somewhere in the schedule.
- Each day is filled up to `available_hours`, splitting a task's remaining hours across multiple days if needed.
- A task is never scheduled *after* its own deadline.
- If the available hours aren't enough to fully schedule a task before its deadline, the task is **not** silently dropped — it's listed in a `warnings` array explaining how many hours are still missing.

### Successful Response

```json
{
    "status": 201,
    "message": "Study plan generated successfully",
    "data": {
        "id": 2,
        "available_hours": 2,
        "generated_plan": {
            "days": [
                {
                    "date": "2026-08-13",
                    "sessions": [
                        { "task_id": 5, "title": "Quick Quiz Review", "hours": 2 }
                    ]
                }
            ],
            "warnings": [
                {
                    "task_id": 5,
                    "title": "Quick Quiz Review",
                    "hours_missing": 2,
                    "message": "\"Quick Quiz Review\" won't be fully covered before its deadline with the available hours."
                }
            ]
        },
        "created_at": "2026-08-13T04:22:25.000000Z"
    }
}
```

---

## Get Latest Study Plan

Returns the authenticated user's most recently generated plan.

### Request

```http
GET /api/study-plan
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Successful Response

Same shape as the "Generate Study Plan" response above.

### If no plan exists yet

```json
{
    "status": 404,
    "message": "No study plan found yet",
    "data": null
}
```

---

## Get Study Plan History

Returns all of the authenticated user's previously generated plans, most recent first.

### Request

```http
GET /api/study-plan/history
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Successful Response

```json
{
    "status": 200,
    "message": "Study plan history retrieved successfully",
    "data": [
        {
            "id": 2,
            "available_hours": 2,
            "generated_plan": { "days": [ /* ... */ ], "warnings": [ /* ... */ ] },
            "created_at": "2026-08-13T04:22:25.000000Z"
        }
    ]
}
```

---

## Study Plan Authorization

A user can only ever generate, view, or list their own study plans — enforced by scoping every query through `$request->user()->studyPlans()` and by validating that every submitted `task_id` belongs to a course owned by the authenticated user.

---

## AI Integration (Grok)

Plan generation is handled by a swappable service, following the same pattern used elsewhere in the project (e.g. `MAIL_MAILER=log` for Notifications):

- **`StudyPlanGeneratorService`** — the real implementation. Sends the selected tasks and available hours to the Grok API and parses its JSON response into the plan shape shown above.
- **`FakeStudyPlanGeneratorService`** — a local, deterministic implementation that runs the same day-by-day allocation logic in PHP, with no external API call. Used for development and testing while a Grok API key isn't available yet, and useful afterwards for fast local testing without hitting the real API.

`StudyPlanController` is currently wired to use `FakeStudyPlanGeneratorService`. Switching to the real Grok service once an API key is available is a one-line change in the controller's constructor (marked with a `TODO` comment).

### Required environment variables

```env
# AI Planner URL
AI_PLANNER_URL=https://detest-eggnog-process.ngrok-free.dev
```
---

## Database Schema: Study Plans

Unlike Notifications, the `study_plans` table matches the original ERD exactly — no changes were needed:

```text
study_plans
├── id                bigint, primary key
├── user_id           bigint, FK → users.id, cascade on delete
├── available_hours   decimal(4,1)
├── generated_plan    json
├── created_at        timestamp
└── updated_at        timestamp
```

`available_hours` deliberately stores a single number (applied the same for every day), not a per-weekday breakdown — this was a considered simplification, not an oversight.

---

## Implementation Details: Study Plan

### New Files Created

| File | Purpose |
| ---- | ------- |
| `database/migrations/..._create_study_plans_table.php` | Creates the `study_plans` table. |
| `app/Models/StudyPlan.php` | Eloquent model. `generated_plan` cast to `array` for automatic JSON encode/decode. |
| `app/Http/Requests/StoreStudyPlanRequest.php` | Validates `available_hours` and `task_ids`, and ensures every task ID belongs to the authenticated user. |
| `app/Http/Controllers/StudyPlanController.php` | Handles `tasksForChecklist`, `store`, `index`, and `history`. |
| `app/Http/Resources/StudyPlanResource.php` | Formats a study plan for API responses. |
| `app/Services/StudyPlanGeneratorService.php` | Real Grok-backed plan generator. |
| `app/Services/FakeStudyPlanGeneratorService.php` | Deterministic local generator used during development/testing. |
| `config/grok.php` | Reads Grok API settings from `.env`. |

### Existing Files Modified

| File | What changed |
| ---- | ------------- |
| `app/Models/User.php` | Added `studyPlans(): HasMany` relationship. |
| `routes/api.php` | Registered the four Study Plan routes inside the existing `auth:sanctum` middleware group. |
| `.env.example` | Added `GROK_API_KEY`, `GROK_API_URL`, `GROK_MODEL` (all empty/default — no real key committed). |

### Design Decisions Worth Knowing

- **Plans are never overwritten** — every generation request creates a new row, preserving history for future analytics and letting a user compare past plans to what actually happened.
- **A plan is a snapshot.** It stores task titles/hours as they were at generation time, inside the `generated_plan` JSON — so later editing or deleting a task doesn't retroactively change a plan that already included it.
- **The plan's length is driven by the furthest deadline among the selected tasks**, not a fixed number of days — this guarantees every selected task has somewhere to be scheduled.
- **Warnings instead of silent failure.** If the available hours can't fit a task in before its deadline, it's reported in a `warnings` array rather than being dropped or scheduled late without explanation.
- **The Fake and real generators return the exact same JSON shape** (`days` + `warnings`), so the frontend never needs to know which one is active.



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

# Data Science Integration

The backend is integrated with the local `Data-Science` module to generate analytics for the authenticated user's dashboard.

Instead of requiring the frontend to call Python directly, Laravel exports the latest database data to CSV, runs the data science script, and returns the generated KPIs and Plotly chart configuration through a protected API endpoint.

## Data Science Dashboard Endpoint

| Method | Endpoint | Authentication | Description |
| ------ | -------- | -------------- | ----------- |
| GET | `/api/dashboard/data-science` | Yes | Return data science KPIs and visuals for the authenticated user |

### Request

```http
GET /api/dashboard/data-science
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

### Successful Response

```json
{
    "status": 200,
    "message": "Data science dashboard retrieved successfully",
    "data": {
        "user_id": 1,
        "dashboard": {
            "kpis": {
                "user_id": 1,
                "task_completion_rate": 75,
                "time_utilization_rate": 60,
                "overall_productivity_score": 69
            },
            "visuals": {
                "tasks_status_donut": {},
                "tasks_by_priority_bar": {}
            }
        }
    }
}
```

## How It Works

```text
Authenticated Request
        ↓
GET /api/dashboard/data-science
        ↓
DataScienceDashboardService
        ↓
CsvExportService exports fresh CSV files
        ↓
storage/app/exports
        ↓
Data-Science/src/connect.py USER_ID
        ↓
Python + Pandas + Plotly
        ↓
Laravel returns JSON to frontend
```

The service sets `DATA_DIR` automatically, so the Python code reads from:

```text
storage/app/exports
```

## Required Python Dependencies

The data science module uses the dependencies listed in:

```text
Data-Science/requirements.txt
```

For local development without Docker:

```bash
python3 -m venv .venv
source .venv/bin/activate
pip install -r Data-Science/requirements.txt
```

Then set the Python executable in `.env`:

```env
DATA_SCIENCE_PYTHON=/absolute/path/to/Backend/.venv/bin/python
DATA_SCIENCE_TIMEOUT=60
```

If you want to use the system Python instead:

```env
DATA_SCIENCE_PYTHON=python3
DATA_SCIENCE_TIMEOUT=60
```

## Docker Setup

The Docker image installs Python and creates a dedicated virtual environment:

```text
/opt/data-science-venv/bin/python
```

The container sets:

```env
DATA_SCIENCE_PYTHON=/opt/data-science-venv/bin/python
```

After changing the Dockerfile or Python requirements, rebuild the container:

```bash
docker compose down
docker compose build --no-cache
docker compose up -d
```

Then clear Laravel config cache:

```bash
docker compose exec app php artisan config:clear
```

## Postman Testing

1. Login and copy the Sanctum token:

```http
POST /api/login
Content-Type: application/json
Accept: application/json
```

```json
{
    "email": "your_email@example.com",
    "password": "your_password"
}
```

2. Send the token to the data science endpoint:

```http
GET /api/dashboard/data-science
Accept: application/json
Authorization: Bearer YOUR_TOKEN
```

## Troubleshooting

If the response says `python3: not found`, the app environment cannot find Python. Rebuild the Docker image or set `DATA_SCIENCE_PYTHON` to a valid Python executable.

If the response says `ModuleNotFoundError: No module named 'pandas'`, Laravel is using a Python environment without the data science dependencies. Install the requirements or point `DATA_SCIENCE_PYTHON` to the virtual environment that has them installed.

Useful Docker checks:

```bash
docker compose exec app printenv DATA_SCIENCE_PYTHON
docker compose exec app /opt/data-science-venv/bin/python -c "import pandas; print(pandas.__version__)"
docker compose exec app php artisan config:clear
docker compose restart app
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
