# High Level Architecture

This section establishes the foundational architectural principles for the Pharmacy Management System. It defines the overall structure, key components, and guiding patterns that will inform all subsequent design and development decisions. The choices made here are derived directly from the project's explicit constraints as an educational capstone, prioritizing simplicity, portability, and learnability.

## Technical Summary

The system is designed as a **monolithic fullstack application** housed within a **single monorepo**. The user-facing component is a **Flutter mobile application for Android**, which communicates with a **Laravel backend API** via a standard **RESTful interface**. All application data is persisted in a local **SQLite database**, and prescription images are stored on the local filesystem. This self-contained architecture ensures zero-cost local development and maximum portability, directly supporting the PRD's goals of creating a streamlined, efficient, and easily manageable system for its educational context.

## Platform and Infrastructure Choice

-   **Platform:** Local Development Environment
-   **Key Services:** Flutter SDK, PHP/Composer, Local Web Server (e.g., Laravel Sail, Artisan Serve), SQLite Database Engine.
-   **Deployment Host and Regions:** N/A. The architecture is explicitly designed for local execution and is not intended for deployment to a cloud provider like AWS, Vercel, or Azure. This decision is a direct consequence of the project's zero-dollar budget constraint.

## Repository Structure

-   **Structure:** Monorepo
-   **Monorepo Tool:** N/A (Simple folder structure). For this project's scale, complex tooling like Nx or Turborepo is unnecessary.
-   **Package Organization:** The repository will contain two primary directories: `frontend/` for the Flutter application and `backend/` for the Laravel API. This structure simplifies version control and setup for a single developer.

## High Level Architecture Diagram

```mermaid
graph TD
    subgraph User
        U(Client / Staff)
    end

    subgraph "Device (Android)"
        F[Flutter Mobile App]
    end

    subgraph "Local Server Environment"
        A[Laravel REST API]
        D[SQLite Database]
        S[File Storage <br/>(Prescription Images)]
    end

    U -- Interacts with --> F
    F -- REST API Calls (HTTP/JSON) --> A
    A -- Reads/Writes --> D
    A -- Saves/Retrieves --> S
```

## Architectural Patterns

-   **Monolithic Architecture:** The backend is a single, unified Laravel application. This is the most pragmatic approach for a single-developer project, reducing deployment complexity and simplifying data management.
    -   *Rationale:* Aligns with the project's resource constraints and educational goals by focusing on core application logic rather than distributed systems overhead.
-   **Model-View-ViewModel (MVVM) with Provider:** The Flutter application will use the Provider package to implement the MVVM pattern, separating UI (View), state management (ViewModel), and data logic (Model).
    -   *Rationale:* This is a standard, effective pattern in the Flutter community that promotes a clean, testable, and maintainable frontend codebase.
-   **Repository Pattern:** The Laravel backend will use the Repository pattern to abstract data access logic. This decouples the business logic (services) from the data source (Eloquent models), making the application easier to test and maintain.
    -   *Rationale:* Directly supports the PRD's testing requirements by allowing business logic to be unit-tested without a live database connection.
-   **Role-Based Access Control (RBAC):** API endpoints will be protected using a clear, middleware-based RBAC system to enforce the permissions defined in the use case specifications.
    -   *Rationale:* This is a critical security pattern that ensures users can only perform actions appropriate for their assigned role.

---
