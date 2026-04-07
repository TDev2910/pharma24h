# Pharma24H — AI Healthcare & Pharmacy Platform

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php&logoColor=white"/>
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/Vue.js-3-4FC08D?style=flat-square&logo=vue.js&logoColor=white"/>
  <img src="https://img.shields.io/badge/Inertia.js-9553E9?style=flat-square&logo=inertia&logoColor=white"/>
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white"/>
  <img src="https://img.shields.io/badge/Docker-ready-2496ED?style=flat-square&logo=docker&logoColor=white"/>
  <img src="https://img.shields.io/badge/CI%2FCD-GitHub_Actions-2088FF?style=flat-square&logo=github-actions&logoColor=white"/>
  <img src="https://img.shields.io/badge/Live-healthviet.com-brightgreen?style=flat-square"/>
</p>

> A full-stack healthcare & pharmacy platform featuring an AI medical assistant (Gemini RAG), real-time payment processing, live chat, and automated logistics — built solo in 6+ months and deployed to production.

**Live Demo:** [healthviet.com](https://healthviet.com)

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Git Workflow & Commit Convention](#git-workflow--commit-convention)
- [CI/CD & Deployment](#cicd--deployment)
- [Test Accounts](#test-accounts)
- [Load Testing](#load-testing)
- [Author](#author)

---

## Overview

Pharma24H is a production-ready healthcare platform that allows users to:
- Browse and purchase pharmaceutical products online
- Consult an AI-powered medical assistant in real time
- Pay securely via VNPay or SePay
- Track orders with GHN shipping integration
- Chat live with pharmacy staff

Admins and staff manage products, orders, users, and content through a dedicated dashboard with role-based access control.

---

## Features

### AI Medical Assistant (RAG)
- Self-implemented **Retrieval-Augmented Generation** pipeline using **Google Gemini API**
- AI answers health-related questions based on a curated local dataset — not raw API calls
- Responses streamed in real time via **Server-Sent Events (SSE)**

### Authentication & Authorization
- **Laravel Sanctum** + **Google OAuth** for secure login
- Role-based access control: `Admin` / `Staff` / `User`
- Rate limiting on sensitive endpoints to prevent abuse

### Payment Integration
- **VNPay** and **SePay** integrated and tested in both sandbox and production environments
- Webhook/IPN handling with checksum verification to prevent transaction spoofing
- Idempotent payment processing to handle duplicate IPN callbacks

### Real-time Features
- Live chat between users and staff using **Laravel Broadcasting + Pusher**
- Background jobs (email notifications, order status updates) via **Laravel Queues**

### Logistics
- **GHN API** integration for automated shipping fee calculation and order tracking

###  DevOps
- Containerized with **Docker** (separate local and production compose files)
- **GitHub Actions** CI/CD pipeline with branch-based workflow (`dev` → `main`)

---

## Tech Stack

| Layer | Technologies |
|---|---|
| **Backend** | PHP 8.2, Laravel 12, Laravel Sanctum, Eloquent ORM, RESTful API |
| **Frontend** | Vue 3 (Composition API), Inertia.js, Bootstrap 5, Axios, Vite |
| **Database** | MySQL 8.x, Firebase (real-time data) |
| **AI** | Google Gemini API (RAG strategy), Server-Sent Events (SSE) |
| **Payments** | VNPay, SePay (sandbox + production tested) |
| **Real-time** | Laravel Broadcasting, Pusher, Laravel Echo |
| **Queue** | Laravel Queues (Database driver) |
| **DevOps** | Docker, Docker Compose, GitHub Actions (CI/CD), CPanel |
| **Logistics** | GHN API (shipping fee + order tracking) |
| **Testing** | K6 Load Testing |
| **Tools** | Git, Postman, Laragon, Vercel, Stitch |

---

## Architecture

This project follows **Hexagonal Architecture** (Ports & Adapters), clearly separating domain logic from infrastructure and framework concerns. Each module (e.g. `Customer`, `Order`, `Product`) is self-contained with its own Domain, Ports, Application, and Infrastructure layers.

```
app/
├── Core/
│   └── Customer/
│       ├── Domain/
│       │   └── DTOs/
│       │       └── CustomerData.php
│       ├── Ports/
│       │   ├── Inbound/
│       │   │   └── CustomerUseCaseInterface.php
│       │   └── Outbound/
│       │       └── CustomerRepositoryInterface.php
│       └── Application/
│           └── Services/
│               └── CustomerService.php        # Implements Inbound Port, depends on Outbound Port
│
├── Infrastructure/
│   └── Persistence/
│       └── Eloquent/
│           └── CustomerRepository.php         # Implements Outbound Port
│
└── Http/
    └── Controllers/
        ├── Admin/Customer/CustomerController.php
        └── Staff/StaffCustomerController.php
```

### Layer Responsibilities

| Layer | Responsibility |
|---|---|
| **Domain / DTOs** | Pure data structures — no framework dependency |
| **Ports / Inbound** | Interface defining what use cases the application exposes |
| **Ports / Outbound** | Interface defining what the application needs from infrastructure |
| **Application / Services** | Implements Inbound Port, orchestrates domain logic, calls Outbound Port |
| **Infrastructure / Eloquent** | Concrete implementation of Outbound Port using Laravel Eloquent |
| **Http / Controllers** | Entry point — validates request, calls Application Service, returns response |

### Data Flow Example (Place Order)

```
HTTP Request
    │
    ▼
OrderController          # Http layer — validates, delegates
    │
    ▼
OrderService             # Application layer — orchestrates business logic
    │  (via OrderRepositoryInterface — Outbound Port)
    ▼
EloquentOrderRepository  # Infrastructure layer — Eloquent + MySQL
```

This structure keeps domain logic **framework-agnostic** — swapping Eloquent for another ORM requires changes only in the Infrastructure layer.

---

## Git Workflow & Commit Convention

### Branch Strategy

```
main          ← production-ready, deployed to healthviet.com
  └── dev     ← integration branch, CI/CD tested here first
        ├── feature/add-payment-vnpay
        ├── feature/ai-rag-gemini
        ├── fix/ipn-race-condition
        └── chore/update-docker-config
```

**Rules:**
- All development happens on `feature/*` or `fix/*` branches cut from `dev`
- Pull requests merge into `dev` first — must pass CI before merging
- `dev` → `main` only when the build is clean, tested, and stable
- Direct pushes to `main` are not allowed

### Commit Message Convention

This project follows [Conventional Commits v1.0.0](https://www.conventionalcommits.org/en/v1.0.0/).

**Format:**
```
<type>[optional scope]: <description>

[optional body]

[optional footer]
```

**Types used in this project:**

| Type | When to use |
|---|---|
| `feat` | New feature (e.g. `feat(payment): integrate SePay webhook`) |
| `fix` | Bug fix (e.g. `fix(ipn): handle duplicate VNPay callbacks`) |
| `refactor` | Code restructure without behavior change |
| `chore` | Tooling, config, dependencies (e.g. `chore: update docker-compose`) |
| `docs` | Documentation only (e.g. `docs: update README architecture`) |
| `test` | Add or update tests |
| `ci` | CI/CD pipeline changes |

**Examples:**
```bash
feat(auth): add Google OAuth login with Sanctum
fix(queue): retry failed email jobs with exponential backoff
feat(ai): implement RAG pipeline with Gemini API and SSE streaming
chore(docker): separate local and production compose files
ci: add GitHub Actions workflow for dev branch deployment
```

---

## CI/CD & Deployment

```
feature/* or fix/*
    │
    │  Pull Request → dev
    ▼
dev branch
    │
    │  GitHub Actions triggered on push to dev
    ├── composer install --no-dev
    ├── npm run build
    └── php artisan config:cache
    │
    │  ✅ All checks pass → Pull Request: dev → main
    ▼
main branch
    │
    │  GitHub Actions triggered on push to main
    ▼
SSH into production server (CPanel)
    │
    ├── git pull origin main
    ├── composer install --no-dev --optimize-autoloader
    ├── php artisan migrate --force
    ├── php artisan config:cache
    ├── php artisan route:cache
    └── php artisan queue:restart
    │
    ▼
🚀 Live at healthviet.com (~3–4 minutes)
```

---

## 🔑 Test Accounts

You can log in to explore the system with the following demo accounts:

> ⚠️ These accounts are for demo/testing purposes only. Please do not change passwords or delete data.
> ⚠️ These accounts are for demo/testing purposes only. Please do not change passwords or delete data.
> Nếu mọi người có test - là người tốt mong đừng phá data và đổi tài khoản nhé vì mình muốn mọi người có thể trải nghiệm
                                    | Role | Email | Password |
                                    |---|---|---|
                                    | **Admin** | admin@example.com | `Trong123` |
                                    | **Staff** | banhmibosua123@gmail.com | `Trong2910` |
                                    | **User** | phamchitrong2910@gmail.com | `Trong123` |
> ⚠️ These accounts are for demo/testing purposes only. Please do not change passwords or delete data.
> ⚠️ These accounts are for demo/testing purposes only. Please do not change passwords or delete data.



## Load Testing

Performed with **K6** on key endpoints (product listing, checkout):

| Metric | Result |
|---|---|
| Virtual Users | 25–30 VU |
| Avg Response Time | **486ms** |
| Environment | Local Docker |
| Endpoints Tested | `/products`, `/checkout`, `/orders` |

```bash
k6 run stress_test.js
```

Identified bottleneck: unindexed category/price filter queries on the products table.

---

## Author

**Phạm Chí Trọng**
- phamchitrong2910@gmail.com
- 0901 645 269
- [github.com/TDev2910](https://github.com/TDev2910)
- [healthviet.com](https://healthviet.com)

---

<p align="center">Built with ❤️ by Phạm Chí Trọng — 2024/2025</p>
