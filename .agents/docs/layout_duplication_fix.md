# Documentation: Layout Duplication Fix in Inertia Vue 3

## 1. Overview
This document outlines the root causes and permanent resolution for the persistent "Duplicate Layout" (double Header/Footer) issue encountered on the home page and during logo-based navigation in the **Suckhoe24h** project.

## 2. Issues Encountered
- **Duplicate UI Components**: Multiple instances of Header and Footer appearing on the home page.
- **Inertia Raw Data Screen**: Intermittent display of raw JSON data instead of a rendered Vue component.
- **Full Page Reloads**: Clicking navigation links (specifically the logo) triggered a full browser refresh instead of a smooth SPA (Single Page Application) transition.
- **Multiple Vue Instances**: Console logs showed both Vue 2.3 (legacy) and Vue 3 (Inertia) running simultaneously.

## 3. Root Causes Identification
- **Internal Glob Invocation**: Calling `import.meta.glob` inside the `resolve` function of `createInertiaApp` caused redundant file system scans on every navigation, leading to layout re-mounting conflicts.
- **Legacy Script Conflicts**: Files like `cart.js` and `user.js` in `public/js/` used `DOMContentLoaded` and manual DOM manipulations that conflicted with the Vue 3 mounting cycle.
- **Link Implementation**: Navigation elements using plain `<a>` tags bypassed the Inertia router, forcing full document resets.
- **Module Resolution Latency**: Synchronous component resolution occasionally failed during high-traffic or slow network states, exposing raw props data.

## 4. The Resolution (Implemented Fixes)

### 4.1. Optimized `app.js` Structure
We refactored `resources/js/app.js` to handle module loading outside the core loop and implemented an async resolver:
```javascript
// Optimized: Glob called once outside
const pages = import.meta.glob("./Pages/**/*.vue");

createInertiaApp({
    resolve: async (name) => {
        const pagePath = `./Pages/${name}.vue`;
        const pageModule = await pages[pagePath](); // Async resolution
        const page = pageModule.default;

        // Static Layout Assignment
        if (name.startsWith("Admin/")) {
            page.layout = page.layout || AdminLayout;
        } else if (name.startsWith("Public/")) {
            page.layout = page.layout || PublicLayout;
        }
        return page;
    },
    // ... setup logic
});
```

### 4.2. Universal Link Interception
A manual click listener was added to ensure all links with `data-inertia` (especially those in heritage sections or generated via Blade) are treated as SPA navigations:
```javascript
document.addEventListener("click", (event) => {
    const anchor = event.target.closest("a[data-inertia]");
    if (!anchor) return;
    const href = anchor.getAttribute("href");
    if (!href || href.startsWith("http")) return;
    event.preventDefault();
    router.visit(href);
});
```

### 4.3. Blade Template Cleanup
Legacy scripts were commented out in `resources/views/app.blade.php` to prevent the "Double Vue" conflict:
- `admin-notifications.js`
- `cart.js`
- `user.js`

## 5. Deployment Verification
To apply these fixes correctly, the following steps must be followed:
1. `npm run build` (Ensures the optimized bundle contains the new logic).
2. `php artisan optimize:clear` (Server-side cache clearing).
3. `Ctrl + F5` (Hard browser refresh on desktop/mobile) to clear old JavaScript assets from the browser cache.

---
**Last Updated:** 2026-04-07
**Status:** Resolved
