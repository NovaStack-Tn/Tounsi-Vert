# 📡 TounsiVert API Documentation

## Base URL
```
http://localhost:8000
```

---

## 🌍 Public Endpoints

### Get All Events
**Endpoint:** `GET /events`

**Query Parameters:**
- `category` (optional) - Filter by event category ID
- `region` (optional) - Filter by region name
- `city` (optional) - Filter by city name
- `type` (optional) - Filter by type (online/onsite/hybrid)
- `date` (optional) - Filter by start date (YYYY-MM-DD)
- `page` (optional) - Pagination page number

**Response:** HTML View with paginated events

---

### Get Event Details
**Endpoint:** `GET /events/{id}`

**Parameters:**
- `id` (required) - Event ID

**Response:** HTML View with event details, reviews, and organization info

---

### Get All Organizations
**Endpoint:** `GET /organizations`

**Query Parameters:**
- `category` (optional) - Filter by organization category ID
- `region` (optional) - Filter by region name
- `city` (optional) - Filter by city name
- `page` (optional) - Pagination page number

**Response:** HTML View with paginated organizations

---

### Get Organization Details
**Endpoint:** `GET /organizations/{id}`

**Parameters:**
- `id` (required) - Organization ID

**Response:** HTML View with organization profile and events

---

## 🔐 Authenticated Endpoints

### Join Event
**Endpoint:** `POST /events/{id}/attend`

**Authentication:** Required

**Parameters:**
- `id` (required) - Event ID

**Response:** Redirect with success/error message

**Score:** +10 points

---

### Follow Event
**Endpoint:** `POST /events/{id}/follow`

**Authentication:** Required

**Parameters:**
- `id` (required) - Event ID

**Response:** Redirect with success/error message

**Score:** +1 point

---

### Share Event
**Endpoint:** `POST /events/{id}/share`

**Authentication:** Required

**Parameters:**
- `id` (required) - Event ID

**Body:**
- `platform` (optional) - Social media platform name

**Response:** Redirect with success/error message

**Score:** +2 points

---

### Create Donation
**Endpoint:** `POST /events/{id}/donate`

**Authentication:** Required

**Parameters:**
- `id` (required) - Event ID

**Body:**
```json
{
  "amount": 50.00
}
```

**Validation:**
- `amount`: required, numeric, min:1, max:10000

**Response:** Redirect to event page with success message

**Score:** +floor(amount) points (e.g., 50 TND = +50 points)

---

### Submit Review
**Endpoint:** `POST /events/{id}/reviews`

**Authentication:** Required

**Parameters:**
- `id` (required) - Event ID

**Body:**
```json
{
  "rate": 5,
  "comment": "Great event!"
}
```

**Validation:**
- `rate`: required, integer, 1-5
- `comment`: optional, string, max:1000

**Requirements:**
- User must have participated in the event (attend/donation/follow)
- One review per user per event

**Response:** Redirect with success/error message

---

### Submit Report
**Endpoint:** `POST /reports`

**Authentication:** Required

**Body:**
```json
{
  "event_id": 1,
  "organization_id": null,
  "reason": "Spam",
  "details": "This event appears to be spam..."
}
```

**Validation:**
- `event_id`: optional, exists in events table
- `organization_id`: optional, exists in organizations table
- `reason`: required, string, max:200
- `details`: optional, string, max:2000

**Note:** Either `event_id` or `organization_id` must be provided

**Response:** Redirect with success message

---

## 👔 Organizer Endpoints

### List My Organizations
**Endpoint:** `GET /organizer/organizations`

**Authentication:** Required (Organizer/Admin)

**Response:** HTML View with user's organizations

---

### Create Organization
**Endpoint:** `POST /organizer/organizations`

**Authentication:** Required (Organizer/Admin)

**Body (Form Data):**
```
org_category_id: 1
name: "My Organization"
description: "Description..."
address: "123 Main St"
region: "Tunis"
city: "Tunis"
zipcode: "1000"
phone_number: "+216 XX XXX XXX"
logo_path: [FILE]
```

**Validation:**
- `org_category_id`: required, exists
- `name`: required, max:150
- `description`: optional
- `logo_path`: optional, image, max:2048KB

**Response:** Redirect to organization page

---

### List My Events
**Endpoint:** `GET /organizer/events`

**Authentication:** Required (Organizer/Admin)

**Response:** HTML View with user's organization events

---

### Create Event
**Endpoint:** `POST /organizer/events`

**Authentication:** Required (Organizer/Admin)

**Body (Form Data):**
```
organization_id: 1
event_category_id: 2
type: "onsite"
title: "Tree Planting Event"
description: "Join us to plant trees..."
start_at: "2024-03-15 09:00:00"
end_at: "2024-03-15 17:00:00"
max_participants: 50
address: "Central Park"
region: "Tunis"
city: "Ariana"
zipcode: "2080"
poster_path: [FILE]
```

**Validation:**
- `organization_id`: required, must own organization
- `event_category_id`: required, exists
- `type`: required, in:online,onsite,hybrid
- `title`: required, max:150
- `start_at`: required, date, after:now
- `end_at`: optional, date, after:start_at
- `max_participants`: optional, integer, min:1
- `meeting_url`: optional for online/hybrid, url
- `address`: required for onsite/hybrid
- `poster_path`: optional, image, max:2048KB

