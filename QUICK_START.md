# 🚀 TounsiVert - Quick Start (5 Minutes)

## Step-by-Step Setup

### 1️⃣ Open PowerShell/Terminal in Backend Directory
```powershell
cd d:/TounsiVert/backend
```

### 2️⃣ Install PHP Dependencies
```powershell
composer install
```

### 3️⃣ Generate Application Key
```powershell
php artisan key:generate
```

### 4️⃣ Create MySQL Database
Open MySQL and run:
```sql
CREATE DATABASE tounsivert CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Or use phpMyAdmin/MySQL Workbench to create database named: **tounsivert**

### 5️⃣ Update .env File (if needed)
The file `d:/TounsiVert/backend/.env` should already have:
```env
DB_DATABASE=tounsivert
DB_USERNAME=root
DB_PASSWORD=
```
Update `DB_PASSWORD` if your MySQL has a password.

### 6️⃣ Run Migrations
```powershell
php artisan migrate
```

### 7️⃣ Seed Database with Demo Data
```powershell
php artisan db:seed
```

### 8️⃣ Create Storage Symlink
```powershell
php artisan storage:link
```

### 9️⃣ Install NPM Dependencies & Build Assets
```powershell
npm install
npm run dev
```
**Keep this terminal running!**

### 🔟 Start Laravel Server
Open a **NEW terminal** and run:
```powershell
cd d:/TounsiVert/backend
php artisan serve
```

---

## 🎉 Access Your Application

**URL:** http://localhost:8000

### Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@tounsivert.tn | password |
| **Organizer** | organizer@tounsivert.tn | password |
| **Member** | member@tounsivert.tn | password |

---

## 📱 What to Test

1. **Visit Home Page** - See upcoming events and featured organizations
2. **Browse Events** - Filter by category, region, type
3. **Browse Organizations** - Verified organizations
4. **Login as Member** - Join events, donate, review
5. **Login as Organizer** - Create organizations and events
6. **Login as Admin** - Verify organizations, manage reports

---

## ⚠️ Common Issues

### ❌ "SQLSTATE[HY000] [1045] Access denied"
**Fix:** Update database password in `.env` file

### ❌ "Class 'OrgCategorySeeder' not found"
**Fix:** Run `composer dump-autoload` then `php artisan db:seed`

### ❌ "The stream or file could not be opened"
**Fix (Windows):** Run as Administrator:
```powershell
icacls storage /grant "Users:(OI)(CI)F" /T
icacls bootstrap/cache /grant "Users:(OI)(CI)F" /T
```

### ❌ "Vite manifest not found"
**Fix:** Make sure `npm run dev` is running

---

## 🎯 Quick Navigation

- **Home:** http://localhost:8000
- **Events:** http://localhost:8000/events
- **Organizations:** http://localhost:8000/organizations
- **Login:** http://localhost:8000/login
- **Register:** http://localhost:8000/register
- **Member Dashboard:** http://localhost:8000/dashboard
- **Organizer Panel:** http://localhost:8000/organizer/events
- **Admin Panel:** http://localhost:8000/admin/dashboard

---

**That's it! Your TounsiVert platform is running! 🎊**
