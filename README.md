# FitTrack API (fitness-planner-backend)

Laravel REST API za FitTrack aplikaciju (Dashboard / Exercises / Planner / Journal / Insights). Frontend je poseban React + Vite projekat (`fitness-planner`); ovaj repo je samo backend, testira se kroz Postman (nema frontend view-ove).

Videti i: [REQUIREMENTS_MAPPING.md](REQUIREMENTS_MAPPING.md) (gde je koji zahtev implementiran) i [GIT_WORKFLOW.md](GIT_WORKFLOW.md) (GitHub Classroom + podela komitova u timu).

## Pokretanje lokalno

Potrebno: PHP 8.2+, Composer.

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # ili: New-Item database/database.sqlite (PowerShell)
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

php -v
composer -V
cd C:\Users\Nikola\dev-projects\fitness-planner-backend
php artisan serve

API je dostupan na `http://127.0.0.1:8000/api`.

Podrazumevani nalozi (iz seedera, `database/seeders/DatabaseSeeder.php`):
- Admin: `admin@fittrack.test` / `password`
- Član: `test@fittrack.test` / `password`

## Testiranje kroz Postman

1. Napravi Postman kolekciju sa base URL-om `http://127.0.0.1:8000/api`.
2. `POST /login` sa `{"email":"test@fittrack.test","password":"password"}` → kopiraj `token` iz odgovora.
3. Za sve zaštićene rute dodaj header `Authorization: Bearer <token>`.
4. Za slike dokumentacije: uključi datum/vreme na ekranu (npr. taskbar sat) da bude vidljivo na screenshotu, kako zahteva template dokumentacije.

### Pregled ruta

| Metoda | Ruta | Ko sme | Opis |
|---|---|---|---|
| POST | `/api/register` | svi | Registracija (kreira `member` korisnika) |
| POST | `/api/login` | svi | Prijava, vraća Sanctum token |
| POST | `/api/logout` | ulogovan | Odjava (briše trenutni token) |
| GET | `/api/me` | ulogovan | Podaci o trenutnom korisniku |
| POST | `/api/forgot-password` | svi | Šalje reset link (loguje se u `storage/logs/laravel.log` jer je `MAIL_MAILER=log`) |
| POST | `/api/reset-password` | svi | Postavlja novu lozinku uz token iz emaila |
| GET | `/api/exercises` | svi (i gost) | Katalog vežbi — `?search=&category=&muscle=&sort=&per_page=` |
| GET | `/api/exercises/{id}` | svi (i gost) | Detalji jedne vežbe |
| POST/PUT/DELETE | `/api/exercises[/{id}]` | admin | CRUD nad katalogom vežbi |
| POST | `/api/exercises/sync` | admin | Povlači vežbe sa javnog wger.de API-ja |
| GET/POST | `/api/workout-sessions` | ulogovan | Resource ruta — treninzi (Planner/Journal); admin može `?all=1` |
| GET/PUT/DELETE | `/api/workout-sessions/{id}` | vlasnik/admin | CRUD nad jednim treningom |
| GET | `/api/workout-sessions/export` | ulogovan | CSV izvoz istorije treninga |
| GET/POST | `/api/workout-sessions/{id}/exercises` | vlasnik/admin | Ugnježdena ruta — stavke treninga |
| PUT/DELETE | `/api/workout-sessions/{id}/exercises/{itemId}` | vlasnik/admin | Izmena/brisanje stavke |
| GET | `/api/insights/summary` | ulogovan | Agregirani podaci za grafike (volumen po nedelji, treninzi po danu, trenutna temperatura) |
| POST | `/api/user/avatar` | ulogovan | Upload profilne slike (multipart/form-data, polje `avatar`) |
| GET | `/api/admin/users` | admin | Lista svih korisnika |

## Tehnologije

- **Laravel 13** + **Sanctum** (token-based API auth)
- **SQLite** (lokalno, `database/database.sqlite`) — lako se menja na MySQL kroz `.env` (`DB_CONNECTION=mysql`)
- Eksterni javni servisi: [wger.de](https://wger.de/en/software/api) (katalog vežbi), [Open-Meteo](https://open-meteo.com/) (vremenska prognoza, bez API ključa)

## Povezivanje sa React frontendom

Frontend (`fitness-planner`, poseban repo) treba da čita `VITE_API_URL=http://127.0.0.1:8000/api` i da token čuva (npr. u `localStorage`) posle logina, pa ga šalje kao `Authorization: Bearer <token>` na svaki zahtev ka zaštićenim rutama.
