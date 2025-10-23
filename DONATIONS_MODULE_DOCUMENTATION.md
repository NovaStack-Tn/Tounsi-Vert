# 🎯 Donations Module - Complete Documentation

## 📋 Overview

**Module Owner:** Fadi Zaghdoud  
**Implementation Date:** October 23, 2025  
**Status:** ✅ Complete

This module implements a comprehensive donation management system with full CRUD operations, advanced filtering, statistics, and export capabilities for the Tounsi-Vert platform.

---

## 🗄️ Database Schema

### Tables Involved

#### 1. **donations** Table
```sql
- id (primary key)
- participation_id (foreign key → participations)
- organization_id (foreign key → organizations, nullable)
- event_id (foreign key → events, nullable)
- amount (decimal 12,2)
- status (enum: pending, succeeded, failed, refunded)
- payment_ref (string, nullable)
- paid_at (datetime, nullable)
- timestamps (created_at, updated_at)
```

#### 2. **organizations** Table
```sql
- id (primary key)
- owner_id (foreign key → users)
- org_category_id (foreign key → org_categories)
- name (string)
- description (text, nullable)
- region (string, nullable)
- city (string, nullable)
- is_verified (boolean)
- is_blocked (boolean)
- timestamps + soft deletes
```

### Key Relationships
- `Donation` **belongsTo** `Organization`
- `Donation` **belongsTo** `Event`
- `Donation` **belongsTo** `Participation`
- `Donation` **hasOneThrough** `User` (via Participation)
- `Organization` **hasMany** `Donation`

---

## 🏗️ Architecture

### File Structure
```
backend/
├── app/
│   ├── Models/
│   │   └── Donation.php (enhanced with scopes)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Member/
│   │   │       └── DonationController.php
│   │   └── Requests/
│   │       ├── StoreDonationRequest.php
│   │       └── UpdateDonationRequest.php
├── resources/
│   └── views/
│       └── member/
│           └── donations/
│               ├── index.blade.php
│               ├── create.blade.php
│               ├── show.blade.php
│               ├── edit.blade.php
│               ├── statistics.blade.php
│               └── pdf.blade.php
└── routes/
    └── web.php (donations routes)
```

---

## 🔧 Features Implemented

### ✅ 1. CRUD Operations
- **Create:** Add new donations with organization and optional event
- **Read:** View all donations with detailed information
- **Update:** Edit pending donations
- **Delete:** Remove pending or failed donations

### ✅ 2. Advanced Filtering
- Filter by organization
- Filter by status (pending, succeeded, failed, refunded)
- Filter by date range (start date - end date)
- Filter "my donations only"
- Clear all filters option

### ✅ 3. Statistics & Analytics
- **Summary Cards:**
  - Total donation amount
  - Succeeded amount
  - Pending amount
  - Total donation count

- **Charts (Chart.js):**
  - Monthly donations bar chart
  - Donations by organization doughnut chart

- **Breakdown Table:**
  - Donations grouped by organization
  - Count and total per organization
  - Percentage visualization with progress bars

### ✅ 4. Export Capabilities
- **PDF Export:** Professional report with summary and detailed table
- **CSV Export:** Spreadsheet-compatible format for data analysis

### ✅ 5. Validation
- **Amount:** Minimum 1.01 TND, Maximum 1,000,000 TND
- **Organization:** Must exist and be valid
- **Event:** Optional, must exist if provided
- **Status:** Must be valid enum value

### ✅ 6. Authorization
- Users can only view/edit/delete their own donations
- Only pending donations can be edited
- Only pending/failed donations can be deleted

---

## 🚀 Installation & Setup

### Prerequisites
- Laravel 10.x
- PHP 8.1+
- MySQL/MariaDB

### Step 1: Install PDF Package
```bash
cd backend
composer require barryvdh/laravel-dompdf
```

