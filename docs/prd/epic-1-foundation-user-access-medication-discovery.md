# Epic 1: Foundation, User Access & Medication Discovery

**Epic Goal:** To establish the complete project foundation, including the monorepo structure, core application setup for both Flutter and Laravel, and a basic CI workflow. This epic will deliver the complete, end-to-end user authentication system (registration, login, role-based access control) and the first piece of tangible user value: the public-facing medication search functionality.

## Story 1.1: Project Scaffolding & Core Setup

As a Developer,
I want a complete project scaffold for the Flutter and Laravel applications within a single monorepo,
so that I have a stable and consistent foundation for all future development.

### Acceptance Criteria

1.  A Git monorepo is initialized containing two primary directories: `frontend` (for Flutter) and `backend` (for Laravel).
2.  The Laravel backend is configured to use SQLite for its database, and the initial database migrations can be run successfully.
3.  The Flutter application is initialized with a clean, feature-based folder structure and necessary dependencies (Provider, Dio, etc.).
4.  A basic health-check endpoint (e.g., `GET /api/health`) is created in Laravel that returns a `200 OK` status.
5.  The Flutter app can successfully make an API call to the health-check endpoint.
6.  A root `README.md` file is created with comprehensive setup instructions. This must include prerequisites and a step-by-step guide covering:
 *   Initial project creation (e.g., `laravel new backend`).
 *   Dependency installation (`composer install`, `flutter pub get`).
 *   **Crucial framework setup, including the command to install Sanctum for an API-only SPA (`php artisan install:api`).**
 *   Environment configuration (`.env` setup, `php artisan key:generate`).
 *   Database setup (`php artisan migrate --seed`).
 *   Instructions for running both services concurrently.
7.  A Laravel database seeder is implemented and documented in the README. The seeder must populate the database with sample data, including at least one user for each role (Manager, Salesperson, etc.) and a small catalog of sample medications, to make the system immediately testable.

## Story 1.2: Implement Public Medication Search

As a User,
I want to search for medications by name and view their details,
so that I can check for availability and pricing without needing an account.

### Acceptance Criteria

1.  A public API endpoint (`GET /api/medications`) is created that allows searching for medications by name.
2.  The Flutter application has a search screen with a text input field.
3.  As the user types, the app calls the API and displays a list of matching medications, showing name, price, and availability.
4.  If no medications match the search term, a "No results found" message is displayed.
5.  If a network error occurs, a "Connection problem. Please try again." message is displayed.

## Story 1.3: Implement User Authentication API

As a System,
I want secure API endpoints for user registration and login,
so that users can create accounts and authenticate securely.

### Acceptance Criteria

1.  A `POST /api/register` endpoint is created that accepts user details and creates a new account with the 'Client' role by default.
2.  The registration endpoint returns an error if the email is already in use.
3.  A `POST /api/login` endpoint is created that validates credentials and returns a Laravel Sanctum API token upon success.
4.  The login endpoint returns an "Invalid credentials" error for incorrect login attempts.
5.  A `POST /api/logout` endpoint is created that in-validates the user's current Sanctum token on the server.

## Story 1.4: Implement Client Registration & Login UI

As a Visitor,
I want to create an account and log in through the mobile app,
so that I can access authenticated features.

### Acceptance Criteria

1.  A registration screen is built in Flutter that captures user details and calls the `POST /api/register` endpoint.
2.  A login screen is built that captures credentials and calls the `POST /api/login` endpoint.
3.  Upon successful login, the Sanctum API token is securely stored on the device.
4.  All subsequent authenticated API calls from the app include the stored token in the `Authorization` header.
5.  Upon successful logout, the token is cleared from local storage and the user is returned to the login screen.
6.  Backend validation for registration must enforce a minimum password length of 8 characters. The UI must display this requirement clearly if validation fails.
7.  The registration screen must include a mandatory 'I accept the Terms of Use' checkbox. The system must prevent registration if the box is not checked.

## Story 1.5: Implement Foundational Role-Based Access Control (RBAC)

As a System,
I want to protect API endpoints based on user roles,
so that users can only access data and perform actions they are authorized for.

### Acceptance Criteria

1.  Laravel middleware is created that can check the authenticated user's role against a list of allowed roles.
2.  A test endpoint (e.g., `/api/admin-test`) is created and protected by this middleware, restricted to the 'Manager' role.
3.  When a user with the 'Manager' role accesses the test endpoint, they receive a `200 OK` response.
4.  When a user with a 'Client' role (or no authentication) attempts to access the test endpoint, they receive a `403 Forbidden` error.

## Story 1.6: Implement Foundational Caching for Read-Only Data

As a User,
I want the app to cache non-critical data,
so that I have a better experience during brief network interruptions and data loads faster.

### Acceptance Criteria

1.  A simple, in-memory caching solution is added to the Flutter application's API service layer.
2.  API responses for public medication searches (`GET /api/medications`) are cached for a short duration (e.g., 5 minutes).
3.  API responses for the user's notifications (`GET /api/notifications`) are cached. When the cache is displayed, a background request to fetch fresh data is still initiated.
4.  The cache is invalidated when the user logs out.
5.  This implementation must satisfy the requirements of NFR4.

---
