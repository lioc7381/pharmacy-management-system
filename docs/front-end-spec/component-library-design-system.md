# Component Library / Design System

<!--docs/front-end-spec/[title].md-->

This section defines the approach to our component library and details the foundational, reusable UI components that will ensure consistency, speed up development, and create a cohesive user experience across the entire application.

### Design System Approach

Given the project's constraints (educational capstone, single developer) and the choice of Flutter for development, our approach will be to **extend, not reinvent**. We will adopt Google's **Material Design** system as our foundational framework. This provides a robust, well-documented, and accessible set of components out of the box.

Our work will focus on:
1.  **Customization:** Applying our specific branding, color palette, and typography (as defined in the upcoming Style Guide section) to the standard Material components.
2.  **Composition:** Creating specific, reusable "composite components" from the base Material elements to handle application-specific needs (e.g., an `OrderCard` component).
3.  **Consistency:** Ensuring that any given UI element (like a primary button or a text input) looks and behaves identically wherever it appears.

### Core Components

The following are the initial set of core components to be developed. They represent the most critical building blocks needed to construct the MVP screens.

#### Component: Primary Button
*   **Purpose:** Used for the single most important action on a screen, such as "Submit," "Create Order," or "Log In."
*   **Variants:** Filled (Default), Text Button.
*   **States:** Default, Hover, Pressed, Disabled.
*   **Usage Guidelines:** There should be only one primary, filled button visible on a screen at any time.

#### Component: Form Input Field
*   **Purpose:** To collect user-entered data in forms.
*   **Variants:** Standard Text, Password, Search.
*   **States:** Default, Focused, Error, Disabled.
*   **Usage Guidelines:** Every input field must have a clear, visible label.

#### Component: Order Status Tag
*   **Purpose:** A small, colored tag used to clearly and consistently communicate the status of an order or prescription.
*   **Variants:** In Preparation, Ready for Delivery, Completed, Cancelled / Failed, Pending.
*   **States:** This component is static and primarily has one state (display).
*   **Usage Guidelines:** The color-coding must be used consistently throughout the application wherever a status is displayed.

### Composite Components

These components are built by combining core components into reusable patterns that solve specific UI problems within our application.

#### Composite Component: OrderCard
*   **Purpose:** To display a summary of a single order in a list view, such as on the "My Orders" screen for a client or the "Order Management" screen for a salesperson. It must be scannable, informative, and tappable.
*   **Composition:** This component is composed of a `Card` container, `Text` elements, and the `Order Status Tag` component.
*   **Layout:**
    *   **Top Row:** Order ID on the left, `Order Status Tag` on the right.
    *   **Middle Row:** Client Name.
    *   **Bottom Row:** Timestamp on the left, Total Amount on the right.
*   **States:** Default, Pressed (with visual feedback on tap).
*   **Usage Guidelines:** Used in any list that displays multiple orders. The layout should remain consistent across all roles to ensure familiarity.

#### Composite Component: KPI Card
*   **Purpose:** To display a single, high-level metric on the Manager Dashboard in an easily scannable format.
*   **Composition:** A `Card` container holding a large `Text` element for the value and a smaller `Text` element for the label.
*   **Layout:** The numeric value is displayed prominently with the descriptive label directly below it.
*   **States:** This is a display-only component with a single default state.
*   **Usage Guidelines:** Used exclusively on the Manager Dashboard to provide an at-a-glance overview of key performance indicators.

#### Composite Component: Notification Item
*   **Purpose:** To display a single notification in a list, clearly indicating its content and read/unread status.
*   **Composition:** A `ListTile` containing an `Icon` (to indicate notification type), two `Text` elements (for title and summary), and a trailing `Text` element (for the timestamp).
*   **Layout:** Icon on the left, title and summary stacked vertically in the center, and timestamp on the right.
*   **States:**
    *   **Unread:** Features a visual indicator (e.g., a colored dot) and may use a bolder font weight for the title.
    *   **Read:** Standard font weight and no visual indicator.
    *   **Pressed:** Visual feedback on tap.
*   **Usage Guidelines:** Used exclusively on the "Notifications" screen. Tapping an item should navigate the user to the relevant context (e.g., the specific order detail screen).

---
