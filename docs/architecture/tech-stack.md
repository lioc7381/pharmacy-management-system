# Tech Stack

<!--docs/architecture/[title].md-->

This section serves as the **definitive and single source of truth** for all technologies, frameworks, and libraries to be used in the Pharmacy Management System. All development, tooling, and setup must adhere to these specific choices and versions. This precision is critical to ensure a stable, reproducible development environment, which is a core requirement for this educational project. Each selection is directly derived from the explicit constraints and preferences outlined in the provided PRD and technical requirements documents.

## Technology Stack Table

| Category | Technology | Version | Purpose | Rationale |
| :--- | :--- | :--- | :--- | :--- |
| **Frontend Language** | Dart | ^3.9.0 | Primary language for Flutter development. | Required by the Flutter framework. |
| **Frontend Framework**| Flutter | 3.35.x | UI toolkit for building the Android application. | A core, non-negotiable project requirement. |
| **UI Component Lib** | Material Design 3 | (Built-in) | Provides the foundational UI components and styling. | Specified in the UI/UX spec for a modern, dark theme. |
| **State Management** | Provider | ^6.1.5+1 | Manages application state on the frontend. | Explicitly required in the technical specifications. |
| **Routing** | go_router | ^16.2.1 | Manages declarative, type-safe routing and navigation. | Chosen for its robust, centralized configuration, type-safe navigation, and built-in support for authentication guards, aligning with the architecture's security and maintainability goals. |
| **HTTP Client** | dio | ^5.9.0 | For making REST API calls to the Laravel backend. | A powerful HTTP client for Dart, providing features like interceptors and error handling essential for robust API communication. |
| **Local DB Driver** | sqflite | ^2.4.2 | Flutter plugin for interacting with the SQLite database. | The standard package for direct SQLite access in Flutter, providing the necessary interface to the required database. |
| **Secure Storage** | flutter_secure_storage | ^9.2.4 | To securely persist authentication tokens on the device. | Uses platform-specific keystores to securely store sensitive data like API tokens, a requirement for the authentication mechanism. |
| **Device Features** | image_picker | ^1.2.0 | Allows users to select prescription images from the gallery or camera. | A core dependency for the prescription upload feature, abstracting platform-specific code for accessing device media. |
| **Device Features** | connectivity_plus | ^6.1.5 | To check the device's network connectivity status. | Essential for providing a good user experience by detecting offline status and preventing failed API calls. |
| **Utilities** | image | ^4.5.4 | Provides image decoding, processing, and encoding capabilities. | A utility library for pre-processing or validating images selected via `image_picker` before uploading. |
| **Utilities** | flutter_dotenv | ^6.0.0 | Manages environment-specific variables (e.g., API base URL). | Best practice for separating configuration from code, allowing for different settings without code changes. |
| **Backend Language** | PHP | 8.2.x | Primary language for the Laravel backend. | Required by the Laravel framework. |
| **Backend Framework**| Laravel | 12.x | Provides the core structure for the REST API. | A core, non-negotiable project requirement. |
| **API Style** | REST | (Standard) | Defines the communication protocol between frontend and backend. | Specified in the technical requirements. |
| **Database** | SQLite | 3.x | The exclusive database for all application data. | A critical, non-negotiable constraint for portability. |
| **File Storage** | Local Filesystem | N/A | Stores uploaded prescription images on the server. | Simplest approach for a local-only, zero-budget project. |
| **Authentication** | Laravel Sanctum | 4.x | Manages API token-based authentication. | Explicitly required in the technical specifications. |
| **Frontend Testing** | flutter_test | (SDK) | For widget and unit testing the Flutter application. | Standard, built-in testing framework for Flutter. |
| **Backend Testing** | PHPUnit | 11.x | For unit and feature testing the Laravel API. | Standard, built-in testing framework for Laravel. |
| **E2E Testing** | integration_test | (SDK) | For full-stack, end-to-end user journey testing. | Flutter's official package for integration testing. |
| **Linting** | flutter_lints | ^6.0.0 | Provides static analysis to identify and report on potential code issues. | Enforces a consistent coding style and helps prevent common errors, improving code quality and maintainability. |
| **CI/CD** | GitHub Actions | N/A | For automated testing on push (post-MVP). | Mentioned as a "Should-have" in the technical specs. |
| **Logging** | dart:developer / Laravel Logging | (Built-in) | Provides structured logging for debugging. | Specified in the technical requirements. |
| **IaC Tool** | N/A | N/A | Not applicable for a local-only development project. | Out of scope; no cloud infrastructure will be provisioned. |
| **Monitoring** | N/A | N/A | Not applicable for a non-production, educational project. | Out of scope; no production monitoring is required. |