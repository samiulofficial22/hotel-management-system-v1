# Hotel Management System (HMS) - User Manual 
# হোটেল ম্যানেজমেন্ট সিস্টেম (HMS) - ইউজার ম্যানুয়াল

## 1. Introduction | ১. ভূমিকা
**[EN]** Welcome to the Hotel Management System (HMS). This platform is built to streamline end-to-end hotel operations smoothly and efficiently. From guest bookings, front desk management, POS down to human resources and accounting.
**[BN]** হোটেল ম্যানেজমেন্ট সিস্টেমে (HMS) আপনাকে স্বাগতম। হোটেলের পরিচালনা এবং দৈনন্দিন কার্যক্রম সহজ ও সুন্দরভাবে সম্পন্ন করার জন্য এই সফটওয়্যারটি তৈরি করা হয়েছে।

## 2. User Roles & Access (7 Roles) | ২. ইউজারের ধরন ও অ্যাক্সেস (৭টি রোল)
The system supports 7 distinct roles, each with specific permissions:

1. **Super Admin (সুপার এডমিন):** Has absolute control over everything. Can manage all modules, settings, roles, users, laundry, and spa. | সিস্টেমের সবকিছুর সম্পূর্ণ নিয়ন্ত্রণ রয়েছে। সমস্ত মডিউল, সেটিংস, ইউজার, লন্ড্রি ও স্পা নিয়ন্ত্রণ করতে পারবেন।
2. **Admin (এডমিন):** Similar to Super Admin, manages core configurations, finances, reports, operations, and settings. | প্রায় সুপার এডমিনের মতোই ক্ষমতা। ব্যবসার হিসাব, রিপোর্ট, অপারেশন এবং সিস্টেম সেটিংস ম্যানেজ করতে পারবেন।
3. **Manager (ম্যানেজার):** Oversees day-to-day operations: Rooms, Guests, Bookings, POS, Kitchen, Banquet, Housekeeping, Minibar, Store, and Departments. | হোটেলের দৈনন্দিন কাজ তদারকি করেন। রুম, বুকিং, রেস্টুরেন্ট, কিচেন, হাউস কিপিং, স্টোর এবং ডিপার্টমেন্ট পরিচালনা করেন।
4. **Accountant (অ্যাকাউন্ট্যান্ট):** Manages finances. Handles Invoices, Payments, Accounts, general ledger, and full Payroll alongside financial reports. | সম্পূর্ণ হিসাবরক্ষণ করেন। ইনভয়েস, পেমেন্ট, অ্যাকাউন্টস লেজার এবং কর্মচারীদের বেতন (Payroll) তৈরি ও প্রদান করা তার কাজ।
5. **Receptionist (রিসেপশনিস্ট):** Handles Front Desk duties. Manages Rooms, Guests, Bookings, Check-ins/outs, Invoices, POS orders, and guest portal requests. | ফ্রন্ট ডেস্কের সব দায়িত্ব পালন করেন। বুকিং, চেক-ইন/আউট, বিল তৈরি এবং রেস্টুরেন্ট পস অর্ডার নেন।
6. **Housekeeper (হাউসকিপার):** Views room status, updates cleaning assignments, manages laundry operations, and handles room refreshments/minibar items. | রুম পরিষ্কারের কাজ আপডেট করেন, লন্ড্রির কাজ পরিচালনা করেন এবং রুমের ভেতরের রিফ্রেশমেন্ট বা মিনিবার আইটেমের হিসাব রাখেন।
7. **Guest (গেস্ট):** Logs into the guest portal to book rooms online, view their own profile, and check past booking history. | গেস্ট পোর্টালে লগইন করে অনলাইনে রুম বুকিং করতে পারেন এবং নিজের আগের বুকিং এর ইতিহাস দেখতে পারেন।

## 3. Core Modules | ৩. মূল মডিউলসমূহ

### 3.1. Rooms & Bookings | রুম এবং বুকিং
*   **Room Types (রুমের ধরন):** Configure room categories (e.g. Deluxe, Suite) and capacities. | রুমের ক্যাটাগরি, বেস প্রাইস এবং ক্যাপাসিটি সেট করুন।
*   **Rooms (রুম ম্যানেজমেন্ট):** Add specific room numbers and monitor real-time availability status. | নির্দিষ্ট রুম যুক্ত করুন এবং বর্তমানে রুম ফাঁকা আছে কিনা তা লাইভ মনিটর করুন।
*   **Bookings & Calendar (বুকিং ও ক্যালেন্ডার):** Create bookings, manage check-ins/outs, and approve online guest requests via the visual calendar. | বুকিং তৈরি করুন, চেক-ইন/আউট করুন এবং ক্যালেন্ডারের মাধ্যমে অনলাইনে বুকিং রিকোয়েস্ট অ্যাপ্রুভ করুন।
*   **Guests (গেস্ট ডিরেক্টরি):** Maintain digital profiles, IDs, and stay histories of all guests. | সব গেস্টের প্রোফাইল, আইডি কার্ড এবং থাকার রেকর্ড সংরক্ষণ করুন।

