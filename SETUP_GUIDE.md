# TounsiVert - Complete Setup Guide

## 🚀 Quick Start Commands

Run these commands in order to set up your TounsiVert platform:

### 1. Navigate to Backend Directory
```bash
cd d:/TounsiVert/backend
```

### 2. Install Dependencies (if not already done)
```bash
composer install
npm install
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Create Database
Create a MySQL database named `tounsivert`:
```sql
CREATE DATABASE tounsivert CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run Migrations
```bash
php artisan migrate
```

### 6. Seed Database with Demo Data
```bash
php artisan db:seed
```

### 7. Create Storage Link
```bash
php artisan storage:link
```

### 8. Build Frontend Assets
```bash
npm run dev
```
*(Keep this running in one terminal)*

### 9. Start Development Server
Open a new terminal and run:
```bash
php artisan serve
```

### 10. Access the Application
Open your browser and visit: **http://localhost:8000**

---

## 📋 Demo User Accounts

After seeding, you can login with these accounts:

### Admin Account
- **Email:** admin@tounsivert.tn
- **Password:** password
- **Role:** Full admin access

### Organizer Account
- **Email:** organizer@tounsivert.tn
- **Password:** password
- **Role:** Can create organizations and events

### Member Account
- **Email:** member@tounsivert.tn
- **Password:** password
- **Role:** Regular user (can participate, donate, review)

---

## 🗂️ Project Structure

### Backend (Laravel 10)
```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php
│   │   │   ├── EventController.php
│   │   │   ├── OrganizationController.php
│   │   │   ├── Member/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ParticipationController.php
│   │   │   │   ├── DonationController.php
│   │   │   │   ├── ReviewController.php
│   │   │   │   └── ReportController.php
│   │   │   ├── Organizer/
│   │   │   │   ├── OrganizerEventController.php
│   │   │   │   └── OrganizerOrganizationController.php
│   │   │   └── Admin/
│   │   │       ├── AdminDashboardController.php
│   │   │       ├── AdminOrganizationController.php
│   │   │       └── AdminReportController.php
│   │   ├── Middleware/
│   │   │   └── AdminMiddleware.php
│   │   └── Policies/
│   │       ├── EventPolicy.php
│   │       └── OrganizationPolicy.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── OrgCategory.php
│   │   ├── Organization.php
│   │   ├── OrganizationSocialLink.php
│   │   ├── EventCategory.php
│   │   ├── Event.php
│   │   ├── Participation.php
│   │   ├── Donation.php
│   │   ├── Review.php
│   │   └── Report.php
│   └── Policies/
├── database/
│   ├── migrations/
│   │   ├── 2014_10_12_000000_create_users_table.php
│   │   ├── 2024_01_01_000001_create_org_categories_table.php
│   │   ├── 2024_01_01_000002_create_organizations_table.php
│   │   ├── 2024_01_01_000003_create_organization_social_links_table.php
│   │   ├── 2024_01_01_000004_create_organization_followers_table.php
│   │   ├── 2024_01_01_000005_create_event_categories_table.php
│   │   ├── 2024_01_01_000006_create_events_table.php
│   │   ├── 2024_01_01_000007_create_participations_table.php
│   │   ├── 2024_01_01_000008_create_donations_table.php
│   │   ├── 2024_01_01_000009_create_reports_table.php
│   │   └── 2024_01_01_000010_create_reviews_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── OrgCategorySeeder.php
│       └── EventCategorySeeder.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php (Laravel Breeze)
│       │   └── public.blade.php (Bootstrap 5)
│       ├── home.blade.php
│       ├── about.blade.php
│       ├── events/
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── organizations/
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       └── member/
│           ├── dashboard.blade.php
│           └── donations/
│               └── create.blade.php
└── routes/
    └── web.php
```

---

## 🎯 Key Features Implemented

### 1. **User Management**
- ✅ Three roles: Member, Organizer, Admin
- ✅ Laravel Breeze authentication
- ✅ User profiles with score tracking

### 2. **Organizations**
- ✅ Organization categories (Charity, NGO, Mosque, etc.)
- ✅ Verification system
- ✅ Social links
- ✅ Follower system

### 3. **Events**
- ✅ Event categories (Food Aid, Tree Planting, etc.)
- ✅ Three types: Online, Onsite, Hybrid
- ✅ Max participants limit
- ✅ Published/unpublished status
- ✅ Filtering by category, region, city, type, date

### 4. **Participations**
- ✅ Attend events (+10 points)
- ✅ Follow events (+1 point)
- ✅ Share events (+2 points)
- ✅ Unique constraint per user/event/type

### 5. **Donations**
- ✅ Linked to participations
- ✅ Status tracking (pending, succeeded, failed, refunded)
- ✅ Score calculation (+1 point per TND)
- ✅ Demo payment flow

### 6. **Reviews**
- ✅ 1-5 star rating system
- ✅ Comments
- ✅ Only participants can review
- ✅ One review per user per event

### 7. **Reports**
- ✅ Report events or organizations
- ✅ Status workflow (open, in_review, resolved, dismissed)
- ✅ Admin moderation

