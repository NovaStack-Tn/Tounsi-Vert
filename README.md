# 🌱 TounsiVert - Community Impact Platform

![TounsiVert](https://img.shields.io/badge/Laravel-10-red?style=flat-square&logo=laravel)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple?style=flat-square&logo=bootstrap)
![PHP](https://img.shields.io/badge/PHP-8.2-blue?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange?style=flat-square&logo=mysql)

**TounsiVert** is a comprehensive community platform designed for organizing and supporting impact-driven events across Tunisia. Connect citizens, associations, NGOs, mosques, municipalities, and sponsors to create meaningful change.

---

## 🎯 Key Features

### 🌟 For Visitors
- Browse public events and organizations
- Filter by category, region, city, and type
- View event details, reviews, and ratings
- Discover verified organizations

### 👥 For Members
- Join events (onsite/online/hybrid)
- Donate to events and organizations
- Follow events for updates
- Share events on social media
- Write reviews and ratings
- Track personal impact score
- Dashboard with participation history

### 🏢 For Organizers
- Create and manage organizations
- Publish and manage events
- View participants and donations
- Export attendee lists
- Track event performance

### 👨‍💼 For Admins
- Verify organizations
- Manage reports and moderation
- View platform statistics
- Manage users and content
- Dashboard with key metrics

---

## 🛠️ Tech Stack

### Backend
- **Laravel 10.x** - PHP Framework
- **PHP 8.2+** - Programming Language
- **MySQL/MariaDB** - Database
- **Eloquent ORM** - Database ORM
- **Laravel Breeze** - Authentication

### Frontend
- **Blade Templates** - Templating Engine
- **Bootstrap 5.3** - CSS Framework
- **Alpine.js** - Lightweight JavaScript
- **Bootstrap Icons** - Icon Library

### Architecture
- MVC Pattern
- RESTful Routes
- Policy-based Authorization
- Database Migrations & Seeders

---

## 📦 Installation

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL >= 8.0
- Git

### Quick Setup (5 Minutes)

```bash
# 1. Navigate to backend
cd d:/TounsiVert/backend

# 2. Install dependencies
composer install
npm install

# 3. Generate app key
php artisan key:generate

# 4. Create database (MySQL)
mysql -u root -p
CREATE DATABASE tounsivert CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# 5. Run migrations
php artisan migrate

# 6. Seed demo data
php artisan db:seed

# 7. Create storage link
php artisan storage:link

# 8. Build assets (keep running)
npm run dev

# 9. Start server (new terminal)
php artisan serve
```

**Access:** http://localhost:8000

---

## 👤 Demo Accounts

| Role | Email | Password | Permissions |
|------|-------|----------|-------------|
| Admin | admin@tounsivert.tn | password | Full access |
| Organizer | organizer@tounsivert.tn | password | Create orgs/events |
| Member | member@tounsivert.tn | password | Participate |

---

## 📊 Database Schema

### Core Tables (11)
1. **users** - User accounts with roles and scores
2. **org_categories** - Organization categories
3. **organizations** - NGOs, associations, mosques
4. **organization_social_links** - Social media links
5. **organization_followers** - User follows
6. **event_categories** - Event classifications
7. **events** - Community events
8. **participations** - User-event interactions
9. **donations** - Financial contributions
10. **reviews** - Event ratings (1-5 stars)
11. **reports** - Moderation system

### Relationships
- User → Organizations (owner)
- User → Participations → Events
- Organization → Events
- Event → Reviews, Donations
- Participation → Donation (1:1)

---

## 🎨 Features Overview

### Event Management
- **Types:** Online, Onsite, Hybrid
- **Categories:** Food Aid, Tree Planting, Clean Up, Education, Health, etc.
- **Filtering:** By category, region, city, type, date
- **Participation Limits:** Max attendees per event
- **Status:** Published/Unpublished

### Impact Scoring System
| Action | Points |
|--------|--------|
| Attend Event | +10 |
| Donate | +1 per TND |
| Follow Event | +1 |
| Share Event | +2 |

### Participation Types
- **Attend** - Join onsite/online
- **Follow** - Get updates
- **Share** - Spread awareness
- **Donate** - Financial support

### Moderation
- Report events/organizations
- Status workflow: Open → In Review → Resolved/Dismissed
- Admin panel for management

---

## 🗂️ Project Structure

```
TounsiVert/
├── backend/                    # Laravel Application
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/   # 13 Controllers
│   │   │   ├── Middleware/    # Custom Middleware
│   │   │   └── Policies/      # Authorization Policies
│   │   ├── Models/            # 9 Eloquent Models
│   ├── database/
│   │   ├── migrations/        # 11 Migrations
│   │   └── seeders/           # Demo Data Seeders
│   ├── resources/
│   │   └── views/             # Blade Templates
│   │       ├── layouts/       # Public & Auth Layouts
│   │       ├── events/        # Event Views
│   │       ├── organizations/ # Organization Views
│   │       ├── member/        # Member Dashboard
│   │       └── admin/         # Admin Panel
│   ├── routes/
│   │   └── web.php            # All Routes
│   └── .env                   # Configuration
├── report.md                  # Functional Specification
├── SETUP_GUIDE.md            # Detailed Setup
├── QUICK_START.md            # 5-Minute Setup
└── README.md                 # This File
```

---

## 🛣️ Routes

### Public
- `GET /` - Home
- `GET /events` - Browse Events
- `GET /events/{id}` - Event Details
- `GET /organizations` - Browse Organizations
- `GET /organizations/{id}` - Organization Profile

### Member (Auth Required)
- `GET /dashboard` - Member Dashboard
- `POST /events/{id}/attend` - Join Event
- `POST /events/{id}/donate` - Donate
- `POST /events/{id}/reviews` - Submit Review

### Organizer (Auth + Role)
- `GET /organizer/organizations` - My Organizations
- `POST /organizer/organizations` - Create Organization
- `GET /organizer/events` - My Events
- `POST /organizer/events` - Create Event

### Admin (Auth + Admin Role)
- `GET /admin/dashboard` - Admin Dashboard
- `POST /admin/organizations/{id}/verify` - Verify Organization
- `GET /admin/reports` - Manage Reports

---

## 🎯 Use Cases

### UC-01: Browse Events
Visitors can filter and view events by category, region, and type.

### UC-02: Join Event
Authenticated members can join events and earn impact points.

### UC-03: Donate
Members can support events/organizations financially.

### UC-04: Create Organization
Organizers create verified organizations to host events.

### UC-05: Create Event
Organizers publish events (online/onsite/hybrid) with details.

### UC-06: Review Event
Members who participated can rate and review events.

### UC-07: Report Issue
Members can report spam, abuse, or misinformation.

### UC-08: Admin Moderation
Admins verify organizations and resolve reports.

---

## 🔐 Security

- **Authentication:** Laravel Breeze
- **Authorization:** Policies & Middleware
- **CSRF Protection:** Built-in
- **Password Hashing:** Bcrypt
- **SQL Injection:** Eloquent ORM Protection
- **File Uploads:** Validation & Size Limits

---

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure database credentials
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Set up SSL certificate
- [ ] Configure CDN for assets
- [ ] Set up automated backups
- [ ] Configure email service
- [ ] Integrate payment gateway

---

## 📈 Future Enhancements

- [ ] Mobile PWA
- [ ] Real-time notifications
- [ ] Event calendar view
- [ ] Geolocation & maps
- [ ] Multi-language (AR/FR)
- [ ] Payment gateway integration
- [ ] Email notifications
- [ ] SMS alerts
- [ ] Advanced search
- [ ] Badges & achievements
- [ ] Volunteer certificates
- [ ] Analytics dashboard

---

## 📝 License

This project is open-source and available for educational purposes.

---

## 👥 Contributing

This is an educational project. For improvements:
1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

---

## 📞 Support

For questions or issues:
- Review `SETUP_GUIDE.md` for detailed documentation
- Check `QUICK_START.md` for quick setup
- Review `report.md` for functional specifications
- Laravel Docs: https://laravel.com/docs/10.x
- Bootstrap Docs: https://getbootstrap.com/docs/5.3

---

## 🙏 Credits

Built with:
- [Laravel](https://laravel.com) - PHP Framework
- [Bootstrap](https://getbootstrap.com) - CSS Framework
- [Alpine.js](https://alpinejs.dev) - JavaScript Framework
- [Bootstrap Icons](https://icons.getbootstrap.com) - Icon Library

---

**Made with ❤️ for the Tunisian Community**

*Empowering citizens to make a positive impact* 🌱
