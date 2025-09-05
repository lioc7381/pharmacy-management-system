# Wireframes & Mockups

<!--docs/front-end-spec/[title].md-->

This section outlines the layout and key components for the application's screens.

**Source of Truth:** This document is the single source of truth for all UI/UX specifications. The screen layouts described below are the final blueprints for development and have been designed to be implemented using the minimal set of dependencies and architectural patterns mandated by the project plan.

## Guiding Architectural Principles on UI

The user experience is a direct reflection of the underlying architecture. The following principles from the simplification plan dictate the behavior of the UI:

*   **Strict Online-Only Operation:** The application has no offline caching. Every piece of data is fetched from the network on demand. The UI will not proactively check for network status; it will only react to failed network requests.
*   **Stateless Interaction Model:** UI components will not maintain complex state. Multi-step actions are vulnerable to interruption. If a network request fails mid-process due to an invalid token, the user will be logged out, and any unsaved work will be lost.
*   **Failure-Driven Error Handling:** The primary way the user is notified of a problem (e.g., no internet connection, invalid data) is after a network request fails. The UI will display an error message and typically require the user to retry the action manually.

## Key Screen Layouts

### Screen: Prescription Submission (Client)

*   **Purpose:** To provide a clear and functional interface for a client to upload a prescription image.
*   **Key Elements:**
    *   **Image Placeholder/Preview:** A central area that either prompts the user to add an image or displays a preview of the selected image.
    *   **"Select Image" Button:** A primary button that opens the device's native camera or photo gallery selector.
    *   **"Submit" Button:** The final call-to-action, which is disabled until an image has been selected.
    *   **Helper Text:** A brief instruction (e.g., "Please upload a clear photo of your prescription.").
*   **Interaction Notes:**
    *   Upon tapping "Select Image," the user is presented with a choice ("Take Photo" or "Choose from Gallery").
    *   Once an image is selected, it appears in the preview area.
    *   Tapping "Submit" will display a loading indicator and attempt to upload the image.
    *   **On Failure:** If the upload fails (e.g., due to a network issue), an error message will be displayed. The user must tap "Submit" again to retry the upload. If the failure is due to an invalid token, the user will be redirected to the login screen.

### Screen: Prescription Processing (Salesperson)

*   **Purpose:** To provide a functional interface for a salesperson to review a prescription image and create a corresponding order based on available medications.
*   **Key Elements:**
    *   **Prescription Image Viewer:** A view of the client's uploaded prescription image.
    *   **Medication Search Input:** A text field and a "Search" button to find medications in the catalog.
    *   **Order Item List:** A list of medications that have been added to the current order, showing name and quantity.
    *   **Order Summary:** A section displaying the subtotal and total price.
    *   **Primary Actions:** Buttons for "Create Order" and "Reject Prescription".
*   **Interaction Notes:**
    *   **Search is manual.** The salesperson types a medication name and taps the "Search" button. A loading indicator is shown, and a list of results is displayed upon success.
    *   Adding an item to the order list does **not** perform a real-time stock check. The list is for draft purposes only.
    *   The "Create Order" button is enabled once at least one medication is added. Tapping it sends the entire order to the backend for validation.
    *   **On Submission Failure:** If the order creation fails for any reason (e.g., an item is out of stock, network error), the entire submission is rejected. An error message will be displayed, and the salesperson must manually correct the order (e.g., remove the out-of-stock item) and tap "Create Order" again.
    *   **Token Invalidation Risk:** This is a multi-step process. If the salesperson's session becomes invalid before they submit the order, they will be logged out upon tapping "Create Order," and **all progress on the current order will be lost.**

### Screen: Manager Dashboard

*   **Purpose:** To serve as the manager's entry point, providing a summary view of key business metrics and direct links to management functions.
*   **Key Elements:**
    *   **KPI Summary Cards:** A row of cards displaying key metrics (e.g., "Total Orders," "Total Revenue," "New Prescriptions").
    *   **Alerts Section:** A section for important notifications, such as a "Low Stock Items" alert with a count of affected items.
    *   **Management Quick Links:** A grid or list of buttons for navigation ("Manage Medications," "Manage Staff," "Manage Clients").
*   **Interaction Notes:**
    *   The data on this screen is fetched from the network each time the user navigates to it. **It does not update in real-time.**
    *   To see the latest data, the user must perform a "pull-to-refresh" gesture, which will trigger a new network request.
    *   Tapping the "Low Stock Items" alert navigates the manager to the relevant report screen, fetching that data on demand.

### Screen: Salesperson Order Management View

*   **Purpose:** To provide the salesperson with an organized view of orders, allowing them to see orders based on their current status.
*   **Key Elements:**
    *   **Status Tabs:** A tabbed navigation bar to filter the order list by status (e.g., "In Preparation," "Ready for Delivery," "Completed").
    *   **Order List:** A scrollable list of orders corresponding to the selected status tab.
    *   **Order Card:** Each item in the list summarizes key order information: Order ID, Client Name, and Total Amount.
*   **Interaction Notes:**
    *   The screen defaults to the "In Preparation" tab, triggering an initial network request for those orders.
    *   Switching to a different tab triggers a **new network request** to fetch the orders for that status. A loading indicator will be shown during the fetch.
    *   The list does not update automatically. The user must use a "pull-to-refresh" gesture to fetch the latest order data for the currently viewed tab.
    *   Tapping on an Order Card navigates the user to the "Order Details" screen.

---