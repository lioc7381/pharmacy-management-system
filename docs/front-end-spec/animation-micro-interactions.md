# Animation & Micro-interactions

In this application, animation is not for decoration; it is a functional tool used to enhance clarity, provide feedback, and build user confidence. Our approach is to use motion to make the interface feel more responsive and intuitive, directly supporting our design principles of "Efficiency is the Feature" and "Provide Constant Reassurance." All animations will be subtle, quick, and purposeful to avoid distracting from the user's primary task.

## Motion Principles

1.  **Functional & Purposeful:** Every animation must have a clear purpose, such as guiding the user's attention, confirming an action, or indicating a change in state. We will avoid purely decorative motion.
2.  **Responsive & Immediate:** The interface must provide immediate feedback for every user interaction. Tapping a button or selecting an item should trigger a subtle but instant visual response, making the app feel fast and reliable.
3.  **Consistent & Predictable:** Animation patterns will be used consistently throughout the application. For example, the transition for navigating to a new screen will always be the same, helping users build a predictable mental model of the app's structure.
4.  **Subtle & Unobtrusive:** Animations will be brief and understated, enhancing the user experience without getting in the way. The goal is to assist, not to distract or cause delays.

## Key Animations

The following are specific, high-impact animations and micro-interactions that will be implemented to improve the user experience.

*   **State Transitions:** Tapping any interactive element (buttons, list items) will trigger a subtle visual feedback, like the Material Design "ripple" effect. This confirms the system has registered the user's touch. (Duration: ~200ms, Easing: Linear)
*   **Screen Navigation:** When navigating deeper into a task (e.g., from the order list to an order detail screen), the new screen will slide in from the right. When returning, it will slide out to the right. This reinforces the hierarchical structure of the app. (Duration: ~300ms, Easing: Decelerate)
*   **Loading Indicators:** For any asynchronous action that takes more than a moment (e.g., uploading a prescription, fetching a list of orders), a clear, non-blocking loading indicator (e.g., a circular progress spinner) will be displayed. This manages user expectations and prevents them from thinking the app is frozen.
*   **Confirmation Feedback:** Upon a successful critical action, like submitting a prescription, the confirmation message and icon (e.g., ✅ "Submission Successful!") will subtly animate in (e.g., a gentle fade-in and scale-up). This provides a moment of positive reinforcement and clearly communicates success. (Duration: ~400ms, Easing: Overshoot)

## Performance & Accessibility Considerations

To ensure our animation strategy enhances the experience for all users without compromising performance or inclusivity, the following requirements must be met.

### Performance

*   **Risk:** Poorly implemented animations can negatively impact performance on lower-end devices, violating our goal of a "fast and reliable" app.
*   **Mitigation:**
    1.  All animations must be hardware-accelerated to ensure smoothness (standard Flutter animations typically handle this well).
    2.  Any custom animations must be profiled to ensure they maintain a consistent 60 FPS and do not cause frame drops ("jank").
    3.  The "Subtle & Unobtrusive" principle also applies to performance. An animation that causes a noticeable delay must be revised or removed.

### Accessibility

*   **Risk:** For users with vestibular disorders or motion sensitivity, animations can be a significant barrier to usability.
*   **Mitigation:**
    1.  To comply with our WCAG AA target, the application **must** respect the system-level "Remove animations" or "Reduce motion" setting in Android's accessibility options.
    2.  When this setting is enabled, all non-essential animations will be disabled or replaced. For example:
        *   Screen transitions will become simple cross-fades instead of slides.
        *   The "Confirmation Feedback" animation will be disabled, with the message appearing instantly.
    3.  Essential feedback animations, such as loading indicators, will remain as they communicate a necessary system state.

---
