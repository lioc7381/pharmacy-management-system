# Monitoring and Observability

<!--docs/architecture/[title].md-->

This section defines the strategy for monitoring the application's health and observing its behavior. Given the project's explicit constraint as a **local-only, non-production educational capstone**, this strategy is focused exclusively on providing developers with the tools and metrics needed for effective **local development, debugging, and performance tuning**. It does not include provisions for production-grade, automated monitoring or alerting systems.

## Monitoring Stack

The monitoring "stack" for this project will consist of the free, first-party tools provided by the chosen frameworks. The goal is to leverage these tools to gain insight into the application's runtime behavior without introducing the complexity or cost of external services.

*   **Frontend Monitoring:** **Flutter DevTools**. This is the primary tool for observing the frontend. We will specifically use:
    *   The **Logging View** to inspect structured application logs from `dart:developer`.
    *   The **Network View** to inspect all API requests and responses, including headers, status codes, and timings.
    *   The **Performance View** to profile widget builds and detect rendering performance issues ("jank").
    *   The **Provider Tab** to inspect application state and verify state changes.
*   **Backend Monitoring:** **Laravel Telescope**. This will be our primary tool for backend observability. It provides a comprehensive UI for inspecting:
    *   **Requests:** All incoming API requests and their associated payloads, headers, and responses.
    *   **Exceptions:** A dedicated, searchable UI for all exceptions thrown by the application.
    *   **Queries:** A detailed log of all database queries, including execution time. This is critical for identifying performance bottlenecks like N+1 problems.
    *   **Logs:** A viewer for messages written to Laravel's logging system.
*   **Error Tracking:** **Laravel Telescope's Exception Tracker**. This replaces the need for manual log inspection for errors, providing a clean, stack-traced view of any exceptions.
*   **Performance Monitoring:** A combination of **Flutter DevTools** for the frontend and **Laravel Telescope's Query Watcher** for the backend.

## Structured Logging Best Practices

To maximize the utility of our monitoring tools, all logging statements **must** be structured. Instead of logging simple strings, developers should log machine-parsable objects (maps/JSON) that provide context.

*   **Frontend (Dart):** Use the `log` function from `dart:developer` with a structured map as the message body.
    ```dart
    import 'dart:developer' as developer;
    developer.log(
      'Processing order failed',
      name: 'order.provider',
      error: {
        'orderId': 123,
        'reason': 'Stock check failed',
        'exception': e.toString(),
      },
    );
    ```
*   **Backend (PHP):** Use Laravel's `Log` facade with a context array.
    ```php
    use Illuminate\Support\Facades\Log;
    Log::warning('Failed to process prescription.', [
        'prescription_id' => $prescription->id,
        'user_id' => $user->id,
        'exception' => $e->getMessage(),
    ]);
    ```

## Key Metrics to Observe During Development

Developers should actively observe the following indicators during development and testing to ensure application quality:

*   **Frontend Metrics:**
    *   **UI Rendering Performance:** No dropped frames ("jank") in Flutter DevTools during animations or list scrolling.
    *   **API Response Times:** Using the Network View in DevTools to ensure P95 backend responses are consistently <200ms.
*   **Backend Metrics (via Telescope):**
    *   **Database Query Count:** For any given API request that returns a list of items, the number of database queries should be constant and low (typically 2-3), not `N+1`.
    *   **Slow Queries:** No individual database query should take longer than 50ms.
    *   **Exception Rate:** The Telescope exceptions dashboard should be free of unexpected errors during normal user flows.

## Future Vision: Production-Ready Monitoring

While out of scope for this project, a production-ready version would expand on this foundation with a dedicated, cloud-based monitoring stack. This would likely include:

*   **Centralized Logging:** Shipping logs from both Flutter and Laravel to a service like Datadog, New Relic, or an ELK stack.
*   **Automated Error Tracking:** Integrating a service like Sentry or Bugsnag for real-time error reporting, alerting, and aggregation.
*   **Performance Monitoring (APM):** Using an Application Performance Monitoring tool to track transaction traces, identify bottlenecks, and set up automated alerts for performance degradation.

This future vision underscores why the current, local-only strategy is appropriate and sufficient for the project's defined scope.