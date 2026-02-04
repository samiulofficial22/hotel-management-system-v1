# Hotel Management Software

Production-ready Laravel 10+ hotel management system with REST API, Service/Repository pattern, SoftDeletes, validation, and transaction-safe operations.

## Requirements

- PHP 8.2+
- Laravel 10+
- MySQL (or SQLite for local dev)
- Composer

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
# For MySQL: set DB_CONNECTION=mysql, DB_DATABASE=hotel_management and create the database
# For SQLite: set DB_CONNECTION=sqlite and DB_DATABASE=database/database.sqlite (file must exist)
php artisan migrate --seed
```

## Run

```bash
php artisan serve
```

- **Web admin:** http://localhost:8000  
- **Login:** admin@hotel.test / password  
- **API base:** http://localhost:8000/api/v1  

## API (v1)

- **Auth:** POST `/api/v1/login`, POST `/api/v1/register`, POST `/api/v1/logout` (auth:sanctum), GET `/api/v1/user`
- **Room types:** GET/POST `/api/v1/room-types`, GET/PUT/DELETE `/api/v1/room-types/{id}`
- **Rooms:** GET/POST `/api/v1/rooms`, GET/PUT/DELETE `/api/v1/rooms/{id}`
- **Guests:** GET/POST `/api/v1/guests`, GET/PUT/DELETE `/api/v1/guests/{id}`
- **Bookings:** GET/POST `/api/v1/bookings`, GET/PUT/DELETE `/api/v1/bookings/{id}`
- **Invoices:** GET/POST `/api/v1/invoices`, GET/PUT/DELETE `/api/v1/invoices/{id}`
- **Payments:** GET/POST `/api/v1/payments`, GET/PUT/DELETE `/api/v1/payments/{id}`
- **Reports:** GET `/api/v1/reports/dashboard`, `/reports/occupancy`, `/reports/revenue`

All API responses: `{ "success": true, "message": "...", "data": {} }`. Use Laravel Sanctum token in header: `Authorization: Bearer {token}`.

## Auth & Security

- Laravel Sanctum for API tokens
- Spatie Laravel Permission for roles: Admin, Manager, Receptionist, Housekeeping, Accountant
- Permissions: dashboard.view, room_types.manage, rooms.manage, guests.manage, bookings.manage, bookings.checkin_checkout, invoices.manage, payments.manage, reports.view, users.manage, roles.manage

## Modules Implemented (Phase 1)

1. **Dashboard** – Occupancy today, revenue today/month, alerts (check-ins/check-outs today, rooms cleaning)
2. **Room Management** – room_types, rooms, status (available, occupied, cleaning, maintenance, out_of_order); auto status change on checkout to cleaning
3. **Guest Management** – CRUD, search
4. **Booking & Reservation** – Advance & walk-in, overbooking protection, check-in/check-out with auto bill (invoice) and optional late checkout fee
5. **Invoice & Billing** – Room charge, extra services, discount, tax/VAT; invoice items
6. **Payments** – Cash, card, mobile banking; partial payment; refund handling
7. **Notification log** table for future email/SMS
8. **Multi-language** – language_preference on users (en, bn ready)

## Database

- Migrations: users (with language_preference), room_types, rooms, guests, bookings, invoices, invoice_items, payments, notification_logs; Spatie permission tables
- Indexes: check_in_date, check_out_date, room_id, guest_id, booking_id, status
- SoftDeletes on room_types, rooms, guests, bookings, invoices, payments
- Foreign keys with cascade/nullOnDelete as appropriate

## Architecture

- **Service layer** – Business logic (BookingService, InvoiceService, PaymentService, DashboardService, etc.)
- **Repository layer** – Data access (RoomTypeRepository, RoomRepository, GuestRepository, BookingRepository, etc.)
- **Validation** – Form/API validation in controllers using service rules
- **Transactions** – DB::transaction for check-in, check-out, payment, invoice creation
- **API Resources** – JSON transformation (UserResource, RoomTypeResource, RoomResource, GuestResource, BookingResource, InvoiceResource, PaymentResource)

## Frontend

- Blade templates with Bootstrap 5
- Mobile-friendly admin: Dashboard, Room Types, Rooms, Guests, Bookings (list, create, show, edit, check-in, check-out)

## Seeded Data

- Roles and permissions
- Admin user: admin@hotel.test / password
- Manager: manager@hotel.test / password
- Receptionist: reception@hotel.test / password
- Room types (Standard, Deluxe, Superior, Suite) and rooms
- 30 guests, sample bookings
