---
description: How to Set Up Pusher for Real-time Chat (Laravel & Vue.js)
---

# Workflow: Real-time Chat with Pusher

1. **Pusher Setup**
   - Create account and app at [Pusher.com](https://pusher.com/).
   - Copy credentials to `.env`.

2. **Backend Configuration**
   - Install PHP SDK: `composer require pusher/pusher-php-server`.
   - Update `BROADCAST_CONNECTION=pusher` in `.env`.
   - Create Event: `php artisan make:event MessageSent`.
   - Define Logic in Event: Ensure it implements `ShouldBroadcast`.

3. **Frontend Integration**
   - Install JS libraries: `npm install --save-dev laravel-echo pusher-js`.
   - Configure Echo in `resources/js/bootstrap.js`.
   - Build UI: Create Vue components to send/receive messages.
   - Run Vite: `npm run dev`.

4. **Deployment & Process Management**
   - Ensure `queue:work` is running if using queues for broadcasting.
   - Verify connection in Pusher Debug Console.
