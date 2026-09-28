# CookedLocal

A marketplace web app that connects **local home cooks** with nearby **buyers** who want home-cooked meals. Cooks register, prove they hold a food-hygiene certificate, and list dishes. An admin approves both cooks and dishes before anything goes live, and buyers search for dishes and place orders.

## User Roles

| Role | What they can do |
|------|------------------|
| **Buyer** | Register, log in, search dishes (by name, cuisine or location), view dish details, check out and order, update their profile |
| **Seller (home cook)** | Register with a profile photo and a **food hygiene certificate**, then upload dishes (price, portions, allergens, description, available days and times), manage their dishes, and update their profile |
| **Admin** | Approve or reject new sellers, approve or reject new dishes, manage registered users, and read contact inquiries |

## How It Works

```
Seller registers ─► approve_cook (pending) ──admin approves──► sellers
Seller uploads dish ─► approval_dishes (pending) ──admin approves──► dishes ─► visible to buyers
Buyer searches ─► dish_details ─► checkout ─► orders
Visitor ─► inquiry form ─► inquiries ─► admin inbox
```

Because every cook and every dish must be approved first, only verified cooks with hygiene certificates can sell food.

## Pages & Scripts

| Area | Files |
|------|-------|
| Public | `index.html`, `search.html` / `search.php`, `dish_details.php`, `hygiene.html`, `inquiry.html` / `submit_inquiry.php` |
| Auth | `login.html` / `login.php`, `buyer.html` / `buyer_reg.php`, `seller_reg.html` / `seller_reg.php` |
| Buyer | `buyer_dashboard.html`, `buyer_update.*`, `get_buyer_info.php`, `checkout.php` |
| Seller | `seller_dashboard.html`, `upload_food.html` / `upload_dish.php`, `manage_dishes.html`, `get_seller_dishes.php`, `delete_dish.php`, `seller_update.*`, `update_seller.php`, `get_seller_info.php` |
| Admin | `admin.html`, `dish_approval.*`, `seller_action.php`, `manage_registerd.html`, `get_sellers.php`, `admin_inquiry.php` |
| Data API | `get_dishes.php` and the other `get_*.php` files return JSON to the HTML pages |

## Tech Stack

- PHP with MySQL (`mysqli`, prepared statements)
- HTML, CSS and vanilla JavaScript front end
- Passwords hashed with bcrypt (`password_hash`)

## Setup

1. Install **XAMPP** (or a similar Apache + PHP + MySQL stack).
2. Copy the repository into `htdocs/cookedlocal/`.
3. Create a MySQL database called `cookedlocal` with these tables: `buyers`, `sellers`, `approve_cook`, `dishes`, `approval_dishes`, `orders` and `inquiries`.
4. Create an `uploads/` folder that the web server can write to. Profile photos, certificates and dish images are saved there.
5. Visit `http://localhost/cookedlocal/`.

The database connection defaults to `localhost` with user `root` and no password, which suits local development only.

## Known Limitations / Improvements

This was a university project. Before any real deployment:

- Remove the hardcoded test admin login in `login.php` and store admins in the database with hashed passwords
- Validate uploaded files (MIME type, extension allow-list, size limit), rename them randomly, and keep them outside the web root
- Add CSRF tokens and server-side session/role checks to every admin and seller endpoint
- Replace the remaining string-built SQL in `seller_action.php` with prepared statements
- Load database credentials from a single config file or environment variables
- Commit a `schema.sql`, and remove the timestamped test images from the repository
