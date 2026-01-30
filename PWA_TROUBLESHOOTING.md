# PWA Troubleshooting (Mobile Not Working)

Follow these steps **on your server** and when testing on mobile.

## 1. Set APP_URL on the server

In your **server** `.env` file, set the full HTTPS URL **with no trailing slash**:

```env
APP_URL=https://ledgerly.3eyon-host.com
```

Then clear and rebuild config cache:

```bash
php artisan config:clear
php artisan config:cache
```

If `APP_URL` is wrong (e.g. `http://` or `localhost`), the manifest and service worker may fail on mobile.

---

## 2. Web server document root must be `public/`

Laravel must be served with the **document root** pointing to the `public` folder so that:

- `https://your-domain.com/sw.js` → serves `public/sw.js`
- `https://your-domain.com/assets/...` → serves `public/assets/...`

**Nginx** example:

```nginx
root /path/to/billing-app/public;
```

**Apache** (in `public/.htaccess` or vhost): `DocumentRoot` should be `.../public`.

If your document root is the project root instead of `public`, `/sw.js` will 404 and the PWA won’t install.

---

## 3. Test these URLs in a browser (desktop or mobile)

Open each URL and confirm it loads (no 404):

| URL | What to check |
|-----|----------------|
| `https://ledgerly.3eyon-host.com/manifest.webmanifest` | Returns JSON with `name`, `start_url`, `icons`. No PHP errors or HTML. |
| `https://ledgerly.3eyon-host.com/sw.js` | Returns JavaScript (service worker code). |
| `https://ledgerly.3eyon-host.com/assets/minia/images/favicon.ico` | Returns the favicon image. |

If any return 404 or HTML (Laravel error page), fix routing or document root first.

---

## 4. HTTPS only

Service workers and “Add to Home Screen” require **HTTPS** (or `localhost`).  
If the site is only HTTP on the server, PWA will not work. Ensure SSL is enabled and `APP_URL` uses `https://`.

---

## 5. Mobile testing checklist

1. **Clear site data** for your domain on the phone:
   - **Chrome (Android):** Settings → Site settings → [your site] → Clear & reset storage.
   - **Safari (iOS):** Settings → Safari → Advanced → Website Data → remove your site.

2. **Reload** the site over HTTPS (e.g. `https://ledgerly.3eyon-host.com`).

3. **Install / Add to Home Screen:**
   - **Android Chrome:** Menu (⋮) → “Install app” or “Add to Home screen”.
   - **iOS Safari:** Share → “Add to Home Screen”.

4. If there is no install option:
   - **Android:** Manifest and service worker must load without errors (see step 3).
   - **iOS:** “Add to Home Screen” works without a service worker; ensure manifest and `apple-mobile-web-app-capable` are present (already in the app layouts).

---

## 6. Optional: check service worker in Chrome (desktop)

1. Open `https://ledgerly.3eyon-host.com`.
2. F12 → **Application** tab → **Service Workers**.
3. You should see the worker for `/sw.js` (e.g. “activated and is running”).
4. If it shows “Registration failed” or “Script load failed”, fix `/sw.js` (must be same origin and served as JS) and document root.

---

## Quick server checklist (after git pull)

```bash
# On server
cd /path/to/billing-app
git pull

# Ensure .env has correct APP_URL (no trailing slash)
# APP_URL=https://ledgerly.3eyon-host.com

php artisan config:clear
php artisan config:cache
# If you use route cache:
php artisan route:clear
php artisan route:cache
```

Then test the manifest and `/sw.js` URLs and try “Add to Home Screen” again on mobile.