### Step 2: Publish Configuration (Optional)
```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### Step 3: Run Migrations
```bash
php artisan migrate
```

### Step 4: Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

## 📍 API Routes

### Public Access (Authenticated Users)
```php
GET     /donations                      # List all donations (with filters)
GET     /donations/statistics          # View statistics
GET     /donations/create              # Create donation form
POST    /donations                     # Store new donation
GET     /donations/{donation}          # View donation details
GET     /donations/{donation}/edit     # Edit donation form
PUT     /donations/{donation}          # Update donation
DELETE  /donations/{donation}          # Delete donation

# Export Routes
GET     /donations-export/pdf          # Export to PDF
GET     /donations-export/csv          # Export to CSV

# Legacy Routes (backwards compatibility)
GET     /events/{event}/donate         # Event-specific donation form
POST    /events/{event}/donate         # Store event donation
```

---

## 💻 Usage Examples

### Create a Donation
```php
// From any event page
<a href="{{ route('events.donate.create', $event) }}" class="btn btn-success">
    <i class="bi bi-heart-fill"></i> Donate
</a>

// General donation
<a href="{{ route('donations.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg"></i> New Donation
</a>
```

### Filter Donations
```php
// URL with filters
/donations?organization_id=1&status=succeeded&my_donations=1
```

### Export Donations
```php
// PDF Export
<a href="{{ route('donations.export.pdf', request()->query()) }}" class="btn btn-danger">
    <i class="bi bi-file-pdf"></i> Export PDF
</a>

// CSV Export
<a href="{{ route('donations.export.csv', request()->query()) }}" class="btn btn-success">
    <i class="bi bi-file-excel"></i> Export CSV
</a>
```

### Using Model Scopes
```php
// Get donations by user
$donations = Donation::byUser($userId)->get();

// Get donations by organization
$donations = Donation::byOrganization($orgId)->get();

// Get donations by status
$donations = Donation::byStatus('succeeded')->get();

// Combine scopes with date range
$donations = Donation::byUser($userId)
    ->byStatus('succeeded')
    ->dateRange('2025-01-01', '2025-12-31')
    ->get();
