# Epic 3: Core Order Fulfillment Workflow (Simplified Architecture)

<!--docs/prd/[title].md-->

**Epic Goal:** To build the essential internal workflow for pharmacy staff, enabling them to efficiently process submitted prescriptions, create corresponding orders with robust, transactional stock management, and manage the order's status through to the point of delivery handoff. This epic delivers the core operational value proposition of the system.

## Story 3.1: Implement Staff Prescription Queue

As a Salesperson,
I want to view a queue of all pending prescriptions,
so that I can begin processing new client submissions in a timely manner.

### Acceptance Criteria

1.  **Backend (Laravel):** A secure API endpoint (`GET /api/prescriptions?status=pending`) is created.
    *   Access is protected by route middleware, restricted to users with 'Salesperson' or 'Manager' roles.
    *   The response MUST be formatted using a `PrescriptionResourceCollection` to ensure a consistent data structure.
2.  **Frontend (Flutter):** The Flutter application defines a route for the "Prescription Queue" screen within `go_router`.
    *   Access to this route is protected by a redirect guard that verifies the user is authenticated and has the appropriate role.
3.  **Frontend (Flutter):** The screen's corresponding `PrescriptionProvider` (using `ChangeNotifier`) will fetch the data using the `http` package. The UI will listen to the provider and display the list of prescriptions, showing the client's name and submission time.
4.  **Frontend (Flutter):** If the provider's list of prescriptions is empty, a message "No pending prescriptions to process" is displayed.
5.  **Frontend (Flutter):** Tapping on a prescription uses `go_router` to navigate the user to the processing screen (e.g., `/prescriptions/123/process`), passing the prescription ID as a path parameter.

## Story 3.2: Process Prescription into an Order

As a Salesperson,
I want to process a submitted prescription by creating a new order with the specified medications,
so that I can fulfill the client's request and begin the preparation workflow.

### Acceptance Criteria

**UI & Interaction (Flutter):**

1.  On the 'Prescription Processing' screen, the client's uploaded prescription image is displayed prominently.
2.  As the Salesperson searches for and adds medications, the UI MUST fetch and display the **real-time** stock availability for each item. In accordance with the online-only architecture, no stock data will be cached on the client.

**Backend (Laravel) - Happy Path & Business Logic:**

3.  A `CreateOrderRequest` FormRequest class will handle all incoming validation for the `POST /api/orders` endpoint. It must validate the structure of the incoming medication list and quantities.
4.  The `OrderService` will receive the validated data array directly from the `CreateOrderRequest` (`$request->validated()`).
5.  The `OrderService` MUST wrap all database operations within a single `DB::transaction()` block to ensure atomicity. The transaction will:
    a.  Perform a final check to ensure the `current_quantity` for each medication is sufficient, locking the rows to prevent race conditions.
    b.  Decrement the `current_quantity` for each medication in the `Medications` table.
    c.  Update the `status` of the corresponding `Prescription` to 'Processed'.
    d.  Create a new `Order` with a `status` of 'In Preparation'.
    e.  Create a new `Notification` for the client, containing the new Order ID.
6.  Upon a successful transaction, the API will return the newly created order, formatted via an `OrderResource`.

**Backend & UI - Exception/Error Handling:**

7.  If the stock validation within the `OrderService` fails, the service MUST throw an exception that results in a `409 Conflict` HTTP response, with a message identifying the out-of-stock item(s).
8.  If the Flutter app receives a `409 Conflict` response, the `OrderProvider` will update a simple `errorMessage` state property (e.g., `String? errorMessage`). The UI will listen to this property and display a persistent, non-dismissible error message (e.g., a banner or dialog) until the user takes action to correct the order. The `ViewEvent` pattern is explicitly forbidden.

## Story 3.3: Reject Invalid Prescription

As a Salesperson,
I want to reject an invalid prescription submission and provide a reason,
so that the submission is removed from the active queue and the client is informed.

### Acceptance Criteria

1.  On the 'Prescription Processing' screen, a 'Reject' button is available. Tapping it shows a simple dialog to input a rejection reason.
2.  **Backend (Laravel):** A `RejectPrescriptionRequest` FormRequest will validate the request, ensuring the `reason` field is present and non-empty.
3.  **Backend (Laravel):** The corresponding `PrescriptionService` method will update the prescription's status to 'Rejected' and save the rejection reason.
4.  **Backend (Laravel):** The service will then create an in-app notification for the client, informing them of the rejection and including the reason.
5.  A rejected prescription no longer appears in the API response for the 'pending' prescription queue.

## Story 3.4: Manage Order Status

As a Salesperson,
I want to manage the status of an order,
so that I can track its progress through the fulfillment workflow and prepare it for delivery.

### Acceptance Criteria

1.  An "Order Management" screen, protected by the `go_router` guard for staff roles, displays a list of orders fetched from the API.
2.  The Salesperson can change an order's status from 'In Preparation' to 'Ready for Delivery'.
3.  **Backend (Laravel):** This action is handled by a secure API endpoint (e.g., `PATCH /api/orders/{order}`). Access is controlled via route middleware.
4.  **Backend (Laravel):** An `UpdateOrderStatusRequest` FormRequest validates that the new status is a valid transition.
5.  **Backend (Laravel):** When the `OrderService` updates the status to 'Ready for Delivery', it also generates an in-app notification for the client.

## Story 3.5: Fulfill Delivery

As a Delivery Person,
I want to view my assigned deliveries and update their final status,
so that the system has an accurate, real-time record of the order's completion.

### Acceptance Criteria

1.  A "My Deliveries" screen is available, protected by a `go_router` guard that restricts access to users with the 'Delivery' role. It lists orders with the status 'Ready for Delivery' assigned to them.
2.  The Delivery Person can update an order's status to 'Completed' or 'Failed Delivery'.
3.  **Backend (Laravel):** The status update endpoint uses a `FormRequest` to validate the final status and is protected by middleware to ensure only the assigned delivery person (or a manager) can update it.
4.  Once an order's status is updated to 'Completed' or 'Failed Delivery', it is removed from the Delivery Person's active queue on the next refresh.
5.  **Backend (Laravel):** The `OrderService` sends a final notification to the client when their order is marked as 'Completed'.

---