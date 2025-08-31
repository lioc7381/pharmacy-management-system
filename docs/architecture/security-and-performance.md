# Security and Performance

<!--docs/architecture/[title].md-->

This section defines the critical, cross-cutting requirements for security and performance. These principles are mandatory and must be integrated into the development of every feature. The goal is to build an application that is not only functional but also secure by design and provides a responsive, high-quality user experience.

## Security Requirements

The security architecture follows a defense-in-depth strategy, combining framework-level protections with specific application-level rules to safeguard user data and system integrity.

*   **Frontend Security:**
    *   **Secure Token Storage:** The Sanctum API token **must** be stored exclusively in `flutter_secure_storage`. It must never be stored in less secure locations like `shared_preferences`.
    *   **Input Sanitization:** While the primary defense is on the backend, all user-generated content displayed in the app (e.g., advice request responses) should be treated as untrusted and properly handled to prevent potential rendering issues or script injection if a WebView were ever introduced.
    *   **Session Management:** The client application is responsible for implementing the 30-minute inactivity timer that automatically logs the user out by clearing the stored token and navigating to the login screen.

*   **Backend Security:**
    *   **Input Validation:** All incoming data from the client **must** be validated using Laravel's Form Requests. The system will never trust client-side validation. This is the primary defense against malformed data and injection attacks.
    *   **Mass Assignment Protection:** All Eloquent models **must** use the `$fillable` property to explicitly whitelist attributes that can be mass-assigned, preventing unauthorized data modification.
    *   **SQL Injection Prevention:** The exclusive use of Laravel's Eloquent ORM and Query Builder with parameter binding provides comprehensive protection against SQL injection vulnerabilities. Raw SQL queries are forbidden.
    *   **Cross-Site Scripting (XSS) Prevention:** Laravel's Blade templating engine (if ever used for web views) automatically escapes output. For the API, the responsibility lies with the frontend to correctly render data.
    *   **File Upload Security:** All uploaded prescription images **must** be validated on the server-side for both file type (MIME) and size, regardless of client-side checks. Files will be stored with unique, non-guessable filenames in a non-public directory.

*   **Authentication Security:**
    *   **Password Hashing:** All user passwords **must** be hashed using Laravel's default Bcrypt hashing algorithm. Plain-text passwords will never be stored.
    *   **Password Policy:** The system will enforce a minimum password length of 8 characters, as handled by Laravel's default validation rules. This aligns with the open question in the PRD and serves as a sensible default.
    *   **Rate Limiting:** The login and registration endpoints (`/api/login`, `/api/register`) **must** be protected by Laravel's built-in rate limiting to mitigate brute-force attacks.

## Performance Optimization

The performance strategy is holistic, addressing both the perceived performance on the client and the efficiency of the backend server.

*   **Frontend Performance:**
    *   **Efficient List Rendering:** All scrollable lists (e.g., orders, notifications) **must** be built using virtualized lists (`ListView.builder`) to ensure smooth scrolling with large datasets.
    *   **Image Optimization:** As specified in the PRD, prescription images **must** be compressed on the client-side before upload to reduce network payload and upload time. The UI must show a determinate progress indicator during this process.
    *   **Asynchronous Operations:** All network and database operations **must** be performed off the main UI thread to prevent the interface from freezing. The UI must provide immediate feedback (e.g., loading indicators) for any user action that triggers an async operation.
    *   **Strategic Caching:** The application will implement the specified read-only caching for medication search results and notifications using `sqflite` to provide an instant-loading experience on subsequent views and during brief network outages.

*   **Backend Performance:**
    *   **Database Indexing:** The database schema includes indexes on frequently queried columns (e.g., `users.email`, `orders.status`). These are critical for ensuring fast query performance.
    *   **Eager Loading (Prevent N+1 Queries):** When retrieving a model with its relationships (e.g., an order with its items), Eloquent's eager loading (`with()`) **must** be used to prevent the N+1 query problem, which is a major source of performance degradation.
    *   **Response Payload Optimization:** API endpoints should only return the data necessary for the specific view. Avoid serializing large, unnecessary relationship data.
    *   **Response Time Target:** The P95 (95th percentile) response time for all standard API endpoints should be under **200ms**.

---
