# Student Portfolio & FYP Showcase System

A basic PHP + MySQL project for a local Laragon environment.

## Requirements
- Laragon with Apache and MySQL/MariaDB
- PHP with `mysqli` and `fileinfo` enabled
- HeidiSQL (included with many Laragon installations)
- Internet connection for the Bootstrap CDN, or download Bootstrap locally

## Setup
1. Copy the `MiniProject2` folder to `C:\laragon\www\`.
2. Start Apache and MySQL in Laragon.
3. Open HeidiSQL and run/import `database.sql`.
4. Check `db.php` and adjust the database username/password if your local setup differs.
5. Log in at `http://localhost/MiniProject2/login.php` as the default admin:
   - Email: `admin@gmail.com`
   - Password: `1234`
   (The account is created automatically if it does not exist.)
6. As Admin, create assignments.
7. Register a Student account at `register.php` to test live search and submissions.

> The default admin password is for local learning only. Change it before any real deployment.

## Pages
- `register.php` — Student registration
- `login.php` / `logout.php` — Authentication
- `admin_dashboard.php` — Admin overview
- `create_assignment.php` — Admin creates assignments
- `view_submissions.php` — Admin views and downloads submissions
- `student_dashboard.php` — Student AJAX live search
- `submit_assignment.php` — Student picks an assignment and uploads PDF/DOCX/TXT (5 MB maximum)
- `my_submissions.php` — Student views their own submission list

## Notes
- Public registration always creates Student accounts. The only built-in Admin is `admin@gmail.com`.
- Uploaded files are stored in `uploads/` under random names; original names are kept in the database.
- This is a learning project. For a real deployment, use HTTPS, CSRF tokens, stronger upload isolation, rate limiting, and production-grade error logging.
