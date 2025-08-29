# Tech Stack

This section serves as the **definitive and single source of truth** for all technologies, frameworks, and libraries to be used in the Pharmacy Management System. All development, tooling, and setup must adhere to these specific choices and versions. This precision is critical to ensure a stable, reproducible development environment, which is a core requirement for this educational project. Each selection is directly derived from the explicit constraints and preferences outlined in the provided PRD and technical requirements documents.

## Technology Stack Table

| Category | Technology | Version | Purpose | Rationale |
| :--- | :--- | :--- | :--- | :--- |
| **Frontend Language** | Dart | 3.9.x | Primary language for Flutter development. | Required by the Flutter framework. |
| **Frontend Framework**| Flutter | 3.35.x | UI toolkit for building the Android application. | A core, non-negotiable project requirement. |
| **UI Component Lib** | Material Design 3 | (Built-in) | Provides the foundational UI components and styling. | Specified in the UI/UX spec for a modern, dark theme. |
| **State Management** | Provider | 6.1.x | Manages application state on the frontend. | Explicitly required in the technical specifications. |
| **Backend Language** | PHP | 8.2.x | Primary language for the Laravel backend. | Required by the Laravel framework. |
| **Backend Framework**| Laravel | 12.x | Provides the core structure for the REST API. | A core, non-negotiable project requirement. |
| **API Style** | REST | (Standard) | Defines the communication protocol between frontend and backend. | Specified in the technical requirements. |
| **Database** | SQLite | 3.x | The exclusive database for all application data. | A critical, non-negotiable constraint for portability. |
| **File Storage** | Local Filesystem | N/A | Stores uploaded prescription images on the server. | Simplest approach for a local-only, zero-budget project. |
| **Authentication** | Laravel Sanctum | 4.x | Manages API token-based authentication. | Explicitly required in the technical specifications. |
| **Routing** | go_router | 16.1.x | Manages declarative, type-safe routing and navigation. | Chosen for its robust, centralized configuration, type-safe navigation, and built-in support for authentication guards, aligning with the architecture's security and maintainability goals. |
| **Frontend Testing** | flutter_test | (SDK) | For widget and unit testing the Flutter application. | Standard, built-in testing framework for Flutter. |
| **Backend Testing** | PHPUnit | 11.x | For unit and feature testing the Laravel API. | Standard, built-in testing framework for Laravel. |
| **E2E Testing** | integration_test | (SDK) | For full-stack, end-to-end user journey testing. | Flutter's official package for integration testing. |
| **CI/CD** | GitHub Actions | N/A | For automated testing on push (post-MVP). | Mentioned as a "Should-have" in the technical specs. |
| **Logging** | dart:developer / Laravel Logging | (Built-in) | Provides structured logging for debugging. | Specified in the technical requirements. |
| **IaC Tool** | N/A | N/A | Not applicable for a local-only development project. | Out of scope; no cloud infrastructure will be provisioned. |
| **Monitoring** | N/A | N/A | Not applicable for a non-production, educational project. | Out of scope; no production monitoring is required. |

---
