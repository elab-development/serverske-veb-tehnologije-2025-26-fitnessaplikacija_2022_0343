# Git / GitHub Classroom uputstvo za tim

Ovaj kod je (na tvoj zahtev) napravljen odjednom, van git istorije — repozitorijum još nije `git init`-ovan. Zadatak eksplicitno zabranjuje "komitovanje gotovog projekta i naknadno dodavanje komentara", zato **nemoj** raditi jedan veliki `git add . && git commit`. Ispod je recept kako da ovaj kod pretvorite u smislenu istoriju sa 20+ komitova, podeljenu na više članova tima.

## 1. Poveži repo sa GitHub Classroom-om

1. Otvori link koji ste dobili za GitHub Classroom zadatak (spomenut u tekstu zahteva) i prihvati zadatak — Classroom će automatski napraviti privatni/javni repo unutar Elab organizacije i dodati te kao kolaboratora.
2. **Repo mora biti public** (ne private) — proveri u Settings → General → Danger Zone na GitHub-u.
3. Svaki član tima treba da prihvati isti Classroom link (ili da ga admin doda kao kolaboratora u Settings → Collaborators) tako da svako ima svoj nalog povezan sa repoom.
4. Lokalno:
   ```bash
   cd fitness-planner-backend
   git init
   git remote add origin <URL_KOJI_JE_GITHUB_CLASSROOM_GENERISAO>
   ```

## 2. Podela na smislene komitove (predlog redosleda)

Ovi fajlovi su već grupisani po logičkim celinama — dodaj ih u ovom redosledu, po jedan `git add` + `git commit` po celini. Svaki komit treba da bude delo osobe koja ga stvarno razume (podelite celine među članovima tima, promenite `git config user.name/user.email` po potrebi ili radite svako sa svoje mašine/naloga).

```bash
# 1) Laravel skeleton
git add composer.json composer.lock artisan bootstrap config public resources storage tests phpunit.xml .env.example .gitignore vite.config.js package.json
git commit -m "Init: Laravel skeleton"

# 2) Sanctum
git add composer.json composer.lock config/sanctum.php app/Models/User.php database/migrations/*personal_access_tokens*
git commit -m "Add Sanctum for API token authentication"

# 3) Migracije — modeli podataka
git add database/migrations/2026_08_13_150000_create_exercises_table.php database/migrations/2026_08_13_150100_create_workout_sessions_table.php database/migrations/2026_08_13_150200_create_workout_exercises_table.php
git commit -m "Add exercises, workout_sessions, workout_exercises tables"

git add database/migrations/2026_08_13_150300_add_role_and_avatar_to_users_table.php
git commit -m "Add role and avatar_path columns to users"

git add database/migrations/2026_08_13_150400_modify_notes_column_on_workout_sessions_table.php
git commit -m "Widen workout_sessions.notes to TEXT"

git add database/migrations/2026_08_13_150500_drop_legacy_source_from_exercises_table.php
git commit -m "Drop unused legacy_source column from exercises"

git add database/migrations/2026_08_13_150600_add_foreign_keys_to_workout_tables.php
git commit -m "Add foreign key constraints to workout tables"

git add database/migrations/2026_08_13_150700_add_unique_constraint_to_workout_exercises_table.php
git commit -m "Add unique constraint to prevent duplicate exercise slots"

# 4) Modeli
git add app/Models/Exercise.php app/Models/WorkoutSession.php app/Models/WorkoutExercise.php app/Models/User.php
git commit -m "Add Eloquent models and relationships"

# 5) Factories + seeder
git add database/factories database/seeders
git commit -m "Add factories and seeder for demo data"

# 6) Auth
git add app/Http/Controllers/Api/AuthController.php app/Http/Middleware/EnsureUserHasRole.php app/Providers/AppServiceProvider.php config/app.php routes/web.php bootstrap/app.php
git commit -m "Implement register/login/logout/forgot-reset password and role middleware"

# 7) Exercises API
git add app/Http/Controllers/Api/ExerciseController.php app/Http/Requests/StoreExerciseRequest.php app/Http/Requests/UpdateExerciseRequest.php app/Services/WgerService.php
git commit -m "Add exercises API with wger.de sync and caching"

# 8) Workout sessions + nested exercises
git add app/Http/Controllers/Api/WorkoutSessionController.php app/Http/Controllers/Api/WorkoutExerciseController.php app/Http/Requests/StoreWorkoutSessionRequest.php app/Http/Requests/UpdateWorkoutSessionRequest.php app/Http/Requests/WorkoutExerciseItemRequest.php
git commit -m "Add workout sessions resource API and nested exercise items routes"

# 9) Insights + weather
git add app/Http/Controllers/Api/InsightsController.php app/Services/WeatherService.php
git commit -m "Add insights aggregation endpoint with Open-Meteo integration"

# 10) File upload + admin users + CSV export je vec u WorkoutSessionController (commit 8),
#     ovde samo UserController
git add app/Http/Controllers/Api/UserController.php
git commit -m "Add avatar upload and admin user listing"

# 11) Rute
git add routes/api.php
git commit -m "Wire up all API routes with auth and role middleware groups"

# 12) Dokumentacija
git add README.md REQUIREMENTS_MAPPING.md GIT_WORKFLOW.md
git commit -m "Add README, requirements mapping and git workflow docs"
```

To je već 12 komitova; ako svaki član tima doda i po koji svoj (npr. sitne izmene, ispravke, testiranje, dodatni seed podaci, docs screenshotovi), lako pređete traženih 10 (HW2) odnosno 20 (seminarski, računajući i HW komitove).

## 3. Realan rad ubuduće (ne samo za ovaj initial dump)

Za sve što radite **posle** ovog initial commit-ovanja, radite normalno malim koracima:
- svaka feature grana (`feature/insights-export`, `fix/role-middleware`...) ide kroz svoj mali broj komitova
- pravite Pull Request-ove međusobno da svi imaju vidljive doprinose (GitHub broji i PR review komentare, ali komitovi su ono što se eksplicitno traži)
- izbegavajte da jedna osoba commit-uje sav kod za sve — nastavnik proverava da li svaki član ima commitove

## 4. Push

```bash
git branch -M main
git push -u origin main
```

Proveri na GitHub-u da repo ostane **Public** i da je vidljiv u Elab organizaciji.
