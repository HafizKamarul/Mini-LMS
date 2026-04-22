# Mini-LMS

Mini-LMS is a Laravel 12 based learning management system for timed online exams with role-based access:
- Admin: manage exams, question bank, question import/export, and result oversight.
- Student: take exams, submit answers, and view published detailed results.

## Tech Stack
- PHP 8.2
- Laravel 12
- MySQL (XAMPP compatible)
- Laravel Sanctum (token authentication for API)
- Tailwind CSS + Vite

## Quick Setup
1. Clone and install dependencies:

```bash
composer install
npm install
```

2. Configure environment:

```bash
copy .env.example .env
php artisan key:generate
```

3. Configure database in `.env` (example for XAMPP):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mini_lms
DB_USERNAME=root
DB_PASSWORD=
```

4. Run migration and seed default users:

```bash
php artisan migrate --seed
```

5. Build frontend assets:

```bash
npm run build
```

6. Start local server:

```bash
php artisan serve
```

Then open: http://127.0.0.1:8000

## Seeded Login Credentials
- Admin:
  - Email: admin@mini-lms.test
  - Password: password
- Student:
  - Email: student@mini-lms.test
  - Password: password

Defined in: `database/seeders/DatabaseSeeder.php`.

## How To Test (Admin)
1. Login as Admin.
2. Create an exam from Admin > Exams.
3. Add questions:
- Add manually (MCQ or Subjective), or
- Bulk upload CSV/XLS/XLSX, or
- Reuse from Question Bank.
4. Publish the exam and set future `starts_at` to verify it appears in Upcoming Exams on dashboards.
5. Set start time to now/past to verify it moves to Ongoing/Available.
6. Review results from Admin > Results after student submits.

## How To Test (Student)
1. Login as Student.
2. Open Student Dashboard:
- Available Exams: published and active now.
- Upcoming Exams: published but not yet started.
3. Start an available exam and answer questions.
4. Submit exam:
- Empty submission is blocked.
- Double submission is blocked.
5. View result details when published.

## Basic API Docs (Sanctum)
Base URL:

```text
http://127.0.0.1:8000/api/v1
```

### 1) Login (get token)
`POST /login`

Request:

```json
{
  "email": "student@mini-lms.test",
  "password": "password",
  "device_name": "postman"
}
```

Response includes `access_token`.

### 2) Logout (revoke current token)
`POST /logout`

Header:

```text
Authorization: Bearer <access_token>
Accept: application/json
```

### 3) List exams
`GET /exams`

Header:

```text
Authorization: Bearer <access_token>
Accept: application/json
```

### 4) Submit result
`POST /results/{exam_id}`

Header:

```text
Authorization: Bearer <access_token>
Accept: application/json
Content-Type: application/json
```

Request sample:

```json
{
  "answers": [
    {
      "question_id": 1,
      "question_option_id": 3,
      "is_flagged": false
    },
    {
      "question_id": 2,
      "answer_text": "inheritance and polymorphism",
      "is_flagged": true
    }
  ]
}
```

Rules:
- Empty submission is rejected.
- Double submission returns conflict.

### 5) Student transcript
`GET /student/{id}/transcript`

Header:

```text
Authorization: Bearer <access_token>
Accept: application/json
```

Authorization:
- Student can view own transcript.
- Admin can view any student transcript.

## Postman (Bonus)
Create a collection named `Mini-LMS API` and add these requests:
- `POST /api/v1/login`
- `POST /api/v1/logout`
- `GET /api/v1/exams`
- `POST /api/v1/results/{exam_id}`
- `GET /api/v1/student/{id}/transcript`

Set a collection variable `baseUrl` = `http://127.0.0.1:8000`.
Use test script on login to save token:

```javascript
pm.collectionVariables.set("token", pm.response.json().data.access_token);
```

Use header on protected routes:

```text
Authorization: Bearer {{token}}
```

## Testing Guide
### Manual Regression Checklist
- Auth:
  - Admin and student can login.
  - API login returns token.
- Exam lifecycle:
  - Future published exam appears in Upcoming (Admin + Student).
  - Exam becomes Available/Ongoing after start time.
- Question management:
  - Manual create/update works.
  - Import file type validation blocks invalid files.
  - Question Bank stores and reuses questions.
- Submission validation:
  - Empty submit is blocked.
  - Double submit is blocked.
- Results:
  - Student sees own published details.
  - Transcript API authorization works.

### Run Automated Tests
```bash
php artisan test
```

If needed, clear cached config/routes before retesting:

```bash
php artisan optimize:clear
```
