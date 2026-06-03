# Hartslag Lopik — AED Beheerdashboard

Een webapplicatie voor **Stichting Hartslag Lopik** voor het beheren van de eigen administratie rondom AED's. De applicatie is een aanvulling op het landelijke systeem [HartslagNu](https://www.hartslagnu.nl) en biedt een centraal overzicht van alle AED's, onderhoudshistorie, keuringen en signaleringen.

---

## Inhoudsopgave

- [Over het project](#over-het-project)
- [Functionaliteiten](#functionaliteiten)
- [Technische stack](#technische-stack)
- [Vereisten](#vereisten)
- [Installatie](#installatie)
- [Configuratie](#configuratie)
- [Gebruik](#gebruik)
- [Onderhoud](#onderhoud)
- [Projectstructuur](#projectstructuur)
- [Continuïteit en beheer](#continuïteit-en-beheer)
- [Licentie](#licentie)

---

## Over het project

Stichting Hartslag Lopik beheert een netwerk van openbare AED's in de gemeente Lopik. Deze applicatie is ontwikkeld als afstudeerstage-project en vervangt de versnipperde administratie in losse Excel-bestanden, e-mails en persoonlijke notities door één centraal beheersysteem.

De applicatie is **niet** bedoeld als vervanging van HartslagNu, maar als aanvulling daarop — specifiek voor het interne beheer, de onderhoudshistorie en het signaleren van verlopen keuringen of batterijen.

---

## Functionaliteiten

- **AED-registratie** — toevoegen, bewerken en archiveren van AED-locaties
- **Onderhoudsbeheer** — vastleggen van keuringen, batterijvervangingen en elektrodewissels
- **Signaleringen** — automatische meldingen bij naderende vervaldatums
- **Onderhoudshistorie** — aantoonbaar overzicht per AED voor bij incidenten of audits
- **Beheerders** — koppeling van AED's aan contactpersonen of locatiebeheerders
- **Dekkingsoverzicht** — inzicht in gebieden met en zonder openbare AED-dekking
- **Gebruikersbeheer** — rolgebaseerde toegang voor bestuursleden

---

## Technische stack

| Onderdeel | Technologie |
|---|---|
| Framework | [Laravel 12](https://laravel.com) |
| Taal | PHP 8.2+ |
| Frontend | Blade, [Tailwind CSS](https://tailwindcss.com) |
| Authenticatie | [Laravel Breeze](https://laravel.com/docs/starter-kits) |
| Database | SQLite (development) / MySQL (productie) |
| Build tool | [Vite](https://vitejs.dev) |
| Vertalingen | laravel-lang |

---

## Vereisten

Zorg dat de volgende software geïnstalleerd is voordat je begint:

- **PHP** 8.2 of hoger
- **Composer** 2.x
- **Node.js** 18 of hoger en **npm**
- **Git**

Optioneel voor productie:
- MySQL 8 of MariaDB 10.4+
- Een webserver zoals Nginx of Apache

---

## Installatie

### 1. Repository klonen

```bash
git clone https://github.com/Kevin-1111102/hartslag-lopik-dashboard.git
cd hartslag-lopik-dashboard
```

### 2. PHP-afhankelijkheden installeren

```bash
composer install
```

### 3. Omgevingsbestand aanmaken

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database aanmaken en migraties uitvoeren

```bash
touch database/database.sqlite
php artisan migrate --seed
```

### 5. Frontend-afhankelijkheden installeren en bouwen

```bash
npm install
npm run build
```

### 6. Applicatie starten

```bash
php artisan serve
```

De applicatie is nu bereikbaar op [http://localhost:8000](http://localhost:8000).

---

## Configuratie

Stel de volgende variabelen in het `.env`-bestand in:

```dotenv
APP_NAME="Hartslag Lopik"
APP_ENV=production
APP_URL=https://jouw-domein.nl

# Database (productie: schakel over naar MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hartslag_lopik
DB_USERNAME=gebruiker
DB_PASSWORD=wachtwoord

# E-mail voor notificaties
MAIL_MAILER=smtp
MAIL_HOST=smtp.jouwprovider.nl
MAIL_PORT=587
MAIL_USERNAME=noreply@jouwdomein.nl
MAIL_PASSWORD=wachtwoord
MAIL_FROM_ADDRESS="noreply@jouwdomein.nl"
MAIL_FROM_NAME="Hartslag Lopik"
```

> ⚠️ Sla het `.env`-bestand nooit op in Git. Het bevat gevoelige gegevens.

---

## Gebruik

### Inloggen

Na de eerste installatie zijn er standaard geen gebruikers aangemaakt. Maak een eerste account aan via de registratiepagina, of voeg een seed toe via de database seeder.

### Dagelijks gebruik

Raadpleeg de **Beheerdershandleiding** (los PDF-document) voor uitleg over het toevoegen van AED's, het registreren van keuringen en het interpreteren van signaleringen.

---

## Onderhoud

### Periodiek onderhoud (aanbevolen schema)

| Frequentie | Actie |
|---|---|
| Maandelijks | `composer outdated` uitvoeren — controleer op beveiligingsupdates |
| Per kwartaal | Laravel updaten naar nieuwste patch-versie (`composer update`) |
| Halfjaarlijks | `npm outdated` uitvoeren — frontend-dependencies bijwerken |
| Jaarlijks | Controleer of de PHP-versie op de server nog ondersteund wordt |

### Dependency-updates uitvoeren

```bash
# PHP-packages updaten
composer update

# Daarna altijd migraties controleren
php artisan migrate

# Frontend opnieuw bouwen
npm install
npm run build
```

> Maak altijd een **database-backup** vóór een update:
> ```bash
> php artisan db:monitor
> # Of direct via de hostingprovider een dump maken
> ```

### Beveiligingsproblemen melden

Ontdek je een kwetsbaarheid? Neem dan rechtstreeks contact op met de projectbeheerder en publiceer dit niet openbaar.

---

## Projectstructuur

```
hartslag-lopik-dashboard/
├── app/
│   ├── Http/Controllers/   # Applicatielogica
│   ├── Models/             # Eloquent-modellen (AED, Keuring, Beheerder, ...)
│   └── Notifications/      # E-mailnotificaties voor signaleringen
├── database/
│   ├── migrations/         # Databasestructuur
│   └── seeders/            # Testdata
├── resources/
│   ├── views/              # Blade-templates
│   └── css/ & js/          # Frontend-bronbestanden
├── routes/
│   └── web.php             # Alle applicatieroutes
├── tests/                  # Geautomatiseerde tests
├── .env.example            # Voorbeeldconfiguratie
└── README.md               # Dit bestand
```

---

## Continuïteit en beheer

Dit project is ontwikkeld als afstudeerstage-opdracht. Onderstaande afspraken zijn gemaakt om de continuïteit na de stageperiode te borgen.

### Eigenaarschap

| Rol | Verantwoordelijkheid |
|---|---|
| Stichting Hartslag Lopik | Eigenaar van de applicatie en de data |
| Aangewezen bestuurslid | Eerste aanspreekpunt bij vragen of storingen |
| Technisch beheerder / opvolger | Uitvoeren van updates en technisch onderhoud |

### Toegangen die overgedragen moeten worden

- [ ] Inloggegevens hostingprovider
- [ ] Databasetoegang (productie)
- [ ] Domeinnaam en verloopdatum
- [ ] GitHub-toegang als collaborator
- [ ] E-mailaccount voor notificaties

### Wat te doen bij een storing

1. Controleer of de server bereikbaar is via de hostingprovider
2. Bekijk de Laravel-logbestanden: `storage/logs/laravel.log`
3. Raadpleeg de beheerdershandleiding voor veelvoorkomende problemen
4. Neem contact op met de technisch beheerder of een Laravel-developer

### Noodprocedure bij uitval

Als de applicatie tijdelijk niet beschikbaar is, kan teruggevallen worden op een CSV-export van de AED-gegevens. Deze export is beschikbaarop de aed scherm  in de applicatie.

---

## Licentie

Dit project is eigendom van Stichting Hartslag Lopik. De broncode is uitsluitend bestemd voor intern gebruik door of namens de stichting.

---

*Ontwikkeld door Kevin — Afstudeerstage Software Development, 2025*
