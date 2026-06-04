<div align="center">

# 🛍️ Rymo

### *A decoupled, high-performance monolithic commerce platform—engineered for disciplined MVPs and enterprise-grade scale.*

[![Laravel](https://img.shields.io/badge/Laravel-11%2B-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue](https://img.shields.io/badge/Vue%203-4FC08D?style=for-the-badge&logo=vuedotjs&logoColor=white)](https://vuejs.org)
[![TypeScript](https://img.shields.io/badge/TypeScript-3178C6?style=for-the-badge&logo=typescript&logoColor=white)](https://www.typescriptlang.org)
[![Inertia](https://img.shields.io/badge/Inertia.js-9558D9?style=for-the-badge)](https://inertiajs.com)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com)

**Rymo** is a full-stack e-commerce system that pairs a battle-tested Laravel backend with a reactive Vue 3 storefront—unified through Inertia.js as a single deployable monolith, without sacrificing frontend velocity or data integrity.

</div>

---

## 📌 Executive Summary

Rymo is not a tutorial clone. It is a **financially disciplined MVP**: every schema decision, UI component, and checkout pathway is designed to minimize rework cost while preserving a credible path toward multi-currency operations, variant matrices, and admin orchestration at scale.

The current release delivers a polished customer-facing storefront (migrated from a legacy static design system) atop a **relational core** that already encodes production-minded commerce rules—**guest checkout**, client-side cart persistence, immutable order line pricing, and nullable admin governance.

---

## 🧬 The Evolution Journey

> *Great architecture is rarely born on the first pass—it is earned.*

### 🌱 Phase I — The Legacy Storefront

The original Rymo experience began as a **static HTML/CSS storefront**—hand-crafted with Bootstrap 4 and Font Awesome 4 during my early development journey. It was visually cohesive and commercially convincing, but inherently **brittle**: duplicated markup, jQuery/Bootstrap JS coupling, no transactional backbone, and zero protection against catalog price drift after checkout.

### 🏗️ Phase II — The Systems-Builder Overhaul

Years later, I revisited that same brand identity with a mature **systems-builder mindset**:

| Dimension | Before | After |
|-----------|--------|-------|
| **Presentation** | Monolithic `.html` files | Vue 3 SFCs + Composition API (`<script setup>`) |
| **Type Safety** | None | TypeScript interfaces across products, cart, and blog domains |
| **State** | DOM scripts & Bootstrap collapse | Reactive `ref` / `computed` + `localStorage` cart via Inertia |
| **Data** | Hard-coded placeholders | MySQL schema with normalized carts, orders, and catalog tables |
| **Layout** | Copy-pasted nav/footer | `ShopLayout.vue` + reusable `ProductCard.vue` |

This repository is therefore a **living portfolio artifact**: it documents both the aesthetic foundation I established early on *and* the architectural maturity I applied when transforming it into a defensible commerce platform.

---

## 🧰 Tech Stack Matrix

| Layer | Technology | Role |
|-------|------------|------|
| **Backend** | Laravel 11+ (12.x runtime) | Routing, validation, ORM, queues, and domain persistence |
| **Frontend** | Vue 3 + Vite | Component-driven UI with Composition API |
| **Monolithic Bridge** | Inertia.js v2 | SPA ergonomics without a separate API surface |
| **Type Safety** | TypeScript | Prop contracts (`Product`, `CartItem`, `BlogPost`, `ProductDetail`) |
| **Styling** | Bootstrap 4 + Font Awesome 4 + custom CSS | Legacy-faithful brand system, integrated via Vite |
| **Database** | MySQL | Relational commerce schema (SQLite supported for local dev) |
| **Routing Helpers** | Ziggy | Named Laravel routes inside Vue templates |
| **Quality** | Pest PHP | Feature tests for storefront, contact, and guest checkout |

---

## 🏛️ Architectural Integrity (MVP Highlights)

These decisions separate Rymo from generic CRUD demos:

### 🔀 Decoupled Cart vs. Order Logic

```
LocalStorage cart (client)     POST /checkout (OrderController)
        │                                │
        ▼                                ▼
carts ──< cart_items          orders ──< order_items
  │         (analytics)            │
  └── linked via order_id          └── immutable commercial record
```

- **Client-side cart** (`useCart` composable + `localStorage`) models *live shopping intent*—add/remove items, quantity edits, and coupon UI on the Cart page without round-trips to the server.
- **`carts` / `cart_items`** capture an **analytics snapshot** at checkout time—linked to the guest order via `order_id`, with `price_at_addition` frozen from server-verified catalog prices.
- **`orders` / `order_items`** model *commitment*—created only after checkout succeeds inside a `DB::transaction()`.

**Why it matters:** Clearing the browser cart never corrupts historical orders. Customer support, refunds, and analytics always read from an append-only order ledger—eliminating state deadlocks common in single-table "cart-order" hybrids.

---

### 💰 Point-in-Time Pricing (`price_at_purchase`)

Every `order_items` row stores:

```sql
price_at_purchase DECIMAL(10, 2)  -- frozen at checkout millisecond
```

**Why it matters:** Catalog prices may change due to promotions, inflation, or FX adjustments. Invoices, tax reports, and dispute resolution must reflect what the customer *actually agreed to pay*—not today's list price. Rymo enforces financial immutability at the line-item level.

---

### 🛒 Guest Checkout Pipeline

Checkout is **public**—no account or login required.

| Step | Page | Action |
|------|------|--------|
| 1 | `/cart` | Review items, apply coupon (UI), click **Proceed to Checkout** |
| 2 | `/checkout` | Enter name, phone, and shipping address; review order summary |
| 3 | `POST /checkout` | Server validates customer info, verifies product IDs, recalculates totals from `products.price`, persists order + line items + analytics cart snapshot |
| 4 | `/cart` (redirect) | Success flash, localStorage cart cleared, empty-cart confirmation |

**Security rules enforced server-side:**

- Frontend prices from `localStorage` are **never trusted**—`OrderController@store` reads authoritative prices from the database.
- The entire persistence path runs inside `DB::transaction()` for atomicity.
- Guest orders store `customer_name`, `customer_phone`, and `shipping_address` on the order row; `user_id` remains nullable.

---

### 🛡️ Safe Administrative Pipelines

The `orders` table includes:

```sql
admin_id BIGINT UNSIGNED NULLABLE  -- FK → admins.id (ON DELETE SET NULL)
status   ENUM('pending','processing','shipped','delivered','cancelled')
payment_method DEFAULT 'COD'
```

**Why it matters:**

- ✅ Customers complete **frictionless guest-style checkout** without admin pre-approval.
- ✅ Back-office staff attach to orders *after* submission—preserving role validation without blocking conversion.
- ✅ Cash-on-Delivery (`COD`) is first-class—ideal for regional MVP launches before card gateways arrive.

---

## ✨ Current MVP Features

### 🖥️ Storefront (Customer Experience)

- 🏠 **Home** — Hero, brand strip, promotional grids, and multi-category product showcases
- 🛒 **Shop** — Paginated catalog with coral-themed Bootstrap pagination
- 🔍 **Product Detail** — Gallery thumbnails, size selector, quantity input, related products
- 📰 **Blog** — Editorial layout migrated from static templates
- 🧺 **Reactive Cart** — Client-side `localStorage` cart with live subtotal / shipping / total via Vue `computed` state; coupon panel on Cart page
- 🧾 **Guest Checkout** — Dedicated `/checkout` page for shipping details and order summary; places orders without authentication
- 📬 **Contact** — Brand-aligned form with server-side validation & flash feedback
- 📱 **Responsive Navbar** — Mobile collapse via Vue (no Bootstrap JS / jQuery dependency conflicts)

### 🧱 Engineering Conventions

- 🧩 **Reusable components** — `ProductCard.vue`, `ShopLayout.vue`
- 🔗 **SEO-ready catalog** — `products.slug` unique index (route migration in progress)
- 🎨 **Poppins typography** — Google Fonts import matching the original design system
- ✅ **Automated tests** — Pest feature coverage for shop, home, storefront pages, and guest checkout (`CheckoutOrderTest`)

### 🗄️ Data Model

| Entity | Purpose |
|--------|---------|
| `categories` | Catalog taxonomy |
| `products` | Slug, SKU, stock, pricing, ratings, gallery, available sizes |
| `carts` / `cart_items` | Post-checkout analytics snapshots (`user_id` nullable, `order_id` FK) |
| `orders` / `order_items` | Guest/authenticated orders with frozen `price_at_purchase` per line |
| `admins` | Operational approval & fulfillment actors |

**Guest order columns:** `customer_name`, `customer_phone`, `shipping_address`, nullable `user_id`, `status` defaulting to `pending`, `payment_method` defaulting to `COD`.

---

## 🗺️ Scalability Roadmap

### Phase 2 — Advanced Products

- [ ] Migrate from flat `products.color` / `products.size` columns to a **Parent–Child variant matrix**
- [ ] Introduce `product_variants` with overlapping attribute combinations and dynamic SKUs
- [ ] Admin UI for variant inventory sync and thumbnail galleries per SKU

### Phase 3 — Localization & FinTech

- [ ] Multi-language routing (**Arabic / English**) with locale-aware slugs and RTL layout toggles
- [ ] Dual-currency engine (**USD / IQD**) with runtime exchange resolution
- [ ] IQD-specific numeric formatting—suppress decimals and apply smart rounding rules exclusively for Iraqi Dinar display

### Phase 4 — Operations & Retention

- [ ] Real-time order tracking (broadcasting / websockets)
- [ ] Push notification service for shipment state changes
- [ ] Native customer loyalty point ledger and redemption checkout hooks

---

## 📁 Project Structure (High Level)

```
rymo-ecommerce/
├── app/
│   ├── Http/Controllers/
│   │   ├── OrderController.php # Guest checkout persistence
│   │   └── ShopController.php  # Catalog & product detail
│   └── Models/                 # Order, OrderItem, Cart, CartItem, Product, …
├── database/
│   ├── migrations/             # Commerce schema (guest checkout fields, …)
│   └── seeders/
├── resources/
│   ├── css/app.css             # Bootstrap-era brand styles + shop overrides
│   └── js/
│       ├── components/         # ProductCard, Header, …
│       ├── composables/        # useCart.ts (localStorage cart state)
│       ├── layouts/            # ShopLayout.vue
│       ├── pages/              # Home, Shop, Cart, Checkout, Blog, Contact, …
│       └── types/              # TypeScript domain interfaces
├── public/img/                 # Storefront media assets
├── routes/web.php              # Storefront + checkout routes
└── tests/Feature/              # Pest coverage (incl. CheckoutOrderTest)
```

---

## ⚙️ Installation Guide

### Prerequisites

- PHP **8.2+**
- Composer **2.x**
- Node.js **18+** & npm
- MySQL **8.x** (recommended) — or SQLite for rapid local prototyping

---

### 1️⃣ Clone & Install Dependencies

```bash
git clone <your-repository-url> rymo-ecommerce
cd rymo-ecommerce

composer install
npm install
```

---

### 2️⃣ Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` for **MySQL**:

```env
APP_NAME=Rymo
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rymo
DB_USERNAME=root
DB_PASSWORD=
```

> 💡 **Tip:** For zero-config local trials, keep `DB_CONNECTION=sqlite` and create `database/database.sqlite`.

---

### 3️⃣ Database Migration & Seeding

```bash
php artisan migrate:fresh --seed
```

---

### 4️⃣ Frontend Assets

Ensure storefront images are present under `public/img/` (brand, shop, blog, cart media).

```bash
npm run dev
```

---

### 5️⃣ Run the Application

**Option A — Dual process (recommended for development):**

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

**Option B — Unified dev orchestration:**

```bash
composer run dev
```

> Runs `php artisan serve`, queue listener, and Vite concurrently.

---

### 6️⃣ Run Tests

```bash
php artisan test --compact
```

---

## 🌐 Default Routes

| Route | Method | Page / Action |
|-------|--------|---------------|
| `/` | GET | Home |
| `/shop` | GET | Product catalog |
| `/shop/{slug}` | GET | Product detail |
| `/blog` | GET | Blog index |
| `/cart` | GET | Shopping cart |
| `/checkout` | GET | Checkout (shipping details & order summary) |
| `/checkout` | POST | Place order (`checkout.store`) — guest-friendly, no auth |
| `/contact` | GET | Contact form |
| `/contact` | POST | Submit contact message |
| `/about` | GET | About (placeholder) |

### Checkout payload (POST `/checkout`)

```json
{
  "customer_info": {
    "name": "Jane Guest",
    "phone": "+1 555-0100",
    "address": "123 Main Street, Springfield, US"
  },
  "cart_items": [
    { "id": 1, "quantity": 2 }
  ]
}
```

On success, the server redirects to `/cart` with a flash message; the client clears `localStorage` and shows the confirmation state.

---

## 📜 License

This project is open-source under the **[MIT License](LICENSE)** unless otherwise specified by the repository owner.

---

<div align="center">

**Built with structural discipline. Designed to scale without rewriting history.**

⭐ *If Rymo helped you evaluate my engineering trajectory—consider starring the repo.*

</div>
