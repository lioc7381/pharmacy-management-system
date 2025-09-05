# Coding Standards

<!--docs/architecture/[title].md-->

This document defines project-specific architectural standards. For all other stylistic and formatting conventions, this project formally adopts the official style guides for its respective technologies:

*   **For all Dart/Flutter code:** The [Effective Dart Style Guide](https://dart.dev/guides/language/effective-dart/style).
*   **For all PHP/Laravel code:** The [PSR-12 Extended Coding Style Guide](https://www.php-fig.org/psr/psr-12/), which is the standard followed by the Laravel framework.

The rules defined below are architectural mandates that take precedence over any conflicting suggestions in the general style guides.

## Critical Fullstack Rules

These rules are the most important directives and directly impact the structural integrity of the codebase. Violation of these rules is considered an architectural defect.

*   **API Interaction Pattern:** All frontend API calls **must** be made through a feature-specific `ApiService` (e.g., `OrderApiService`), which in turn uses a central API client wrapper. Providers or UI widgets **must never** call the `http` package directly. This enforces the layered architecture and centralizes concerns like error handling and adding authentication headers.
*   **Backend Business Logic:** All business logic in the Laravel backend **must** be encapsulated within Service classes (e.g., `OrderService`). Controllers **must** remain lean and are only responsible for handling the HTTP request/response cycle by delegating to services.
*   **Backend Data Access:** Service classes **must** interact directly with Eloquent models for all database operations. The Repository pattern is explicitly prohibited to reduce boilerplate and improve code traceability. The standard data access flow is `Controller -> Service -> Eloquent Model`.
*   **Transactional Integrity:** Any backend operation that involves multiple, related database writes (especially those involving stock management and order creation) **must** be wrapped in a database transaction (`DB::transaction()`) to ensure atomicity.
*   **State Management:**
    *   Application-level or feature-wide state **must** be managed through a `Provider` (e.g., `OrdersProvider`). Specifically, `AuthProvider` (for role-based access control across all screens) and `NotificationProvider` (for app-wide notifications) **must** be implemented as global providers.
    *   For purely local UI state (e.g., text field controllers, toggle states, loading indicators for a single button), use a `StatefulWidget`'s local state to avoid unnecessary provider overhead.
    *   One-time events (e.g., showing a snackbar, navigation) **must** be handled by listening to simple state changes in the `ChangeNotifier` (e.g., a `string? successMessage` or `bool navigationRequested`). The `ViewEvent` pattern is explicitly prohibited.
*   **Backend Environment Variable Access:** In the Laravel backend, environment variables **must** be accessed through Laravel's `config()` helper function and configuration files in the `/config` directory. Code within services or controllers **must not** access `env()` directly outside of configuration files. This ensures proper configuration caching in production.
*   **Mandatory Documentation Comments:** All public classes, methods, and functions in both the frontend (Dart) and backend (PHP) codebases **must** be documented using their respective standard documentation comment formats: **DartDoc** for Flutter/Dart and **PHPDoc** for Laravel/PHP.
    *   **Rationale:** This is not merely for human readability. For AI agents, these structured comments are essential for understanding the purpose, parameters, and return values of code, leading to more accurate and context-aware code generation and modification. It also enables automated documentation generation.
    *   **Example: DartDoc**
        ```dart
        /// Fetches a list of orders for the currently authenticated user.
        ///
        /// Throws an [ApiException] if the network request fails.
        Future<List<Order>> getOrders();
        ```
    *   **Example: PHPDoc**
        ```php
        /**
         * Creates a new order from validated request data.
         *
         * @param array $validatedData The validated data from the FormRequest.
         * @return Order The newly created order model.
         * @throws InsufficientStockException if any item is out of stock.
         */
        public function createOrder(array $validatedData): Order;
        ```

## Mandatory Laravel Practices

The following established Laravel best practices are mandated by the simplification plan to reduce boilerplate and enforce consistency.

*   **Form Request Validation:** All input validation **must** be implemented using custom `FormRequest` classes. This keeps controllers slim and centralizes validation rules.
*   **Data Transfer via FormRequests:** The validated data array from a `FormRequest` (`$request->validated()`) **must** be passed directly to the Service layer. Dedicated Data Transfer Objects (DTOs) are prohibited.
*   **Simplified Authorization:**
    1.  Action-specific authorization (e.g., "can this user update *this specific* post?") **must** be handled within the `authorize()` method of a `FormRequest`.
    2.  General role-based access control (e.g., "is this user an admin?") **must** be handled via route middleware.
    3.  Dedicated `Policy` classes should be avoided unless authorization logic is exceptionally complex and reused across multiple non-standard actions.
*   **API Resources (JsonResource Classes):** All API responses **must** be transformed using `JsonResource` or `ResourceCollection` classes. This prevents accidental data leakage and enforces a consistent API schema.
*   **Route Model Binding:** Controllers **must** use implicit route model binding to resolve Eloquent models from route parameters. This eliminates boilerplate `Model::findOrFail($id)` lookups.
*   **Resourceful Routing:** RESTful endpoints **must** be defined using `Route::apiResource()` wherever applicable to keep route files clean and consistent.

## Naming Conventions

Consistency in naming is crucial for a predictable and easily navigable codebase. The following conventions are mandatory.

| Element                | Frontend (Flutter/Dart)                  | Backend (Laravel/PHP)                     | Example                                       |
|------------------------|-----------------------------------------|------------------------------------------|-----------------------------------------------|
| **Files**              | `snake_case.dart`                        | `PascalCase.php`                          | `order_list_screen.dart`, `OrderService.php`  |
| **Components/Widgets** | `PascalCase`                             | N/A                                       | `PrimaryButton`, `OrderCard`                  |
| **State Providers**    | `PascalCase` ending in `Provider`       | N/A                                       | `OrderListProvider`                           |
| **API Controllers**    | N/A                                      | `PascalCase` ending in `Controller`      | `OrderController`                             |
| **Service Classes**    | N/A                                      | `PascalCase` ending in `Service`         | `InventoryService`                            |
| **Repository Classes** | N/A                                      | **N/A (Prohibited)**                      | N/A                                           |
| **Database Tables**    | N/A                                      | `snake_case` (plural)                     | `order_items`                                 |
| **API Routes**         | N/A                                      | `kebab-case`                              | `/api/low-stock`                              |