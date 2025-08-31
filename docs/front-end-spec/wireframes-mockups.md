# Wireframes & Mockups

<!--docs/front-end-spec/[title].md-->

This section provides a bridge between the abstract user flows and the final visual design. It outlines the source of truth for high-fidelity designs and specifies the layout and key components for the application's most critical screens.

**Source of Truth:** This document is the single source of truth for all UI/UX specifications. The screen layouts described below are the final designs and definitive blueprints for development. They define the required elements, their arrangement, and their behavior, and must be implemented directly from these specifications.

## Key Screen Layouts

The following are conceptual layouts for the core screens identified in the Information Architecture and User Flows. They define the purpose, key elements, and interaction model for each view, serving as the definitive blueprint for front-end development.

### Screen: Prescription Submission (Client)

*   **Purpose:** To provide a fast, simple, and reassuring interface for a client to upload a prescription image. The design must minimize cognitive load and guide the user to a successful submission with confidence.
*   **Key Elements:**
    *   **Image Placeholder/Preview:** A large, central area that either prompts the user to add an image or displays a clear preview of the selected image.
    *   **"Select Image" Button:** A primary button that opens the device's native camera or photo gallery selector.
    *   **"Submit" Button:** The final call-to-action, which should be disabled until an image has been selected and previewed.
    *   **Helper Text:** A brief, clear instruction (e.g., "Please upload a clear photo of your entire prescription.").
*   **Interaction Notes:**
    *   Upon tapping "Select Image," the user is presented with a choice ("Take Photo" or "Choose from Gallery").
    *   Once an image is selected, it appears in the preview area. The user should have an option to clear the selection and choose a different image.
    *   Tapping "Submit" will display a non-blocking loading indicator while the image is compressed and uploaded.

### Screen: Prescription Processing (Salesperson)

*   **Purpose:** To provide an efficient, all-in-one interface for a salesperson to review a prescription, create an accurate order, and handle exceptions like rejection or out-of-stock items. The layout must support the dual tasks of viewing an image and building an order simultaneously.
*   **Key Elements:**
    *   **Prescription Image Viewer:** A prominent, zoomable view of the client's uploaded prescription image.
    *   **Medication Search Input:** A powerful search bar with real-time results to quickly find medications in the catalog.
    *   **Order Item List:** A running list of medications added to the order, showing name, quantity, price, and a real-time stock status indicator for each item.
    *   **Order Summary:** A section displaying the subtotal and total price, which updates as items are added or removed.
    *   **Primary Actions:** Clearly distinct buttons for "Create Order" and "Reject Prescription".
*   **Interaction Notes:**
    *   The layout should be optimized for a mobile screen, potentially using a top/bottom split view or a tabbed interface to switch between the image and the order form.
    *   As the salesperson types in the search bar, a list of matching medications appears. Tapping a result adds it to the order list with a default quantity of 1.
    *   The quantity of each item in the list should be easily editable.
    *   The "Create Order" button remains disabled until at least one valid, in-stock medication is added to the list.
    *   Tapping "Reject Prescription" will open a modal dialog requiring a reason for rejection before proceeding.

### Screen: Manager Dashboard

*   **Purpose:** To serve as the manager's command center, providing a high-level, at-a-glance overview of business operations and immediately surfacing critical issues that require their attention. The design must facilitate proactive, data-driven decision-making.
*   **Key Elements:**
    *   **KPI Summary Cards:** A row of prominent, easy-to-read cards at the top of the screen displaying the day's key metrics (e.g., "Total Orders," "Total Revenue," "New Prescriptions").
    *   **Urgent Alerts Section:** A visually distinct section directly below the KPIs, designed to draw immediate attention. It will feature a high-priority alert for "Low Stock Items" with a clear warning icon (⚠️) and a count of affected items.
    *   **Management Quick Links:** A grid or list of clearly labeled buttons that provide one-tap access to the core administrative functions ("Manage Medications," "Manage Staff," "Manage Clients").
*   **Interaction Notes:**
    *   The KPI cards are primarily for quick-look information. In a future version, tapping them could lead to more detailed reports.
    *   Tapping the "Low Stock Items" alert is a critical action. It will navigate the manager directly to the low-stock report screen, bypassing any intermediate menus.
    *   Each "Quick Link" button navigates directly to its corresponding management screen.

### Screen: Salesperson Order Management View

*   **Purpose:** To provide the salesperson with a clear, organized, and actionable view of the entire order fulfillment pipeline. The interface must make it easy to track orders, understand their current status, and perform the necessary actions to move them through the workflow.
*   **Key Elements:**
    *   **Status Tabs:** A tabbed navigation bar at the top of the screen to filter the order list by its most relevant statuses (e.g., "In Preparation," "Ready for Delivery," "Completed"). This is the primary organizational paradigm for the screen.
    *   **Order List:** Below the tabs, a scrollable list of orders that dynamically updates based on the selected status tab.
    *   **Order Card:** Each item in the list will be a self-contained card summarizing the most important order information: Order ID, Client Name, Time since order creation, and Total Amount.
    *   **Search/Filter Bar (Optional but Recommended):** A search bar above the tabs to allow finding a specific order by ID or client name, which is crucial for handling customer inquiries.
*   **Interaction Notes:**
    *   The screen will default to the "In Preparation" tab, as this is the most actionable queue for the salesperson.
    *   Tapping on an Order Card will navigate the user to a detailed "Order Details" screen where they can perform status updates (e.g., "Mark as Ready for Delivery") or other management tasks.
    *   The list should support a "pull-to-refresh" gesture to fetch the latest order data.

---
