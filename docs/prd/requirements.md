# Requirements

<!--docs/prd/[title].md-->

## Functional

*   **FR1:** The system shall allow any user (public or authenticated) to search for medications by name and view their details, including price and availability.
*   **FR2:** The system shall provide a registration workflow for new users, who will be assigned the 'Client' role by default. It must also provide a secure login/logout mechanism for all user roles.
*   **FR3:** Authenticated clients shall be able to submit a new prescription by selecting an image, which the client application will compress on-device before uploading.
*   **FR4:** The application must implement a simple, **read-only** caching strategy using local storage for non-critical data (e.g., search results, notifications). The cache is for display during brief network interruptions only; all actions that modify data are disabled when offline.
*   **FR5:** Authenticated managers shall have the ability to perform core administrative tasks, including managing client accounts, employee accounts, and the medication catalog.
*   **FR6:** The system shall provide a database-backed, in-app notification system to deliver asynchronous updates to users regarding their orders and other relevant events.

## Non-Functional

*   **NFR1:** The client-facing application shall be a mobile application built exclusively for the Android platform using Flutter.
*   **NFR2:** The backend shall be a REST API built with Laravel, using SQLite as the exclusive database to ensure portability and meet the zero-budget constraint.
*   **NFR3:** All user-facing communication that is not instantaneous (e.g., advice responses, order updates) shall be handled asynchronously via the notification system, with no requirement for real-time technologies like WebSockets.
*   **NFR4:** The application must implement a simple, read-only caching strategy for non-critical data (e.g., search results, notifications) to improve the user experience during brief network interruptions.
*   **NFR5:** The entire system must be deliverable with a zero-dollar budget for third-party services or hosting.
*   **NFR6:** The user interface shall be designed with a mobile-first, dark Material theme.
*   **NFR7:** The client application must perform on-device image compression for prescription uploads to ensure files are under a 5MB limit.

---
