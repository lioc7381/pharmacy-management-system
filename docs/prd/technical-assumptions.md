# Technical Assumptions

This section documents the key technical decisions and constraints that will guide the Architect. These choices are derived directly from the project's foundational documents and are considered binding for the MVP.

## Repository Structure: Monorepo

A single repository will be used to house both the Flutter frontend application and the Laravel backend API. This approach is chosen for its simplicity, which aligns with the project's constraints as an educational capstone managed by a single developer. It simplifies version control, setup, and dependency management.

## Service Architecture: Monolith

The backend will be a single, monolithic Laravel service. This architecture is the most direct and efficient way to deliver the required functionality while avoiding the unnecessary operational complexity of microservices or serverless functions, which are explicitly out of scope.

## Testing Requirements: Unit + Integration

The testing strategy will focus on a pragmatic mix of unit and integration tests. This includes unit tests for critical backend business logic (e.g., stock management), widget tests for key Flutter screens, and at least one end-to-end integration test covering a complete user journey to validate the full stack.

## Additional Technical Assumptions and Requests

The following are critical technical directives consolidated from the project's technical requirements and must be adhered to by the Architect:

*   **Platform & Frameworks:** The frontend will be built **only for Android** using **Flutter**. The backend will be a **Laravel REST API**.
*   **Database:** The system will **only use SQLite**. It will not be designed for or tested against any other database system.
*   **Authentication:** API security will be managed exclusively by **Laravel Sanctum** for token-based authentication.
*   **State Management:** The Flutter application will use the **Provider** package for state management.
*   **Communication Protocol:** All client-server communication will be **asynchronous and REST-based**. Real-time technologies like WebSockets are explicitly forbidden for the MVP.
*   **File Handling:** The system must handle prescription image uploads, including client-side compression and secure server-side storage.

---
