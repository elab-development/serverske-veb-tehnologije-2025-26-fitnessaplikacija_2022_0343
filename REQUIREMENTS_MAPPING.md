# Mapa zahteva → kod

Ovaj dokument pokazuje tačno gde se u projektu nalazi svaki zahtev iz teksta domaćeg zadatka (Laravel) i seminarskog rada. Putanje su relativne u odnosu na koren ovog repozitorijuma (`fitness-planner-backend`).

## Domaći zadatak — minimalni zahtevi

| # | Zahtev | Gde se nalazi |
|---|---|---|
| 1 | Laravel aplikacija testirana kroz Postman | Cela `routes/api.php`; instrukcije za Postman su u [README.md](README.md#testiranje-kroz-postman) |
| 2 | Git verzionisanje, min. 10 komitova, svi članovi kolaboratori | Videti [GIT_WORKFLOW.md](GIT_WORKFLOW.md) — objašnjeno kako podeliti ovaj kod u smislene komitove |
| 3 | GitHub, public repo, Elab organizacija | Videti [GIT_WORKFLOW.md](GIT_WORKFLOW.md) |
| 4 | Min. 3 međusobno povezana modela | `app/Models/User.php`, `app/Models/Exercise.php`, `app/Models/WorkoutSession.php`, `app/Models/WorkoutExercise.php` — 4 modela, veze definisane relacijama (`hasMany`, `belongsTo`) |
| 5 | Min. 5 različitih tipova migracija | `database/migrations/2026_08_13_*` — 8 fajlova, tipovi: CREATE TABLE (`150000`, `150100`, `150200`), ADD COLUMN (`150300`), CHANGE COLUMN (`150400`), DROP COLUMN (`150500`), ADD FOREIGN KEY (`150600`), ADD CONSTRAINT/UNIQUE (`150700`) |
| 6 | API rute + kontroleri po REST konvenciji | `routes/api.php` + `app/Http/Controllers/Api/*.php` |
| 7 | JSON za uspešne odgovore i greške | Svi kontroleri vraćaju `response()->json(...)`; greške centralizovano u `bootstrap/app.php` (`withExceptions`) — validacija (422), auth (401), not found (404), role (403) |
| 8 | Min. 1 resource ruta + 3 druga tipa API ruta | Resource: `Route::apiResource('workout-sessions', ...)` u `routes/api.php`. Ostali tipovi: obične GET/POST rute (`/exercises/sync`), ugnježdene rute (`/workout-sessions/{id}/exercises`), rute sa middleware grupama (`role:admin`) |
| 9 | Auth rute: register/login/logout | `app/Http/Controllers/Api/AuthController.php` (`register`, `login`, `logout`) + rute u `routes/api.php` |
| 10 | Zaštita ruta za CREATE/UPDATE/DELETE samo za ulogovane | `auth:sanctum` middleware grupa u `routes/api.php` obavija sve rute za izmenu podataka |
| 11 | Dokumentacija sa Postman slikama | Priprema se odvojeno (template je dobijen od predmetnog nastavnika) — ovaj repo daje sav kod i objašnjenja koja treba ubaciti u dokument |

## Domaći zadatak — zahtevi za višu ocenu (izabrano 5 od traženih min. 3)

| # | Zahtev | Gde se nalazi |
|---|---|---|
| 1 | Paginacija i filtriranje | `ExerciseController::index()` (search/category/muscle/sort + `paginate()`), `WorkoutSessionController::index()` (status filter + paginate) |
| 2 | Izmena lozinke (zaboravljena lozinka) | `AuthController::forgotPassword()` + `resetPassword()`, rute `/forgot-password`, `/reset-password` |
| 3 | 3+ uloge u sistemu | guest (bez tokena, vidi javne rute), `member` i `admin` (kolona `role` na `users`, `EnsureUserHasRole` middleware) |
| 4 | Upload fajlova | `UserController::uploadAvatar()`, ruta `POST /api/user/avatar` |
| 5 | Keširanje podataka | `WeatherService::currentTemperature()` (Cache 10 min), `ExerciseController::sync()` (Cache 1h za wger stranice) |
| 6 | Seeders, factories, resources za sve modele | `database/factories/*Factory.php` (4 fajla), `database/seeders/DatabaseSeeder.php` |
| 7 | Pretraga po kriterijumima | `ExerciseController::index()` — `?search=`, `?category=`, `?muscle=` |
| 8 | Export podataka (CSV) | `WorkoutSessionController::export()`, ruta `GET /api/workout-sessions/export` |
| 9 | Ruta bazirana na javnom servisu | `InsightsController::summary()` poziva `WeatherService` (Open-Meteo); `ExerciseController::sync()` poziva `WgerService` (wger.de) |

## Seminarski rad — minimalni zahtevi

| # | Zahtev | Gde se nalazi |
|---|---|---|
| Baza + CRUD | `database/database.sqlite`, sve operacije kroz Eloquent modele u kontrolerima |
| Migracije (5+ tipova) | Isto kao gore, tabela za HW zahtev #5 |
| Poziv javnog servisa vezanog za temu | `WgerService` (katalog vežbi) + `WeatherService` (vremenska prognoza za Insights) |
| Min. 4 API rute | `routes/api.php` sadrži ~20 ruta |
| Min. 3 korisničke uloge | Isto kao gore |
| Upravljanje sesijom (login/logout/register) | `AuthController` + Sanctum tokeni |
| Min. 3 dodatne funkcionalnosti | Isto kao "zahtevi za višu ocenu" iznad (implementirano 5) |

## Seminarski rad — zahtevi za višu ocenu

| # | Zahtev | Gde se nalazi |
|---|---|---|
| 4+ povezane tabele + JOIN | `users` ↔ `workout_sessions` ↔ `workout_exercises` ↔ `exercises`; JOIN upiti u `InsightsController::summary()` (`DB::table(...)->join(...)`) |
| MVC patern | Standardna Laravel struktura: `app/Models`, `app/Http/Controllers`, rute u `routes/api.php` |
| Sigurnost (min. 2 kriterijuma) | (1) Hash lozinke — `Hash::make()` u `AuthController::register()`/`resetPassword()`; (2) Zaštita od SQL injection — sve query preko Eloquent/Query Buildera (parametrizovano), nema raw string konkatenacije; (3) bonus: rate limiting (`throttle:10,1`) na auth rutama protiv brute-force napada, IDOR zaštita kroz `authorizeAccess()` provere vlasništva |
| Napredna manipulacija podacima | Agregacija/GROUP BY u `InsightsController::summary()` (nedeljni volumen, treninzi po danu); DB transakcija u `WorkoutSessionController::store()` (kreiranje treninga + stavki je atomarno) |
| 4+ tipa funkcija (GET/POST/PUT/DELETE) | `routes/api.php` — svih 4 metode se koriste kroz resource + nested rute |
| Min. 2 ugnježdene rute | `/workout-sessions/{id}/exercises` (GET, POST) i `/workout-sessions/{id}/exercises/{itemId}` (PUT, DELETE) |
| Poziv min. 2 javna REST servisa | wger.de (`WgerService`) + Open-Meteo (`WeatherService`) |

## Napomena o FormRequest validaciji

Sva validacija ulaznih podataka je izdvojena u `app/Http/Requests/*.php` (npr. `StoreWorkoutSessionRequest`, `WorkoutExerciseItemRequest`) umesto da bude direktno u kontrolerima — ovo je standardna Laravel praksa koja razdvaja validaciju od poslovne logike.