**Response:** Redirect to event page

---

### Update Event
**Endpoint:** `PUT /organizer/events/{id}`

**Authentication:** Required (Owner/Admin)

**Parameters:**
- `id` (required) - Event ID

**Body:** Same as Create Event (except organization_id)

**Response:** Redirect to event page

---

### Delete Event
**Endpoint:** `DELETE /organizer/events/{id}`

**Authentication:** Required (Owner/Admin)

**Parameters:**
- `id` (required) - Event ID

**Response:** Redirect to events list

---

## 👨‍💼 Admin Endpoints

### Admin Dashboard
**Endpoint:** `GET /admin/dashboard`

**Authentication:** Required (Admin only)

**Response:** HTML View with platform statistics

**Statistics:**
- Total users
- Total organizations
- Verified organizations
- Total events
- Published events
- Open reports
- Total donations (succeeded)

---

### List All Organizations
**Endpoint:** `GET /admin/organizations`

**Authentication:** Required (Admin only)

**Response:** HTML View with all organizations

---

### Verify Organization
**Endpoint:** `POST /admin/organizations/{id}/verify`

**Authentication:** Required (Admin only)

**Parameters:**
- `id` (required) - Organization ID

**Response:** Redirect with success message

**Effect:** Sets `is_verified = true`

---

### Unverify Organization
**Endpoint:** `POST /admin/organizations/{id}/unverify`

**Authentication:** Required (Admin only)

**Parameters:**
- `id` (required) - Organization ID

**Response:** Redirect with success message

**Effect:** Sets `is_verified = false`

---

### Delete Organization
**Endpoint:** `DELETE /admin/organizations/{id}`

**Authentication:** Required (Admin only)

**Parameters:**
- `id` (required) - Organization ID

**Response:** Redirect with success message

**Effect:** Soft delete organization

---

### List All Reports
**Endpoint:** `GET /admin/reports`

**Authentication:** Required (Admin only)

**Response:** HTML View with all reports

---

### Update Report Status
**Endpoint:** `PATCH /admin/reports/{id}/status`

**Authentication:** Required (Admin only)

**Parameters:**
- `id` (required) - Report ID

**Body:**
```json
{
  "status": "resolved"
}
```

**Validation:**
- `status`: required, in:open,in_review,resolved,dismissed

**Response:** Redirect with success message

---

## 🔑 Authentication

TounsiVert uses **Laravel Breeze** for authentication.

### Login
**Endpoint:** `POST /login`

**Body:**
```json
{
  "email": "user@example.com",
  "password": "password"
}
```

**Response:** Redirect to dashboard

---

### Register
**Endpoint:** `POST /register`

**Body:**
```json
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

**Response:** Redirect to dashboard

---

### Logout
**Endpoint:** `POST /logout`

**Authentication:** Required

**Response:** Redirect to home

---

## 📊 Response Formats

### Success Response (Redirect)
All successful requests redirect with a flash message:
```php
return redirect()->back()->with('success', 'Action completed successfully!');
```

### Error Response (Redirect)
Failed requests redirect with error message:
```php
return redirect()->back()->with('error', 'Action failed.');
```

### Validation Errors
Form validation errors are automatically returned and displayed in views.

---

## 🚦 HTTP Status Codes

- `200` - Success
- `302` - Redirect (standard for Laravel web routes)
- `403` - Forbidden (unauthorized action)
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

---

## 🔒 Authorization

### Middleware
- `auth` - Requires authentication
- `admin` - Requires admin role

### Policies
- **EventPolicy** - Controls event management
- **OrganizationPolicy** - Controls organization management

**Policy Rules:**
- Owner can view/update/delete their resources
- Admin can perform all actions

---

## 📝 Notes

### Unique Constraints
- One participation per user/event/type
- One review per user per event
- One social link per organization/title

### Soft Deletes
The following models use soft deletes:
- User
- Organization
- Event
- Review

### Score Calculation
Scores are automatically updated when:
- User participates (attend/follow/share)
- Donation status changes to "succeeded"

### File Uploads
- Accepted formats: jpg, jpeg, png, gif, svg
- Max size: 2048KB (2MB)
- Storage: `storage/app/public/`
- Access via: `Storage::url($path)`

---

## 🧪 Testing with Postman/cURL

### Example: Join Event
```bash
curl -X POST http://localhost:8000/events/1/attend \
  -H "Cookie: laravel_session=YOUR_SESSION_COOKIE" \
  -H "X-CSRF-TOKEN: YOUR_CSRF_TOKEN"
```

### Example: Create Donation
```bash
curl -X POST http://localhost:8000/events/1/donate \
  -H "Cookie: laravel_session=YOUR_SESSION_COOKIE" \
  -H "X-CSRF-TOKEN: YOUR_CSRF_TOKEN" \
  -d "amount=50"
```

---

**For a REST API (JSON responses), consider creating API routes with Laravel Sanctum in the future.**
