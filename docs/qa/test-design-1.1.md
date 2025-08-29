### **Test Design: 1.1 - Project Scaffolding & Core Setup**

**Objective:** To verify that the monorepo, backend, and frontend applications are scaffolded correctly, configured according to architectural standards, and that core communication pathways are functional.

---

### **1. Test Scenarios & Cases**

#### **Area: Project Structure & Setup (Manual Verification)**

*   **Scenario 1.1: Monorepo Initialization**
    *   **Given** a developer has cloned the repository.
    *   **When** listing the contents of the root directory.
    *   **Then** a `frontend` directory must be present.
    *   **And** a `backend` directory must be present.
    *   **And** a root `README.md` file must be present.

*   **Scenario 1.2: README Documentation**
    *   **Given** the project has been successfully set up by following the `README.md`.
    *   **When** a developer follows the instructions in the root `README.md` from a clean clone.
    *   **Then** all prerequisite software is listed.
    *   **And** all commands (`composer install`, `flutter pub get`, `php artisan install:api`, `php artisan key:generate`, `php artisan migrate --seed`) execute successfully.
    *   **And** the instructions for running both services concurrently are clear and result in both applications running.

---

#### **Area: Backend (Automated & Manual)**

*   **Scenario 2.1: Health Check Endpoint (Automated Feature Test)**
    *   **Given** the Laravel application is running.
    *   **When** an HTTP GET request is sent to `/api/health`.
    *   **Then** the API should return a `200 OK` status code.
    *   **And** the response body should be a JSON object like `{"status": "ok"}`.

*   **Scenario 2.2: Database Configuration & Seeding (Manual/Automated)**
    *   **Given** the Laravel application is configured.
    *   **When** the command `php artisan migrate --seed` is executed.
    *   **Then** the command should complete without any errors.
    *   **And** the `database/database.sqlite` file should be created and not be empty.
    *   **And** the `users` table should contain at least one record for each user role (Manager, Salesperson).
    *   **And** the `medications` table should contain sample medication records.

*   **Scenario 2.3: Authentication Setup (Manual Verification)**
    *   **Given** the backend setup is complete.
    *   **When** inspecting the project configuration.
    *   **Then** Laravel Sanctum package must be listed as a dependency in `composer.json`.
    *   **And** the Sanctum configuration file (`config/sanctum.php`) must exist.

---

#### **Area: Frontend (Automated & Manual)**

*   **Scenario 3.1: Dependency Configuration (Manual Verification)**
    *   **Given** the Flutter project is initialized.
    *   **When** inspecting the `frontend/pubspec.yaml` file.
    *   **Then** `provider`, `dio`, `go_router`, and `flutter_secure_storage` must be listed as dependencies.

*   **Scenario 3.2: Folder Structure (Manual Verification)**
    *   **Given** the Flutter project is initialized.
    *   **When** inspecting the `frontend/lib/` directory.
    *   **Then** a feature-based directory structure (e.g., `core/`, `features/`, `shared/`) must be present as defined in the architecture.

*   **Scenario 3.3: API Health Check Call - Success (Automated Widget Test & E2E)**
    *   **Given** the Flutter app is running and the backend is healthy.
    *   **When** the user taps the "Health Check" button.
    *   **Then** the `ApiClientService` should be called to make a GET request to `/api/health`.
    *   **And** a `SnackBar` should appear with a success message (e.g., "Backend connection successful!").

*   **Scenario 3.4: API Health Check Call - Failure (Automated Widget Test & E2E)**
    *   **Given** the Flutter app is running and the backend is unreachable or returns an error.
    *   **When** the user taps the "Health Check" button.
    *   **Then** the `ApiClientService` should handle the API exception.
    *   **And** a `SnackBar` should appear with a user-friendly error message (e.g., "Error: Could not connect to the server.").

---

### **2. Test Levels & Strategy**

*   **Unit Tests:**
    *   **Backend:** Focus on the `HealthCheckController` to ensure it returns the correct `JsonResponse`.
    *   **Frontend:** Test the `ApiClientService` with a mocked `Dio` client to verify it handles success and error responses correctly.

*   **Integration/Feature Tests:**
    *   **Backend:** A feature test (`HealthCheckTest.php`) will be created to test the `/api/health` route from entry to response, ensuring the full request lifecycle works as expected.
    *   **Frontend:** A widget test (`health_check_button_test.dart`) will verify that tapping the button correctly invokes the `ApiClientService` (using a mock) and displays the appropriate UI feedback (SnackBar).

*   **End-to-End (E2E) Tests:**
    *   A single E2E test will simulate the full user journey:
        1.  Start both the backend and frontend servers.
        2.  Launch the Flutter application.
        3.  Tap the "Health Check" button.
        4.  Assert that the success SnackBar is displayed, confirming the entire communication flow from UI to database (implicitly, as the server is running) and back.

*   **Manual Tests:**
    *   A full run-through of the `README.md` setup instructions on a clean checkout is required to validate the developer setup experience.
    *   Manual inspection of file structures and configuration files (`.env`, `pubspec.yaml`) to ensure they meet architectural standards.

### **3. Requirements Traceability Matrix**

| Acceptance Criterion | Test Scenario(s) | Test Level |
| :--- | :--- | :--- |
| AC 1: Monorepo Structure | 1.1 | Manual |
| AC 2: Laravel & SQLite | 2.2, 2.3 | Manual / Automated |
| AC 3: Flutter Structure | 3.1, 3.2 | Manual |
| AC 4: Health-Check Endpoint | 2.1 | Automated |
| AC 5: Flutter API Call | 3.3, 3.4 | Automated / E2E |
| AC 6: README Instructions | 1.2 | Manual |
| AC 7: Database Seeder | 2.2 | Manual / Automated |
