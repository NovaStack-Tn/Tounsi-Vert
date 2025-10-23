# 🚀 Donations Module - Quick Start Guide

## ⚡ Installation (5 minutes)

### Step 1: Install Required Package
```bash
cd backend
composer require barryvdh/laravel-dompdf
```

### Step 2: Verify Database
The migrations already exist. If not run yet:
```bash
php artisan migrate
```

### Step 3: Clear Cache
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

### Step 4: Test Routes
```bash
php artisan route:list --name=donations
```

You should see:
```
donations.index           GET    /donations
donations.statistics      GET    /donations/statistics
donations.create          GET    /donations/create
donations.store           POST   /donations
donations.show            GET    /donations/{donation}
donations.edit            GET    /donations/{donation}/edit
donations.update          PUT    /donations/{donation}
donations.destroy         DELETE /donations/{donation}
donations.export.pdf      GET    /donations-export/pdf
donations.export.csv      GET    /donations-export/csv
```

---

## 🧪 Quick Testing

### Test 1: Create a Donation
1. Login as a regular user
2. Navigate to `/donations/create`
3. Select an organization
4. Enter amount: 50
5. Click "Complete Donation"
6. ✅ Should redirect to `/donations` with success message

### Test 2: View Donations List
1. Navigate to `/donations`
2. ✅ Should see your donation in the list
3. ✅ Should see statistics cards at the top
4. ✅ Should see donations by organization breakdown

### Test 3: Filter Donations
1. On `/donations` page
2. Select an organization from filter
3. Click "Filter"
4. ✅ Should show only donations for that organization

### Test 4: View Statistics
1. Navigate to `/donations/statistics`
2. ✅ Should see 4 summary cards
3. ✅ Should see monthly bar chart
4. ✅ Should see organization pie chart
5. ✅ Should see breakdown table

### Test 5: Export to PDF
1. On `/donations` page
2. Click "Export PDF" button
3. ✅ Should download a PDF file with donations report

### Test 6: Export to CSV
1. On `/donations` page
2. Click "Export CSV" button
3. ✅ Should download a CSV file
4. Open in Excel/Sheets
5. ✅ Should see properly formatted data

### Test 7: Edit Donation (Pending Only)
1. Create a new donation (it will be succeeded by default in demo)
2. Try to edit it
3. ✅ Should show error "Only pending donations can be edited"

### Test 8: Delete Donation
1. Try to delete a succeeded donation
2. ✅ Should show error "Only pending or failed donations can be deleted"

---

## 🎯 Access URLs

| Page | URL | Description |
|------|-----|-------------|
| Donations List | `/donations` | View all donations with filters |
| Create Donation | `/donations/create` | Create new donation |
| Donation Details | `/donations/{id}` | View single donation |
| Edit Donation | `/donations/{id}/edit` | Edit pending donation |
| Statistics | `/donations/statistics` | View charts and analytics |
| Export PDF | `/donations-export/pdf` | Download PDF report |
| Export CSV | `/donations-export/csv` | Download CSV file |
| Event Donation | `/events/{id}/donate` | Donate to specific event |

---

## 📋 Sample Data for Testing

### Create Test Organizations (if needed)
```sql
INSERT INTO organizations (owner_id, org_category_id, name, description, region, is_verified, created_at, updated_at)
VALUES 
(1, 1, 'Green Tunisia', 'Environmental conservation', 'Tunis', 1, NOW(), NOW()),
(1, 1, 'Clean Beaches', 'Beach cleanup initiatives', 'Sousse', 1, NOW(), NOW()),
(1, 1, 'Tree Planters', 'Urban forestation', 'Sfax', 1, NOW(), NOW());
```

### Create Test Events (optional)
```sql
INSERT INTO events (organization_id, event_category_id, title, description, location, start_date, end_date, is_published, created_at, updated_at)
VALUES 
(1, 1, 'Beach Cleanup Day', 'Join us for beach cleanup', 'La Marsa Beach', '2025-11-01 09:00:00', '2025-11-01 17:00:00', 1, NOW(), NOW()),
(2, 1, 'Tree Planting Campaign', 'Plant 1000 trees', 'Central Park', '2025-11-15 08:00:00', '2025-11-15 18:00:00', 1, NOW(), NOW());
```

---

## 🔍 Debugging Tips

### Issue: Routes not found
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### Issue: PDF not generating
Check if package is installed:
```bash
composer show barryvdh/laravel-dompdf
```

If not installed:
```bash
composer require barryvdh/laravel-dompdf
```

### Issue: Charts not displaying
1. Check browser console for errors
2. Verify Chart.js CDN is accessible
3. Check that statistics data exists

### Issue: Validation errors
Common causes:
- Amount less than 1.01
- Organization doesn't exist
- Event doesn't exist
- Missing CSRF token

### Issue: Authorization errors
- Make sure user is logged in
- User can only access their own donations
- Check if donation belongs to the user

---

## 📊 Expected Results

After testing, you should see:

### Donations List Page
- Statistics cards showing totals
- Filter form
- Donations by organization table
- Paginated donations list
- Export buttons

### Statistics Page
- 4 summary cards
- Monthly bar chart (showing donations per month)
- Organization doughnut chart
- Detailed breakdown table
- Recent donations list

### PDF Export
- Professional report layout
- Summary section
- Detailed table
- Footer with date

### CSV Export
- Proper headers
- One row per donation
- All relevant data
- Excel-compatible format

---

## ✅ Verification Checklist

- [ ] Package installed: `barryvdh/laravel-dompdf`
- [ ] Routes registered and accessible
- [ ] Can create new donation
- [ ] Can view donations list
- [ ] Filters work correctly
- [ ] Can view donation details
- [ ] Edit restrictions work (pending only)
- [ ] Delete restrictions work (pending/failed only)
- [ ] Statistics page displays correctly
- [ ] Charts render properly
- [ ] PDF export works
- [ ] CSV export works
- [ ] Authorization checks work
- [ ] Validation messages appear correctly

---

## 🎓 Quick Reference

### Model Scopes
```php
// Filter by user
Donation::byUser($userId)->get();

// Filter by organization
Donation::byOrganization($orgId)->get();

// Filter by status
Donation::byStatus('succeeded')->get();

// Date range
Donation::dateRange('2025-01-01', '2025-12-31')->get();
```

### Validation Rules
- **amount:** required, numeric, min:1.01, max:1000000
- **organization_id:** required, exists:organizations,id
- **event_id:** nullable, exists:events,id
- **status:** nullable, in:pending,succeeded,failed,refunded

### Status Values
- `pending` - Payment not completed
- `succeeded` - Payment successful
- `failed` - Payment failed
- `refunded` - Payment refunded

---

## 🎯 Next Steps

1. **Test all features** using the checklist above
2. **Review the code** in the following files:
   - `app/Models/Donation.php`
   - `app/Http/Controllers/Member/DonationController.php`
   - `app/Http/Requests/StoreDonationRequest.php`
   - `app/Http/Requests/UpdateDonationRequest.php`

3. **Customize if needed:**
   - Adjust validation rules
   - Modify chart colors
   - Change export formats
   - Add additional filters

4. **Integrate with other modules:**
   - Link from organization pages
   - Add donation widgets to dashboard
   - Show donation stats on user profiles

---

## 📞 Need Help?

- Check `DONATIONS_MODULE_DOCUMENTATION.md` for detailed docs
- Review Laravel logs: `storage/logs/laravel.log`
- Check browser console for JavaScript errors
- Verify database records are correct

---

**Ready to test!** Start with the installation steps above and follow the testing guide.

Good luck! 🚀
