##Dự án đang trong quá trình phát triển lại giao diện - một số bug và giao diện sẽ bị vỡ đang trong quá trình phát triển lại tối ưu hơn

#  Pharma24H — AI Healthcare & Pharmacy Platform

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php&logoColor=white"/>
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/Vue.js-3-4FC08D?style=flat-square&logo=vue.js&logoColor=white"/>
  <img src="https://img.shields.io/badge/Docker-ready-2496ED?style=flat-square&logo=docker&logoColor=white"/>
  <img src="https://img.shields.io/badge/CI%2FCD-GitHub_Actions-2088FF?style=flat-square&logo=github-actions&logoColor=white"/>
  <img src="https://img.shields.io/badge/Live-healthviet.com-brightgreen?style=flat-square"/>
</p>

> A full-stack healthcare & pharmacy platform featuring an AI medical assistant, real-time payment processing, live chat, and automated logistics — built solo in 6+ months and deployed to production.

**Live Demo:** [healthviet.com](https://healthviet.com)
account staff : banhmibosua123@gmail.com / Trong2910
account admin : admin@example.com / Trong123

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Getting Started](#getting-started)
- [Environment Variables](#environment-variables)
- [CI/CD & Deployment](#cicd--deployment)
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

### DevOps
- Containerized with **Docker** (separate local and production compose files)
- **GitHub Actions** CI/CD pipeline → auto-deploy to CPanel on every push to `main`

---

## Tech Stack

| Layer | Technologies |
|---|---|
| **Backend** | PHP 8.2, Laravel 12, Laravel Sanctum, Eloquent ORM |
| **Frontend** | Vue 3 (Composition API), Inertia.js, Bootstrap 5, Axios |
| **Database** | MySQL, Firebase (real-time data) |
| **AI** | Google Gemini API (RAG strategy) |
| **Payments** | VNPay, SePay |
| **Real-time** | Laravel Broadcasting, Pusher |
| **DevOps** | Docker, GitHub Actions (CI/CD), CPanel |
| **Logistics** | GHN API |
| **Testing** | K6 Load Testing |

---

## Architecture

This project follows **Hexagonal Architecture** (Ports & Adapters), separating domain logic from infrastructure concerns:

```
app/
├── Domain/              # Business entities, interfaces, rules
│   ├── Order/
│   ├── Product/
│   └── User/
├── Application/         # Use cases orchestrating domain logic
│   ├── PlaceOrderUseCase.php
│   └── ConsultAIUseCase.php
├── Infrastructure/      # Concrete implementations (Eloquent, APIs)
│   ├── Repositories/
│   ├── Payment/
│   └── Shipping/
└── Http/                # Controllers, Middleware, Requests
```

This structure keeps domain logic framework-agnostic and makes each layer independently testable.

---

## Getting Started

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- Docker & Docker Compose
- MySQL 8.x

### Local Setup (Docker)

```bash
# Clone the repository
git clone https://github.com/TDev2910/pharma24h.git
cd pharma24h

# Copy environment file
cp .env.example .env

# Start Docker containers
docker-compose -f docker-compose.local.yml up -d

# Install dependencies
docker exec -it app composer install
docker exec -it app php artisan key:generate
docker exec -it app php artisan migrate --seed

# Install frontend dependencies
npm install && npm run dev
```

Visit `http://localhost` in your browser.

---



## CI/CD & Deployment

GitHub Actions workflow on push to `main`:

```
git push main
    │
    ▼
GitHub Actions triggered
    │
    ├── composer install
    ├── npm run build
    │
    ▼
SSH into production server
    │
    ├── git pull origin main
    ├── php artisan migrate --force
    ├── php artisan config:cache
    └── php artisan queue:restart
    │
    ▼
Live at healthviet.com ✓
```

Total deploy time: ~3–4 minutes.

---

## Load Testing

Performed with **K6** on key endpoints (product listing, checkout):

| Metric | Result |
|---|---|
| Virtual Users | 25–30 VU |
| Avg Response Time | **486ms** |
| Environment | Local Docker |
| Endpoints Tested | `/products`, `/checkout`, `/orders` |

<img width="1114" height="645" alt="image" src="https://github.com/user-attachments/assets/084f5383-1cbc-4cd9-83fe-be6a4dbeb233" />

```bash
# Run load test
k6 run stress_test.js
```

Identified bottleneck: unindexed category/price filter queries on the products table.

---

## Author

**Phạm Chí Trọng**
-  phamchitrong2910@gmail.com
- 0901 645 269
- [github.com/TDev2910](https://github.com/TDev2910)
- [healthviet.com](https://healthviet.com)


