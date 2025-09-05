# Component Library / Design System

<!--docs/front-end-spec/[title].md-->

This section defines the foundational, reusable UI components for the application.

### Design System Approach

Given the project's constraints and the choice of Flutter, our approach is to **extend, not reinvent**. We will adopt Google's **Material Design** system as our foundational framework.

Our work will focus on:
1.  **Customization:** Applying our specific branding, color palette, and typography to the standard Material components.
2.  **Composition:** Creating specific, reusable "composite components" from the base Material elements to handle application-specific needs.
3.  **Architectural Integration:** Ensuring every component integrates seamlessly with the mandated architecture:
    *   **State Management:** Component states (e.g., disabled, error, loading) will be driven directly by properties within our simplified `Provider` classes (e.g., an `isLoading` boolean or an `errorMessage` string).
    *   **Data Models:** Components that display data will accept the manually-created Dart data models as their input.
    *   **Navigation:** Tappable components that trigger navigation will do so by invoking `go_router`.

### Core Components

These are the fundamental building blocks of the UI.

#### Component: Primary Button
*   **Purpose:** Used for the single most important action on a screen, such as "Submit," "Create Order," or "Log In."
*   **Variants:** Filled (Default), Text Button.
*   **States:** Default, Hover, Pressed, Disabled.
*   **State Management:** The `Disabled` state is controlled by a `bool` flag. This flag will typically be an `isLoading` property from a `Provider` or a local `StatefulWidget` to prevent multiple submissions while a network request is in progress.

#### Component: Form Input Field
*   **Purpose:** To collect user-entered data in forms.
*   **Variants:** Standard Text, Password, Search.
*   **States:** Default, Focused, Error, Disabled.
*   **State Management:**
    *   The `Error` state is activated by passing a non-null error string to the widget. This string will come directly from a `Provider`'s `errorMessage` property.
    *   The `Disabled` state is controlled by a `bool` flag, typically `isLoading` from a `Provider`.
*   **Usage Guidelines:** Every input field must have a clear, visible label.

#### Component: Order Status Tag
*   **Purpose:** A small, colored tag to communicate the status of an order.
*   **Variants:** In Preparation, Ready for Delivery, Completed, Cancelled / Failed, Pending.
*   **Data Model:** The variant displayed is determined by the `OrderStatus` enum from the manually-defined `Order` data model. The component will accept this enum as a parameter and map it to the correct color and text.
*   **States:** This component is static and has no interactive states.

### Composite Components

These components are built by combining core components to solve specific UI problems.

#### Composite Component: OrderCard
*   **Purpose:** To display a summary of a single order in a list view.
*   **Composition:** A `Card` container, `Text` elements, and the `Order Status Tag` component.
*   **Data & Interaction:**
    *   **Data:** This widget must accept a single `Order` data model object as its main parameter. It will populate its layout using properties from this object (e.g., `order.id`, `order.clientName`, `order.status`).
    *   **Interaction:** The entire card is tappable. The `onTap` callback will execute a `go_router` navigation call to the specific order's detail screen (e.g., `context.go('/orders/${order.id}')`).
*   **States:** Default, Pressed (with visual feedback on tap).

#### Composite Component: KPI Card
*   **Purpose:** To display a single, high-level metric on the Manager Dashboard.
*   **Composition:** A `Card` container holding a large `Text` element for the value and a smaller `Text` element for the label.
*   **Data & Interaction:**
    *   **Data:** This is a simple display component that accepts a `String` for the value and a `String` for the label.
    *   **Interaction:** This component is not interactive.
*   **Usage Guidelines:** Used exclusively on the Manager Dashboard.

#### Composite Component: Notification Item
*   **Purpose:** To display a single notification in a list, indicating its content and read/unread status.
*   **Composition:** A `ListTile` containing an `Icon`, two `Text` elements, and a trailing `Text` element.
*   **Data & Interaction:**
    *   **Data:** This widget must accept a single `Notification` data model object as its parameter. The `isRead` boolean property on this model will determine the visual state (bold vs. regular font weight).
    *   **Interaction:** The `onTap` callback will use `go_router` to navigate the user to the relevant context (e.g., the specific order detail screen associated with the notification).
*   **States:** Unread, Read, Pressed.

### State & Feedback Components

These components are essential for communicating the application's state to the user, as mandated by the online-only, simplified architecture.

#### Component: LoadingIndicator
*   **Purpose:** To provide visual feedback to the user when the application is fetching data from the network.
*   **Composition:** A centered `CircularProgressIndicator`.
*   **State Management:** Its visibility is controlled by an `isLoading` boolean property from a `Provider`. It is typically displayed as an overlay or in place of content that is being loaded.

#### Component: RetryableErrorDisplay
*   **Purpose:** To inform the user of a failed network request (due to no connectivity or a server error) and provide a way to retry the action. This component is critical for our online-only architecture, which infers offline status from failed HTTP requests.
*   **Composition:**
    *   An `Icon` representing an error or offline state.
    *   A `Text` element to display the error message.
    *   A `Primary Button` with the text "Retry".
*   **Data & Interaction:**
    *   **Data:** Accepts an `errorMessage` string to display to the user.
    *   **Interaction:** The "Retry" button's `onPressed` callback must be wired to re-execute the function that failed (e.g., `orderProvider.fetchOrders()`).
*   **Usage Guidelines:** This component should be displayed in place of the main content area whenever a critical data fetch fails.

---