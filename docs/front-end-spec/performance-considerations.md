# Performance Considerations

<!--docs/front-end-spec/[title].md-->

Performance is a critical aspect of the user experience. A fast, responsive application feels reliable and professional. This section defines our performance targets and the design strategies we will employ to meet them, operating strictly within the constraints of the project's simplification plan. Our focus is on achieving a responsive UI while adhering to an online-only architecture with minimal dependencies.

## Performance Goals

To ensure a high-quality user experience, the application will adhere to the following performance benchmarks:

*   **App Startup Time:** The application must launch from a cold start to an interactive state in under **2 seconds**.
*   **Screen Transition Time:** Navigating between screens must feel nearly instantaneous, with transitions completing in under **500ms**. This will be facilitated by the declarative, centralized routing of `go_router`.
*   **Interaction Response:** The UI must provide immediate feedback for user input (e.g., a button tap) in under **100ms**.
*   **Animation Smoothness:** All animations and scrolling must maintain a consistent **60 frames per second (FPS)** to avoid "jank" or stuttering.

## Design Strategies

Our design and development approach will incorporate the following strategies to achieve our performance goals. These strategies are explicitly designed to align with the project's online-only, minimal-dependency architecture.

*   **Single Source of Truth: The Backend**
    *   In line with the online-only architecture, **no client-side caching will be implemented**. The application will fetch all data directly from the network on demand. This ensures that the user always sees the most current data and eliminates the complexity of cache validation logic. Performance will be achieved through efficient data fetching and responsive UI feedback during network operations.

*   **Efficient List Rendering and Data Fetching**
    *   All lists in the application (e.g., order history, prescription queue) will be built using virtualized scrolling (`ListView.builder`). This is a core Flutter feature that ensures smooth performance even with large datasets by rendering only the items visible on screen.
    *   **Network UX:** When fetching list data, the UI will display a single, centralized loading indicator (e.g., `CircularProgressIndicator`). If the network request fails, the indicator will be replaced with a clear error message and a "Retry" button, directly implementing the "infer offline from failed requests" strategy.

*   **Asynchronous Operations and Immediate Feedback**
    *   No network requests or heavy computations will be performed on the main UI thread. All API calls will be asynchronous to keep the UI from freezing.
    *   **Network UX:** For any action that initiates a network call (e.g., "Create Order," "Submit Prescription"), the UI **must** provide immediate feedback. The button should enter a disabled/loading state to prevent duplicate taps, and the form should become non-interactive until a server response is received. This communicates that the system is working without requiring complex state management.

*   **Simplified Image Handling**
    *   As per the simplification plan, the `image` package is removed. All user-uploaded prescription images will be compressed using the built-in `imageQuality` parameter of the `image_picker` package before upload. This provides a reasonable level of optimization without adding an external dependency.
    *   **Network UX:** During image uploads, the UI will display an **indeterminate loading spinner**. This provides clear feedback that an operation is in progress and aligns with the architectural goal of using the simpler `http` package, which does not have built-in support for determinate progress tracking. The primary goal is user feedback with minimal implementation complexity.

---