### 3.2. F&B, POS & Kitchen | রেস্টুরেন্ট, পস এবং কিচেন
*   **Outlets (আউটলেট):** Create separate menus and table layouts for different restaurants. | হোটেলের বিভিন্ন রেস্টুরেন্টের জন্য আলাদা মেনু এবং টেবিলের লেআউট তৈরি করুন।
*   **POS System (পস সিস্টেম):** Take orders, manage tables, process payments, or post restaurant bills directly to a guest's room. | অর্ডার নিন, পেমেন্ট সম্পূর্ণ করুন অথবা বিল সরাসরি গেস্টের রুমের বিলের সাথে যুক্ত করুন।
*   **Kitchen Display (কিচেন ডিসপ্লে):** Live screen for kitchen staff to view incoming orders and mark food as "Ready". | রান্নাঘরের স্টাফদের জন্য লাইভ স্ক্রিন, যেখানে তারা নতুন অর্ডার দেখতে পারবে।
*   **Banquet (ব্যাংকোয়েট / হলরুম):** Manage event hall bookings, venues, and catering. | বড় অনুষ্ঠানের জন্য হলরুম বুকিং এবং ক্যাটারিং ম্যানেজ করুন।

### 3.3. Housekeeping & Operations | হাউসকিপিং ও অপারেশন
*   **Cleaning Assignments (রুম পরিষ্কার):** Allocate dirty rooms to housekeepers and track status. | হাউসকিপারদের কাজ ভাগ করে দিন এবং কাজের বর্তমান অবস্থা ট্র্যাক করুন।
*   **Refreshments & Minibar (মিনিবার):** Log items consumed from the minibar to automatically update the guest's upcoming bill. | রুমের মিনিবার থেকে ব্যবহৃত খাবার স্ক্যান করে সরাসরি গেস্টের বিলে যুক্ত করুন।
*   **Maintenance (রক্ষণাবেক্ষণ):** Report broken fixtures (e.g. AC) and track repair statuses. | নষ্ট হওয়া জিনিসপত্রের রিপোর্ট করুন এবং সেগুলোর মেরামতের আপডেট দেখুন।
*   **Laundry & Spa (লন্ড্রি ও স্পা):** Manage guest laundry service orders and process Spa bookings. | গেস্টদের লন্ড্রি সার্ভিসের অর্ডার এবং স্পা বুকিং ম্যানেজমেন্ট।

### 3.4. Accounts, HR & Analytics | হিসাবরক্ষণ, এইচআর এবং রিপোর্ট
*   **Accounts (হিসাবরক্ষণ):** Record daily expenses, hotel revenues, and view detailed profit/loss ledgers. | প্রতিদিনের খরচ এবং আয় রেকর্ড করুন এবং বিস্তারিত প্রফিট/লস লেজার দেখুন।
*   **HR & Payroll (এইচআর ও পে-রোল):** Maintain staff records, mark daily attendance, and process monthly salaries/payroll. | কর্মচারীদের তথ্য সংরক্ষণ, হাজিরা গ্রহণ এবং মাসের শেষে বেতন তৈরি করুন।
*   **Reports (রিপোর্টস):** Generate PDF reports for Occupancy Rates, Daily Cash Flow, and Financial analytics. | রুম বুকিংয়ের হার, ক্যাশ ফ্লো এবং আর্থিক অ্যানালিটিক্স এর রিপোর্ট জেনারেট করুন।
*   **Marketing (মার্কেটিং):** Create SMS/Email promotional campaigns for guests. | গেস্টদের জন্য বিশেষ অফার বা প্রমোশনাল এসএমএস ও ইমেইল তৈরি করুন।

### 3.5. System Settings | সিস্টেম সেটিংস
*   **Departments (ডিপার্টমেন্ট):** Structure your hotel teams (e.g., Front Office, Kitchen, HR). | আপনার হোটেলের বিভিন্ন বিভাগ তৈরি করুন।
*   **Users (ব্যবহারকারী):** Create login credentials for your staff. | কর্মচারীদের লগইন করার জন্য অ্যাকাউন্ট তৈরি করে দিন।
*   **Roles & Permissions (রোল ও পারমিশন):** Assign exact access rights to ensure users only see what they need to see. | কাকে সফটওয়্যারের কতটুকু অ্যাক্সেস দিবেন, তা এখান থেকে সেট করুন।
