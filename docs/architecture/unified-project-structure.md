# Unified Project Structure

<!--docs/architecture/[title].md-->

This section defines the physical layout of the monorepo. This structure is the concrete implementation of the architectural principles discussed earlier, providing a clean, feature-based organization that accommodates both the Flutter frontend and the Laravel backend within a single, manageable codebase. The layout is intentionally kept simple to align with the project's single-developer constraint, avoiding complex tooling in favor of a clear and logical folder convention.

```plaintext
pharmacy-management-system/
├── .github/                    # CI/CD workflows (Post-MVP)
│   └── workflows/
│       └── ci.yaml             # Runs backend and frontend tests on push
├── backend/                    # The Laravel API application
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/    # Lean controllers for handling HTTP requests
│   │   │   ├── Middleware/     # Custom middleware (e.g., RBAC)
│   │   │   └── Requests/       # Form Request validation classes
│   │   ├── Models/             # Eloquent models (must align with OpenAPI spec)
│   │   ├── Repositories/       # Data access layer (Repository Pattern)
│   │   │   ├── Contracts/
│   │   │   └── Eloquent/
│   │   └── Services/           # Core business logic
│   ├── ...
│   ├── tests/
│   ├── .env.example            # Template for backend environment variables
│   └── composer.json
├── frontend/                   # The Flutter (Android) application
│   ├── lib/
│   │   ├── core/
│   │   │   ├── api/
│   │   │   ├── models/
│   │   │   │   └── generated/  # Dart models auto-generated from OpenAPI spec
│   │   │   └── ...
│   │   ├── features/           # Feature-based modules (auth, orders, etc.)
│   │   └── shared/             # Shared widgets, theme, and utilities
│   ├── test/
│   ├── .env.example            # Template for frontend environment variables
│   └── pubspec.yaml
├── scripts/                    # Top-level helper scripts (e.g., generate-models, run-all)
├── docs/                       # Project documentation
│   ├── openapi.yaml            # SINGLE SOURCE OF TRUTH for API contracts
│   ├── prd.md
│   └── architecture.md         
├── .gitignore
└── README.md                   # Project overview and setup instructions
```

## Rationale & Key Strategies

*   **Monorepo Simplicity:** This structure places both applications in a single repository, which is ideal for a solo developer. It simplifies version control with atomic commits that can span both frontend and backend.
*   **Clear Separation of Concerns:** The top-level `backend/` and `frontend/` directories create an unambiguous boundary between the two applications. There is no risk of code overlapping or dependencies becoming tangled.
*   **Feature-Based Organization:** Both the Laravel (`app/Services`, `app/Http/Controllers`) and Flutter (`lib/features/`) applications follow a feature-based organization internally. This improves modularity and makes it easier for a developer to find all code related to a specific piece of functionality.
*   **Shared Data Contracts via Code Generation:** The `docs/openapi.yaml` file is the **single source of truth** for the API contract. A code generation script (e.g., in `scripts/generate-models.sh`) will be used to create the Dart data models for the Flutter app, ensuring the client is always synchronized with the API specification.
*   **Encapsulated Environment Variables:** Each application manages its own environment variables (`backend/.env`, `frontend/.env`). This provides strong encapsulation and prevents backend secrets from being exposed to the frontend build process.

---
