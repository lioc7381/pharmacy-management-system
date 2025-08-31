# Epic 3: Core Order Fulfillment Workflow

<!--docs/prd/[title].md-->

**Epic Goal:** To build the essential internal workflow for pharmacy staff, enabling them to efficiently process submitted prescriptions, create corresponding orders with robust, transactional stock management, and manage the order's status through to the point of delivery handoff. This epic delivers the core operational value proposition of the system.

## Story 3.1: Implement Staff Prescription Queue

As a Salesperson,
I want to view a queue of all pending prescriptions,
so that I can begin processing new client submissions in a timely manner.

### Acceptance Criteria

1.  A secure API endpoint is created that returns a list of all prescriptions with a 'pending' status.
2.  The Flutter application has a "Prescription Queue" screen, accessible only to authenticated staff with the 'Salesperson' role or higher.
3.  The screen calls the API and displays the pending prescriptions in a list, showing key information like the client's name and submission time.
4.  If there are no pending prescriptions, a message "No pending prescriptions to process" is displayed.
5.  Tapping on a prescription in the list navigates the user to the processing screen (to be built in Story 3.2).

## Story 3.2: Process Prescription into an Order

As a Salesperson,
I want to process a submitted prescription by creating a new order with the specified medications,
so that I can fulfill the client's request and begin the preparation workflow.

### Acceptance Criteria

**UI & Interaction:**

1.  Given the Salesperson is on the 'Prescription Processing' screen, Then the client's uploaded prescription image is displayed prominently.
2.  Given the Salesperson is processing a prescription, Then they can search for medications by name and add them to a list, specifying a quantity for each. **The UI must display the current stock availability for each medication as it is being added.**

**Backend - Happy Path & Business Logic:**

3.  When the 'Create Order' API endpoint is called, it MUST first validate that the `current_quantity` for each medication in the request is greater than or equal to the requested quantity.
4.  Given a successful stock validation, the creation of the order and all related database updates MUST occur within a single database transaction to ensure data integrity.
5.  Given a successful transaction, the `current_quantity` for each medication in the `Medications` table MUST be decremented by the ordered quantity.
6.  Given a successful transaction, the `status` of the corresponding record in the `Prescriptions` table MUST be updated to 'Processed'.
7.  Given a successful transaction, a new record MUST be created in the `Orders` table with a `status` of 'In Preparation'.
8.  Given a successful transaction, a new record MUST be created in the `Notifications` table for the client, containing the new Order ID.

**Backend & UI - Exception/Error Handling:**

9.  When the 'Create Order' API endpoint is called and at least one medication has insufficient stock, Then the API MUST return a `409 Conflict` error with a message identifying the out-of-stock item(s).
10. Given the API returns a `409 Conflict` error for insufficient stock, Then the frontend MUST display a clear, non-dismissible error message to the Salesperson, and the order creation process MUST be halted.

## Story 3.3: Reject Invalid Prescription

As a Salesperson,
I want to reject an invalid prescription submission and provide a reason,
so that the submission is removed from the active queue and the client is informed.

### Acceptance Criteria

1.  Given the Salesperson is on the 'Prescription Processing' screen, a 'Reject' button is available.
2.  When the 'Reject' button is tapped, the system prompts the Salesperson to enter a mandatory reason for the rejection (e.g., "Image is blurry," "Prescription is incomplete").
3.  Upon confirming the rejection, a secure API endpoint updates the prescription's status to 'Rejected' and saves the rejection reason.
4.  The system generates an in-app notification for the client informing them that their submission was rejected and includes the reason provided by the Salesperson.
5.  A rejected prescription is removed from the main 'pending' queue for Salespeople.

## Story 3.4: Manage Order Status

As a Salesperson,
I want to manage the status of an order,
so that I can track its progress through the fulfillment workflow and prepare it for delivery.

### Acceptance Criteria

1.  An "Order Management" screen allows the Salesperson to view orders, filterable by status (e.g., 'In Preparation', 'Ready for Delivery').
2.  The Salesperson can change an order's status from 'In Preparation' to 'Ready for Delivery'.
3.  When an order's status is updated to 'Ready for Delivery', the system sends an in-app notification to the client.
4.  The API endpoints for updating status are protected and accessible only to authorized staff.

## Story 3.5: Fulfill Delivery

As a Delivery Person,
I want to view my assigned deliveries and update their final status,
so that the system has an accurate, real-time record of the order's completion.

### Acceptance Criteria

1.  A "My Deliveries" screen is available to users with the 'Delivery' role, showing a list of orders assigned to them with the status 'Ready for Delivery'.
2.  The Delivery Person can update an order's status to 'Completed' upon successful delivery.
3.  The Delivery Person can update an order's status to 'Failed Delivery' if the delivery could not be completed.
4.  Once an order's status is updated to 'Completed' or 'Failed Delivery', it is removed from the Delivery Person's active queue.
5.  The system sends a final notification to the client when their order is marked as 'Completed'.

---
