# Epic 2: End-to-End Prescription Submission Workflow

**Epic Goal:** To deliver the primary customer value proposition by building the complete, end-to-end workflow for a client to submit a prescription. This epic includes the creation of the foundational notification system, the secure API for file uploads, and the client-facing UI for both submitting a prescription and viewing subsequent status updates.

## Story 2.1: Implement Foundational Notification System (Backend)

As a System,
I want a backend mechanism to create, store, and retrieve user-specific notifications,
so that important asynchronous updates can be delivered reliably to users.

### Acceptance Criteria

1.  A `Notifications` table is created in the database with fields for user ID, title, message, and a read status.
2.  An internal service or method is created that allows other parts of the backend (e.g., the prescription service) to generate a notification for a specific user.
3.  A secure API endpoint (`GET /api/notifications`) is created that fetches all notifications for the currently authenticated user.
4.  The API endpoint for fetching notifications returns them in reverse chronological order (newest first).

## Story 2.2: Implement Prescription Submission API

As a System,
I want a secure API endpoint for clients to upload a prescription image,
so that their submissions can be received and stored for processing by staff.

### Acceptance Criteria

1.  A `POST /api/prescriptions` endpoint is created and protected by middleware, accessible only to users with the 'Client' role.
2.  The endpoint accepts an image file (JPG, PNG) and validates that its size is under the 5MB limit.
3.  Upon successful upload, the image is stored securely on the server, and a new record is created in the `Prescriptions` table with a 'pending' status.
4.  The API response for a successful submission must include the unique reference number for the created prescription record.
5.  After the prescription record is created, the system generates an in-app notification for the client that includes the reference number (e.g., "Your prescription #P12345 has been submitted successfully!").
6.  If the file is invalid (type or size), the endpoint returns a `422 Unprocessable Entity` error with a clear message.

## Story 2.3: Build Prescription Submission UI

As a Client,
I want to upload a prescription image through the app,
so that I can submit it for processing without visiting the pharmacy.

### Acceptance Criteria

1.  A "Submit Prescription" screen is created in the Flutter app, accessible to logged-in clients.
2.  The user can tap a button to select an image from their device's gallery or camera.
3.  The application performs client-side compression on the selected image before uploading.
4.  Upon tapping "Submit," the app calls the `POST /api/prescriptions` endpoint with the compressed image.
5.  A success message is displayed to the user upon a successful submission, which includes the reference number returned by the API (e.g., "Submission successful! Your reference is #P12345.").
6.  If the upload fails due to a network error or server validation error, a user-friendly error message is displayed.

## Story 2.4: Implement Notification Viewing UI

As a Client,
I want to view a list of my notifications,
so that I can stay informed about the status of my prescriptions and other important updates.

### Acceptance Criteria

1.  A "Notifications" screen is created in the Flutter app, accessible to logged-in clients.
2.  The screen calls the `GET /api/notifications` endpoint to fetch and display the user's notifications.
3.  Notifications are displayed in a list, with the newest items at the top.
4.  A visual indicator (e.g., a dot) is present on unread notifications.
5.  If the user has no notifications, a message like "You have no new notifications" is displayed.
6.  Upon successfully loading the notification list, any app-wide notification indicators (e.g., a badge on the navigation bar) are cleared.

---
