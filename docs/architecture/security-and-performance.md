# Security and Performance

<!--docs/architecture/[title].md-->

## Security Requirements

The security architecture focuses on robust backend defenses while maintaining a lean and simple frontend.

*   **Frontend Security:**
    *   **Token Storage:** In accordance with the simplification plan, the API token **must** be stored using the `shared_preferences` package. The `flutter_secure_storage` package is explicitly forbidden to minimize dependencies.
    *   **Input Sanitization:** While the primary defense is on the backend, all user-generated content displayed in the app should be treated as untrusted and handled properly to prevent potential rendering issues.
    *   **Session Management:** The application will operate under the assumption that a stored token is always valid and **never expires**. All logic related to inactivity timers or scheduled token checks **must be removed**. A session is only terminated when the user explicitly logs out or an API call fails due to an invalid token, at which point the user must be redirected to the login screen.

*   **Backend Security:**
    *   **Input Validation:** All incoming data from the client **must** be validated using Laravel's Form Requests. The system will never trust client-side validation. This is the primary defense against malformed data and injection attacks.
    *   **Mass Assignment Protection:** All Eloquent models **must** use the `$fillable` property to explicitly whitelist attributes that can be mass-assigned, preventing unauthorized data modification.
    *   **SQL Injection Prevention:** The exclusive use of Laravel's Eloquent ORM and Query Builder with parameter binding provides comprehensive protection against SQL injection vulnerabilities. Raw SQL queries are forbidden.
    *   **Cross-Site Scripting (XSS) Prevention:** Laravel's Blade templating engine (if ever used for web views) automatically escapes output. For the API, the responsibility lies with the frontend to correctly render data.
    *   **File Upload Security:** All uploaded prescription images **must** be validated on the server-side for both file type (MIME) and size, regardless of client-side checks. Files will be stored with unique, non-guessable filenames in a non-public directory.

*   **Authentication Security:**
    *   **Password Hashing:** All user passwords **must** be hashed using Laravel's default Bcrypt hashing algorithm. Plain-text passwords will never be stored.
    *   **Password Policy:** The system will enforce a minimum password length of 8 characters, as handled by Laravel's default validation rules.
    *   **Rate Limiting:** The login and registration endpoints (`/api/login`, `/api/register`) **must** be protected by Laravel's built-in rate limiting to mitigate brute-force attacks.

## Performance Optimization

The performance strategy focuses on efficient UI rendering and network usage within the constraints of an online-only architecture.

*   **Frontend Performance:**
    *   **Efficient List Rendering:** All scrollable lists (e.g., orders, notifications) **must** be built using virtualized lists (`ListView.builder`) to ensure smooth scrolling with large datasets.
    *   **Image Optimization:** To reduce network payload, prescription images **must** be compressed on the client-side before upload. This **must** be achieved exclusively using the `imageQuality` parameter of the `image_picker` package. The `image` package or any other third-party image processing libraries are not permitted.
    *   **Asynchronous Operations:** All network operations **must** be performed off the main UI thread to prevent the interface from freezing. The UI must provide immediate feedback (e.g., loading indicators) for any user action that triggers an async operation.
    *   **Online-Only Operation:** The application **must not** implement any form of data caching or offline storage. The `sqflite` package and any other persistence mechanisms are forbidden. All data must be fetched directly from the network on demand. UI should handle network errors gracefully by displaying an appropriate message and offering a retry option.

*   **Backend Performance:**
    *   **Database Indexing:** The database schema includes indexes on frequently queried columns (e.g., `users.email`, `orders.status`). These are critical for ensuring fast query performance.
    *   **Eager Loading (Prevent N+1 Queries):** When retrieving a model with its relationships (e.g., an order with its items), Eloquent's eager loading (`with()`) **must** be used to prevent the N+1 query problem.
    *   **Response Payload Optimization:** API endpoints should only return the data necessary for the specific view. Avoid serializing large, unnecessary relationship data.
    *   **Response Time Target:** The P95 (95th percentile) response time for all standard API endpoints should be under **200ms**.

---