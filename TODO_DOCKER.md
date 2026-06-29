# Docker / Dev omgeving - Checklist

- [x] Dockerfile (dev) aangemaakt
- [x] docker-compose.yml aangemaakt met services: app, db (MySQL), phpMyAdmin
- [x] DevDatabaseSeeder toegevoegd (calls DatabaseSeeder)
- [ ] Controleer command in docker-compose: migrate + db:seed draait
- [ ] Starten:
  - `docker compose up --build`
- [ ] Open:
  - App: http://localhost:8000
  - phpMyAdmin: http://localhost:8080