```

---

## 🎨 UI Components

### Key Pages

1. **Donations Index** (`/donations`)
   - Statistics cards
   - Advanced filters
   - Donations by organization summary
   - Paginated donations list
   - Export buttons

2. **Create Donation** (`/donations/create`)
   - Organization selector
   - Optional event selector
   - Amount input with quick buttons
   - Validation feedback

3. **Donation Details** (`/donations/{id}`)
   - Full donation information
   - Status badge
   - Action buttons (edit/delete)
   - Related links

4. **Edit Donation** (`/donations/{id}/edit`)
   - Similar to create form
   - Pre-filled with current data
   - Only for pending donations

5. **Statistics Dashboard** (`/donations/statistics`)
   - Summary cards
   - Monthly bar chart
   - Organization pie chart
   - Detailed breakdown table
   - Recent donations

---

## 🔐 Security Features

1. **Authentication:** All routes require authentication
2. **Authorization:** Users can only manage their own donations
3. **Validation:** Strong server-side validation
4. **CSRF Protection:** All forms include CSRF tokens
5. **SQL Injection Prevention:** Using Eloquent ORM
6. **XSS Protection:** Blade templating auto-escapes output

---

## 📊 Statistics & Analytics

### Available Metrics
- Total donation amount
- Succeeded donations amount
- Pending donations amount
- Total donation count
- Monthly breakdown (current year)
- Per-organization breakdown
- Success rate percentage

### Chart Types
- **Bar Chart:** Monthly donations trend
- **Doughnut Chart:** Organization distribution

### Technology
- **Chart.js v4.4.0** for data visualization
- Responsive and interactive charts
- Custom tooltips and labels

---

## 📤 Export Features

### PDF Export
- Professional report layout
- Header with logo/title
- Summary statistics
- Detailed table
- Footer with generation date
- Styled with CSS

### CSV Export
- Headers: ID, User, Organization, Event, Amount, Status, Payment Ref, Date
- Excel-compatible format
- Respects current filters
- Downloadable file

---

## 🧪 Testing Checklist

### Functional Tests
- ✅ Create donation (with/without event)
- ✅ View donation details
- ✅ Edit pending donation
- ✅ Delete pending/failed donation
- ✅ Filter by organization
- ✅ Filter by status
- ✅ Filter by date range
- ✅ Export to PDF
- ✅ Export to CSV
- ✅ View statistics
- ✅ Authorization checks

### Edge Cases
- ✅ Cannot edit succeeded donations
- ✅ Cannot delete succeeded donations
- ✅ Cannot access other users' donations
- ✅ Validation error handling
- ✅ Empty state displays

---

## 🔄 Integration Points

### With Events Module
- Donations can be linked to specific events
- Event donation button redirects to donation form
- Event page shows total donations

### With Organizations Module
- Donations must be linked to an organization
- Organization page can show total donations received
- Only verified organizations can receive donations

### With Users Module
- User score increases with donation amount
- User dashboard shows donation history
- User participation tracking

---

## 🎯 Key Criteria Met

### ✅ Module Requirements (Fadi Zaghdoud)
1. **Two Tables with Jointure:** ✅
   - `donations` table
   - `organizations` table
   - Join: `Donation::with('organization', 'user')->get()`

2. **CRUD Operations:** ✅
   - Create: `DonationController@store`
   - Read: `DonationController@index`, `@show`
   - Update: `DonationController@update`
   - Delete: `DonationController@destroy`

3. **Validation:** ✅
   - Amount: min > 1.01, max 1,000,000
   - Organization: must exist
   - Status: valid enum
   - Form Requests: `StoreDonationRequest`, `UpdateDonationRequest`

4. **Views:** ✅
   - Display donations by user
   - Total donations per organization
   - Filtered views

5. **Advanced Features:** ✅
   - Statistics with Chart.js
   - Filtering by organization/date
   - PDF Export
   - CSV Export

---

## 🚧 Future Enhancements

### Potential Additions
1. **Payment Gateway Integration**
   - Stripe/PayPal integration
   - Real payment processing
   - Webhook handling

2. **Recurring Donations**
   - Monthly/yearly subscriptions
   - Automatic billing

3. **Donation Campaigns**
   - Time-limited campaigns
   - Goal tracking
   - Progress bars

4. **Tax Receipts**
   - Automated receipt generation
   - Email delivery
   - Tax-deductible tracking

5. **Social Features**
   - Donation leaderboards
   - Share on social media
   - Donor recognition wall

---

## 📞 Support & Maintenance

### Common Issues

**Issue 1:** PDF export not working  
**Solution:** Install dompdf package: `composer require barryvdh/laravel-dompdf`

**Issue 2:** Charts not displaying  
**Solution:** Check that Chart.js CDN is accessible

**Issue 3:** Validation errors  
**Solution:** Check that amount is >= 1.01 and organization exists

---

## 📝 Code Standards

- **PSR-12** coding standard
- **Eloquent ORM** for database queries
- **Form Requests** for validation
- **Blade Templates** for views
- **Bootstrap 5** for styling
- **Chart.js** for visualization

---

## 🎓 Learning Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Chart.js Documentation](https://www.chartjs.org/docs)
- [Bootstrap Documentation](https://getbootstrap.com/docs)
- [Laravel DomPDF](https://github.com/barryvdh/laravel-dompdf)

---

## ✅ Module Completion Status

**Status:** 🟢 **COMPLETE**

All requirements have been successfully implemented:
- ✅ Database schema with 2+ tables and jointure
- ✅ Full CRUD operations
- ✅ Comprehensive validation
- ✅ Multiple views (index, create, edit, show, statistics)
- ✅ Advanced filtering
- ✅ Statistics with charts (Chart.js)
- ✅ Export to PDF and CSV
- ✅ Security and authorization
- ✅ Responsive UI with Bootstrap
- ✅ Documentation complete

---

**Last Updated:** October 23, 2025  
**Version:** 1.0.0  
**Maintainer:** Fadi Zaghdoud
