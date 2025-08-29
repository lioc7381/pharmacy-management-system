# Coding Standards

This document defines project-specific architectural standards. For all other stylistic and formatting conventions, this project formally adopts the official style guides for its respective technologies:

*   **For all Dart/Flutter code:** The [Effective Dart Style Guide](https://dart.dev/guides/language/effective-dart/style).
*   **For all PHP/Laravel code:** The [PSR-12 Extended Coding Style Guide](https://www.php-fig.org/psr/psr-12/), which is the standard followed by the Laravel framework.

The rules defined below are architectural mandates that take precedence over any conflicting suggestions in the general style guides.

## Critical Fullstack Rules

These rules are the most important directives and directly impact the structural integrity of the codebase. Violation of these rules is considered an architectural defect.

*   **API Interaction Pattern:** All frontend API calls **must** be made through a feature-specific `ApiService` (e.g., `OrderApiService`), which in turn uses the central `ApiClientService`. Providers or UI widgets **must never** call the `ApiClientService` or `dio` directly. This enforces the layered architecture and centralizes concerns like error handling and authentication.
*   **Backend Business Logic:** All business logic in the Laravel backend **must** be encapsulated within Service classes (e.g., `OrderService`). Controllers **must** remain lean and are only responsible for handling the HTTP request/response cycle by delegating to services.
*   **Backend Data Access:** All database queries in the Laravel backend **must** be performed through the Repository pattern. Service classes **must** depend on Repository interfaces and **must not** use Eloquent models or the Query Builder directly. This is critical for testability and separation of concerns.
*   **Transactional Integrity:** Any backend operation that involves multiple, related database writes (especially those involving stock management and order creation) **must** be wrapped in a database transaction (`DB::transaction()`) to ensure atomicity.
*   **State Management:** All frontend state modifications **must** be handled through a `Provider`. UI widgets **must not** contain business logic or mutate state directly. One-time events (e.g., showing a snackbar, navigation) **must** be handled using the established `ViewEvent` pattern to prevent side effects during UI rebuilds.
*   **Environment Variable Access:** Environment variables **must** be accessed through a dedicated configuration object/service. Code within features **must not** access them directly (e.g., via `dotenv` or `Platform.environment`). This decouples the application logic from the environment configuration.
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
         * Process a prescription and create a corresponding order.
         *
         * @param int $prescriptionId The ID of the prescription to process.
         * @param array $items The list of medication items for the order.
         * @return Order The newly created order.
         * @throws InsufficientStockException if any item is out of stock.
         */
        public function processPrescription(int $prescriptionId, array $items): Order;
        ```

## Naming Conventions

Consistency in naming is crucial for a predictable and easily navigable codebase. The following conventions are mandatory.

| Element | Frontend (Flutter/Dart) | Backend (Laravel/PHP) | Example |
| :--- | :--- | :--- | :--- |
| **Files** | `snake_case.dart` | `PascalCase.php` | `order_list_screen.dart`, `OrderService.php` |
| **Components/Widgets** | `PascalCase` | N/A | `PrimaryButton`, `OrderCard` |
| **State Providers** | `PascalCase` ending in `Provider` | N/A | `OrderListProvider` |
| **API Controllers** | N/A | `PascalCase` ending in `Controller` | `OrderController` |
| **Service Classes** | N/A | `PascalCase` ending in `Service` | `InventoryService` |
| **Repository Classes** | N/A | `PascalCase` ending in `Repository` | `EloquentOrderRepository` |
| **Database Tables** | N/A | `snake_case` (plural) | `order_items` |
| **API Routes** | N/A | `kebab-case` | `/api/low-stock` |

---
