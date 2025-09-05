# User Interface Design Goals

<!--docs/prd/[title].md-->

## Overall UX Vision

The user experience will prioritize a clear, straightforward, and efficient digital workflow. The primary goal is to replace complex manual processes with a simple, step-by-step digital interface that is predictable and reliable **when connected to the network**. Key principles are clarity, simplicity, and speed, minimizing the number of steps required to complete core tasks like submitting a prescription or processing an order.

## Technical & UX Constraints

The following constraints, derived from the project's simplification plans, are foundational to the UI/UX design and must be adhered to:

1.  **Online-Only Operation:** The application requires a constant and stable internet connection to function. There will be no data caching or offline capabilities. The UI must be designed to handle network failures on a per-action basis (e.g., showing an error message with a "retry" option after a failed request) rather than supporting a global offline mode.
2.  **Server-Authoritative Validation:** The client will perform only basic input validation (e.g., checking for empty fields). All critical validation for data, business rules, and file uploads (e.g., prescription image type and size) will be handled exclusively by the server. The UI should be prepared to display clear error messages returned from the API.
3.  **Simplified Security Model:** For the MVP, authentication tokens are stored using the device's standard, non-secure preferences (`shared_preferences`). The application's security model relies on API-level authorization and assumes a non-compromised device environment.
4.  **Stateless Authentication:** The application will assume that a stored authentication token is always valid and will not implement logic for token refreshing or proactive expiration checks. If an API call fails due to an invalid token, the user will be logged out and returned to the login screen.

## Key Interaction Paradigms

The application will adhere to standard mobile interaction patterns familiar to Android users. This includes taps, swipes, and scrolling for navigation, along with standard form inputs for data entry. A key interaction will be the image selection and upload workflow for prescriptions, which must be seamless and straightforward.

## Primary Navigation

To ensure a consistent and intuitive user experience, the application will use a **Bottom Navigation Bar** for primary navigation on the main authenticated screens. The navigation bar will be visible to all authenticated user roles but will display different tabs based on the user's role.

*   **Client Role:**
    *   Dashboard / Home
    *   Submit Prescription
    *   My Orders
    *   Notifications
*   **Staff Roles (Salesperson, Delivery, etc.):**
    *   Dashboard / Queue
    *   Order Management
    *   Notifications
*   **Manager Role:**
    *   Dashboard
    *   Management (leading to sub-screens for Client, Staff, and Medication management)
    *   Notifications

Secondary navigation (e.g., navigating from a list to a detail view) will be handled through standard screen-to-screen transitions, managed by a declarative routing solution.

## Responsive Design

While the application is "Mobile Only (Android)", it must be responsive enough to provide a usable experience across a range of screen sizes, from small phones to larger tablets in portrait orientation.

*   **Layouts:** Use fluid layouts (e.g., Flutter's `Expanded` and `Flexible` widgets) that adapt to the available width. Avoid fixed-width elements.
*   **Breakpoints:** While a complex breakpoint system is not required for the MVP, the UI should not break or have overflow errors on common device widths (e.g., 360dp to 800dp).
*   **Text:** Text should wrap correctly and remain legible on all screen sizes.
*   **Orientation:** The application should be locked to **portrait mode** to simplify the responsive design effort for the MVP.

## Core Screens and Views

This is a conceptual list of the essential screens required to deliver the MVP functionality, intended to guide the Design Architect:

*   Login / Registration Screen
*   Public Medication Search Screen
*   Client Dashboard (Main authenticated screen for clients)
*   Prescription Submission Form (including image selection)
*   Order History / Status List (for clients)
*   Staff Dashboard (Role-specific view for authenticated staff)
*   Prescription Processing Queue (for staff)
*   Order Management View (for staff)
*   Notification Center
*   Client Management Screen (for searching, viewing, and editing clients)
*   Staff Management Screen (for adding, viewing, and editing employees)
*   Medication Management Screen (for adding, viewing, and editing medications)

## Accessibility

Accessibility: WCAG AA

## Branding

The application will use a clean, mobile-first, dark Material theme. No specific corporate branding elements (logos, color palettes) have been provided at this stage.

## Target Device and Platforms

Target Device and Platforms: Mobile Only (Android)

---