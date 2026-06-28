# Restaurant Menu Management System

PHP + MySQL + Bootstrap 5 mini-project. An admin can register, log in, and
manage a restaurant menu (add / edit / delete items with food images),
browse it as cards and as an animated table, and export it to Excel.

## Tech
- PHP 8 (MySQLi, prepared statements)
- MariaDB / MySQL
- Bootstrap 5 + Bootstrap Icons (CDN)
- Custom CSS animation in `assets/css/style.css`

## Setup (XAMPP)

1. Copy this folder into `C:\xampp\htdocs\` (it already lives in
   `PHP Restaurant Management`).
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Create the database — either:
   - open **http://localhost/PHP%20Restaurant%20Management/setup.php** once, or
   - import `database.sql` in phpMyAdmin.
4. Visit **http://localhost/PHP%20Restaurant%20Management/login.php**.

### Demo login (created by setup.php)
- **Email:** `admin@demo.com`
- **Password:** `admin123`

You can also create your own account on the Register page.

## Database port note
On this machine port **3306** was already taken by another MySQL instance, so
XAMPP's MariaDB runs on **3307**. That is set in `config/db.php` (`$DB_PORT`).
If your MySQL is on the standard port, change `$DB_PORT` back to `3306`
(and `$DB_HOST` to `localhost`).

## Files
| File | Requirement |
|------|-------------|
| `register.php` | 3.1 Registration (`password_hash`) |
| `login.php` | 3.2 Login (`password_verify`) |
| `config/auth.php` | 3.3 Session handling / page protection |
| `includes/header.php` | 3.4 Navbar & Logout |
| `add.php` / `edit.php` / `delete.php` | 3.5 Menu CRUD |
| `add.php` (`handleImageUpload`) | 3.6 Image upload → `uploads/` |
| `index.php` | 3.7 Card display + 3.9 Bootstrap carousel |
| `view.php` + `assets/css/style.css` | 3.8 Table with animated gradient + hover |
| `export.php` | 3.10 Excel export |
| `database.sql` / `setup.php` | 5. Database design (`users`, `menu_items`) |

## Security
- Passwords stored as bcrypt hashes (`password_hash` / `password_verify`).
- All queries use MySQLi **prepared statements**.
- Output escaped with `htmlspecialchars` to prevent XSS.
- Uploads validated by extension and size (max 2 MB) and renamed uniquely.
- Internal pages guarded by `config/auth.php`.

> `setup.php` is a convenience for first run — you can delete it afterwards.
