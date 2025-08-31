# Epic 4: Administrative System Management

<!--docs/prd/[title].md-->

**Epic Goal:** To empower the 'Manager' role with the essential administrative tools to manage the system's foundational data, including the medication catalog, client accounts, and employee accounts. This epic ensures the system is maintainable and can be administered effectively post-launch.

## Story 4.1: Manage Medication Catalog

As a Manager,
I want to add, edit, and disable medications in the catalog,
so that I can maintain an accurate and up-to-date list of available products.

### Acceptance Criteria

1.  A "Medication Management" screen is created in the Flutter app, accessible only to users with the 'Manager' role.
2.  The screen provides functionality to search for existing medications and initiate adding a new one.
3.  Secure API endpoints (`POST`, `PUT`, `DELETE` on `/api/medications`) are implemented and protected by middleware, accessible only to the 'Manager' role.
4.  The UI allows a Manager to create a new medication by providing all required details (Name, Price, Initial Quantity, etc.).
5.  The UI allows a Manager to edit the details of an existing medication.
6.  The UI allows a Manager to disable a medication. The system must prevent this action if the medication is part of any order that is not in a 'Completed' or 'Cancelled' state, displaying an appropriate error message.
7.  Medications marked as 'disabled' must not appear in public search results for clients.

## Story 4.2: Manage Client Accounts

As a Manager,
I want to view, edit, and disable client accounts,
so that I can manage the user base and handle administrative requests.

### Acceptance Criteria

1.  A "Client Management" screen is created in the Flutter app, accessible only to users with the 'Manager' role.
2.  The screen provides functionality to search for clients by name or email.
3.  Secure API endpoints (`GET`, `PUT`, `DELETE` on `/api/clients/{id}`) are implemented and protected by middleware, accessible only to the 'Manager' role.
4.  The UI allows a Manager to edit a client's details (e.g., Name, Email).
5.  The UI allows a Manager to disable a client's account. The system must prevent this action if the client has any orders that are not in a 'Completed' or 'Cancelled' state, displaying an appropriate error message.
6.  A user whose account is disabled must be prevented from logging into the application.

## Story 4.3: Manage Employee Accounts

As a Manager,
I want to add, edit, and disable employee accounts and assign their roles,
so that I can manage staff access to the system.

### Acceptance Criteria

1.  A "Staff Management" screen is created in the Flutter app, accessible only to users with the 'Manager' role.
2.  Secure API endpoints (`POST`, `PUT`, `DELETE` on `/api/employees`) are implemented and protected by middleware, accessible only to the 'Manager' role.
3.  The UI form for adding a new employee must include a dropdown menu to assign a specific role (e.g., 'Pharmacist', 'Salesperson', 'Delivery').
4.  The UI allows a Manager to edit an existing employee's details, including their assigned role.
5.  The UI allows a Manager to disable an employee's account. The system must prevent this action if the employee is assigned to any active orders.
6.  A user whose employee account is disabled must be prevented from logging into the application.

---
