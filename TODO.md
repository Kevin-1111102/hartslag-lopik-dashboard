# Admin User Dashboard TODO

# ✅ Admin User Dashboard COMPLETE!

## Implemented:
- [✅] AdminMiddleware & registration
- [✅] AdminController (index/store/destroy)
- [✅] Routes: /admin/users
- [✅] Nav tab "Gebruikers" (admin-only)
- [✅] Full view: users table + add form + delete + flashes

## Test:
```
php artisan migrate
# Make a user is_admin=1 in DB
# Login → Gebruikers tab → Test add/delete
```

**Optional:** Delete unused `resources/views/admin/dashboard.blade.php`