### 8. **Impact Scoring**
- ✅ Automatic score calculation
- ✅ Dashboard display
- ✅ Leaderboard ready

### 9. **Admin Panel**
- ✅ Organization verification
- ✅ Report management
- ✅ Statistics dashboard

### 10. **Frontend (Bootstrap 5)**
- ✅ Responsive design
- ✅ Modern UI with cards and icons
- ✅ Alpine.js for interactivity
- ✅ Public layout for visitors
- ✅ Auth layout from Breeze

---

## 🗄️ Database Schema

### Tables Created (11 total):
1. **users** - User accounts with roles
2. **org_categories** - Organization categories
3. **organizations** - Organizations/NGOs
4. **organization_social_links** - Social media links
5. **organization_followers** - User-Organization follows
6. **event_categories** - Event categories
7. **events** - Community events
8. **participations** - User event interactions
9. **donations** - Financial contributions
10. **reviews** - Event reviews and ratings
11. **reports** - Moderation reports

---

## 🛣️ Available Routes

### Public Routes
- `GET /` - Home page
- `GET /about` - About page
- `GET /events` - Browse events
- `GET /events/{event}` - Event details
- `GET /organizations` - Browse organizations
- `GET /organizations/{organization}` - Organization profile

### Member Routes (Authenticated)
- `GET /dashboard` - Member dashboard
- `POST /events/{event}/attend` - Join event
- `POST /events/{event}/follow` - Follow event
- `POST /events/{event}/share` - Share event
- `GET /events/{event}/donate` - Donation form
- `POST /events/{event}/donate` - Process donation
- `POST /events/{event}/reviews` - Submit review
- `POST /reports` - Submit report

### Organizer Routes
- `GET /organizer/organizations` - My organizations
- `POST /organizer/organizations` - Create organization
- `GET /organizer/events` - My events
- `POST /organizer/events` - Create event
- `PUT /organizer/events/{event}` - Update event
- `DELETE /organizer/events/{event}` - Delete event

### Admin Routes
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/organizations` - Manage organizations
- `POST /admin/organizations/{organization}/verify` - Verify org
- `GET /admin/reports` - Manage reports
- `PATCH /admin/reports/{report}/status` - Update report status

---

## 🎨 Frontend Stack

- **Framework:** Laravel Blade Templates
- **CSS:** Bootstrap 5.3.2
- **Icons:** Bootstrap Icons 1.11.1
- **JS:** Alpine.js 3.x
- **Responsive:** Mobile-first design

---

## 🔧 Configuration

### Environment Variables (.env)
```env
APP_NAME="TounsiVert"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_DATABASE=tounsivert
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public
```

### Important Settings
- Session lifetime: 120 minutes
- File uploads: logos, posters
- Max file size: 2048KB (2MB)
- Pagination: 12-20 items per page

---

## 📊 Impact Score Weights

- **Attend event:** +10 points
- **Donation:** +1 point per 1 TND
- **Follow event:** +1 point
- **Share event:** +2 points

---

## 🚦 Next Steps for Production

1. **Payment Integration**
   - Integrate with payment gateway (Stripe, PayPal, etc.)
   - Update DonationController with real payment flow

2. **Email Notifications**
   - Event reminders
   - Donation receipts
   - Report updates

3. **File Upload Optimization**
   - Image compression
   - CDN integration
   - Better file validation

4. **Advanced Features**
   - Real-time notifications
   - Event calendar view
   - Advanced search with Elasticsearch
   - Mobile app API
   - Multi-language support (Arabic/French)

5. **Security Enhancements**
   - Rate limiting
   - CAPTCHA on forms
   - Two-factor authentication
   - Audit logging

6. **Performance**
   - Database query optimization
   - Caching strategy
   - Queue jobs for heavy operations
   - CDN for static assets

---

## 🐛 Troubleshooting

### Database Connection Error
```bash
# Check MySQL is running
# Verify credentials in .env file
# Create database: CREATE DATABASE tounsivert;
```

### Migration Errors
```bash
# Fresh start
php artisan migrate:fresh --seed
```

### Permission Errors (Storage/Bootstrap)
```bash
# Windows (PowerShell as Admin)
icacls storage /grant "Users:(OI)(CI)F" /T
icacls bootstrap/cache /grant "Users:(OI)(CI)F" /T
```

### NPM Build Errors
```bash
# Clear cache and reinstall
rm -rf node_modules package-lock.json
npm install
npm run dev
```

---

## 📞 Support

For issues or questions:
- Check Laravel documentation: https://laravel.com/docs/10.x
- Bootstrap 5 docs: https://getbootstrap.com/docs/5.3
- Review the code comments in controllers and models

---

## ✅ Checklist

- [x] Laravel 10 installed
- [x] Database migrations created (11 tables)
- [x] Models with relationships created (9 models)
- [x] Controllers created (13 controllers)
- [x] Routes configured (public, member, organizer, admin)
- [x] Middleware and Policies set up
- [x] Blade views created with Bootstrap 5
- [x] Seeders for demo data
- [x] Authentication with Laravel Breeze
- [x] Role-based access control

**Your TounsiVert platform is ready to use! 🎉**
