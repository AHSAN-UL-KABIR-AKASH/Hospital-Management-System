# Cancer Care — Setup Instructions (XAMPP)

Cancer Care is a cancer-patient donation & fundraising platform built with
**HTML5, CSS3, Vanilla JavaScript, PHP, and MySQL only** (no frameworks).

## 1. Requirements
- XAMPP (Apache + MySQL + PHP 8+)

## 2. Install the project
1. Copy the entire `cancer-care` folder into `C:\xampp\htdocs\` so the path is:
   `C:\xampp\htdocs\cancer-care\`
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.

## 3. Create the database
1. Open `http://localhost/phpMyAdmin`.
2. Click **Import**, choose `database.sql` from the project folder, and click **Go**.
   - This creates the `cancer_care` database, all tables, and demo/sample data.
3. Alternatively, run in the MySQL console:
   ```
   mysql -u root -p < database.sql
   ```

## 4. Configure the database connection
Open `config/database.php` and confirm these match your local MySQL setup
(defaults work for a stock XAMPP install):
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cancer_care');
define('DB_USER', 'root');
define('DB_PASS', '');
```

## 5. Set upload folder permissions
Ensure the web server can write to:
- `uploads/patients/`
- `uploads/documents/`

(On Windows/XAMPP this is usually fine by default.)

## 6. Open the site
Visit: `http://localhost/cancer-care/`

## 7. Demo accounts
All demo accounts use the password: **Password123!**

| Role       | Email                     |
|------------|---------------------------|
| Admin      | admin@cancercare.test     |
| Fundraiser | karim@example.com         |
| Fundraiser | fatima@example.com        |
| Donor      | sabbir@example.com        |

## 8. Key features included
- User registration/login with hashed passwords (`password_hash`/`password_verify`), PHP sessions, CSRF protection
- Role-based access: donor, fundraiser, admin
- Create fundraiser campaigns with patient photo + private medical document uploads
- Admin verification workflow (pending → verified/rejected/suspended); documents are
  never publicly accessible — only served via `admin/view_document.php` after admin login
  (also blocked at the web-server level via `uploads/documents/.htaccess`)
- Demo donation system (bKash/Nagad/Rocket/Bank/Card selectors — no real payment processing)
  with server-side amount/progress calculations and transaction IDs
- Campaign search & filters, save/bookmark campaigns, social sharing, report-campaign flow
- Admin dashboard: users, campaigns, verification queue, donations, reports
- Reusable JSON API under `/api` (auth, campaigns, donations, users, admin) so a future
  mobile or PC app can reuse the same PHP backend and MySQL database without rebuilding it

## 9. Notes on the demo payment system
No real payment gateway is integrated. The `payment_method` field in the `donations`
table already anticipates bKash, Nagad, Rocket, bank transfer, and card, so a real
Bangladeshi payment gateway can be wired in later inside `models/Donation.php`
without changing the database schema or the rest of the app.
