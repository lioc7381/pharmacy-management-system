# UI Feedback and Animation Guidelines

<!--docs/front-end-spec/[title].md-->

## Introduction

Animation is not a feature. It is a by-product of using standard Flutter widgets. We will not implement custom animations, transitions, or effects. The focus is on leveraging the built-in, default behaviors of the Flutter framework to provide essential user feedback without adding any implementation overhead.

## Guiding Principles

1.  **Leverage Framework Defaults:** All UI feedback will be derived from the default behavior of standard Material Design widgets (e.g., `ElevatedButton`, `InkWell`, `CircularProgressIndicator`). No custom animation code will be written.
2.  **Clarity Over Decoration:** The only "animations" permitted are those that serve a critical function, such as indicating a loading state. All purely decorative or aesthetic motion is prohibited.
3.  **Minimal Implementation:** The method for providing user feedback must be the one that requires the least amount of code and no new dependencies.
4.  **Consistency Through Simplicity:** The UI will be consistent because we will use the same standard widgets for the same purposes everywhere. We will not create custom animated components.

## Approved UI Feedback Mechanisms

The following are the only approved methods for providing visual feedback in the application.

*   **Tap Feedback (Ripple Effect):** Interactive elements like buttons, cards, and list items will use the default Material Design ripple effect provided by widgets like `InkWell` or built into buttons. This requires no extra implementation.
*   **Loading Indicators:** For any asynchronous network call, a standard `CircularProgressIndicator` will be displayed. This clearly communicates a "loading" state to the user and is a core Flutter widget.
*   **Screen Navigation:** We will use `go_router` with its default page transition. No custom slide, fade, or scale transitions will be created. The standard platform-adaptive transition provided by Flutter is sufficient.
*   **Confirmation & Error Feedback:** For feedback on completed actions (e.g., "Submission Successful," "Invalid Password"), a `SnackBar` will be used. This is a simple, standard, and non-blocking way to display temporary messages.

## Prohibited Implementations

To maintain alignment with the simplification plan, the following are explicitly forbidden:

*   **Custom Screen Transitions:** Do not use `CustomTransitionPage` or similar APIs in `go_router` to alter navigation animations.
*   **Custom Animated Feedback:** Do not implement custom animations for success/error messages (e.g., a checkmark that animates in). Use a `SnackBar`.
*   **`AnimationController`:** Avoid using `AnimationController`, `AnimatedBuilder`, or other explicit animation APIs. If a feature seems to require them, it is a sign that the feature's UI is too complex for the scope of this project and must be simplified.
*   **Third-Party Animation Packages:** No new dependencies for animation (like `lottie` or `rive`) are permitted.

## Performance & Accessibility

### Performance

By exclusively using standard Flutter widgets and their default animations, we rely on the performance optimizations built into the framework. No manual performance profiling of animations is required, as we are not creating any.

### Accessibility

The project will not implement a custom "reduce motion" or "remove animations" mode. The default animations provided by the Material widget set are minimal and functional. The complexity of implementing a separate mode for disabling these subtle effects directly contradicts the project's primary goal of simplicity and a minimal codebase. We will rely on the framework's default accessibility features.